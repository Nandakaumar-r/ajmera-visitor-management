<?php

namespace App\Http\Controllers;

use App\Mail\TechNocNotification;
use App\Models\ExitProcess;
use Illuminate\Http\Request;
use App\Models\Resignation;
use App\Services\SnipeITService;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AssetCollectionController extends Controller
{
    private $snipeIT;
    protected $snipeITService;
    
    public function __construct(SnipeITService $snipeITService)
    {
        $this->snipeIT = new Client([
            'base_uri' => config('services.snipeit.url'),
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.snipeit.token'),
                'Accept' => 'application/json',
            ]
        ]);
        $this->snipeITService = $snipeITService;
    }

    public function index(){
        // Get Resignation based on LWD of the Current Date  >= LWD
        $resignations = Resignation::where('manager_last_working_day', '>=', now())->with('employee')->get();
        return view('asset-collection.index', compact('resignations'));
    }

    public function show($resignation_id)
    {
        $resignation = Resignation::findOrFail($resignation_id);
        // Call the service SnipeITService function getUserByEmail
        $user =  $this->snipeITService->getUserByEmail($resignation->employee->employee_email);
        if(empty($user)){
            return back()->with('error', 'Failed to fetch employee details from Snipe IT');
        }

        // Fetch employee's assets from Snipe IT
        try {
            $response = $this->snipeIT->get("/api/v1/users/".$user['rows'][0]['id']."/assets");
            if(empty($response)){
                $assets = [];
            }else{
                $assets = json_decode((string) $response->getBody(), true);
            }

            $response = $this->snipeIT->get("/api/v1/users/" .$user['rows'][0]['id']. "/accessories");
            if(empty($response)){
                $accessories = [];
            }else{
                $accessories = json_decode((string) $response->getBody(), true);
            }

            $response = $this->snipeIT->get("/api/v1/users/" .$user['rows'][0]['id']. "/licenses");
            if(empty($response)){
                $licenses = [];
            }else{
                $licenses = json_decode((string) $response->getBody(), true);
            }

            return view('asset-collection.show', compact('resignation', 'assets', 'accessories', 'licenses'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error('Failed to fetch assets from Snipe IT: ' . $e->getMessage());
            return back()->with('error', 'Failed to fetch assets from Snipe IT');
        }
    }

    public function collect(Request $request, $resignation_id)
    {
        $resignation = Resignation::findOrFail($resignation_id);
        
        // Validate request
        $request->validate([
            'collected_assets' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        try {
            // Mark assets as returned in Snipe IT
            foreach ($request->collected_assets as $asset_id) {
                $this->snipeIT->post("/api/v1/hardware/{$asset_id}/checkin", [
                    'json' => [
                        'note' => $request->notes,
                        'status_id' => 1 // Assuming 1 is your "Available" status
                    ]
                ]);
            }

            // Update resignation status
            $resignation->update([
                'assets_collected' => true,
                'asset_collection_date' => now(),
                'asset_collection_notes' => $request->notes
            ]);

            ExitProcess::where('employee_id', $resignation->employee_id)->update(['assets_collected' => 1]);

            return redirect()->back()->with('success', 'Assets collected successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process asset collection');
        }
    }

    public function generateNOC($resignation_id)
    {
        $resignation = Resignation::findOrFail($resignation_id);
        // Ensure all assets are collected
        // if (!$resignation->assets_collected) {
        //     return back()->with('error', 'Cannot generate NOC until all assets are collected');
        // }
    
        // Fetch assets, accessories, and licenses from Snipe-IT API
        $assets = json_decode((string) $this->snipeIT->get("/api/v1/users/".$resignation->employee_id."/assets")->getBody(), true)['rows'];
        $accessories = json_decode((string) $this->snipeIT->get("/api/v1/users/".$resignation->employee_id."/accessories")->getBody(), true)['rows'];
        $licenses = json_decode((string) $this->snipeIT->get("/api/v1/users/".$resignation->employee_id."/licenses")->getBody(), true)['rows'];
    
        // Update NOC status
        $resignation->update([
            'noc_generated' => true,
            'noc_date' => now()
        ]);
    
        // Generate NOC PDF
        $pdf = Pdf::loadView('pdf.technoc', compact('resignation', 'assets', 'accessories', 'licenses'));
    
        // Save PDF and email to HR and employee
        $pdfPath = storage_path("app/public/noc/NOC_{$resignation->id}.pdf");
        $pdf->save($pdfPath);
    
        // resignation->employee->email
        Mail::to(['employee@example.com', 'hr@example.com'])->send(new TechNocNotification($resignation, $pdfPath));
    
        return back()->with('success', 'NOC generated and sent to HR and Employee successfully');
    }
    
}

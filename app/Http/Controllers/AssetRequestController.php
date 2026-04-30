<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetCategory;
use App\Models\AssetMaintenanceRecord;
use App\Notifications\AssetRequestStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\Employee;

class AssetRequestController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->employee;
        $requests = AssetRequest::where('employee_id', $employee->employee_id)
            ->with(['asset', 'asset.category'])
            ->latest()
            ->paginate(10);

        return view('assets.index', compact('requests'));
    }

    public function create()
    {
        $assetCategories = AssetCategory::all();
        return view('assets.create', compact('assetCategories'));
    }

    public function getAssetsByCategory($categoryId)
    {
        $assets = Asset::where('category_id', $categoryId)
            ->where('quantity', '>', 0)
            ->select('id', 'name', 'quantity', 'unit')
            ->get();

        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
            'justification' => 'nullable|string'
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        
        // if ($asset->quantity < $request->quantity) {
        //     return back()->with('error', 'Requested quantity is not available in stock.');
        // }

        $user = auth()->user();
        $employee = $user->employee;

        $assetRequest = AssetRequest::create([
            'employee_id' => $employee->employee_id,
            'asset_id' => $request->asset_id,
            'quantity' => $request->quantity,
            'justification' => $request->justification,
            'status' => 'pending'
        ]);

        $employee = Employee::where('employee_id', $assetRequest->employee_id)->first();

        // Notify employee
        $employee->user->notify(new AssetRequestStatusChanged($assetRequest));

        // Notify HR
        $hrUsers = Employee::whereHas('user.roles', function($query) {
            $query->where('name', 'HR');
        })->get();

        Notification::send($hrUsers, new AssetRequestStatusChanged($assetRequest));

        // Notify Tech Support if asset category is Electronics
        if ($asset->category->name === 'Electronics') {
            $techSupportUsers = Employee::whereHas('user.roles', function($query) {
                $query->where('name', 'Tech');
            })->get();

            Notification::send($techSupportUsers, new AssetRequestStatusChanged($assetRequest));
        }
        
        return redirect()->route('assets.show', $assetRequest)
            ->with('success', 'Asset request submitted successfully.');
    }

    public function show(AssetRequest $assetRequest)
    {        
        return view('assets.show', compact('assetRequest'));    
    }

    public function approve(AssetRequest $assetRequest)
    {
        $this->authorize('approve', $assetRequest);

        if ($assetRequest->asset->quantity < $assetRequest->quantity) {
            return back()->with('error', 'Insufficient quantity in stock.');
        }

        $assetRequest->update([
            'status' => 'approved',
            'handover_date' => now(),
            'approved_by' => auth()->user()->employee->employee_id
        ]);

        // Update asset quantity
        $assetRequest->asset->decrement('quantity', $assetRequest->quantity);

        // Notify employee
        $assetRequest->employee->user->notify(new AssetRequestStatusChanged($assetRequest));

        return back()->with('success', 'Asset request approved successfully.');
    }

    public function reject(AssetRequest $assetRequest)
    {
        $this->authorize('approve', $assetRequest);

        $assetRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->user()->employee->employee_id
        ]);

        // Notify employee
        $assetRequest->employee->user->notify(new AssetRequestStatusChanged($assetRequest));

        return back()->with('success', 'Asset request rejected.');
    }

    public function return(AssetRequest $assetRequest, Request $request)
    {
        $this->authorize('return', $assetRequest);

        $request->validate([
            'condition_on_return' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        $assetRequest->update([
            'status' => 'returned',
            'return_date' => now(),
            'condition_on_return' => $request->condition_on_return,
            'remarks' => $request->remarks
        ]);

        // Increase asset quantity only for consumables
        if ($assetRequest->asset->isConsumable()) {
            $assetRequest->asset->increment('quantity', $assetRequest->quantity);
        }

        return back()->with('success', 'Asset returned successfully.');
    }

    public function addMaintenance(Asset $asset, Request $request)
    {
        $this->authorize('manage', $asset);

        $request->validate([
            'maintenance_date' => 'required|date',
            'description' => 'required|string',
            'cost' => 'nullable|numeric',
            'vendor' => 'nullable|string',
            'next_maintenance_due' => 'nullable|date|after:maintenance_date'
        ]);

        AssetMaintenanceRecord::create([
            'asset_id' => $asset->id,
            'maintenance_date' => $request->maintenance_date,
            'description' => $request->description,
            'cost' => $request->cost,
            'vendor' => $request->vendor,
            'next_maintenance_due' => $request->next_maintenance_due
        ]);

        return back()->with('success', 'Maintenance record added successfully.');
    }
}

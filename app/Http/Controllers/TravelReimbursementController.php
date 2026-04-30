<?php

namespace App\Http\Controllers;

use App\Constants\RoleEmails;
use App\Models\BankDetail;
use App\Models\TravelRequest;
use App\Models\TravelReimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ReimbursementSubmitted;
use App\Notifications\ReimbursementStatusUpdated;
use App\Services\ChatbotService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class TravelReimbursementController extends Controller
{

    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }
    public function create()
    {
        //$this->authorize('createReimbursement', $travelRequest);

        return view('travel.reimbursements.create');
    }

    // public function store(TravelRequest $travelRequest, Request $request)
    // {
    //    // $this->authorize('createReimbursement', $travelRequest);

    //     $validated = $request->validate([
    //         'amount' => 'required|numeric|min:0',
    //         'description' => 'required|string',
    //         'receipts.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
    //     ]);

    //     $receiptPaths = [];
    //     foreach ($request->file('receipts') as $receipt) {
    //         $path = $receipt->store('travel-receipts', 'public');
    //         $receiptPaths[] = $path;
    //     }

    //     $reimbursement = TravelReimbursement::create([
    //         'travel_request_id' => $travelRequest->id,
    //         'user_id' => Auth::id(),
    //         'amount' => $validated['amount'],
    //         'description' => $validated['description'],
    //         'receipt_files' => $receiptPaths,
    //         'status' => 'pending',
    //     ]);

    //     // Notify HR
    //    // $hrUsers = User::role('hr')->get();
    //    // Notification::send($hrUsers, new ReimbursementSubmitted($reimbursement));

    //     return redirect()->route('travel.show', $travelRequest)
    //         ->with('success', 'Reimbursement request submitted successfully.');
    // }
    // public function tableCreation(TravelRequest $travelRequest, Request $request)
    // {
    //     $validated = $request->validate([
    //         // 'amount' => 'required|numeric|min:0',
    //         // 'description' => 'required|string',
    //         'receipts' => 'required|array|min:1',
    //         'receipts.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
    //     ]);

    //     $receiptPaths = [];
    //     $ocrResults = [];
    //     $aiResponse = [];

    //     foreach ($request->file('receipts') as $index => $receipt) {
    //         $userId = Auth::id();
    //         $month = Carbon::now()->format('F'); // e.g., 'April'

    //         // Create destination path and filename
    //         $folderPath = "reimbursement/{$userId}/{$month}";
    //         $filename = uniqid() . '.' . $receipt->getClientOriginalExtension();

    //         $destinationPath = public_path($folderPath);

    //         if (!file_exists($destinationPath)) {
    //             mkdir($destinationPath, 0755, true);
    //         }

    //         $receipt->move($destinationPath, $filename);

    //         $localPath = "{$folderPath}/{$filename}";
    //         $receiptPaths[] = $localPath;

    //         // Get MIME type
    //         $mimeType = mime_content_type(public_path($localPath));
    //         $base64 = base64_encode(file_get_contents(public_path($localPath)));

    //         // OCR API call
    //         $response = Http::timeout(30)
    //             ->withOptions(['verify' => false])
    //             ->asForm()
    //             ->post('https://api.ocr.space/parse/image', [
    //                 'apikey' => 'K81263119288957',
    //                 'base64Image' => 'data:' . $mimeType . ';base64,' . $base64,
    //                 'language' => 'eng',
    //             ]);
    //         // dd($response);
    //         if ($response->successful()) {
    //             $result = $response->json();
    //             $ocrText = $result['ParsedResults'][0]['ParsedText'] ?? null;
    //             if ($ocrText) {
    //                 $ocrResults[] = $ocrText;
    //                 $aiResponse[$index] = $this->chatbotService->extractBillDetails($ocrText);
    //                 $aiResponse[$index]['filePath'] = $localPath;
    //             } else {
    //                 Log::warning("OCR parsing failed for file index $index", $result);
    //             }
    //         } else {
    //             Log::error("OCR API request failed for file index $index", [
    //                 'status' => $response->status(),
    //                 'body' => $response->body(),
    //             ]);
    //         }
    //     }
    //     return $aiResponse;

    //     // $reimbursement = TravelReimbursement::create([
    //     //     'travel_request_id' => $travelRequest->id,
    //     //     'user_id' => Auth::id(),
    //     //     'amount' => $validated['amount'] ?? 0,
    //     //     'description' => $validated['description'] ?? '',
    //     //     'receipt_files' => $receiptPaths,
    //     //     'status' => 'pending',
    //     // ]);

    //     // Log::info('OCR Extracted Results:', $ocrResults);
    //     // Log::info('AI Parsed Responses:', $aiResponse);

    //     // return redirect()->route('travel.show', $travelRequest)
    //     //     ->with('success', 'Reimbursement submitted. OCR data extracted.');
    // }

    public function tableCreation(TravelRequest $travelRequest, Request $request)
    {
        $validated = $request->validate([
            'receipts' => 'nullable|array',
            'receipts.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'reimbursement_excel' => 'nullable|file|mimes:xlsx,xls',
        ]);

        if (!$request->hasFile('receipts') && !$request->hasFile('reimbursement_excel')) {
            return response()->json(['message' => 'Please upload at least receipts or excel file.'], 422);
        }


        $receiptPaths = [];
        $ocrResults = [];
        $aiResponse = [];
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $index => $receipt) {
                $userId = Auth::id();
                $month = Carbon::now()->format('F');

                $folderPath = "reimbursement/{$userId}/{$month}";
                $filename = uniqid() . '.' . $receipt->getClientOriginalExtension();
                $destinationPath = public_path($folderPath);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $receipt->move($destinationPath, $filename);
                $localPath = "{$folderPath}/{$filename}";
                $receiptPaths[] = $localPath;

                $mimeType = mime_content_type(public_path($localPath));
                $base64 = base64_encode(file_get_contents(public_path($localPath)));

                $response = Http::timeout(180)
                    ->withOptions(['verify' => false])
                    ->asForm()
                    ->post('https://api.ocr.space/parse/image', [
                        'apikey' => 'K81263119288957',
                        'base64Image' => 'data:' . $mimeType . ';base64,' . $base64,
                        'language' => 'eng',
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $ocrText = $result['ParsedResults'][0]['ParsedText'] ?? null;
                    if ($ocrText) {
                        $ocrResults[] = $ocrText;
                        $aiResponse[$index] = $this->chatbotService->extractBillDetails($ocrText);
                        $aiResponse[$index]['filePath'] = $localPath;
                    }
                }
            }
        }
        // ✅ NEW: If Excel uploaded, parse it too
        $excelData = [];
        if ($request->hasFile('reimbursement_excel')) {
            $excelArray = Excel::toArray([], $request->file('reimbursement_excel'));
            $sheet = $excelArray[0] ?? [];
            $excelData = [
                'raw' => $sheet,
                'summary' => [
                    'name' => $sheet[2][2] ?? null,
                    'empId' => $sheet[3][2] ?? null,
                ]
            ];
        }

        return response()->json([
            'receipts' => $aiResponse,
            'excel' => $excelData,
        ]);
    }


    // public function store(Request $request)
    // {

    //     try {
    //         $data = $request->input('reimbursementData');
    //         $totalAmount = 0;

    //         foreach ($data as $item) {
    //             $totalAmount += floatval($item['amount']);
    //         }

    //         $total = round($totalAmount, 2);


    //         if (!isset($data[0]['manager_email']) || empty($data[0]['manager_email'])) {
    //             return response()->json(['message' => 'Manager email is required.'], 400);
    //         }
    //         $reimbursement = TravelReimbursement::create([
    //             //'travel_request_id' => 1,
    //             'user_id' => Auth::id(),
    //             'details' => json_encode($data),
    //             'status' => 'pending',
    //             'manager_email' => $data[0]['manager_email'],
    //             'amount' => $total
    //         ]);

    //         //  }
    //         $link = url('/reimbursement/action/' . $reimbursement->id);

    //         Mail::send('emails.reimbursement', [
    //             // 'recipientName' => 'Manager',
    //             'content' => "<p>A new reimbursement request has been submitted by <strong>" . Auth::user()->name . "</strong>.</p>
    //                             <p><a href=\"{$link}\">Click here to review the request</a></p>",
    //             'logoUrl' => asset('images/logo.png'),
    //         ], function ($message) use ($data) {
    //             $message->to($data[0]['manager_email'])
    //                 ->subject('Reimbursement Request Submitted for Review');
    //         });

    //         return response()->json(['message' => 'Reimbursement data submitted successfully.']);
    //     } catch (\Exception $e) {
    //         Log::error('Error saving reimbursement data: ' . $e->getMessage());
    //         return response()->json(['message' => 'Error saving reimbursement data.'], 500);
    //     }
    // }

    public function store(Request $request)
    {
        try {
            $managerEmail = null; // Initialize
            $company = null; // Initialize

            $combinedData = [];   // ✅ Store both receipts & excel data here

            // ✅ If Excel file uploaded
            if ($request->hasFile('reimbursement_excel')) {
                $request->validate([
                    'reimbursement_excel' => 'file|mimes:xlsx,xls',
                    'manager_email' => 'required|email',
                    'company' => 'required|string|max:255',
                ]);

                $excelData = Excel::toArray([], $request->file('reimbursement_excel'));
                $sheet = $excelData[0];

                $managerEmail = $request->input('manager_email');
                $company = $request->input('company');

                // Parse Excel rows
                $i = 0;
                while ($i < count($sheet)) {
                    $row = $sheet[$i];
                    if (isset($row[0]) && trim(strtolower($row[0])) === 'date') {
                        $i++;
                        while ($i < count($sheet)) {
                            $dataRow = $sheet[$i];
                            $isEmpty = empty(array_filter($dataRow));
                            $isNewBlock = isset($dataRow[0]) && is_string($dataRow[0]) && str_contains(strtolower($dataRow[0]), 'reimbursement');
                            if ($isEmpty || $isNewBlock) break;

                            $excelDate = $dataRow[0];
                            $formattedDate = '';
                            if (is_numeric($excelDate)) {
                                $epoch = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate);
                                $formattedDate = $epoch->format('Y-m-d');
                            }

                            $description = $dataRow[2] ?? '';
                            $event = $dataRow[9] ?? '';
                            $amount = $dataRow[12] ?? 0;

                            if ($description && $amount) {
                                $combinedData[] = [
                                    'date' => $formattedDate,
                                    'description' => $description,
                                    'event' => $event,
                                    'amount' => $amount,
                                    'bill' => '',
                                    'currency' => 'INR',
                                    'manager_email' => $managerEmail,
                                    'company' => $company
                                ];
                            }

                            $i++;
                        }
                    } else {
                        $i++;
                    }
                }

                // ✅ Also save any uploaded receipts (if any!)
                if ($request->hasFile('receipts')) {
                    $userId = Auth::id();
                    $monthName = now()->format('F');

                    foreach ($request->file('receipts') as $file) {
                        $filePath = $file->store("reimbursement/{$userId}/{$monthName}", 'public');

                        $combinedData[] = [
                            'bill' => $filePath,
                            'date' => 'undefined',
                            'description' => 'undefined',
                            'event' => 'undefined',
                            'amount' => null,
                            'currency' => 'INR',
                            'manager_email' => $managerEmail,
                            'company' => $company
                        ];
                    }
                }


                // ✅ Save to DB
                $totalAmount = 0;
                foreach ($combinedData as $item) {
                    $totalAmount += floatval($item['amount'] ?? 0);
                }

                $reimbursement = TravelReimbursement::create([
                    'user_id' => Auth::id(),
                    'details' => json_encode($combinedData),
                    'status' => 'pending',
                    'manager_email' => $managerEmail,
                    'amount' => $totalAmount,
                    'company' => $company,
                ]);
            }
            // ✅ For JSON front-end table submission
            elseif ($request->filled('reimbursementData')) {
                $data = json_decode($request->input('reimbursementData'), true);

                if (!is_array($data)) {
                    return response()->json(['message' => 'Invalid data'], 400);
                }

                $managerEmail = $request->input('manager_email');
                $company = $request->input('company');

                // ✅ DO NOT re-process uploaded receipts here
                // The receipts are already saved on the front-end & URL is in `bill`

                // ✅ Ensure currency key always exists
                $data = array_map(function ($item) {
                    $item['currency'] = $item['currency'] ?? 'INR';
                    return $item;
                }, $data);

                // ✅ Save to DB
                $totalAmount = 0;
                foreach ($data as $item) {
                    $totalAmount += floatval($item['amount'] ?? 0);
                }

                $reimbursement = TravelReimbursement::create([
                    'user_id' => Auth::id(),
                    'details' => json_encode($data),
                    'status' => 'pending',
                    'manager_email' => $managerEmail,
                    'company' => $company,
                    'amount' => $totalAmount,

                ]);
            }

            // ❌ Neither Excel nor JSON data
            else {
                return response()->json(['message' => 'Please submit either Excel or Reimbursement Details.'], 400);
            }

            // ✅ Send mail
            $link = url('/reimbursement/action/' . $reimbursement->id);

            Mail::send('emails.reimbursement', [
                'content' => "<p>A new reimbursement request has been submitted by <strong>" . Auth::user()->name . "</strong>.</p>
                <p><a href=\"{$link}\">Click here to review the request</a></p>",
                'logoUrl' => asset('images/logo.png'),
            ], function ($message) use ($managerEmail) {
                $message->to($managerEmail)
                    ->subject('Reimbursement Request Submitted for Review');
            });

            return response()->json(['message' => 'Reimbursement submitted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error saving reimbursement: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    // Show the form with details
    public function showActionForm($id)
    {
        $reimbursements = TravelReimbursement::with('user')->findOrFail($id);

        return view('travel.reimbursements.action', compact('reimbursements'));
    }
    // Handle the form submission
    public function processAction(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,hold,release_hold',
        ]);

        $reimbursements = TravelReimbursement::findOrFail($id);
        $user = $reimbursements->user;

        if ($reimbursements->status == 'processed') {
            return redirect()->back()->with('status', 'This reimbursement has already been processed.');
        }

        // Handle Release from Hold
        if ($request->status === 'release_hold') {
            if ($reimbursements->status === 'hold' && $reimbursements->previous_status) {
                $reimbursements->status = $reimbursements->previous_status;
                $reimbursements->previous_status = null;
                $reimbursements->rejection_reason = $request->input('rejection_reason');
                $reimbursements->save();

                $redirectUrl = $request->input('redirect_url', url()->previous());
                return redirect($redirectUrl)->with('status', 'Reimbursement released from hold.');
            }
            $redirectUrl = $request->input('redirect_url', url()->previous());
            return redirect($redirectUrl)->with('error', 'Cannot release: not on hold or no previous status.');
        }

        // Handle Hold - Save current status before putting on hold
        if ($request->status === 'hold') {
            $reimbursements->previous_status = $reimbursements->status;
            $reimbursements->status = 'hold';
            $reimbursements->rejection_reason = $request->input('rejection_reason');
            $reimbursements->save();

            // Notify employee
            // Mail::send('emails.reimbursement', [
            //     'recipientName' => $user->name,
            //     'content' => "<p>Your reimbursement request has been <strong>put on hold</strong>.</p>
            //               <p><strong>Reason:</strong> {$reimbursements->rejection_reason}</p>",
            //     'logoUrl' => asset('images/logo.png'),
            // ], function ($message) use ($user) {
            //     $message->to($user->email)->subject("Reimbursement Request On Hold");
            // });

            $redirectUrl = $request->input('redirect_url', url()->previous());
            return redirect($redirectUrl)->with('status', 'Reimbursement put on hold and employee notified.');
        }

        // Handle Rejection
        if ($request->status === 'rejected') {
            $reimbursements->status = 'rejected';
            $reimbursements->rejection_reason = $request->input('rejection_reason');
            $reimbursements->save();

            // Notify employee
            Mail::send('emails.reimbursement', [
                'recipientName' => $user->name,
                'content' => "<p>Your reimbursement request has been <strong>rejected</strong>.</p>
                          <p><strong>Reason:</strong> {$reimbursements->rejection_reason}</p>",
                'logoUrl' => asset('images/logo.png'),
            ], function ($message) use ($user) {
                $message->to($user->email)->subject("Reimbursement Request Rejected");
            });

            return redirect()->back()->with('status', 'Reimbursement rejected and employee notified.');
        }
     if ($request->status === 'approved') {
            $reimbursements->rejection_reason = $request->input('rejection_reason');
            $reimbursements->save();
        }

        $currentStatus = $reimbursements->status;
        $month = now()->format('F');
        //$redirectUrl = '/travel/reimbursements/internal_review';
        $successMessage = 'Reimbursement Approved successfully.';

        switch ($currentStatus) {
            case 'pending':
                $reimbursements->status = 'manager_approved';
                // Send email to HR only when Manager approves
                Mail::to(RoleEmails::HR_APPROVAL_EMAIL)->send(
                    new \App\Mail\InternalReimbursementHRApproval($reimbursements, $month)
                );
                break;

            case 'manager_approved':
                $reimbursements->status = 'hr_approved';
                //$redirectUrl = '/travel/reimbursements/internal_review';
                break;

            case 'hr_approved':
                $reimbursements->status = 'accountant_approved';
                //$redirectUrl = '/travel/reimbursements/accountant';
                break;

            case 'accountant_approved':
                $reimbursements->status = 'cfo_approved';
                //$redirectUrl = '/travel/reimbursements/cfo';
                break;

            case 'cfo_approved':
                $reimbursements->status = 'processed';
                //$redirectUrl = '/travel/reimbursements/finance';
                break;

            default:
                return back()->with('error', 'Invalid status transition or already processed.');
        }
        // If Manager is acting (status was 'pending'), keep him on same page
        if ($reimbursements->getOriginal('status') === 'pending') {
            $redirectUrl = url('/reimbursement/action/' . $reimbursements->id);
        }
        $redirectUrl = $request->input('redirect_url', url()->previous());
        $reimbursements->save();

        return redirect($redirectUrl)->with('success', $successMessage);
    }

    public function sendPendingApprovalEmails()
    {
        $month = now()->format('F');

        // Priority order: HR -> Accountant -> CFO
        if ($reimbursements = TravelReimbursement::where('status', 'hr_approved')->get()) {
            if ($reimbursements->isNotEmpty()) {
                $recipient = RoleEmails::ACCOUNTANT_APPROVAL_EMAIL;
                $mailClass = new \App\Mail\InternalReimbursementAccountantApproval($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Email sent to Accountant.');
            }
        }

        if ($reimbursements = TravelReimbursement::where('status', 'accountant_approved')->get()) {
            if ($reimbursements->isNotEmpty()) {
                $recipient = RoleEmails::CFO_APPROVAL_EMAIL;
                $mailClass = new \App\Mail\InternalReimbursementCFOApproval($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Email sent to CFO.');
            }
        }

        if ($reimbursements = TravelReimbursement::where('status', 'cfo_approved')->get()) {
            if ($reimbursements->isNotEmpty()) {
                $recipient = RoleEmails::FINANCE_APPROVAL_EMAIL;
                $mailClass = new \App\Mail\InternalReimbursementFinalProcessing($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Email sent to Finance.');
            }
        }

        if ($reimbursements = TravelReimbursement::where('status', 'processed')->get()) {
            if ($reimbursements->isNotEmpty()) {
                $recipient = [RoleEmails::HR_APPROVAL_EMAIL, RoleEmails::ACCOUNTANT_APPROVAL_EMAIL];
                $mailClass = new \App\Mail\InternalReimbursementFinalConfirmation($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Final confirmation email sent to HR, CFO and Accountant.');
            }
        }

        return back()->with('info', 'No pending approvals found at any stage.');
    }

    public function internalBulkApproval(Request $request)
    {

        $ids = $request->input('reimbursement_ids', []);
        $approvedCount = 0;
        $skippedCount = 0;
        $month = now()->format('F');

        foreach ($ids as $id) {
            $reimbursement = TravelReimbursement::find($id);
            if (!$reimbursement || in_array($reimbursement->status, ['processed', 'rejected', 'hold'])) {
                $skippedCount++;
                continue;
            }


            switch ($reimbursement->status) {
                case 'manager_approved':
                    $reimbursement->status = 'hr_approved';
                    break;

                case 'hr_approved':
                    $reimbursement->status = 'accountant_approved';
                    break;

                case 'accountant_approved':
                    $reimbursement->status = 'cfo_approved';
                    break;

                case 'cfo_approved':
                    $reimbursement->status = 'processed';
                    break;

                default:
                    return back()->with('error', 'Invalid status transition or Rejected or already processed.');
                    break;
            }

            $reimbursement->save();
            $approvedCount++;
        }

        return back()->with('success', "{$approvedCount} reimbursements approved. {$skippedCount} skipped.");
    }

    public function approve(TravelReimbursement $reimbursement)
    {
        $this->authorize('approveReimbursement', $reimbursement);

        $reimbursement->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Notify employee
        Notification::send($reimbursement->user, new ReimbursementStatusUpdated($reimbursement));

        return redirect()->back()->with('success', 'Reimbursement approved successfully.');
    }

    public function reject(TravelReimbursement $reimbursement, Request $request)
    {
        $this->authorize('rejectReimbursement', $reimbursement);

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $reimbursement->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify employee
        Notification::send($reimbursement->user, new ReimbursementStatusUpdated($reimbursement));

        return redirect()->back()->with('success', 'Reimbursement rejected.');
    }

    public function show(Request $request)
    {
        $query = travelReimbursement::with('user.employee.manager', 'user.employee.designation')
            ->where('user_id', Auth::id());

        // Add filter for Emp ID
        if ($request->filled('emp_id')) {
            $query->where('emp_id', 'like', '%' . $request->emp_id . '%');
        }

        $reimbursements = $query->paginate(10);

        // Decode details JSON for each reimbursement
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->parsed_details = is_array($reimbursement->details)
                ? $reimbursement->details
                : json_decode($reimbursement->details, true);
        }

        return view('travel.reimbursements.show', compact('reimbursements'));
    }

    public function showInternalReview(Request $request)
    {

        $query = travelReimbursement::with('user.employee.manager')
            ->where('status', '!=', 'pending')
            ->orderBy('created_at', 'desc');

        // Filter by month option (default: current month)
        $monthFilter = $request->input('month_filter', 'current');

        switch ($monthFilter) {
            case 'all':
                // No date filter
                break;
            case 'last_1':
                $query->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(1)->year);
                break;
            case 'last_2':
                $query->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(2)->year);
                break;
            case 'last_3':
                $query->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(3)->year);
                break;
            default:
                // current month
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
        }

        // Search by Employee ID or Employee Name from the employee table
        if ($request->filled('emp_id')) {
            $empInput = $request->emp_id;
            $query->whereHas('user.employee', function ($q) use ($empInput) {
                $q->where('employee_id', 'like', '%' . $empInput . '%')
                    ->orWhere('employee_name', 'like', '%' . $empInput . '%')
                    ->orWhere('status', 'like', '%' . $empInput . '%')
                    ->orWhere('company', 'like', '%' . $empInput . '%');
            });
        }

        $reimbursements = $query->paginate(10);
        $reimbursements->appends($request->except('page'));

        // Decode details JSON for each reimbursement
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->parsed_details = is_array($reimbursement->details)
                ? $reimbursement->details
                : json_decode($reimbursement->details, true);
        }

        return view('travel.reimbursements.internal', compact('reimbursements', 'monthFilter'));
    }

    public function showInternalAccountant(Request $request)
    {

        $query = travelReimbursement::with('user.employee.manager')
            ->whereNotIn('status', ['manager_approved', 'pending'])
            ->orderBy('created_at', 'desc');

        // Filter by month option (default: current month)
        $monthFilter = $request->input('month_filter', 'current');

        switch ($monthFilter) {
            case 'all':
                // No date filter
                break;
            case 'last_1':
                $query->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(1)->year);
                break;
            case 'last_2':
                $query->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(2)->year);
                break;
            case 'last_3':
                $query->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(3)->year);
                break;
            default:
                // current month
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
        }

        // Search by Employee ID or Employee Name from the employee table
        if ($request->filled('emp_id')) {
            $empInput = $request->emp_id;
            $query->whereHas('user.employee', function ($q) use ($empInput) {
                $q->where('employee_id', 'like', '%' . $empInput . '%')
                    ->orWhere('employee_name', 'like', '%' . $empInput . '%')
                    ->orWhere('company', 'like', '%' . $empInput . '%')
                    ->orWhere('status', 'like', '%' . $empInput . '%');
            });
        }

        $reimbursements = $query->paginate(10);
        $reimbursements->appends($request->except('page'));

        // Decode details JSON for each reimbursement
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->parsed_details = is_array($reimbursement->details)
                ? $reimbursement->details
                : json_decode($reimbursement->details, true);
        }

        return view('travel.reimbursements.accountant_internal', compact('reimbursements', 'monthFilter'));
    }

    public function accountantActionView($id)
    {
        $reimbursements = TravelReimbursement::with('user')->findOrFail($id);
        return view('travel.reimbursements.accountant_action', compact('reimbursements'));
    }

    public function showInternalCFO(Request $request)
    {

        $query = travelReimbursement::with('user.employee.manager')
            ->whereNotIn('status', ['manager_approved', 'pending', 'hr_approved', 'rejected', 'processed', 'hold'])
            ->orderBy('created_at', 'desc');

        // Filter by month option (default: current month)
        $monthFilter = $request->input('month_filter', 'current');

        switch ($monthFilter) {
            case 'all':
                // No date filter
                break;
            case 'last_1':
                $query->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(1)->year);
                break;
            case 'last_2':
                $query->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(2)->year);
                break;
            case 'last_3':
                $query->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(3)->year);
                break;
            default:
                // current month
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
        }

        // Search by Employee ID or Employee Name from the employee table
        if ($request->filled('emp_id')) {
            $empInput = $request->emp_id;
            $query->whereHas('user.employee', function ($q) use ($empInput) {
                $q->where('employee_id', 'like', '%' . $empInput . '%')
                    ->orWhere('employee_name', 'like', '%' . $empInput . '%')
                    ->orWhere('company', 'like', '%' . $empInput . '%')
                    ->orWhere('status', 'like', '%' . $empInput . '%');
            });
        }

        $reimbursements = $query->paginate(10);
        $reimbursements->appends($request->except('page'));

        // Decode details JSON for each reimbursement
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->parsed_details = is_array($reimbursement->details)
                ? $reimbursement->details
                : json_decode($reimbursement->details, true);
        }

        return view('travel.reimbursements.cfo_internal', compact('reimbursements', 'monthFilter'));
    }

    public function showInternalFinance(Request $request)
    {

        $query = travelReimbursement::with('user.employee.manager')
            ->whereNotIn('status', ['manager_approved', 'pending', 'hr_approved', 'rejected', 'accountant_approved', 'hold'])
            ->orderBy('created_at', 'desc');

        // Filter by month option (default: current month)
        $monthFilter = $request->input('month_filter', 'current');

        switch ($monthFilter) {
            case 'all':
                // No date filter
                break;
            case 'last_1':
                $query->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(1)->year);
                break;
            case 'last_2':
                $query->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(2)->year);
                break;
            case 'last_3':
                $query->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(3)->year);
                break;
            default:
                // current month
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
        }

        // Search by Employee ID or Employee Name from the employee table
        if ($request->filled('emp_id')) {
            $empInput = $request->emp_id;
            $query->whereHas('user.employee', function ($q) use ($empInput) {
                $q->where('employee_id', 'like', '%' . $empInput . '%')
                    ->orWhere('employee_name', 'like', '%' . $empInput . '%')
                    ->orWhere('company', 'like', '%' . $empInput . '%')
                    ->orWhere('status', 'like', '%' . $empInput . '%');
            });
        }

        $reimbursements = $query->paginate(10);
        $reimbursements->appends($request->except('page'));

        // Decode details JSON for each reimbursement
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->parsed_details = is_array($reimbursement->details)
                ? $reimbursement->details
                : json_decode($reimbursement->details, true);
        }

        return view('travel.reimbursements.finance_internal', compact('reimbursements', 'monthFilter'));
    }

    public function showInternalProcessed(Request $request)
    {

        $query = travelReimbursement::with('user.employee.manager')
            ->whereNotIn('status', ['manager_approved', 'pending', 'hr_approved', 'rejected', 'accountant_approved', 'cfo_approved', 'hold'])
            ->orderBy('created_at', 'desc');

        // Search by Employee ID or Employee Name from the employee table
        if ($request->filled('emp_id')) {
            $empInput = $request->emp_id;
            $query->whereHas('user.employee', function ($q) use ($empInput) {
                $q->where('employee_id', 'like', '%' . $empInput . '%')
                    ->orWhere('employee_name', 'like', '%' . $empInput . '%');
            });
        }

        $reimbursements = $query->paginate(10);
        $reimbursements->appends($request->except('page'));

        // Decode details JSON for each reimbursement
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->parsed_details = is_array($reimbursement->details)
                ? $reimbursement->details
                : json_decode($reimbursement->details, true);
        }

        return view('travel.reimbursements.internal', compact('reimbursements'));
    }

    public function internalApprove($id)
    {

        $reimbursement = TravelReimbursement::with('user.employee.manager')->findOrFail($id);

        // Decode the details JSON
        $reimbursement->reimbursement_details = is_array($reimbursement->details)
            ? $reimbursement->details
            : json_decode($reimbursement->details, true);

        return view('travel.reimbursements.internal_approve', compact('reimbursement'));
    }

    public function export(Request $request)
    {
        $fileName = 'Internal_reimbursements_' . now()->format('Y-m-d') . '.csv';

        // Start query with relationships
        $query = TravelReimbursement::with('user.employee.manager')
            ->where('status', '!=', 'pending')
            ->orderBy('created_at', 'desc');

        $monthFilter = $request->input('month_filter', 'current');

        switch ($monthFilter) {
            case 'all':
                // No date filter
                break;
            case 'last_1':
                $query->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(1)->year);
                break;
            case 'last_2':
                $query->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(2)->year);
                break;
            case 'last_3':
                $query->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
                    ->whereYear('created_at', Carbon::now()->subMonth(3)->year);
                break;
            default: // current month
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
        }

        if ($request->filled('emp_id')) {
            $empInput = $request->emp_id;
            $query->whereHas('user.employee', function ($q) use ($empInput) {
                $q->where('employee_id', 'like', '%' . $empInput . '%')
                    ->orWhere('employee_name', 'like', '%' . $empInput . '%')
                    ->orWhere('company', 'like', '%' . $empInput . '%')
                    ->orWhere('status', 'like', '%' . $empInput . '%');
            });
        }

        $reimbursements = $query->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr.No',
            'Emp Name',
            'Emp Email',
            'Emp ID',
            'Manager Name',
            'Designation',
            'Company',
            'Department',
            'Total Amount',
            'Reimbursement Items',
            'Submitted On',
            'Status',
            'Remarks'
        ];

        $callback = function () use ($reimbursements, $columns) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, $columns);

            foreach ($reimbursements as $index => $reimbursement) {
                $user = $reimbursement->user ?? null;
                $employee = $user->employee ?? null;
                $manager = $employee->manager ?? null;

                $details = is_array($reimbursement->details)
                    ? $reimbursement->details
                    : json_decode($reimbursement->details, true);

                // Collect description + amount + bill path
                $items = collect($details)->map(function ($item) {

                    $currency = $item['currency'] ?? 'INR';

                    $symbol = match ($currency) {
                        'AED' => 'د.إ',
                        'INR' => '₹',
                        default => '',
                    };

                    return ($item['description'] ?? 'N/A') .
                        ' - ' . $symbol . ($item['amount'] ?? 0) .
                        ' (' . $currency . ')' .
                        ' (Bill: ' . ($item['bill'] ?? 'N/A') . ')';
                })->implode('; ');

                fputcsv($file, [
                    $index + 1,
                    $user->name ?? 'N/A',
                    $user->email ?? 'N/A',
                    $employee->employee_id ?? 'N/A',
                    $manager->manager_name ?? 'N/A',
                    $employee->employee_designation ?? 'N/A',
                    $reimbursement->company ?? 'N/A',
                    $employee->employee_department ?? 'N/A',
                    $reimbursement->amount ?? 'N/A',
                    $items,
                    optional($reimbursement->created_at)->format('M d, Y') ?? 'N/A',
                    $reimbursement->status ?? 'N/A',
                    $reimbursement->remarks ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function exportCSV()
    {
        $fileName = 'Internal_' . now()->format('Y-m-d') . '.csv';

        $reimbursements = TravelReimbursement::with('user.employee')
            ->whereIn('status', ['processed', 'cfo_approved'])
            ->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            // 'Sr.No',
            'I or N',
            'Amount To Be Paid ',
            'Date of sheet Generation',
            'EMP ID',
            'EMP Name',
            'EMP Account Number',
            'Default Email',
            'Company Account No',
            'Bank Code',
            'EMP IFSC Code',
            'Code "11" to be mentioned defult mode',
            'Remarks',
            'EMP Contact Number',
            'Updated At',
            'Status'
        ];

        $callback = function () use ($reimbursements, $columns) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, $columns);

            foreach ($reimbursements as $reimbursement) {
                $employee = $reimbursement->user->employee ?? null;
                $empName = $employee->employee_name ?? null;

                $bankDetail = BankDetail::where('emp_name', $empName)->first();

                $details = is_array($reimbursement->details)
                    ? $reimbursement->details
                    : json_decode($reimbursement->details, true);

                $items = collect($details)->map(function ($item) {
                    return ($item['description'] ?? 'N/A') . ' - ₹' . ($item['amount'] ?? 0);
                })->implode('; ');

                $dateOfSheet = now()->format('d-m-Y');
                $remarks = 'Reimbursement ' . $reimbursement->created_at->format('M y');

                fputcsv($file, [
                    $bankDetail->i_or_n ?? 'N/A',
                    $reimbursement->amount ?? 'N/A',
                    $dateOfSheet,
                    $employee->employee_id ?? 'N/A',
                    $bankDetail->emp_name ?? ($empName ?? 'N/A'),
                    $bankDetail && $bankDetail->emp_account_number ? "'" . $bankDetail->emp_account_number : 'N/A',
                    $bankDetail->email ?? 'N/A',
                    $bankDetail && $bankDetail->company_account_number ? "'" . $bankDetail->company_account_number : 'N/A',
                    $bankDetail->bank_code ?? 'N/A',
                    $bankDetail->emp_ifsc_code ?? 'N/A',
                    $bankDetail->code ?? 'N/A',
                    $remarks,
                    $bankDetail->emp_contact_number ?? 'N/A',
                    $reimbursement->updated_at->format('d-m-Y H:i:s') ?? 'N/A',
                    $reimbursement->status ?? 'N/A',
                ]);
            }


            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // Download all bills as a ZIP file
    public function downloadAllBills($id)
    {
        $reimbursement = TravelReimbursement::findOrFail($id);

        // Decode details JSON
        $details = is_array($reimbursement->details)
            ? $reimbursement->details
            : json_decode($reimbursement->details, true);

        $zipFileName = 'reimbursement_bills' . '.zip';
        $zipPath = public_path($zipFileName);

        $zip = new \ZipArchive();
        $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($result !== true) {
            return back()->with('error', 'Failed to create ZIP file. Error code: ' . $result);
        }

        foreach ($details as $item) {
            if (!empty($item['bill'])) {
                // Handle both full URLs and relative paths
                $relativePath = parse_url($item['bill'], PHP_URL_PATH);
                $relativePath = ltrim($relativePath, '/');

                $filePath = public_path($relativePath);

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                } else {
                    \log('error')::warning("Missing bill file: $filePath");
                }
            }
        }

        $zip->close();

        // Download and auto-delete
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // public function testSecondTable()
    // {
    //     $test = \App\Models\ExternalUser::all();
    //     dd($test);
    //     return view('travel.reimbursements.test', compact('test'));
    // }
}

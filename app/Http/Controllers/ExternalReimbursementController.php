<?php

namespace App\Http\Controllers;

use App\Constants\RoleEmails;
use App\Models\ExternalEmpBankDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\ExternalReimbursement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use \PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use ZipArchive;
use Illuminate\Support\Facades\Storage;



class ExternalReimbursementController extends Controller
{

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Display the form for creating a new external reimbursement request
     *
     * @return \Illuminate\Http\Response
     */
    /*******  c6ddf27e-3c95-4a2d-8346-42e1821ab3fe  *******/
    public function create()
    {
        return view('external_reimbursements.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'reimbursement_excel' => 'required|file|mimes:xlsx,xls',
            'manager_approval_attachment' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'bills_attachment' => 'required|array|min:1',
            'bills_attachment.*' => 'required|file|mimes:jpg,pdf,jpeg,png|max:10240',
        ]);
        //$excelPath = $request->file('reimbursement_excel')->store('reimbursements');
        $managerAttachmentPath = $request->file('manager_approval_attachment');
        //$userId = Auth::id();
        $month = Carbon::now()->format('F');

        // Create destination path and filename
        $folderPath = "external_reimbursement/{$month}";
        $filename = uniqid('manager_') . '.' . $request->file('manager_approval_attachment')->getClientOriginalExtension();

        $destinationPath = public_path($folderPath);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Bills attachments (multiple files)
        $billsAttachmentPaths = [];
        foreach ($request->file('bills_attachment') as $billFile) {
            $billFilename = uniqid('bill_') . '.' . $billFile->getClientOriginalExtension();
            $billFile->move($destinationPath, $billFilename);
            $billsAttachmentPaths[] = "{$folderPath}/{$billFilename}";
        }

        $managerAttachmentPath->move($destinationPath, $filename);
        $localPath = "{$folderPath}/{$filename}";

        // Load data from Excel
        $excelData = Excel::toArray([], $request->file('reimbursement_excel'));
        // Assume data is in first sheet and first row is header
        $sheet = $excelData[0];

        $name = null;
        $managerName = null;

        foreach ($sheet as $row) {
            foreach ($row as $index => $cell) {
                if (is_string($cell)) {
                    $lowerCell = strtolower(trim($cell));

                    // Find Name
                    if (str_contains($lowerCell, 'name') && !str_contains($lowerCell, 'manager')) {
                        for ($i = $index + 1; $i < count($row); $i++) {
                            if (!empty(trim($row[$i]))) {
                                $name = trim($row[$i]);
                                break;
                            }
                        }
                    }

                    // Find Manager Name
                    if (str_contains($lowerCell, 'manager name')) {
                        for ($i = $index + 1; $i < count($row); $i++) {
                            if (!empty(trim($row[$i]))) {
                                $managerName = trim($row[$i]);
                                break;
                            }
                        }
                    }
                }
            }

            // Optional: Break early if both found
            if ($name && $managerName) {
                break;
            }
        }

        $data = [];
        $data['name'] = $name ?? null;
        $data['managerName'] = $managerName ?? null;
        $data['empId'] = $sheet[2][2] ?? null;
        $data['department'] = $sheet[4][2] ?? null;
        $data['designation'] = $sheet[2][10] ?? null;
        $data['project']     = $sheet[6][2]  ?? null;
        $data['client']      = $sheet[7][2]  ?? null;

        foreach ($sheet as $row) {
            foreach ($row as $index => $cell) {
                if (is_string($cell) && str_contains(strtolower($cell), 'business purpose')) {
                    // Look ahead in the same row to find the next non-empty cell
                    for ($i = $index + 1; $i < count($row); $i++) {
                        if (!empty(trim($row[$i]))) {
                            $data['businessPurpose'] = trim($row[$i]);
                            break 2; // Stop both loops once found
                        }
                    }
                }
            }
        }
        try {
            $fromValue = $sheet[4][13] ?? null;
            $data['from'] = is_numeric($fromValue) ? date('d-m-Y', ExcelDate::excelToTimestamp($fromValue)) : $fromValue;
        } catch (\Exception $e) {
            $data['from'] = 'Invalid Date';
        }

        try {
            $toValue = $sheet[5][13] ?? null;
            $data['to'] = is_numeric($toValue) ? date('d-m-Y', ExcelDate::excelToTimestamp($toValue)) : $toValue;
        } catch (\Exception $e) {
            $data['to'] = 'Invalid Date';
        }

        $data['total_reimbursement_amount'] = $this->getTotalReimbursementAmountFromExcel($request->file('reimbursement_excel'));


        $reimbursement = $this->parseReimbursementDetails($sheet);
        $data['reimbursement_details'] = $reimbursement;

        // Submitted by
        $data['submitted_by'] = null;
        if (isset($sheet[46]) && !empty($sheet[46][0])) {
            // Extract the name after the "Submitted by : " prefix
            $submitted_by = $sheet[46][0];
            if (strpos(strtolower($submitted_by), 'submitted by') !== false) {
                // Remove the "Submitted by : " part and trim any whitespace
                $data['submitted_by'] = trim(str_replace('Submitted by :', '', $submitted_by));
            }
        }

        // Approved by
        $data['approved_by'] = null;
        if (isset($sheet[47]) && !empty($sheet[47][0])) {
            // Extract the name after the "Approved by : " prefix
            $approved_by = $sheet[47][0];
            if (strpos(strtolower($approved_by), 'approved by') !== false) {
                // Remove the "Approved by : " part and trim any whitespace
                $data['approved_by'] = trim(str_replace('Approved by :', '', $approved_by));
            }
        }

        // Validation: check required keys in Excel
        $requiredFields = ['name', 'empId', 'designation', 'managerName', 'department', 'businessPurpose', 'from', 'to'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                return back()->withErrors(["The field '$field' is missing or empty in the Excel file."]);
            }
        }

        // $existing = ExternalReimbursement::where('emp_id', $data['empId'])->first();

        // if ($existing) {
        //     return back()->withErrors(['emp_id' => 'An entry already exists for this Employee ID.']);
        // }
// dd($data);
        $reimbursement = new ExternalReimbursement();
        $reimbursement->name = $data['name'];
        $reimbursement->manager_name = $data['managerName'];
        $reimbursement->emp_id = $data['empId'];
        $reimbursement->department = $data['department'];
        $reimbursement->designation = $data['designation'];
        $reimbursement->project = $data['project'];
        $reimbursement->client = $data['client'];
        $reimbursement->business_purpose = $data['businessPurpose'];
        $reimbursement->from = $data['from'];
        $reimbursement->to = $data['to'];
        $reimbursement->amount = $data['total_reimbursement_amount'];
        $reimbursement->reimbursement_details = $data['reimbursement_details'];
        $reimbursement->submitted_by = $data['submitted_by'];
        $reimbursement->approved_by = $data['approved_by'];
        $reimbursement->manager_approval_attachment = $localPath;
        $reimbursement->bills_attachment = $billsAttachmentPaths;
        // $reimbursement->date = $data['date'];
        //$reimbursement->reimbursement_excel_path = $excelPath;
        $reimbursement->save();
        // Send email to HR Approval
        $monthYear = now()->format('F Y');
        Mail::to('jayarani.g@fidelisgroup.in')->send(new \App\Mail\ReimbursementHRApproval($reimbursement, $monthYear));

        return redirect()->back()->with('success', 'Reimbursement submitted successfully Email Sent To HR!');
    }

    private function parseReimbursementDetails(array $sheet): array
    {
        $reimbursementDetails = [];
        $currentSection = null;
        $headerIndexes = [];
        $readingData = false;

        foreach ($sheet as $row) {
            $row = array_map(fn($cell) => is_string($cell) ? trim($cell) : $cell, $row);

            // Detect section title
            if (!empty($row[0]) && stripos($row[0], 'reimbursement') !== false) {
                $currentSection = $row[0];
                $reimbursementDetails[$currentSection] = [];
                $headerIndexes = [];
                $readingData = false;
                continue;
            }

            // Detect header row
            if ($currentSection && !$readingData) {
                foreach ($row as $index => $cell) {
                    $cellLower = strtolower($cell);
                    if (str_contains($cellLower, 'date')) $headerIndexes['date'] = $index;
                    elseif (str_contains($cellLower, 'description')) $headerIndexes['description'] = $index;
                    elseif (str_contains($cellLower, 'bills')) $headerIndexes['bills'] = $index;
                    elseif (str_contains($cellLower, 'cost')) $headerIndexes['cost'] = $index;
                    elseif (str_contains($cellLower, 'amount')) $headerIndexes['amount'] = $index;
                    elseif (str_contains($cellLower, 'transport')) $headerIndexes['transport_mode'] = $index;
                    elseif (str_contains($cellLower, 'km')) $headerIndexes['total_km'] = $index;
                    elseif (str_contains($cellLower, 'event')) $headerIndexes['event'] = $index;
                }

                if (!empty($headerIndexes)) {
                    $readingData = true;
                }
                continue;
            }

            // Read actual data rows
            if ($readingData && $currentSection) {
                if (empty(array_filter($row))) continue;

                // Skip rows with formula in amount
                $amountIndex = $headerIndexes['amount'] ?? null;
                if ($amountIndex !== null && isset($row[$amountIndex]) && is_string($row[$amountIndex]) && str_starts_with($row[$amountIndex], '=')) {
                    continue;
                }

                $entry = [];
                foreach ($headerIndexes as $key => $index) {
                    //$entry[$key] = $row[$index] ?? null;
                    $value = $row[$index] ?? null;

                    // Handle Excel serial date conversion
                    if ($key === 'date' && is_numeric($value)) {
                        try {

                            $timestamp = ExcelDate::excelToTimestamp($value);
                            $value = date('d-m-Y', $timestamp);
                        } catch (\Exception $e) {
                            // Return fallback message
                            $value = 'Invalid date format';
                        }
                    }

                    $entry[$key] = $value;
                }

                // Skip if all fields are null
                if (!array_filter($entry, fn($v) => !is_null($v) && $v !== '')) {
                    continue;
                }

                $reimbursementDetails[$currentSection][] = $entry;
            }
        }
        $getExpensesData = [];

        $getExpensesData = [
            'otherExpenses' => $reimbursementDetails['Mobile, Internet, Laptop and Others Reimbursement'] ?? [],
            'travelExpenses' => $reimbursementDetails['Travel Reimbursement'] ?? [],
            'foodExpenses' => $reimbursementDetails['Food Reimbursement'] ?? [],
        ];

        return $getExpensesData;
    }

    public function showAll(Request $request)
    {
        $query = ExternalReimbursement::query();
        // Add filter for Emp ID
        if ($request->filled('emp_id')) {
            $query->where('emp_id', 'like', '%' . $request->emp_id . '%');
        }

        $reimbursements = $query->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('external_reimbursements.index', compact('reimbursements'));
    }

    public function show($id)
    {
        $reimbursement = ExternalReimbursement::findOrFail($id);

        // No need to decode manually if cast is defined
        return view('external_reimbursements.show', compact('reimbursement'));
    }

    private function getTotalReimbursementAmountFromExcel($file)
    {
        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            $cell = $sheet->getCell('L46');

            $calculatedAmount = $cell->getCalculatedValue();

            return is_numeric($calculatedAmount) ? (float) $calculatedAmount : null;
        } catch (\Exception $e) {

            return null;
        }
    }

    public function showApprovalForm($id)
    {
        $reimbursement = ExternalReimbursement::findOrFail($id);
        return view('external_reimbursements.approval_form', compact('reimbursement'));
    }
    public function handleApproval(Request $request, $id)
    {
        $reimbursement = ExternalReimbursement::findOrFail($id);

        // Save remarks
        $reimbursement->remarks = $request->input('remarks');

        $action = $request->input('status'); // expects 'approve' or 'reject'

        // Rejection flow
        if ($action === 'rejected') {
            $currentStatus = $reimbursement->status; // ✅ Save original status before updating

            $reimbursement->status = 'rejected';
            $reimbursement->save();

            // Determine recipients based on original status
            $recipients = [];
            $mailClass = \App\Mail\ReimbursementRejection::class;
            $redirectUrl = '/review';
            $errorMessage = 'Reimbursement has been rejected.';

            switch ($currentStatus) {
                case 'pending': // HR rejected
                    $recipients =  [RoleEmails::RAM_EMAIL];
                    break;

                case 'chro_approved': // Accountant rejected
                    $recipients = [RoleEmails::HR_APPROVAL_EMAIL, RoleEmails::RAM_EMAIL];
                    $redirectUrl = '/accountant/approval';
                    break;

                case 'accountant_approved': // CFO rejected
                    $recipients = [RoleEmails::HR_APPROVAL_EMAIL, RoleEmails::RAM_EMAIL, RoleEmails::ACCOUNTANT_APPROVAL_EMAIL];
                    $redirectUrl = '/cfo/approval';
                    break;

                default:
                    $recipients = RoleEmails::RAM_EMAIL; // fallback
                    break;
            }

            // Send rejection emails
            foreach ($recipients as $email) {
                Mail::to($email)->send(new $mailClass($reimbursement));
            }

            return redirect($redirectUrl)->with('success', $errorMessage);
        }


        // Determine next status and recipient
        $recipient = null;
        $mailClass = null;

        $redirectUrl = '/review';
        $successMessage = 'Reimbursement status updated successfully.';

        switch ($reimbursement->status) {
            case 'pending':
                $reimbursement->status = 'chro_approved';
                break;

            case 'chro_approved':
                $reimbursement->status = 'accountant_approved';
                $redirectUrl = '/accountant/approval';
                break;

            case 'accountant_approved':
                $reimbursement->status = 'cfo_approved';
                $redirectUrl = '/cfo/approval';
                break;

            case 'cfo_approved':
                $reimbursement->status = 'processed';
                $redirectUrl = '/finance/approval';
                break;

            default:
                return back()->with('error', 'Invalid status transition or Rejected or already processed.');
        }

        // Save updated status and remarks
        $reimbursement->save();

        // Redirect with success message
        return redirect($redirectUrl)->with('success', $successMessage);
    }

    public function sendExternalPendingApproval()
    {
        $month = now()->format('F');

        // Priority order: HR -> Accountant -> CFO
        if ($reimbursement = ExternalReimbursement::where('status', 'chro_approved')->get()) {
            if ($reimbursement->isNotEmpty()) {
                $recipient = RoleEmails::ACCOUNTANT_APPROVAL_EMAIL;
                $mailClass = new \App\Mail\ReimbursementAccountantApproval($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Email sent to Accountant.');
            }
        }

        if ($reimbursement = ExternalReimbursement::where('status', 'accountant_approved')->get()) {
            if ($reimbursement->isNotEmpty()) {
                $recipient = RoleEmails::CFO_APPROVAL_EMAIL;
                $mailClass = new \App\Mail\ReimbursementCFOApproval($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Email sent to CFO.');
            }
        }

        if ($reimbursements = ExternalReimbursement::where('status', 'cfo_approved')->get()) {
            if ($reimbursements->isNotEmpty()) {
                $recipient = RoleEmails::FINANCE_APPROVAL_EMAIL;
                $mailClass = new \App\Mail\ReimbursementFinanceApproval($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Email sent to Finance.');
            }
        }

        if ($reimbursements = ExternalReimbursement::where('status', 'processed')->get()) {
            if ($reimbursements->isNotEmpty()) {
                $recipient = [RoleEmails::HR_APPROVAL_EMAIL, RoleEmails::ACCOUNTANT_APPROVAL_EMAIL, RoleEmails::RAM_EMAIL];
                $mailClass = new \App\Mail\ReimbursementFinalConfirmation($month);

                Mail::to($recipient)->send($mailClass);

                return back()->with('success', 'Final confirmation email sent to HR, CFO and Accountant.');
            }
        }

        return back()->with('info', 'No pending approvals found at any stage.');
    }

    public function bulkHandleApproval(Request $request)
    {

        $ids = $request->input('reimbursement_ids', []);
        $approvedCount = 0;
        $skippedCount = 0;
        $month = now()->format('F');

        foreach ($ids as $id) {
            $reimbursement = ExternalReimbursement::find($id);
            if (!$reimbursement || in_array($reimbursement->status, ['processed', 'rejected', 'cfo_approved'])) {
                $skippedCount++;
                continue;
            }


            switch ($reimbursement->status) {
                case 'pending':
                    $reimbursement->status = 'chro_approved';
                    break;

                case 'chro_approved':
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

    public function markProcessed($id)
    {
        $reimbursement = ExternalReimbursement::findOrFail($id);
        $reimbursement->final_status = 'Processed';
        $reimbursement->save();

        return redirect()->back()->with('success', 'Reimbursement marked as processed.');
    }

    public function downloadAllAttachments($id)
    {
        $reimbursement = ExternalReimbursement::findOrFail($id);

        $zip = new ZipArchive;
        $fileName = 'attachments_' . time() . '.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Add manager approval attachment
            if ($reimbursement->manager_approval_attachment && file_exists(public_path($reimbursement->manager_approval_attachment))) {
                $zip->addFile(public_path($reimbursement->manager_approval_attachment), basename($reimbursement->manager_approval_attachment));
            }

            // Add all bill attachments
            $bills = is_array($reimbursement->bills_attachment)
                ? $reimbursement->bills_attachment
                : json_decode($reimbursement->bills_attachment, true);

            foreach ($bills as $bill) {
                if (file_exists(public_path($bill))) {
                    $zip->addFile(public_path($bill), basename($bill));
                }
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Could not create ZIP file.');
        }
    }

    public function review()
    {
        $query = ExternalReimbursement::query();

        if (request()->has('search') && request()->search != '') {
            $searchTerm = request()->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('emp_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
            });
        }

        $reimbursements = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());
        return view('external_reimbursements.review', compact('reimbursements'));
    }

    public function accountantApproval()
    {
        $query = ExternalReimbursement::query()
            ->where('status', '!=', 'pending'); // 👈 Exclude 'pending' records

        if (request()->has('search') && request()->search != '') {
            $searchTerm = request()->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('emp_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
            });
        }

        $reimbursements = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('external_reimbursements.review', compact('reimbursements'));
    }

    public function cfoApproval()
    {
        $query = ExternalReimbursement::query()
            ->whereNotIn('status', ['pending', 'chro_approved', 'rejected', 'processed']);

        if (request()->has('search') && request()->search != '') {
            $searchTerm = request()->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('emp_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
            });
        }

        $reimbursements = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('external_reimbursements.review', compact('reimbursements'));
    }

    public function financeApproval()
    {
        $query = ExternalReimbursement::query()
            ->whereNotIn('status', ['pending', 'chro_approved',  'accountant_approved', 'rejected']);

        if (request()->has('search') && request()->search != '') {
            $searchTerm = request()->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('emp_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
            });
        }

        $reimbursements = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('external_reimbursements.review', compact('reimbursements'));
    }

    public function finalProcessed()
    {
        $query = ExternalReimbursement::query()
            ->whereNotIn('status', ['pending', 'chro_approved',  'accountant_approved', 'rejected', 'cfo_approved']);

        if (request()->has('search') && request()->search != '') {
            $searchTerm = request()->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('emp_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $searchTerm . '%');
            });
        }

        $reimbursements = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('external_reimbursements.review', compact('reimbursements'));
    }

    public function export()
    {
        $fileName = 'external_reimbursements_' . now()->format('Y-m-d') . '.csv';

        $reimbursements = ExternalReimbursement::all();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns =
            [
                'Sr.No',
                'Emp Name',
                'Manager',
                'Emp ID',
                'Designation',
                'Client',
                'Project',
                'Purpose',
                'Total Amount',
                'Reimbursement Items',
                'Submitted By',
                'Approved By',
                'From',
                'To',
                'Status',
                'Remarks'
            ];

        $callback = function () use ($reimbursements, $columns) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM to fix special characters in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, $columns);

            foreach ($reimbursements as $index => $reimbursement) {
                $details = is_array($reimbursement->reimbursement_details)
                    ? $reimbursement->reimbursement_details
                    : json_decode($reimbursement->reimbursement_details, true);

                $flattened = collect($details)
                    ->flatMap(fn($group) => is_array($group) ? $group : [])
                    ->map(fn($item) => ($item['description'] ?? 'N/A') . ' - ₹' . ($item['amount'] ?? 0))
                    ->implode('; ');

                fputcsv($file, [
                    $index + 1,
                    $reimbursement->name ?? 'N/A',
                    $reimbursement->manager_name ?? 'N/A',
                    $reimbursement->emp_id ?? 'N/A',
                    $reimbursement->designation ?? 'N/A',
                    $reimbursement->client ?? 'N/A',
                    $reimbursement->project ?? 'N/A',
                    $reimbursement->business_purpose ?? 'N/A',
                    $reimbursement->amount ?? 'N/A',
                    $flattened,
                    $reimbursement->submitted_by ?? 'N/A',
                    $reimbursement->approved_by ?? 'N/A',
                    $reimbursement->from ?? 'N/A',
                    $reimbursement->to  ?? 'N/A',
                    $reimbursement->status ?? 'N/A',
                    $reimbursement->remarks ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function exportExternalCSV()
    {
        $fileName = 'External_' . now()->format('Y-m-d') . '.csv';

        $reimbursements = ExternalReimbursement::all();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
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
        ];

        $callback = function () use ($reimbursements, $columns) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, $columns);

            foreach ($reimbursements as $reimbursement) {
                $empId = $reimbursement->emp_id;
                // Match bank detail using employee ID
                $bankDetail = ExternalEmpBankDetail::where('emp_id', $empId)->first();

                $dateOfSheet = now()->format('d-m-Y');
                $remarks = 'Reimbursement ' . $reimbursement->created_at->format('M y');

                fputcsv($file, [
                    $bankDetail->i_or_n ?? 'N/A',
                    $reimbursement->amount ?? 'N/A',
                    $dateOfSheet ?? 'N/A',
                    $bankDetail->emp_id ?? 'N/A',
                    $bankDetail->emp_name ?? 'N/A',
                    isset($bankDetail) ? "'" . $bankDetail->emp_account_number : 'N/A',
                    $bankDetail->email ?? 'N/A',
                    isset($bankDetail) ? "'" . $bankDetail->company_account_number : 'N/A',
                    $bankDetail->bank_code ?? 'N/A',
                    $bankDetail->emp_ifsc_code ?? 'N/A',
                    $bankDetail->code ?? 'N/A',
                    $remarks ?? 'N/A',
                    $bankDetail->emp_contact_number ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function delete($id)
    {
        try {
            $reimbursement = ExternalReimbursement::findOrFail($id);

            // Delete manager approval attachment
            if ($reimbursement->manager_approval_attachment && file_exists(public_path($reimbursement->manager_approval_attachment))) {
                unlink(public_path($reimbursement->manager_approval_attachment));
            }

            // Delete bill attachments
            $bills = is_array($reimbursement->bills_attachment)
                ? $reimbursement->bills_attachment
                : json_decode($reimbursement->bills_attachment, true);

            foreach ($bills as $bill) {
                if (file_exists(public_path($bill))) {
                    unlink(public_path($bill));
                }
            }

            // Delete the reimbursement record
            $reimbursement->delete();

            return redirect()->back()->with('success', 'Reimbursement deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete reimbursement: ' . $e->getMessage());
        }
    }
}

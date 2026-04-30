<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorBill;
use App\Models\BillStatusHistory;
use App\Models\VendorBankDetail;
use App\Http\Requests\StoreVendorBillRequest;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Mail\VendorDocumentsUpdated;
use App\Models\BillApprovalWorkflow;
use App\Models\User;
use App\Models\VendorApprovalWorkflow;
use Illuminate\Support\Facades\Mail;
use App\Services\VendorDocumentService;

class VendorPortalController extends Controller
{
    public function verifyPending()
    {
        $vendor = Vendor::where('email', Auth::user()->email)->first();
        return view('vendor-portal.verify-pending', compact('vendor'));
    }

    public function verifyBlocked()
    {
        $vendor = Vendor::where('email', Auth::user()->email)->first();
        return view('vendor-portal.verify-blocked', compact('vendor'));
    }

    /**
     * Display vendor dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $recentBills = VendorBill::where('vendor_id', $vendor->id)
            ->latest()
            ->take(5)
            ->get();

        $pendingBills = VendorBill::where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['transferred', 'rejected'])
            ->count();

        $approvedBills = VendorBill::where('vendor_id', $vendor->id)
            ->where('status', 'transferred')
            ->count();

        $rejectedBills = VendorBill::where('vendor_id', $vendor->id)
            ->where('status', 'rejected')
            ->count();

        return view('vendor-portal.dashboard', compact('vendor', 'recentBills', 'pendingBills', 'approvedBills', 'rejectedBills'));
    }

    /**
     * Display vendor profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)
            ->with(['bankDetails', 'documents'])
            ->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        return view('vendor-portal.profile', compact('vendor'));
    }

    /**
     * Show the form for editing vendor profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function editProfile()
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)
            ->with(['bankDetails', 'documents'])
            ->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        return view('vendor-portal.edit-profile', compact('vendor'));
    }

    /**
     * Update vendor profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $validator = Validator::make($request->all(), [
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'nature_of_work' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $vendor->update([
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'nature_of_work' => $request->nature_of_work,
            ]);

            return redirect()->route('vendor.profile')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating profile: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for adding bank details.
     *
     * @return \Illuminate\Http\Response
     */
    public function addBankDetails()
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        return view('vendor-portal.add-bank-details', compact('vendor'));
    }

    /**
     * Store bank details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBankDetails(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'upi_id' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
            'cheque_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // If this is set as primary, unset all other bank details as primary
            if ($request->has('is_primary') && $request->is_primary) {
                VendorBankDetail::where('vendor_id', $vendor->id)
                    ->update(['is_primary' => false]);
            }

            // Create bank details
            VendorBankDetail::create([
                'vendor_id' => $vendor->id,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
                'is_primary' => $request->has('is_primary') ? $request->is_primary : false,
            ]);

            // Store cancelled cheque document
            if ($request->hasFile('cheque_document')) {
                $chequeFile = $request->file('cheque_document');
                $chequePath = $chequeFile->store('vendor_documents', 'public');

                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'cancelled_cheque',
                    'file_path' => $chequePath,
                    'file_name' => $chequeFile->getClientOriginalName(),
                ]);
            }

            DB::commit();

            return redirect()->route('vendor.profile')
                ->with('success', 'Bank details added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error adding bank details: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for creating a credit note for an existing bill.
     *
     * @param  int  $billId  Original bill ID for creating a credit note
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createCreditNote($billId)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $originalBill = VendorBill::where('id', $billId)
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['transferred', 'cfo_approved'])
            ->firstOrFail();

        return view('vendor-portal.create-bill', [
            'vendor' => $vendor,
            'originalBill' => $originalBill,
            'isCreditNote' => true,
            'gstTypes' => ['IGST', 'CGST', 'SGST']
        ]);
    }

    /**
     * Show the form for creating a new bill or credit note.
     *
     * @param  int|null  $billId  Original bill ID for creating a credit note
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createBill(Request $request, $billId = null,)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $originalBill = null;
        $isCreditNote = false;
        $ocrData = [];
        $tempFilePath = null;

        if ($billId) {
            $originalBill = VendorBill::where('id', $billId)
                ->where('vendor_id', $vendor->id)
                ->firstOrFail();
            $isCreditNote = true;
        }

        // Handle OCR data from previous step
        if ($request->session()->has('ocr_data')) {
            $ocrData = $request->session()->get('ocr_data');
            $tempFilePath = $request->session()->get('temp_file_path');
        }

        return view('vendor-portal.create-bill', [
            'vendor' => $vendor,
            'originalBill' => $originalBill,
            'isCreditNote' => $isCreditNote,
            'gstTypes' => ['IGST', 'CGST', 'SGST'],
            'ocrData' => $ocrData,
            'tempFilePath' => $tempFilePath
        ]);
    }

    public function storeBill(StoreVendorBillRequest $request)
    {

        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        try {
            DB::beginTransaction();

            // Handle invoice file (manual upload only, OCR removed)
            $invoicePath = null;
            $documentName = null;
            $documentMimeType = null;
            $documentSize = null;

            if ($request->hasFile('invoice_file')) {
                $invoiceFile = $request->file('invoice_file');
                $invoicePath = $invoiceFile->store('vendor_bills', 'public');

                $documentName = $invoiceFile->getClientOriginalName();
                $documentMimeType = $invoiceFile->getClientMimeType();
                $documentSize = $invoiceFile->getSize();
            } else {
                return redirect()->back()
                    ->with('error', 'Invoice file is required.')
                    ->withInput();
            }

            // Calculate tax amount if not provided
            $amount = $request->amount;
            $taxAmount = $request->tax_amount ?? 0;

            if ($request->has('gst_percentage') && $request->gst_percentage > 0) {
                $taxAmount = ($amount * $request->gst_percentage) / 100;
            }

            $totalAmount = $amount + $taxAmount;

            // Handle credit note file if present
            $creditNotePath = null;
            $creditNotesData = [];
            $creditNoteName = null;
            $creditNoteMimeType = null;
            $creditNoteSize = null;

            if ($request->has('credit_notes')) {
                foreach ($request->credit_notes as $note) {
                    $creditNotePath = null;

                    if (isset($note['credit_note_file']) && $note['credit_note_file'] instanceof \Illuminate\Http\UploadedFile) {
                        $creditNotePath = $note['credit_note_file']->store('credit_notes', 'public');
                    }

                    $creditNotesData[] = [
                        'original_bill_id'      => $note['original_bill_id'] ?? null,
                        'credit_note_number'    => $note['credit_note_number'] ?? null,
                        'credit_note_amount'    => $note['credit_note_amount'] ?? null,
                        'credit_note_gst_amount' => $note['credit_note_gst_amount'] ?? null,
                        'credit_note_date'      => $note['credit_note_date'] ?? null,
                        'credit_note_file_path' => $creditNotePath,
                    ];
                }
            }

            // Create bill
            $billData = [
                'vendor_id' => $vendor->id,
                'bill_number' => $request->bill_number,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'gst_type' => $request->gst_type,
                'billing_period_start' => $request->billing_period_start,
                'billing_period_end' => $request->billing_period_end,
                'description' => $request->description,
                'file_path' => $invoicePath,
                // 'document_name' => $documentName,
                // 'document_mime_type' => $documentMimeType,
                // 'document_size' => $documentSize,
                'status' => 'uploaded',
                // 'is_credit_note' => $request->boolean('is_credit_note'),
                'credit_note' => json_encode($creditNotesData),
                'po_number' => $request->po_number,
                'company' => $request->company,
                'invoice_type' => $request->invoice_type,

            ];

            $bill = VendorBill::create($billData);




            // Create status history
            BillStatusHistory::create([
                'bill_id' => $bill->id,
                'status' => 'uploaded',
                'comments' => $request->boolean('is_credit_note') ? 'Credit note uploaded by vendor' : 'Bill uploaded by vendor',
                'changed_by' => $user->id,
            ]);

            // Step 1: Find the vendor approval workflow for this vendor
            $workflow = VendorApprovalWorkflow::where('vendor_id', $vendor->id)->first();

            if ($workflow) {
                // Step 2: Define the approval chain in sequence
                $approvers = [
                    'initial_approver_id',
                    'hr_approver_id',
                    'finance_approver_id',
                    'cfo_approver_id',
                    'payment_processor_id',
                ];

                // Step 3: Loop through each approver stage
                foreach ($approvers as $approverKey) {
                    $approverId = $workflow->$approverKey;

                    if ($approverId) {
                        $approver = User::find($approverId);

                        if ($approver) {
                            // Step 4: Send email to this approver
                            Mail::to($approver->email)->send(
                                new \App\Mail\BillApprovalNotification($bill, $approver)
                            );

                            // Optionally, stop after sending to the first pending approver
                            break;
                        }
                    }
                }
            }
            DB::commit();

            return redirect()->route('vendor.bills')
                ->with('success', $request->boolean('is_credit_note') ? 'Credit note submitted successfully.' : 'Bill submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing bill: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Display vendor bills.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function bills()
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $bills = VendorBill::with('creditNotes')
            ->where('vendor_id', $vendor->id)
            //->whereNull('original_bill_id') // Only get original bills, not credit notes
            ->latest()
            ->paginate(10);

        return view('vendor-portal.bills', compact('vendor', 'bills'));
    }

    /**
     * Display bill details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showBill($id)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $bill = VendorBill::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->with(['statusHistory.user', 'originalBill', 'creditNotes'])
            ->first();

        if (!$bill) {
            return redirect()->route('vendor.bills')
                ->with('error', 'Bill not found.');
        }

        return view('vendor-portal.show-bill', compact('vendor', 'bill'));
    }

    /**
     * Download bill file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadBill($id)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $bill = VendorBill::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$bill) {
            return redirect()->route('vendor.bills')
                ->with('error', 'Bill not found.');
        }

        if (!Storage::disk('public')->exists($bill->document_path)) {
            return redirect()->back()->with('error', 'Bill file not found.');
        }

        return response()->download(storage_path('app/public/' . $bill->document_path));
    }

    public function updateVendorDetails(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        // Add validation rules for contacts
        $contactRules = [
            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:20',
            'contacts.*.designation' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), array_merge([
            'contact_person' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pan_number' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'tan_number' => 'nullable|string|max:20',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|min:9|max:18',
            'ifsc_code' => 'required|string|max:20',
            'upi_id' => 'nullable|string|max:255',
            'cheque_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pincode' => 'nullable|string|max:10',
            'nature_of_work' => 'nullable|string|max:255',
            // 'website' => 'nullable|url|max:255',
            'msme_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'gst_exemption_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pan_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vendor_contact_person' => 'nullable|string|max:255',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email|max:255',
        ],));
        // ], $contactRules));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = $request->only([
            'contact_person',
            'type',
            'phone',
            'address',
            'city',
            'state',
            'pan_number',
            'gst_number',
            'tan_number',
            'pincode',
            'nature_of_work',
            'vendor_contact_person',
            'vendor_phone',
            'vendor_email',
        ]);

        if ($request->has('company_name')) {
            $updateData['name'] = $request->company_name;
        }

        // Handle GST Exemption Certificate Upload (stored on Vendor model)
        if ($request->hasFile('gst_exemption_certificate')) {
            $certificateFile = $request->file('gst_exemption_certificate');

            // Delete old file if it exists
            if ($vendor->gst_exemption_certificate_path) {
                Storage::disk('public')->delete($vendor->gst_exemption_certificate_path);
            }

            $certificatePath = $certificateFile->store('vendor_documents', 'public');
            $updateData['gst_exemption_certificate_path'] = $certificatePath;
        }

        // Handle MSME Certificate Upload (stored on Vendor model)
        if ($request->hasFile('msme_certificate')) {
            $msmeFile = $request->file('msme_certificate');

            if ($vendor->msme_certificate_path) {
                Storage::disk('public')->delete($vendor->msme_certificate_path);
            }

            $msmePath = $msmeFile->store('vendor_documents', 'public');
            $updateData['msme_certificate_path'] = $msmePath;
        }

        // Handle PAN and GST Document uploads via centralized VendorDocumentService
        /** @var VendorDocumentService $documentService */
        $documentService = app(VendorDocumentService::class);

        // Replace existing PAN document if a new one is provided
        if ($request->hasFile('pan_document')) {
            // Delete existing PAN documents and their files
            $existingPanDocs = VendorDocument::where('vendor_id', $vendor->id)
                ->where('document_type', 'pan')
                ->get();
            foreach ($existingPanDocs as $doc) {
                if ($doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            // Upload new PAN document
            $documentService->uploadDocument($vendor, $request->file('pan_document'), 'pan');
        }

        // Replace existing GST document if a new one is provided
        if ($request->hasFile('gst_document')) {
            // Delete existing GST documents and their files
            $existingGstDocs = VendorDocument::where('vendor_id', $vendor->id)
                ->where('document_type', 'gst_certificate')
                ->get();
            foreach ($existingGstDocs as $doc) {
                if ($doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            // Upload new GST document
            $documentService->uploadDocument($vendor, $request->file('gst_document'), 'gst_certificate');
        }
        // Update vendor details
        $vendor->update($updateData);

        // Handle contacts
        if ($request->has('contacts')) {
            // Delete existing contacts
            $vendor->contacts()->delete();

            // Create new contacts
            foreach ($request->contacts as $contactData) {
                // Skip if all fields are empty except name which is required
                if (!empty($contactData['name'])) {
                    $vendor->contacts()->create($contactData);
                }
            }
        }

        // Handle banking information
        if ($request->has('bank_name') && $request->has('account_number')) {
            // Create bank details
            $bankDetail = VendorBankDetail::create([
                'vendor_id' => $vendor->id,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
                'is_primary' => $request->has('is_primary') ? 1 : 0,
            ]);

            // Handle cheque document upload
            if ($request->hasFile('cheque_document')) {
                $chequeFile = $request->file('cheque_document');
                $chequePath = $chequeFile->store('vendor_bank_documents', 'public');

                // Create VendorDocument for the cheque
                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'cancelled_cheque',
                    'file_path' => $chequePath,
                    'file_name' => $chequeFile->getClientOriginalName(),
                    'file_size' => $chequeFile->getSize(),
                    'file_mime_type' => $chequeFile->getMimeType(),
                ]);
            }
        }

        // ✅ Send email to Initial Approver dynamically
        $workflow = VendorApprovalWorkflow::where('vendor_id', $vendor->id)->first();

        if ($workflow && $workflow->initial_approver_id) {
            $initialApprover = User::find($workflow->initial_approver_id);

            if ($initialApprover && $initialApprover->email) {
                try {
                    Mail::to($initialApprover->email)->send(new \App\Mail\VendorDocumentsUpdated($vendor));
                } catch (\Exception $e) {
                    \Log::error('Failed to send vendor update mail to initial approver: ' . $e->getMessage());
                }
            }
        }


        return redirect()->route('vendor.dashboard')
            ->with('success', 'Vendor details updated successfully.');
    }

    public function editBill($id)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        $bill = VendorBill::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->where('status', 'rejected') // allow only rejected bills to edit
            ->firstOrFail();

        return view('vendor-portal.create-bill', [
            'vendor' => $vendor,
            'originalBill' => $bill,
            'isEditMode' => true,
            'gstTypes' => ['IGST', 'CGST', 'SGST'],
            'ocrData' => [],
            'tempFilePath' => null
        ]);
    }

    public function updateBill(Request $request, $id)
    {
        $user = Auth::user();
        $vendor = Vendor::where('email', $user->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor profile not found.');
        }

        try {
            DB::beginTransaction();

            // Fetch existing bill (only rejected ones can be edited)
            $bill = VendorBill::where('id', $id)
                ->where('vendor_id', $vendor->id)
                ->where('status', 'rejected')
                ->firstOrFail();

            // Handle invoice file (optional update)
            $invoicePath = $bill->file_path; // Keep old one unless new is uploaded
            if ($request->hasFile('invoice_file')) {
                $invoiceFile = $request->file('invoice_file');
                $invoicePath = $invoiceFile->store('vendor_bills', 'public');
            }

            // Calculate tax amount
            $amount = $request->amount;
            $taxAmount = $request->tax_amount ?? 0;

            if ($request->has('gst_percentage') && $request->gst_percentage > 0) {
                $taxAmount = ($amount * $request->gst_percentage) / 100;
            }

            $totalAmount = $amount + $taxAmount;

            // Handle Credit Notes
            $creditNotesData = [];
            if ($request->has('credit_notes')) {
                foreach ($request->credit_notes as $note) {
                    $creditNotePath = $note['existing_credit_note_file'] ?? null; // existing file path

                    if (isset($note['credit_note_file']) && $note['credit_note_file'] instanceof \Illuminate\Http\UploadedFile) {
                        $creditNotePath = $note['credit_note_file']->store('credit_notes', 'public');
                    }

                    $creditNotesData[] = [
                        'original_bill_id'        => $note['original_bill_id'] ?? null,
                        'credit_note_number'      => $note['credit_note_number'] ?? null,
                        'credit_note_amount'      => $note['credit_note_amount'] ?? null,
                        'credit_note_gst_amount'  => $note['credit_note_gst_amount'] ?? null,
                        'credit_note_date'        => $note['credit_note_date'] ?? null,
                        'credit_note_file_path'   => $creditNotePath,
                    ];
                }
            }

            // Update bill details
            $bill->update([
                'bill_number'            => $request->bill_number,
                'bill_date'              => $request->bill_date,
                'due_date'               => $request->due_date,
                'amount'                 => $amount,
                'tax_amount'             => $taxAmount,
                'total_amount'           => $totalAmount,
                'gst_type'               => $request->gst_type,
                'billing_period_start'   => $request->billing_period_start,
                'billing_period_end'     => $request->billing_period_end,
                'description'            => $request->description,
                'file_path'              => $invoicePath,
                'credit_note'            => json_encode($creditNotesData),
                'po_number'              => $request->po_number,
                'status'                 => 'uploaded', // re-uploaded after rejection
            ]);

            // Record status history
            BillStatusHistory::create([
                'bill_id' => $bill->id,
                'status' => 'uploaded',
                'comments' => 'Bill re-uploaded by vendor after rejection',
                'changed_by' => $user->id,
            ]);

            // Step 1: Find the vendor approval workflow for this vendor
            $workflow = VendorApprovalWorkflow::where('vendor_id', $vendor->id)->first();

            if ($workflow) {
                $approvers = [
                    'initial_approver_id',
                    'hr_approver_id',
                    'finance_approver_id',
                    'cfo_approver_id',
                    'payment_processor_id',
                ];

                // Step 3: Send to the first available approver again
                foreach ($approvers as $approverKey) {
                    $approverId = $workflow->$approverKey;

                    if ($approverId) {
                        $approver = User::find($approverId);
                        if ($approver) {
                            Mail::to($approver->email)->send(
                                new \App\Mail\BillApprovalNotification($bill, $approver)
                            );
                            break;
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('vendor.bills')
                ->with('success', 'Bill updated and resubmitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating bill: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}

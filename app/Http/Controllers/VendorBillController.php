<?php

namespace App\Http\Controllers;


use App\Models\VendorBill;
use App\Models\Vendor;
use App\Models\BillStatusHistory;
use App\Models\BillApprovalWorkflow; // ✅ Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorBillController extends Controller
{
    /**
     * Display a listing of the bills.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = VendorBill::with('vendor')->latest()->paginate(10);
        return view('vendor-bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new bill.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vendors = Vendor::where('status', 'onboarded')->get();
        return view('vendor-bills.create', compact('vendors'));
    }

    /**
     * Store a newly created bill in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'bill_number' => 'required|string|max:50',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:bill_date',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'gst_type' => 'nullable|in:IGST,CGST,SGST',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after_or_equal:billing_period_start',
            'description' => 'nullable|string',
            'bill_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate total amount
            $amount = $request->amount;
            $taxAmount = $request->tax_amount ?? 0;
            $totalAmount = $amount + $taxAmount;

            // Store bill file
            $billFile = $request->file('bill_file');
            $billPath = $billFile->store('vendor_bills', 'public');

            // Create bill
            $bill = VendorBill::create([
                'vendor_id' => $request->vendor_id,
                'bill_number' => $request->bill_number,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'billing_period_start' => $request->billing_period_start,
                'billing_period_end' => $request->billing_period_end,
                'description' => $request->description,
                'file_path' => $billPath,
                'gst_type' => $request->gst_type,
                'status' => 'uploaded',
            ]);

            // Create status history
            BillStatusHistory::create([
                'bill_id' => $bill->id,
                'status' => 'uploaded',
                'comments' => 'Bill uploaded by vendor',
                'changed_by' => Auth::id(),
            ]);
            // Create approval workflow
            BillApprovalWorkflow::create([
                'bill_id' => $bill->id
            ]);

            DB::commit();

            return redirect()->route('vendor-bills.index')
                ->with('success', 'Bill uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error uploading bill: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified bill.
     *
     * @param  \App\Models\VendorBill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show(VendorBill $vendorBill)
    {
        $vendorBill->load(['vendor', 'statusHistory.user']);
        return view('vendor-bills.show', compact('vendorBill'));
    }

    /**
     * Update bill status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorBill  $bill
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, VendorBill $vendorBill)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:under_review,hr_approved,cfo_approved,in_transfer,transferred,rejected',
            'comments' => 'nullable|string',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update bill status
            $vendorBill->update([
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            ]);

            // Create status history
            BillStatusHistory::create([
                'bill_id' => $vendorBill->id,
                'status' => $request->status,
                'comments' => $request->comments,
                'changed_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Bill status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating bill status: ' . $e->getMessage());
        }
    }

    /**
     * Display bills pending for HR approval.
     *
     * @return \Illuminate\Http\Response
     */
    public function hrApprovalQueue()
    {
        $bills = VendorBill::where('status', 'uploaded')
            ->orWhere('status', 'under_review')
            ->with('vendor')
            ->latest()
            ->paginate(10);
        
        return view('vendor-bills.hr-approval', compact('bills'));
    }

    /**
     * Display bills pending for CFO approval.
     *
     * @return \Illuminate\Http\Response
     */
    public function cfoApprovalQueue()
    {
        $bills = VendorBill::where('status', 'hr_approved')
            ->with('vendor')
            ->latest()
            ->paginate(10);
        
        return view('vendor-bills.cfo-approval', compact('bills'));
    }

    /**
     * Display bills pending for payment processing.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentQueue()
    {
        $bills = VendorBill::where('status', 'cfo_approved')
            ->with('vendor')
            ->latest()
            ->paginate(10);
        
        return view('vendor-bills.payment-queue', compact('bills'));
    }

    /**
     * Download bill file.
     *
     * @param  \App\Models\VendorBill  $bill
     * @return \Illuminate\Http\Response
     */
    public function downloadBill(VendorBill $vendorBill)
    {
        if (!Storage::disk('public')->exists($vendorBill->document_path)) {
            return redirect()->back()->with('error', 'Bill file not found.');
        }

        return response()->download(storage_path('app/public/' . $vendorBill->document_path));
    }
}

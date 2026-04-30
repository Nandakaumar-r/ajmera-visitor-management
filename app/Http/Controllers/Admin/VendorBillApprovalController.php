<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorBill;
use App\Models\BillApprovalHistory;
use App\Models\BillPayment;
use App\Models\TdsDeduction;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApprovalWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VendorBillApprovalController extends Controller
{
    /**
     * Display a listing of bills pending approval for the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $pendingBills = $this->getBillsForCurrentUserRole($user);

        return view('admin.vendor_bills.approval.index', compact('pendingBills'));
    }

    /**
     * Display the specified bill for approval.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = VendorBill::with(['vendor', 'vendor.approvalWorkflow', 'approvalHistory.approver'])->findOrFail($id);
        $approvalWorkflow = $bill->vendor->approvalWorkflow;
        $currentUserRole = $this->getCurrentUserApprovalRole($bill);

        // Check if the current user is in the approval workflow for this bill
        if (!$currentUserRole) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'You are not authorized to approve this bill.');
        }
        $tds = \App\Models\TdsDeduction::where('bill_id', $bill->id)->first();

        return view('admin.vendor_bills.approval.show', compact('bill', 'approvalWorkflow', 'currentUserRole', 'tds'));
    }

    /**
     * Update the bill approval status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string|max:1000',
        ]);

        $bill = VendorBill::with('vendor')->findOrFail($id);
        $currentUser = Auth::user();
        $currentUserRole = $this->getCurrentUserApprovalRole($bill);

        if (!$currentUserRole) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'You are not authorized to approve this bill.');
        }

        DB::beginTransaction();
        try {
            // Step 1: Record current approval action
            BillApprovalHistory::create([
                'bill_id' => $bill->id,
                'approver_id' => $currentUser->id,
                'approver_role' => $currentUserRole,
                'status' => $request->status,
                'comments' => $request->comments,
                'action_date' => Carbon::now(),
            ]);

            // Step 2: If rejected
            if ($request->status === 'rejected') {
                $bill->status = 'rejected';
                $bill->rejection_reason = $request->comments;
                $bill->save();

                // Fetch workflow for this bill
                $workflow = VendorApprovalWorkflow::where('vendor_id', $bill->vendor_id)->first();

                // Get initial approver and vendor details
                $initialApprover = $workflow && $workflow->initial_approver_id
                    ? User::find($workflow->initial_approver_id)
                    : null;

                $vendor = $bill->vendor;

                // Send email to initial approver (if available)
                if ($initialApprover && $initialApprover->email) {
                    Mail::to($initialApprover->email)->send(
                        new \App\Mail\BillRejectionNotification($bill, $currentUser, 'approver')
                    );
                }

                // Send email to vendor (if available)
                if ($vendor && $vendor->email) {
                    Mail::to($vendor->email)->send(
                        new \App\Mail\BillRejectionNotification($bill, $currentUser, 'vendor')
                    );
                }

                DB::commit();
                return redirect()->route('admin.bills.approval.index')
                    ->with('success', 'Bill has been rejected successfully and notifications sent.');
            }


            // Step 3: Approved — update status
            $this->updateBillStatusBasedOnRole($bill, $currentUserRole);

            // Step 4: Get workflow for vendor
            $workflow = VendorApprovalWorkflow::where('vendor_id', $bill->vendor_id)->first();

            if ($workflow) {
                $approvalFlow = [
                    'initial_approver_id',
                    'hr_approver_id',
                    'finance_approver_id',
                    'cfo_approver_id',
                    'payment_processor_id',
                ];

                // Step 5: Find current approver’s position
                $currentIndex = array_search($currentUserRole . '_id', $approvalFlow);

                // Step 6: Find next approver
                $nextApproverId = null;
                for ($i = $currentIndex + 1; $i < count($approvalFlow); $i++) {
                    $nextApproverId = $workflow->{$approvalFlow[$i]};
                    if ($nextApproverId) break;
                }

                if ($nextApproverId) {
                    $nextApprover = User::find($nextApproverId);
                    if ($nextApprover) {
                        // Send mail to next approver
                        Mail::to($nextApprover->email)->send(
                            new \App\Mail\BillApprovalNotification($bill, $nextApprover)
                        );
                    }
                } else {
                    // If no next approver, mark as fully approved
                    $bill->status = 'approved';
                    $bill->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.bills.approval.index')
                ->with('success', 'Bill has been approved successfully and moved to next approver.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating bill status: ' . $e->getMessage());
            return redirect()->route('admin.bills.approval.index')
                ->with('error', 'An error occurred while updating approval status.');
        }
    }

    /**
     * Process payment for the bill.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function processPayment(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'payment_status' => 'required|in:processing,paid,failed',
            'payment_notes' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $bill = VendorBill::findOrFail($id);
        $user = Auth::user();

        // Check if the current user is the payment processor
        $approvalWorkflow = $bill->vendor->approvalWorkflow;
        if ($approvalWorkflow->payment_processor_id !== $user->id) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'You are not authorized to process payments for this bill.');
        }

        // Update payment status
        $bill->payment_status = $request->payment_status;
        $bill->payment_notes = $request->payment_notes;

        if ($request->payment_status === 'paid') {
            $bill->payment_date = Carbon::now();
            $bill->status = 'transferred';
        }

        $bill->save();

        // Record the payment action
        $approvalHistory = new BillApprovalHistory([
            'bill_id' => $bill->id,
            'approver_id' => Auth::id(),
            'approver_role' => 'payment_processor',
            'status' => $request->payment_status === 'failed' ? 'rejected' : 'approved',
            'comments' => $request->payment_notes,
            'action_date' => Carbon::now(),
        ]);
        $approvalHistory->save();

        BillPayment::create([
            'bill_id' => $bill->id,
            'payment_method' => $request->payment_method ?? 'bank_transfer',
            'transaction_id' => $request->transaction_id ?? null,
            'amount' => $request->amount,
            'payment_date' => Carbon::now(),
            'notes' => $request->payment_notes,
            'status' => $request->payment_status,
        ]);
        // Send payment notification to vendor
        try {
            if ($bill->vendor && $bill->vendor->email) {
                Mail::to($bill->vendor->email)->send(
                    new \App\Mail\BillPaymentNotification($bill, $request->payment_status)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send payment notification: ' . $e->getMessage());
        }

        return redirect()->route('admin.bills.approval.index')
            ->with('success', 'Payment status updated successfully.');
    }

    /**
     * Get the current user's role in the approval workflow for the given bill.
     *
     * @param  \App\Models\VendorBill  $bill
     * @return string|null
     */
    private function getCurrentUserApprovalRole(VendorBill $bill)
    {
        $user = Auth::user();
        $approvalWorkflow = $bill->vendor->approvalWorkflow;

        if ($approvalWorkflow->initial_approver_id === $user->id) {
            return 'initial_approver';
        } elseif ($approvalWorkflow->hr_approver_id === $user->id) {
            return 'hr_approver';
        } elseif ($approvalWorkflow->finance_approver_id === $user->id) {
            return 'finance_approver';
        } elseif ($approvalWorkflow->cfo_approver_id === $user->id) {
            return 'cfo_approver';
        } elseif ($approvalWorkflow->payment_processor_id === $user->id) {
            return 'payment_processor';
        }

        return null;
    }

    /**
     * Update bill status based on the current user's role.
     *
     * @param  \App\Models\VendorBill  $bill
     * @param  string  $currentUserRole
     * @return void
     */
    private function updateBillStatusBasedOnRole(VendorBill $bill, $currentUserRole)
    {
        switch ($currentUserRole) {
            case 'initial_approver':
                $bill->status = 'under_review';
                break;
            case 'hr_approver':
                $bill->status = 'hr_approved';
                break;
            case 'finance_approver':
                $bill->status = 'finance_approved'; // Still needs CFO approval
                break;
            case 'cfo_approver':
                $bill->status = 'cfo_approved';
                $bill->payment_status = 'pending';
                break;
            case 'payment_processor':
                $bill->status = 'in_transfer';
                break;
        }

        $bill->save();
    }

    /**
     * Get bills pending approval for the current user based on their role.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getBillsForCurrentUserRole($user)
    {
        $userId = $user->id;

        // Get vendor IDs where the current user is in the approval workflow
        $initialApproverVendorIds  = VendorApprovalWorkflow::where('initial_approver_id', $userId)->pluck('vendor_id');
        $hrApproverVendorIds       = VendorApprovalWorkflow::where('hr_approver_id', $userId)->pluck('vendor_id');
        $financeApproverVendorIds  = VendorApprovalWorkflow::where('finance_approver_id', $userId)->pluck('vendor_id');
        $cfoApproverVendorIds      = VendorApprovalWorkflow::where('cfo_approver_id', $userId)->pluck('vendor_id');
        $paymentProcessorVendorIds = VendorApprovalWorkflow::where('payment_processor_id', $userId)->pluck('vendor_id');

        // NEW: vendors which don't have any HR approver configured
        $vendorsWithoutHrApprover = VendorApprovalWorkflow::whereNull('hr_approver_id')
            ->pluck('vendor_id');

        $pendingBills = VendorBill::with('vendor')
            ->where(function ($query) use (
                $initialApproverVendorIds,
                $hrApproverVendorIds,
                $financeApproverVendorIds,
                $cfoApproverVendorIds,
                $paymentProcessorVendorIds,
                $vendorsWithoutHrApprover
            ) {
                // 1. Initial approver: uploaded
                $query->where(function ($q) use ($initialApproverVendorIds) {
                    $q->whereIn('vendor_id', $initialApproverVendorIds)
                        ->where('status', 'uploaded');
                });

                // 2. HR approver: under_review
                $query->orWhere(function ($q) use ($hrApproverVendorIds) {
                    $q->whereIn('vendor_id', $hrApproverVendorIds)
                        ->where('status', 'under_review');
                });

                // 3. Finance approver:
                //    - If vendor HAS HR approver  → show bills with status = hr_approved
                //    - If vendor has NO HR approver → skip HR step and show bills with status = under_review
                $query->orWhere(function ($q) use ($financeApproverVendorIds, $vendorsWithoutHrApprover) {
                    $q->whereIn('vendor_id', $financeApproverVendorIds)
                        ->where(function ($inner) use ($vendorsWithoutHrApprover) {
                            $inner
                                // Case A: HR exists → need hr_approved
                                ->where(function ($q1) use ($vendorsWithoutHrApprover) {
                                    $q1->whereNotIn('vendor_id', $vendorsWithoutHrApprover)
                                        ->where('status', 'hr_approved');
                                })
                                // Case B: NO HR approver → allow under_review
                                ->orWhere(function ($q2) use ($vendorsWithoutHrApprover) {
                                    $q2->whereIn('vendor_id', $vendorsWithoutHrApprover)
                                        ->where('status', 'under_review');
                                });
                        });
                });

                // 4. CFO approver: under_review + finance approved in history
                $query->orWhere(function ($q) use ($cfoApproverVendorIds) {
                    $q->whereIn('vendor_id', $cfoApproverVendorIds)
                        ->where('status', 'finance_approved')
                        ->whereExists(function ($subquery) {
                            $subquery->select('id')
                                ->from('bill_approval_histories')
                                ->whereColumn('bill_id', 'vendor_bills.id')
                                ->where('approver_role', 'finance_approver')
                                ->where('status', 'approved');
                        });
                });

                // 5. Payment processor: cfo_approved
                $query->orWhere(function ($q) use ($paymentProcessorVendorIds) {
                    $q->whereIn('vendor_id', $paymentProcessorVendorIds)
                        ->where('status', 'cfo_approved');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $pendingBills;
    }

    // private function getBillsForCurrentUserRole($user)
    // {
    //     $userId = $user->id;

    //     // Get vendor IDs where the current user is in the approval workflow
    //     $initialApproverVendorIds = VendorApprovalWorkflow::where('initial_approver_id', $userId)->pluck('vendor_id');
    //     $hrApproverVendorIds = VendorApprovalWorkflow::where('hr_approver_id', $userId)->pluck('vendor_id');
    //     $financeApproverVendorIds = VendorApprovalWorkflow::where('finance_approver_id', $userId)->pluck('vendor_id');
    //     $cfoApproverVendorIds = VendorApprovalWorkflow::where('cfo_approver_id', $userId)->pluck('vendor_id');
    //     $paymentProcessorVendorIds = VendorApprovalWorkflow::where('payment_processor_id', $userId)->pluck('vendor_id');

    //     // Get bills based on user's role in the approval workflow
    //     $pendingBills = VendorBill::with('vendor')
    //         ->where(function ($query) use ($initialApproverVendorIds, $hrApproverVendorIds, $financeApproverVendorIds, $cfoApproverVendorIds, $paymentProcessorVendorIds) {
    //             // Initial approver: Show uploaded bills for vendors where user is initial approver
    //             $query->whereIn('vendor_id', $initialApproverVendorIds)
    //                 ->where('status', 'uploaded');

    //             // HR approver: Show under_review bills for vendors where user is HR approver
    //             $query->orWhereIn('vendor_id', $hrApproverVendorIds)
    //                 ->where('status', 'under_review');

    //             // Finance approver: Show hr_approved bills for vendors where user is finance approver
    //             $query->orWhereIn('vendor_id', $financeApproverVendorIds)
    //                 ->where('status', 'hr_approved');

    //             // CFO approver: Show under_review bills that have been approved by finance
    //             $query->orWhereIn('vendor_id', $cfoApproverVendorIds)
    //                 ->where('status', 'under_review')
    //                 ->whereExists(function ($subquery) use ($financeApproverVendorIds) {
    //                     $subquery->select('id')
    //                         ->from('bill_approval_histories')
    //                         ->whereColumn('bill_id', 'vendor_bills.id')
    //                         ->where('approver_role', 'finance_approver')
    //                         ->where('status', 'approved');
    //                 });

    //             // Payment processor: Show cfo_approved bills for vendors where user is payment processor
    //             $query->orWhereIn('vendor_id', $paymentProcessorVendorIds)
    //                 ->where('status', 'cfo_approved');
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return $pendingBills;
    // }

    public function saveTds(Request $request, $billId)
    {
        $request->validate([
            'tds_percentage' => 'required|numeric|min:0|max:100',
            'tds_amount' => 'required|numeric|min:0',
            'after_tds' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $bill = VendorBill::findOrFail($billId);

        // Save or update TDS record for this bill
        TdsDeduction::updateOrCreate(
            ['bill_id' => $bill->id],
            [
                'vendor_id' => $bill->vendor_id,
                'deduction_percentage' => $request->tds_percentage,
                'deduction_amount' => $request->tds_amount,
                'after_tds' => $request->after_tds,
                'paid_amount' => $request->paid_amount,
            ]
        );

        // Save checkbox states
        $bill->include_tax_checked = $request->has('include_tax');
        $bill->selected_credit_notes = $request->selected_credit_notes ?: json_encode([]);
        $bill->save();

        return redirect()->back()->with('success', 'TDS details saved successfully.');
    }

    public function updateDate(Request $request, $id)
    {
        $request->validate([
            'payable_date' => 'required|date',
        ]);

        $bill = VendorBill::findOrFail($id);
        $bill->payable_date = $request->payable_date;
        $bill->save();

        return redirect()->back()->with('success', 'Bill date updated successfully.');
    }
}

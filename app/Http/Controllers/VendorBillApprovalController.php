<?php

namespace App\Http\Controllers;

use App\Models\VendorBill;
use App\Models\BillApprovalWorkflow;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\Factory;

class VendorBillApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get bills that need approval by this user
        $workflows = BillApprovalWorkflow::with(['bill', 'bill.vendor'])
            ->where(function($query) use ($user) {
                $query->where('level_1_approver_id', $user->id)
                    ->where('level_1_status', 'pending')
                    ->where('current_level', 1);
            })
            ->orWhere(function($query) use ($user) {
                $query->where('level_2_approver_id', $user->id)
                    ->where('level_2_status', 'pending')
                    ->where('current_level', 2);
            })
            ->orWhere(function($query) use ($user) {
                $query->where('level_3_approver_id', $user->id)
                    ->where('level_3_status', 'pending')
                    ->where('current_level', 3);
            })
            ->orWhere(function($query) use ($user) {
                $query->where('level_4_approver_id', $user->id)
                    ->where('level_4_status', 'pending')
                    ->where('current_level', 4);
            })
            ->orWhere(function($query) use ($user) {
                $query->where('level_5_approver_id', $user->id)
                    ->where('level_5_status', 'pending')
                    ->where('current_level', 5);
            })
            ->orWhere(function($query) use ($user) {
                $query->where('final_approver_id', $user->id)
                    ->where('final_status', 'pending')
                    ->where('current_level', 6);
            })
            ->latest()
            ->paginate(10);
        
        return view('admin.vendor-bills.approval-index', compact('workflows'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id): View
    {
        $workflow = BillApprovalWorkflow::with(['bill', 'bill.vendor', 'bill.statusHistory', 'bill.originalBill', 'bill.creditNotes'])
            ->findOrFail($id);
        
        $user = Auth::user();
        $canApprove = $this->canUserApprove($workflow, $user);
        
        return view('admin.vendor-bills.approval-show', compact('workflow', 'canApprove'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
        ]);
        
        $workflow = BillApprovalWorkflow::findOrFail($id);
        $user = Auth::user();
        
        // Check if user can approve at current level
        if (!$this->canUserApprove($workflow, $user)) {
            return redirect()->back()->with('error', 'You are not authorized to approve at this level.');
        }
        
        try {
            DB::beginTransaction();
            
            $action = $request->action;
            $comments = $request->comments;
            
            // Update workflow based on current level
            switch ($workflow->current_level) {
                case 1:
                    $workflow->level_1_status = $action;
                    $workflow->level_1_comments = $comments;
                    $workflow->level_1_approved_at = now();
                    break;
                case 2:
                    $workflow->level_2_status = $action;
                    $workflow->level_2_comments = $comments;
                    $workflow->level_2_approved_at = now();
                    break;
                case 3:
                    $workflow->level_3_status = $action;
                    $workflow->level_3_comments = $comments;
                    $workflow->level_3_approved_at = now();
                    break;
                case 4:
                    $workflow->level_4_status = $action;
                    $workflow->level_4_comments = $comments;
                    $workflow->level_4_approved_at = now();
                    break;
                case 5:
                    $workflow->level_5_status = $action;
                    $workflow->level_5_comments = $comments;
                    $workflow->level_5_approved_at = now();
                    break;
                case 6:
                    $workflow->final_status = $action;
                    $workflow->final_comments = $comments;
                    $workflow->final_approved_at = now();
                    break;
            }
            
            // Update overall status and next level
            if ($action === 'reject') {
                $workflow->overall_status = 'rejected';
                $workflow->current_level = 0; // No more approvals needed
                
                // Update bill status
                $workflow->bill->status = 'rejected';
                $workflow->bill->save();
                
                // Add status history
                $workflow->bill->statusHistory()->create([
                    'status' => 'rejected',
                    'comments' => $comments,
                    'user_id' => $user->id,
                ]);
            } else {
                // Approved
                if ($workflow->current_level < 6) {
                    // Move to next level
                    $workflow->current_level++;
                    $workflow->overall_status = 'pending';
                } else {
                    // Final approval
                    $workflow->overall_status = 'completed';
                    $workflow->current_level = 0; // No more approvals needed
                    
                    // Update bill status
                    $workflow->bill->status = 'transferred';
                    $workflow->bill->save();
                    
                    // Add status history
                    $workflow->bill->statusHistory()->create([
                        'status' => 'transferred',
                        'comments' => $comments,
                        'user_id' => $user->id,
                    ]);
                }
            }
            
            $workflow->save();
            
            DB::commit();
            
            $message = $action === 'approve' ? 'Bill approved successfully.' : 'Bill rejected successfully.';
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating bill approval status: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }

    /**
     * Process payment for the bill.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function processPayment(Request $request, $id): RedirectResponse
    {
        $workflow = BillApprovalWorkflow::findOrFail($id);
        $user = Auth::user();
        
        // Only final approver can process payment
        if ($workflow->final_approver_id != $user->id) {
            return redirect()->back()->with('error', 'You are not authorized to process payment for this bill.');
        }
        
        try {
            DB::beginTransaction();
            
            // Update bill status
            $workflow->bill->status = 'transferred';
            $workflow->bill->save();
            
            // Add status history
            $workflow->bill->statusHistory()->create([
                'status' => 'transferred',
                'comments' => 'Payment processed and transferred',
                'user_id' => $user->id,
            ]);
            
            // Mark workflow as completed
            $workflow->overall_status = 'completed';
            $workflow->current_level = 0;
            $workflow->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Payment processed successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing bill payment: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }
    
    /**
     * Check if the current user can approve the bill at the current level.
     *
     * @param  BillApprovalWorkflow  $workflow
     * @param  User  $user
     * @return bool
     */
    private function canUserApprove($workflow, User $user)
    {
        switch ($workflow->current_level) {
            case 1:
                return $workflow->level_1_approver_id == $user->id;
            case 2:
                return $workflow->level_2_approver_id == $user->id;
            case 3:
                return $workflow->level_3_approver_id == $user->id;
            case 4:
                return $workflow->level_4_approver_id == $user->id;
            case 5:
                return $workflow->level_5_approver_id == $user->id;
            case 6:
                return $workflow->final_approver_id == $user->id;
            default:
                return false;
        }
    }
}

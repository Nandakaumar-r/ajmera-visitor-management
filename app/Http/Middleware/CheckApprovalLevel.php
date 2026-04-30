<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BillApprovalWorkflow;

class CheckApprovalLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $level
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $workflowId = $request->route('id');
        
        if (!$workflowId) {
            return redirect()->back()->with('error', 'Invalid request.');
        }
        
        $workflow = BillApprovalWorkflow::find($workflowId);
        
        if (!$workflow) {
            return redirect()->back()->with('error', 'Approval workflow not found.');
        }
        
        $canApprove = false;
        
        // Check if user is authorized for the current level of the workflow
        switch ($workflow->current_level) {
            case 1:
                $canApprove = ($workflow->level_1_approver_id == $user->id);
                break;
            case 2:
                $canApprove = ($workflow->level_2_approver_id == $user->id);
                break;
            case 3:
                $canApprove = ($workflow->level_3_approver_id == $user->id);
                break;
            case 4:
                $canApprove = ($workflow->level_4_approver_id == $user->id);
                break;
            case 5:
                $canApprove = ($workflow->level_5_approver_id == $user->id);
                break;
            case 6: // Final approval level
                $canApprove = ($workflow->final_approver_id == $user->id);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid approval level.');
        }
        
        if (!$canApprove) {
            return redirect()->back()->with('error', 'You are not authorized to approve at this level.');
        }
        
        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use App\Constants\RoleEmails;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalNotification;
use App\Mail\OfferCancelledNotification;
use App\Mail\OfferMadeNotification;
use App\Mail\RejectionNotification;
use App\Models\InternalOnboardingCandidateDetails;
use App\Models\InternalORFCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalORFApprovalController extends Controller
{
    public function showRoleList($role)
    {
        // dd(Auth::user()->email);
        $allowedRoles = [
            'hrbp' => RoleEmails::HRBP_EMAIL,
            // 'account_manager' => 'karteek.kr@fidelisgroup.in',
            'delivery_manager' => RoleEmails::DELIVERY_MANAGER_EMAIL,
            'coo' => RoleEmails::COO_EMAIL,
            'cfo' => RoleEmails::CFO_EMAIL,
            'chro' => RoleEmails::CHRO_EMAIL,
            'hr' => RoleEmails::HR_EMAIL,
        ];

        if (!array_key_exists($role, $allowedRoles)) {
            abort(403, 'Invalid role');
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (strtolower(Auth::user()->email) !== strtolower($allowedRoles[$role])) {
            return response()->view('errors.unauthorized', [], 403);
        }

        // Define role dependency
        $roleDependencies = [
            'delivery_manager' => 'hrbp_status',
            'coo' => 'delivery_manager_status',
            'cfo' => 'coo_status',
            'chro' => 'cfo_status',
            'hr' => 'chro_status',
            // hrbp does not depend on anything
        ];

        $query = InternalOnboardingCandidateDetails::query();

        if (array_key_exists($role, $roleDependencies)) {
            $requiredField = $roleDependencies[$role];
            $query->where($requiredField, 'approved');
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('internal_onboarding.role_list', compact('candidates', 'role'));
    }

    public function view($id)
    {
        $orf = InternalOnboardingCandidateDetails::findOrFail($id);
        // Fetch related ORF creation details using email (or another unique key)
        $orfCreation = InternalORFCreation::where('candidate_id', $orf->id)->first();
        return view('internal_onboarding.view', [
            'orf' => $orf,
            'orfCreation' => $orfCreation
        ]);
    }
    public function show()
    {
        $orfs = InternalOnboardingCandidateDetails::orderBy('created_at', 'desc')->paginate(20);
        // Fetch all ORFs from the primary DB

        // Return the view with ORFs
        return view('internal_onboarding.show', compact('orfs'));
    }

    /**
     * Show the specified ORF to the admin for approval.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function viewByRole($role, $id)
    {
        $orf = InternalOnboardingCandidateDetails::findOrFail($id);
        $orfCreation = InternalORFCreation::where('email', $orf->email)->first();

        switch ($role) {
            case 'hrbp':
                return view('internal_onboarding.hrbp', compact('orf', 'orfCreation', 'role'));
                // case 'account_manager':
                //     return view('internal_onboarding.account_manager', compact('orf', 'orfCreation', 'role'));
            case 'delivery_manager':
                return view('internal_onboarding.delivery_manager', compact('orf', 'orfCreation', 'role'));
            case 'coo':
                return view('internal_onboarding.coo', compact('orf', 'orfCreation', 'role'));
            case 'cfo':
                return view('internal_onboarding.cfo', compact('orf', 'orfCreation', 'role'));
            case 'chro':
                return view('internal_onboarding.chro', compact('orf', 'orfCreation', 'role'));
            case 'hr':
                return view('internal_onboarding.hr', compact('orf', 'orfCreation', 'role'));
            default:
                abort(403, 'Invalid Role');
        }
    }



    public function approve(Request $request, $role, $id)
    {
        $orf = InternalOnboardingCandidateDetails::findOrFail($id);

        // 1. Set current role's approval status and remarks
        $orf->{$role . '_status'} = 'approved';
        $orf->{$role . '_remarks'} = $request->input('comments');
        $orf->user_id = Auth::id();
        $orf->save();

        // 2. Check if all roles have approved
        $allRoles = ['hrbp', 'delivery_manager', 'coo', 'cfo', 'chro', 'hr'];
        $approvedCount = 0;

        foreach ($allRoles as $r) {
            if ($orf->{$r . '_status'} === 'approved') {
                $approvedCount++;
            }
        }

        if ($approvedCount === count($allRoles)) {
            $orf->status = 'approved'; // Mark ORF as fully approved
            $orf->save();
        }

        // 3. Send next role's approval email if exists
        $roleSequence = ['hrbp', 'delivery_manager', 'coo', 'cfo', 'chro', 'hr'];
        $index = array_search($role, $roleSequence);
        $nextRole = $roleSequence[$index + 1] ?? null;

        if ($nextRole) {
            $nextEmail = $this->getRoleEmail($nextRole);
            if ($nextEmail) {
                Mail::to($nextEmail)->send(new ApprovalNotification($orf, $role, $nextRole));
            }
        }

        return redirect()->route('orf.view.role', ['role' => $role, 'id' => $id])
            ->with('success', 'Approved and forwarded to next stage.');
    }




    public function reject(Request $request, $role, $id)
    {
        $orf = InternalOnboardingCandidateDetails::findOrFail($id);

        $reason = $request->input('comments');

        // 1. Update role-specific and general rejection status
        $orf->{$role . '_status'} = 'rejected';
        $orf->{$role . '_remarks'} = $reason;
        $orf->remarks = $reason; // ✅ Save final reason in common remarks field
        $orf->status = 'rejected';
        $orf->user_id = Auth::id();
        $orf->save();

        // 2. Send rejection email to recruiter
        $candidate = InternalORFCreation::with('user')->find($id);
        $recruiterEmail = $candidate->user->email;
        Mail::to($recruiterEmail)->send(new RejectionNotification($orf, $role, $reason));

        return redirect()->route('orf.list.role', ['role' => $role])
            ->with('success', 'Candidate has been rejected and recruiter notified.');
    }


    // Map role flow and emails
    private function getRoleFlow()
    {
        return [
            'hrbp' => 'delivery_manager',
            'delivery_manager' => 'coo',
            'coo' => 'cfo',
            'cfo' => 'chro',
            'chro' => 'hr',
            'hr' => null, // Final stage
        ];
    }

    private function getRoleEmail($role)
    {
        $emails = [
            'hrbp' => RoleEmails::HRBP_EMAIL,
            // 'account_manager' => 'karteek.kr@fidelisgroup.in',
            'delivery_manager' => RoleEmails::DELIVERY_MANAGER_EMAIL,
            'coo' => RoleEmails::COO_EMAIL,
            'cfo' => RoleEmails::CFO_EMAIL,
            'chro' => RoleEmails::CHRO_EMAIL,
            'hr' => RoleEmails::HR_EMAIL,
        ];

        return $emails[$role] ?? null;
    }

    public function markOffered(Request $request, $role, $id)
    {
        $orf = InternalOnboardingCandidateDetails::findOrFail($id);

        if ($role === 'hr') {
            $orf->status = 'offered';
            $orf->remarks = 'Marked as Offered by HR';
            $orf->user_id = Auth::id();
            $orf->{$role . '_status'} = 'offered';
            $orf->save();

            // if (!empty($orf->email)) {
            //     Mail::to($orf->email)->send(new OfferMadeNotification($orf));
            // }
        }

        return redirect()->route('orf.view.role', ['role' => $role, 'id' => $id])
            ->with('success', 'Candidate marked as Offered.');
    }

    public function markCancelled(Request $request, $role, $id)
    {
        $orf = InternalOnboardingCandidateDetails::findOrFail($id);

        if ($role === 'hr') {
            $orf->status = 'cancelled';
            $orf->remarks = 'Marked as Cancelled by HR';
            $orf->user_id = Auth::id();
            $orf->{$role . '_status'} = 'cancelled';
            $orf->save();

            if (!empty($orf->email)) {
                Mail::to($orf->email)->send(new OfferCancelledNotification($orf));
            }
        }

        return redirect()->route('orf.view.role', ['role' => $role, 'id' => $id])
            ->with('success', 'Candidate marked as Cancelled.');
    }
}

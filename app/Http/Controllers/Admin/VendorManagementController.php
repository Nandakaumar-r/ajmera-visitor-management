<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Models\VendorBankDetail;
use App\Models\User;
use App\Models\VendorApprovalWorkflow;
use App\Services\VendorDocumentService;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorStatusUpdated;
use App\Mail\VendorWelcomeEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class VendorManagementController extends Controller
{
    protected $documentService;

    public function __construct(VendorDocumentService $documentService)
    {
        $this->documentService = $documentService;
        // $this->middleware(['auth', 'role:admin,hr']);
    }

    /**
     * Display a listing of the vendors
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        $loggedInUser = Auth::user();
        $userRole = $loggedInUser->roles()->first()->name;
        if ($userRole == 'payment_processor' || $userRole == 'finance_approver') {
            // Payment Processor should see ALL vendors
            $query = Vendor::with('user');
        } else {
            // Normal users see only assigned vendors
            $query = Vendor::with('user')
                ->where('contact_person', $loggedInUser->name);
        }

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $vendors = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['status' => $status, 'search' => $search]);

        return view('admin.vendors.index', compact('vendors', 'status', 'search'));
    }

    /**
     * Display the specified vendor details
     */
    public function show($id)
    {
        $vendor = Vendor::with(['documents', 'bankDetails', 'bills'])->findOrFail($id);
        $requiredDocuments = $this->documentService->getRequiredDocuments($vendor->type);
        $missingDocuments = $this->documentService->getMissingRequiredDocuments($vendor);

        return view('admin.vendors.show', compact('vendor', 'requiredDocuments', 'missingDocuments'));
    }

    /**
     * Update vendor status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending_verification,active,blocked',
            // 'comments' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);
        $oldStatus = $vendor->status;

        // Update vendor status
        $vendor->status = $request->status;

        // Optional: update onboarding status based on your rules
        if ($request->status === 'active') {
            $vendor->onboarding_status = 'approved';
        } elseif ($request->status === 'blocked') {
            $vendor->onboarding_status = 'rejected';
        }

        $vendor->save();

        // If vendor is active, update the user status as well
        // if ($request->status === 'active' && $oldStatus !== 'active') {
        //     $user = User::where('email', $vendor->email)->first();
        //     if ($user) {
        //         $user->status = 'active';
        //         $user->save();
        //     }

        // Send email to vendor about activation
        try {
            Mail::to($vendor->email)->send(new \App\Mail\VendorActivated($vendor));
        } catch (\Exception $e) {
            \Log::error('Failed to send vendor activation email: ' . $e->getMessage());
        }
        //}

        return redirect()->route('admin.vendors.show', $vendor->id)
            ->with('success', 'Vendor status updated successfully.');
    }


    /**
     * Download vendor document (legacy method - redirects to new route)
     */
    public function downloadDocument($id)
    {
        $document = VendorDocument::findOrFail($id);
        $vendor = $document->vendor;

        return redirect()->route('admin.vendors.documents.download', [$vendor, $document]);
    }

    /**
     * Export vendors list as CSV
     */
    /**
     * Send welcome email to vendor
     */
    public function sendWelcomeEmail($id)
    {
        $vendor = Vendor::findOrFail($id);

        try {
            Mail::to($vendor->email)->send(new VendorWelcomeEmail($vendor));
            return redirect()->back()->with('success', 'Welcome email sent successfully to ' . $vendor->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email to vendor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send welcome email. Please try again.');
        }
    }

    /**
     * Export vendors list as CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Vendor::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $vendors = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendors.csv"',
        ];

        $callback = function () use ($vendors) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Type',
                'Contact Person',
                'Email',
                'Phone',
                'Status',
                'Onboarding Status',
                'GST Number',
                'GST Verified',
                'PAN Number',
                'PAN Verified',
                'Registration Date'
            ]);

            // Add vendor data
            foreach ($vendors as $vendor) {
                fputcsv($file, [
                    $vendor->id,
                    $vendor->name,
                    ucfirst($vendor->type),
                    $vendor->contact_person,
                    $vendor->email,
                    $vendor->phone,
                    ucfirst($vendor->status),
                    str_replace('_', ' ', ucfirst($vendor->onboarding_status)),
                    $vendor->gst_number ?: 'N/A',
                    $vendor->gst_verified ? 'Yes' : 'No',
                    $vendor->pan_number ?: 'N/A',
                    $vendor->pan_verified ? 'Yes' : 'No',
                    $vendor->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the vendor approval workflow configuration form
     */
    public function showWorkflowConfig($id)
    {
        $vendor = Vendor::with('approvalWorkflow')->findOrFail($id);

        // Get all users who can be approvers (admins, managers, etc.)
        $approvers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'hr', 'finance', 'manager']);
        })->get();

        return view('admin.vendors.workflow-config', compact('vendor', 'approvers'));
    }

    /**
     * Update the vendor approval workflow configuration
     */
    public function updateWorkflowConfig(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'initial_approver_id' => 'required|exists:users,id',
            'hr_approver_id' => 'required|exists:users,id',
            'finance_approver_id' => 'required|exists:users,id',
            'cfo_approver_id' => 'required|exists:users,id',
            'payment_processor_id' => 'required|exists:users,id',
        ]);

        // Create or update the approval workflow for this vendor
        $workflow = $vendor->approvalWorkflow;

        if (!$workflow) {
            $workflow = new VendorApprovalWorkflow();
            $workflow->vendor_id = $vendor->id;
        }

        $workflow->initial_approver_id = $request->initial_approver_id;
        $workflow->hr_approver_id = $request->hr_approver_id;
        $workflow->finance_approver_id = $request->finance_approver_id;
        $workflow->cfo_approver_id = $request->cfo_approver_id;
        $workflow->payment_processor_id = $request->payment_processor_id;

        $workflow->save();

        return redirect()->route('admin.vendors.show', $vendor->id)
            ->with('success', 'Vendor approval workflow configuration updated successfully.');
    }


    public function saveVendor(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'contact_person' => 'required',
            ]);

            $vendor = new user();
            $vendor->name = $request->name;
            $vendor->email = $request->email;
            $vendor->password = bcrypt($request->password);

            $vendor->save();
            $vendor->assignRole('Vendor');

            $vendor->vendor = new Vendor();
            $vendor->vendor->name = $request->name;
            $vendor->vendor->email = $request->email;
            $vendor->vendor->contact_person = $request->contact_person;
            $vendor->vendor->save();

            //Send welcome email to the new vendor
            try {
                Mail::to($vendor->email)->send(new VendorWelcomeEmail($vendor->vendor));
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email to vendor: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to send welcome email. Please try again.');
            }
            return redirect()->route('admin.vendors.index')
                ->with('success', 'Vendor onboarded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroyBankDetail($vendorId, $bankDetailId)
    {
        $bankDetail = VendorBankDetail::findOrFail($bankDetailId);
        $vendorId = $bankDetail->vendor_id;
        $bankDetail->delete();

        return redirect()->route('admin.vendors.show', $vendorId)
            ->with('success', 'Bank detail deleted successfully.');
    }

    public function editBankDetail($vendorId, $bankDetailId)
    {
        $bankDetail = VendorBankDetail::findOrFail($bankDetailId);
        $vendorId = $bankDetail->vendor_id;

        return view('admin.vendors.edit-bank-detail', compact('bankDetail', 'vendorId'));
    }
}

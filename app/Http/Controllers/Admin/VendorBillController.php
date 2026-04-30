<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorBill;
use App\Models\VendorBillStatus;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillStatusUpdated;
use Illuminate\Support\Facades\Storage;

class VendorBillController extends Controller
{
    public function __construct()
    {
        // Different methods need different roles
        // $this->middleware(['auth']);
        // $this->middleware(['role:admin,hr,cfo,finance']);
    }

    /**
     * Display a listing of the bills
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        $vendor_id = $request->get('vendor_id');

        $query = VendorBill::with(['vendor', 'statusHistory' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by vendor
        if ($vendor_id) {
            $query->where('vendor_id', $vendor_id);
        }

        // Search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Role-based filters
        $user = Auth::user();
        $userRole = $user->roles()->first()->name;

        // HR can see uploaded and under_review bills
        if ($userRole === 'hr') {
            $query->whereIn('status', ['uploaded', 'under_review', 'hr_approved', 'rejected']);
        }
        // CFO can see hr_approved bills
        elseif ($userRole === 'cfo') {
            $query->whereIn('status', ['hr_approved', 'cfo_approved', 'rejected']);
        }
        // Finance can see cfo_approved bills
        elseif ($userRole === 'finance') {
            $query->whereIn('status', ['cfo_approved', 'in_transfer', 'transferred', 'rejected']);
        }

        $bills = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['status' => $status, 'search' => $search, 'vendor_id' => $vendor_id]);

        $vendors = Vendor::where('status', 'approved')->get();

        return view('admin.bills.index', compact('bills', 'status', 'search', 'vendor_id', 'vendors', 'userRole'));
    }

    /**
     * Display the specified bill
     */
    public function show($id)
    {
        $bill = VendorBill::with(['vendor', 'statusHistory' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        // Check if user has permission to view this bill based on role and bill status
        $user = Auth::user();
        $userRole = $user->roles()->first()->name;

        $canView = true;

        if ($userRole === 'hr' && !in_array($bill->status, ['uploaded', 'under_review', 'hr_approved', 'rejected'])) {
            $canView = false;
        } elseif ($userRole === 'cfo' && !in_array($bill->status, ['hr_approved', 'cfo_approved', 'rejected'])) {
            $canView = false;
        } elseif ($userRole === 'finance' && !in_array($bill->status, ['cfo_approved', 'in_transfer', 'transferred', 'rejected'])) {
            $canView = false;
        }

        if (!$canView) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'You do not have permission to view this bill.');
        }

        return view('admin.bills.show', compact('bill', 'userRole'));
    }

    /**
     * Update bill status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'comments' => 'nullable|string|max:500',
        ]);

        $bill = VendorBill::findOrFail($id);
        $user = Auth::user();
        $userRole = $user->roles()->first()->name;

        // Validate status transition based on role
        $validTransition = false;

        if ($userRole === 'hr') {
            if (
                in_array($bill->status, ['uploaded', 'under_review']) &&
                in_array($request->status, ['under_review', 'hr_approved', 'rejected'])
            ) {
                $validTransition = true;
            }
        } elseif ($userRole === 'cfo') {
            if (
                $bill->status === 'hr_approved' &&
                in_array($request->status, ['cfo_approved', 'rejected'])
            ) {
                $validTransition = true;
            }
        } elseif ($userRole === 'finance') {
            if (
                $bill->status === 'cfo_approved' &&
                in_array($request->status, ['in_transfer', 'transferred'])
            ) {
                $validTransition = true;
            } elseif (
                $bill->status === 'in_transfer' &&
                $request->status === 'transferred'
            ) {
                $validTransition = true;
            }
        } elseif ($userRole === 'admin') {
            // Admin can make any transition
            $validTransition = true;
        }

        if (!$validTransition) {
            return redirect()->back()
                ->with('error', 'Invalid status transition for your role.');
        }

        // Update bill status
        $oldStatus = $bill->status;
        $bill->status = $request->status;
        $bill->save();

        // Create status history record
        VendorBillStatus::create([
            'vendor_bill_id' => $bill->id,
            'status' => $request->status,
            'comments' => $request->comments,
            'updated_by' => $user->id
        ]);

        // Send email notification to vendor
        try {
            Mail::to($bill->vendor->email)->send(new BillStatusUpdated($bill));
        } catch (\Exception $e) {
            // Log the error but don't stop execution
            \Log::error('Failed to send bill status update email: ' . $e->getMessage());
        }

        return redirect()->route('admin.bills.show', $bill->id)
            ->with('success', 'Bill status updated successfully.');
    }

    /**
     * Download bill document
     */
    public function download($id, $type = 'bill')
    {
        $bill = VendorBill::findOrFail($id);

        if ($type === 'credit_note') {
            $filePath = $bill->credit_note_file_path;

            if (!$filePath) {
                return back()->with('error', 'No credit note file available for this bill.');
            }

            $number = $bill->credit_note_number ?? 'credit_note';
        } else {
            $filePath = $bill->file_path;

            if (!$filePath) {
                return back()->with('error', 'No bill file available.');
            }

            $number = $bill->bill_number ?? 'bill';
        }

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', ucfirst($type) . ' file not found.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        // ✅ sanitize filename
        $safeNumber = str_replace(['/', '\\'], '-', $number);
        $fileName = $safeNumber . '.' . $extension;

        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Export bills as CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status', 'all');
        $vendor_id = $request->get('vendor_id');

        $query = VendorBill::with('vendor');

        // Apply filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($vendor_id) {
            $query->where('vendor_id', $vendor_id);
        }

        // Role-based filters
        $user = Auth::user();
        $userRole = $user->roles()->first()->name;

        if ($userRole === 'hr') {
            $query->whereIn('status', ['uploaded', 'under_review', 'hr_approved', 'rejected']);
        } elseif ($userRole === 'cfo') {
            $query->whereIn('status', ['hr_approved', 'cfo_approved', 'rejected']);
        } elseif ($userRole === 'finance') {
            $query->whereIn('status', ['cfo_approved', 'in_transfer', 'transferred', 'rejected']);
        }

        $bills = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendor_bills.csv"',
        ];

        $callback = function () use ($bills) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Vendor',
                'Bill Number',
                'Bill Date',
                'Due Date',
                'Amount',
                'Tax Amount',
                'Total Amount',
                'Status',
                'Uploaded On'
            ]);

            // Add bill data
            foreach ($bills as $bill) {
                fputcsv($file, [
                    $bill->id,
                    $bill->vendor->name,
                    $bill->bill_number,
                    $bill->bill_date,
                    $bill->due_date ?: 'N/A',
                    $bill->amount,
                    $bill->tax_amount,
                    $bill->total_amount,
                    ucfirst(str_replace('_', ' ', $bill->status)),
                    $bill->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

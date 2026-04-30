<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorBankDetail;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = Vendor::latest()->paginate(10);
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created vendor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required|string|max:20',
            'pan_number' => 'required|string|unique:vendors,pan_number',
            'gst_number' => 'nullable|string|unique:vendors,gst_number',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'nature_of_work' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'upi_id' => 'nullable|string|max:50',
            'pan_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'cheque_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create vendor
            $vendor = Vendor::create([
                'name' => $request->name,
                'type' => $request->type,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'pan_number' => $request->pan_number,
                'gst_number' => $request->gst_number,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'nature_of_work' => $request->nature_of_work,
                'status' => 'pending_verification',
            ]);

            // Create bank details
            VendorBankDetail::create([
                'vendor_id' => $vendor->id,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
                'is_primary' => true,
            ]);

            // Store PAN document
            if ($request->hasFile('pan_document')) {
                $panFile = $request->file('pan_document');
                $panPath = $panFile->store('vendor_documents', 'public');
                
                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'pan',
                    'file_path' => $panPath,
                    'file_name' => $panFile->getClientOriginalName(),
                ]);
            }

            // Store GST document
            if ($request->hasFile('gst_document')) {
                $gstFile = $request->file('gst_document');
                $gstPath = $gstFile->store('vendor_documents', 'public');
                
                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'gst',
                    'file_path' => $gstPath,
                    'file_name' => $gstFile->getClientOriginalName(),
                ]);
            }

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

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating vendor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified vendor.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        $vendor->load(['bankDetails', 'documents', 'bills']);
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        $vendor->load('bankDetails');
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
            'phone' => 'required|string|max:20',
            'pan_number' => 'required|string|unique:vendors,pan_number,' . $vendor->id,
            'gst_number' => 'nullable|string|unique:vendors,gst_number,' . $vendor->id,
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'nature_of_work' => 'required|string|max:255',
            'status' => 'required|in:pending_verification,verified,onboarded,blocked',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update vendor
            $vendor->update([
                'name' => $request->name,
                'type' => $request->type,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'pan_number' => $request->pan_number,
                'gst_number' => $request->gst_number,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'nature_of_work' => $request->nature_of_work,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating vendor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified vendor from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting vendor: ' . $e->getMessage());
        }
    }

    /**
     * Update vendor status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending_verification,verified,onboarded,blocked',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $vendor->update([
                'status' => $request->status,
            ]);

            return redirect()->back()
                ->with('success', 'Vendor status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating vendor status: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\LowerTdsCertificate;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LowerTdsCertificateController extends Controller
{
    /**
     * Show the form for creating a new certificate.
     */
    public function create($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        return view('vendor.tds-certificate-form', compact('vendor'));
    }

    /**
     * Store a newly created certificate in storage.
     */
    public function store(Request $request, $vendorId)
    {
        $request->validate([
            'certificate_number' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'max_value' => 'nullable|numeric|min:0',
            'certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        // Store the certificate file
        $filePath = $request->file('certificate_file')->store('tds-certificates', 'public');
        $fileName = $request->file('certificate_file')->getClientOriginalName();
        $fileSize = $request->file('certificate_file')->getSize();
        $mimeType = $request->file('certificate_file')->getMimeType();

        // Create the certificate record
        $certificate = new LowerTdsCertificate([
            'certificate_number' => $request->certificate_number,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'rate_percentage' => $request->rate_percentage,
            'max_value' => $request->max_value,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_mime_type' => $mimeType,
        ]);

        $vendor->lowerTdsCertificates()->save($certificate);

        return redirect()->route('vendor.show', $vendorId)->with('success', 'Lower TDS Certificate uploaded successfully.');
    }

    /**
     * Display the specified certificate.
     */
    public function show($vendorId, $certificateId)
    {
        $certificate = LowerTdsCertificate::findOrFail($certificateId);
        return view('vendor.tds-certificate-view', compact('certificate'));
    }

    /**
     * Remove the specified certificate from storage.
     */
    public function destroy($vendorId, $certificateId)
    {
        $certificate = LowerTdsCertificate::findOrFail($certificateId);
        Storage::disk('public')->delete($certificate->file_path);
        $certificate->delete();

        return redirect()->route('vendor.show', $vendorId)->with('success', 'Lower TDS Certificate deleted successfully.');
    }
}

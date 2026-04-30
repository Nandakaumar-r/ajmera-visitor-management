<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Services\VendorDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorDocumentController extends Controller
{
    protected $documentService;

    public function __construct(VendorDocumentService $documentService)
    {
        $this->documentService = $documentService;
        // $this->middleware(['auth', 'role:admin|hr|finance']);
    }

    /**
     * Display vendor documents
     *
     * @param Vendor $vendor
     * @return \Illuminate\View\View
     */
    public function index(Vendor $vendor)
    {
        $documents = $vendor->documents;
        $requiredDocuments = $this->documentService->getRequiredDocuments($vendor->type);
        $missingDocuments = $this->documentService->getMissingRequiredDocuments($vendor);

        return view('admin.vendors.documents.index', compact('vendor', 'documents', 'requiredDocuments', 'missingDocuments'));
    }

    /**
     * Show document upload form
     *
     * @param Vendor $vendor
     * @return \Illuminate\View\View
     */
    public function create(Vendor $vendor)
    {
        $requiredDocuments = $this->documentService->getRequiredDocuments($vendor->type);
        $missingDocuments = $this->documentService->getMissingRequiredDocuments($vendor);

        return view('admin.vendors.documents.create', compact('vendor', 'requiredDocuments', 'missingDocuments'));
    }

    /**
     * Store a newly uploaded document
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Vendor $vendor)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $document = $this->documentService->uploadDocument(
            $vendor,
            $request->file('document_file'),
            $request->document_type
        );

        return redirect()->route('admin.vendors.documents.index', $vendor)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show document verification form
     *
     * @param Vendor $vendor
     * @param VendorDocument $document
     * @return \Illuminate\View\View
     */
    public function verify(Vendor $vendor, VendorDocument $document)
    {
        return view('admin.vendors.documents.verify', compact('vendor', 'document'));
    }

    /**
     * Process document verification
     *
     * @param Request $request
     * @param Vendor $vendor
     * @param VendorDocument $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processVerification(Request $request, Vendor $vendor, VendorDocument $document)
    {
        $request->validate([
            'verification_status' => 'required|boolean',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $this->documentService->verifyDocument(
            $document,
            $request->verification_status,
            $request->verification_notes
        );

        return redirect()->route('admin.vendors.documents.index', $vendor)
            ->with('success', 'Document verification processed successfully.');
    }

    /**
     * Download document
     *
     * @param Vendor $vendor
     * @param VendorDocument $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Vendor $vendor, VendorDocument $document)
    {
        return response()->download(storage_path('app/public/' . $document->file_path), $document->file_name);
    }

    /**
     * Delete document
     *
     * @param Vendor $vendor
     * @param VendorDocument $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Vendor $vendor, VendorDocument $document)
    {
        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);
        
        // Delete document record
        $document->delete();
        
        // Update vendor onboarding status
        $this->documentService->updateVendorOnboardingStatus($vendor);

        return redirect()->route('admin.vendors.documents.index', $vendor)
            ->with('success', 'Document deleted successfully.');
    }
}

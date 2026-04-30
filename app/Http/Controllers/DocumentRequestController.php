<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $documentRequests = DocumentRequest::with(['user', 'approver'])
            ->when(auth()->user()->hasRole('employee'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('employees.document-requests.index', compact('documentRequests'));
    }

    public function create()
    {
        $documentTypes = [
            'offer_letter' => 'Offer Letter',
            'experience_certificate' => 'Experience Certificate',
            'id_card' => 'ID Card',
            'salary_certificate' => 'Salary Certificate',
            'other' => 'Other'
        ];

        return view('employees.document-requests.create', compact('documentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'reason' => 'required|string|max:500',
        ]);

        $documentRequest = DocumentRequest::create([
            'user_id' => auth()->id(),
            'document_type' => $validated['document_type'],
            'reason' => $validated['reason'],
            'status' => 'pending'
        ]);

        return redirect()
            ->route('employees.document-requests.index')
            ->with('success', 'Document request submitted successfully.');
    }

    public function show(DocumentRequest $documentRequest)
    {
        $this->authorize('view', $documentRequest);

        return view('employees.document-requests.show', compact('documentRequest'));
    }

    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $this->authorize('update', $documentRequest);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
            'document' => 'required_if:status,approved|nullable|file|max:10240'
        ]);

        if ($request->status === 'approved' && $request->hasFile('document')) {
            $path = $request->file('document')->store('documents');
            $documentRequest->document_path = $path;
        }

        $documentRequest->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('employees.document-requests.index')
            ->with('success', 'Document request ' . $validated['status'] . ' successfully.');
    }

    public function destroy(DocumentRequest $documentRequest)
    {
        $this->authorize('delete', $documentRequest);

        if ($documentRequest->document_path) {
            Storage::delete($documentRequest->document_path);
        }

        $documentRequest->delete();

        return redirect()
            ->route('employees.document-requests.index')
            ->with('success', 'Document request deleted successfully.');
    }

    public function download(DocumentRequest $documentRequest)
    {
        $this->authorize('view', $documentRequest);

        if (!$documentRequest->document_path) {
            return back()->with('error', 'No document available for download.');
        }

        return Storage::download(
            $documentRequest->document_path,
            $documentRequest->document_type . '.' . pathinfo($documentRequest->document_path, PATHINFO_EXTENSION)
        );
    }
}

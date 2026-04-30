<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorDocumentService
{
    /**
     * Get required documents based on vendor type
     *
     * @param string|null $vendorType
     * @return array
     */
    public function getRequiredDocuments(?string $vendorType = null): array
    {
        // Provide a safe default if vendor type is null
        $vendorType = $vendorType ?? 'general';

        $requiredDocuments = [
            'pan' => true, // PAN Card is required for all vendor types
        ];

        if ($vendorType === 'company') {
            $requiredDocuments['gst_certificate'] = true; // GST Certificate is required for companies
            $requiredDocuments['incorporation_certificate'] = true;
        }

        $requiredDocuments['cancelled_cheque'] = true; // Bank details verification
        
        return $requiredDocuments;
    }

    /**
     * Check if vendor has all required documents
     *
     * @param Vendor $vendor
     * @return bool
     */
    public function hasAllRequiredDocuments(Vendor $vendor): bool
    {
        $requiredDocuments = $this->getRequiredDocuments($vendor->type);
        $uploadedDocumentTypes = $vendor->documents->pluck('document_type')->toArray();
        
        foreach ($requiredDocuments as $documentType => $required) {
            if ($required && !in_array($documentType, $uploadedDocumentTypes)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get missing required documents for a vendor
     *
     * @param Vendor $vendor
     * @return array
     */
    public function getMissingRequiredDocuments(Vendor $vendor): array
    {
        $requiredDocuments = $this->getRequiredDocuments($vendor->type);
        $uploadedDocumentTypes = $vendor->documents->pluck('document_type')->toArray();
        $missingDocuments = [];
        
        foreach ($requiredDocuments as $documentType => $required) {
            if ($required && !in_array($documentType, $uploadedDocumentTypes)) {
                $missingDocuments[] = $documentType;
            }
        }
        
        return $missingDocuments;
    }

    /**
     * Upload vendor document
     *
     * @param Vendor $vendor
     * @param UploadedFile $file
     * @param string $documentType
     * @return VendorDocument
     */
    public function uploadDocument(Vendor $vendor, UploadedFile $file, string $documentType): VendorDocument
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('vendor_documents/' . $vendor->id, $fileName, 'public');
        
        $required = array_key_exists($documentType, $this->getRequiredDocuments($vendor->type)) &&
                    $this->getRequiredDocuments($vendor->type)[$documentType];
        
        $document = VendorDocument::create([
            'vendor_id' => $vendor->id,
            'document_type' => $documentType,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'required' => $required,
            'verified' => false,
        ]);
        
        // Update vendor onboarding status if all required documents are uploaded
        $this->updateVendorOnboardingStatus($vendor);
        
        return $document;
    }

    /**
     * Verify vendor document
     *
     * @param VendorDocument $document
     * @param bool $verified
     * @param string|null $notes
     * @return VendorDocument
     */
    public function verifyDocument(VendorDocument $document, bool $verified, ?string $notes = null): VendorDocument
    {
        $document->update([
            'verified' => $verified,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
        
        // Update specific verification flags on vendor
        $vendor = $document->vendor;
        
        if ($document->document_type === 'gst_certificate') {
            $vendor->gst_verified = $verified;
            $vendor->save();
        }
        
        if ($document->document_type === 'pan') {
            $vendor->pan_verified = $verified;
            $vendor->save();
        }
        
        // Update vendor onboarding status
        $this->updateVendorOnboardingStatus($vendor);
        
        return $document;
    }

    /**
     * Update vendor onboarding status based on document verification
     *
     * @param Vendor $vendor
     * @return void
     */
    public function updateVendorOnboardingStatus(Vendor $vendor): void
    {
        $vendor->refresh(); // Refresh to get latest document status
        
        // Check if all required documents are uploaded
        $hasAllRequiredDocuments = $this->hasAllRequiredDocuments($vendor);
        
        // Check if all uploaded required documents are verified
        $allVerified = true;
        $requiredDocumentTypes = array_keys(array_filter($this->getRequiredDocuments($vendor->type)));
        
        foreach ($vendor->documents as $document) {
            if ($document->required && in_array($document->document_type, $requiredDocumentTypes) && !$document->verified) {
                $allVerified = false;
                break;
            }
        }
        
        // Update onboarding status
        if (!$hasAllRequiredDocuments) {
            $vendor->onboarding_status = 'pending_documents';
        } elseif ($hasAllRequiredDocuments && !$allVerified) {
            $vendor->onboarding_status = 'documents_uploaded';
        } elseif ($hasAllRequiredDocuments && $allVerified) {
            $vendor->onboarding_status = 'documents_verified';
            
            // If company type, ensure GST is verified
            if ($vendor->type === 'company' && !$vendor->gst_verified) {
                $vendor->onboarding_status = 'documents_uploaded';
            }
        }
        
        $vendor->save();
    }
}

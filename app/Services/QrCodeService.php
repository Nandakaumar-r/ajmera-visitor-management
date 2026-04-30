<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QrCodeService
{
    public function generateQrCodeForCabin($cabinId): string
    {
        $uniqueCode = Str::random(20);
        $url = url("/cabins/{$cabinId}/book?code=" . $uniqueCode);
        
        return $uniqueCode;
    }

    public function generateQrCodeImage($url): string
    {
        return QrCode::size(300)
            ->format('svg')
            ->generate($url);
    }

    public function validateQrCode($cabinId, $code): bool
    {
        // Validate the QR code matches the cabin
        return \App\Models\Cabin::where('id', $cabinId)
            ->where('qr_code', $code)
            ->exists();
    }
}

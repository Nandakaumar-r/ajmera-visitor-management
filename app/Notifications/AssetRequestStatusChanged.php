<?php

namespace App\Notifications;

use App\Models\AssetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssetRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assetRequest;

    public function __construct(AssetRequest $assetRequest)
    {
        $this->assetRequest = $assetRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $status = ucfirst($this->assetRequest->status) ?? 'Pending';
        $asset = $this->assetRequest->asset->name;

        // Ensure handover date is always a Carbon instance
        $handoverDate = $this->assetRequest->handover_date ? 
            \Carbon\Carbon::parse($this->assetRequest->handover_date) : 
            now()->addDay();

        return (new MailMessage)
            ->subject("Asset Request {$status}")
            ->line("Your asset request for {$asset} has been {$this->assetRequest->status}.")
            ->line("Quantity: {$this->assetRequest->quantity}")
            ->lineIf($this->assetRequest->status === 'approved', "Handover Date: " . $handoverDate->format('Y-m-d H:i:s'))
            ->lineIf($this->assetRequest->remarks, "Remarks: {$this->assetRequest->remarks}")
            ->action('View Request', route('assets.show', $this->assetRequest));
    }

    public function toArray($notifiable)
    {
        $handoverDate = $this->assetRequest->handover_date ? 
            \Carbon\Carbon::parse($this->assetRequest->handover_date) : 
            now()->addDay();

        return [
            'asset_request_id' => $this->assetRequest->id,
            'status' => $this->assetRequest->status,
            'asset_name' => $this->assetRequest->asset->name,
            'quantity' => $this->assetRequest->quantity,
            'handover_date' => $handoverDate,
            'remarks' => $this->assetRequest->remarks
        ];
    }
}

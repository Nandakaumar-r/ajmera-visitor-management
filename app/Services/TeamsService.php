<?php

namespace App\Services;

use App\Models\CabinBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TeamsService
{
    protected $baseUrl = 'https://graph.microsoft.com/v1.0';
    protected $tokenUrl = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token';
    protected $scope = 'https://graph.microsoft.com/.default';

    protected function getAccessToken()
    {
        return Cache::remember('microsoft_graph_token', 3500, function () {
            $tenantId = config('services.microsoft.tenant_id');
            $tokenUrl = sprintf($this->tokenUrl, $tenantId);

            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'scope' => $this->scope,
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'];
            }

            Log::error('Failed to get Microsoft access token: ' . $response->body());
            throw new \Exception('Failed to get Microsoft access token');
        });
    }

    public function createMeeting(CabinBooking $booking)
    {
        $startTime = Carbon::parse($booking->start_time)->format('Y-m-d\TH:i:s\Z');
        $endTime = Carbon::parse($booking->end_time)->format('Y-m-d\TH:i:s\Z');

        try {
            // Get fresh access token
            $accessToken = $this->getAccessToken();

            // Load attendees with their user relationships
            $attendees = $booking->attendees()->with('user')->get();

            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/users/' . config('services.microsoft.user_id', 'me') . '/onlineMeetings', [
                    'startDateTime' => $startTime,
                    'endDateTime' => $endTime,
                    'subject' => "Cabin Meeting: " . $booking->purpose,
                    'participants' => [
                        'attendees' => $attendees->map(function($attendee) {
                            return [
                                'identity' => [
                                    'email' => $attendee->user->email,
                                    'displayName' => $attendee->user->name
                                ],
                                'role' => 'attendee'
                            ];
                        })->toArray()
                    ]
                ]);

            if ($response->successful()) {
                $meetingData = $response->json();
                Log::info('Teams meeting created successfully', ['meeting_data' => $meetingData]);
                return [
                    'success' => true,
                    'meeting_id' => $meetingData['id'],
                    'join_url' => $meetingData['joinUrl'],
                    'meeting_data' => $meetingData
                ];
            } else {
                Log::error('Teams API Error: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Failed to create Teams meeting: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Teams meeting creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SnipeITService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('SNIPEIT_URL');  // Add your Snipe-IT base URL in .env
        $this->apiKey = env('SNIPEIT_API_KEY');  // Add your Snipe-IT API key in .env
    }

    public function getUserById($userId)
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}api/v1/users/{$userId}");

        if ($response->successful()) {
            return $response->json();
        }

        return null; // Return null or handle error if the user is not found
    }

    // Get User by Email
    public function getUserByEmail($email)
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}api/v1/users", [
                'email' => $email,
            ]);

        if ($response->successful()) {
            return $response->json();
        }
    }

    // Fetch current user with caching
    public function getCurrentUser()
    {
        $userEmail = Auth::user()->email;
        $cacheKey = "user_for_{$userEmail}";

        // return Cache::remember($cacheKey, 60 * 60, function () use ($userEmail) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get("{$this->baseUrl}api/v1/users", [
            // 'email' => $userEmail,
        ]);
        // });

        return $response->json();
    }

    // Fetch all assets
    // Fetch hardware with caching
    public function getUserHardware()
    {
        $userEmail = Auth::user()->email;
        $cacheKey = "hardware_for_{$userEmail}";

        return Cache::remember($cacheKey, 60 * 60, function () use ($userEmail) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}api/v1/hardware", [
                'assigned_to' => $userEmail,
            ]);

            return $response->json();
        });
    }

    // Fetch accessories with caching
    public function getUserAccessories()
    {
        $userEmail = Auth::user()->email;
        $cacheKey = "accessories_for_{$userEmail}";

        return Cache::remember($cacheKey, 60 * 60, function () use ($userEmail) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}api/v1/accessories", [
                'assigned_to' => $userEmail,
            ]);

            return $response->json();
        });
    }

    // Fetch licenses with caching
    public function getUserLicenses()
    {
        $userEmail = Auth::user()->email;
        $cacheKey = "licenses_for_{$userEmail}";

        return Cache::remember($cacheKey, 60 * 60, function () use ($userEmail) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}api/v1/licenses", [
                'assigned_to' => $userEmail,
            ]);

            return $response->json();
        });
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    protected $baseUrl = 'https://api.linkedin.com/v2';

    protected function getAccessToken()
    {
        return config('services.linkedin.access_token');
    }

    public function getCompanyPosts()
    {
        try {
            $accessToken = $this->getAccessToken();
            $companyId = config('services.linkedin.company_id');
            
            // First get the organization URN
            $response = Http::withToken($accessToken)
                ->get($this->baseUrl . "/organizationalEntityAcls", [
                    'q' => 'roleAssignee',
                    'role' => 'ADMINISTRATOR'
                ]);

            if (!$response->successful()) {
                Log::error('Failed to get LinkedIn organization URN: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Failed to get organization URN'
                ];
            }

            $elements = $response->json()['elements'] ?? [];
            $orgUrn = null;
            
            foreach ($elements as $element) {
                if (strpos($element['organizationalTarget'], $companyId) !== false) {
                    $orgUrn = $element['organizationalTarget'];
                    break;
                }
            }

            if (!$orgUrn) {
                return [
                    'success' => false,
                    'error' => 'Organization not found'
                ];
            }

            // Now get the posts using the organization URN
            $response = Http::withToken($accessToken)
                ->get($this->baseUrl . "/ugcPosts", [
                    'q' => 'author',
                    'author' => $orgUrn,
                    'count' => 10
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'posts' => $response->json()['elements'] ?? []
                ];
            }

            Log::error('Failed to get LinkedIn company posts: ' . $response->body());
            return [
                'success' => false,
                'error' => 'Failed to fetch company posts'
            ];
        } catch (\Exception $e) {
            Log::error('LinkedIn API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getPersonPosts()
    {
        try {
            $accessToken = $this->getAccessToken();
            
            // First get the member URN
            $response = Http::withToken($accessToken)
                ->get($this->baseUrl . "/me");

            if (!$response->successful()) {
                Log::error('Failed to get LinkedIn member profile: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Failed to get member profile'
                ];
            }

            $personId = $response->json()['id'];
            $personUrn = "urn:li:person:{$personId}";

            // Now get the posts using the person URN
            $response = Http::withToken($accessToken)
                ->get($this->baseUrl . "/ugcPosts", [
                    'q' => 'author',
                    'author' => $personUrn,
                    'count' => 10
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'posts' => $response->json()['elements'] ?? []
                ];
            }

            Log::error('Failed to get LinkedIn person posts: ' . $response->body());
            return [
                'success' => false,
                'error' => 'Failed to fetch person posts'
            ];
        } catch (\Exception $e) {
            Log::error('LinkedIn API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getAllPosts()
    {
        $companyPosts = $this->getCompanyPosts();
        $personPosts = $this->getPersonPosts();

        $allPosts = [];
        
        if ($companyPosts['success']) {
            $allPosts = array_merge($allPosts, $companyPosts['posts']);
        }
        
        if ($personPosts['success']) {
            $allPosts = array_merge($allPosts, $personPosts['posts']);
        }

        // Sort posts by date
        usort($allPosts, function($a, $b) {
            return strtotime($b['created']['time'] ?? 0) - strtotime($a['created']['time'] ?? 0);
        });

        return [
            'success' => true,
            'posts' => array_slice($allPosts, 0, 20) // Return most recent 20 posts
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SnipeITService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;


class SnipeITController extends Controller
{
    protected $snipeITService;

    public function __construct(SnipeITService $snipeITService)
    {
        $this->snipeITService = $snipeITService;
    }

    public function showCurrentUser()
    {
        $users = Cache::remember('current_users', 60, function () {
            return $this->snipeITService->getCurrentUser();
        });
        dd($users);
        return view('snipeit.users', compact('users'));
    }


    public function showUserHardware()
    {
        $hardware = $this->snipeITService->getUserHardware();
        // Get rows from API response
        $hardwareList = $hardware['rows'] ?? [];
        $collection = collect($hardwareList);

        // 🔍 Apply search filter
        $search = request()->get('search');
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['asset_tag']), strtolower($search)) ||
                    str_contains(strtolower($item['name']), strtolower($search));
            });
        }

        // 📄 Paginate
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $pagedData = $collection->forPage($currentPage, $perPage);

        $paginatedHardware = new LengthAwarePaginator(
            $pagedData,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('snipeit.assets', [
            'hardwareList' => $paginatedHardware,
            'search' => $search
        ]);
    }


    public function showUserAccessories()
    {
        $accessories = $this->snipeITService->getUserAccessories();
        $accessoriesList = $accessories['rows'] ?? [];
        return view('snipeit.accessories', compact('accessoriesList'));
    }

    // Show user licenses
    public function showUserLicenses()
    {
        $response  = $this->snipeITService->getUserLicenses();
        $licenses = collect($response['rows'] ?? []);
        // Current page
        $page = request()->get('page', 1);

        $perPage = 10;
        $paginated = new LengthAwarePaginator(
            $licenses->forPage($page, $perPage),
            $licenses->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('snipeit.licenses', ['licenses' => $paginated]);
    }

    public function showUser($user)
    {
        $userData = $this->snipeITService->getUserById($user);
        $hardware = collect($this->snipeITService->getUserHardware($user));
        $accessories = collect($this->snipeITService->getUserAccessories($user));
        $licenses = collect($this->snipeITService->getUserLicenses($user));

        return view('snipeit.user-details', compact('userData', 'hardware', 'accessories', 'licenses'));
    }
}

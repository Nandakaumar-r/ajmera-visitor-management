@extends('layouts.app')

@section('content')
<div class="container-fluid py-2" style="margin-top: 50px;">
    <div class="row g-2">
        <!-- Left: Table (40%) -->
        <div class="col-lg-5 order-lg-1 order-1">
            <div class="card shadow-lg border-0 rounded-3 h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Work From Home Records</h4>
                </div>
                <div class="card-body">

                    {{-- Search & Filters Form --}}
                    <div class="card p-3 mb-3 shadow-sm border rounded">
                        <form method="GET" action="{{ route('wfh.index') }}">
                            <!-- Search Row -->
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="form-control" placeholder="Search by Name">
                                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Filter Row -->
                            <div class="row g-2">
                                <div class="col-12 col-md-5">
                                    <select name="filter" class="form-select">
                                        <option value="">Filter by Date</option>
                                        <option value="today" {{ request('filter')=='today' ? 'selected' : '' }}>Today</option>
                                        <option value="yesterday" {{ request('filter')=='yesterday' ? 'selected' : '' }}>Yesterday</option>
                                        <option value="week" {{ request('filter')=='week' ? 'selected' : '' }}>This Week</option>
                                        <option value="month" {{ request('filter')=='month' ? 'selected' : '' }}>This Month</option>
                                        <option value="last_month" {{ request('filter')=='last_month' ? 'selected' : '' }}>Last Month</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('wfh.export', request()->query()) }}" class="btn btn-success w-100">
                                        <i class="bi bi-download"></i> Download MIS
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Records Table --}}
                    <div class="table-responsive" style="max-height: 65vh; overflow-y:auto;">
                        <table class="table table-hover table-sm align-middle text-nowrap table-bordered mb-0">
                            <thead class="table-dark sticky-top text-center">
                                <tr>
                                    <th style="width: 35%">Name & Details</th>
                                    <th style="width: 35%">Attendance</th>
                                    <th style="width: 30%">Image</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                <tr>
                                    <!-- Name & Details -->
                                    <td>
                                        <strong class="d-block text-truncate" style="max-width: 220px;">
                                            {{ $record->user->name ?? 'N/A' }}
                                        </strong>
                                        <small class="d-block text-muted text-truncate" style="max-width: 220px;">
                                            {{ $record->user->email ?? '' }}
                                        </small>
                                        <span class="badge bg-info text-dark px-2 py-1 mt-1">
                                            {{ ucfirst($record->work_location) }}
                                        </span>
                                        <small class="d-block text-muted mt-1 text-truncate" style="max-width: 220px; font-size: 0.85rem;">
                                            📍 {{ $record->current_location }}
                                        </small>
                                        <small class="d-block text-secondary text-truncate" style="max-width: 220px; font-size: 0.85rem;">
                                            🏢 {{ $record->remarks ?? '-' }}
                                        </small>
                                        <button class="btn btn-sm btn-outline-primary mt-2 view-location-btn"
                                            data-lat="{{ $record->latitude }}"
                                            data-lng="{{ $record->longitude }}"
                                            data-name="{{ $record->user->name ?? 'N/A' }}"
                                            data-location="{{ $record->current_location }}">
                                            View Location
                                        </button>
                                    </td>

                                    <!-- Attendance (Sign In & Sign Out in same column) -->
                                    <td class="text-center">
                                        <div class="border-bottom pb-1 mb-1">
                                            <span class="text-success fw-bold d-block">Sign In</span>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($record->sign_in_time)->format('d M, Y h:i A') }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="text-danger fw-bold d-block">Sign Out</span>
                                            @if($record->sign_out_time)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($record->sign_out_time)->format('d M, Y h:i A') }}
                                            </small>
                                            @else
                                            <span class="badge bg-warning text-dark px-2 py-1">Not Signed Out</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Image (placeholder for now) -->
                                    <td>
                                        @if($record->captured_photo_path)
                                        <a href="{{ asset('storage/' . $record->captured_photo_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->captured_photo_path) }}"
                                                alt="Photo"
                                                width="60"
                                                height="60"
                                                class="rounded shadow-sm border">
                                        </a>
                                        @else
                                        <span class="text-muted">No Photo</span>
                                        @endif
                                    </td> 
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <em>No records found</em>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $records->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Map (60%) -->
        <div class="col-lg-7 order-lg-2 order-2" style="z-index: 0;">
            <div class="card shadow-lg border-0 rounded-3 h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Employee Locations</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 100%; min-height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Scripts --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const locations = @json($locations);

    var map = L.map('map').setView([12.9716, 77.5946], 11); // Default center

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Store markers
    var markers = [];

    locations.forEach(function(loc) {
        if (loc.latitude && loc.longitude) {
            var marker = L.marker([loc.latitude, loc.longitude])
                .addTo(map)
                .bindPopup(`<b>${loc.name}</b><br>${loc.location}`);
            markers.push(marker);
        }
    });

    // Button click handler
    document.querySelectorAll('.view-location-btn').forEach(button => {
        button.addEventListener('click', function() {
            let lat = this.dataset.lat;
            let lng = this.dataset.lng;
            let name = this.dataset.name;
            let location = this.dataset.location;

            if (lat && lng) {
                map.setView([lat, lng], 16); // Zoom into clicked location
                L.popup()
                    .setLatLng([lat, lng])
                    .setContent(`<b>${name}</b><br>${location}`)
                    .openOn(map);
            }
        });
    });
</script>


<style>
    /* Ensure table is fully responsive on small devices */
    @media (max-width: 767.98px) {
        .table-responsive {
            max-height: 50vh;
        }

        .table th,
        .table td {
            font-size: 0.85rem;
            padding: 0.35rem 0.5rem;
        }

        #map {
            min-height: 400px;
        }
    }

    footer {
        display: none !important;
    }
</style>
@endsection
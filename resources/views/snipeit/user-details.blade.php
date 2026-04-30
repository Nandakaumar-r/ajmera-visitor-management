@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold mb-5">Nexole AMS User Details</h1>

        <!-- User Information -->
        <section class="mb-10">
            <h2 class="text-xl font-semibold">User Information</h2>
            <p><strong>Name:</strong> {{ $userData->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $userData->email ?? 'N/A' }}</p>
        </section>

        <!-- User Assets -->
        <section class="mb-10">
            <h2 class="text-xl font-semibold">Assigned Assets</h2>

            @if($hardware->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($hardware['rows'] as $asset)
                        <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <img src="{{ $asset['image'] }}" alt="{{ $asset['name'] }}" class="w-16 h-16 rounded-md mr-4">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $asset['model']['name'] }}</h3>
                                    <p class="text-gray-600">Asset Tag: {{ $asset['asset_tag'] }}</p>
                                    <p class="text-gray-600">Serial: {{ $asset['serial'] }}</p>
                                    <p class="text-gray-500">Category: {{ $asset['category']['name'] }}</p>
                                    <p class="text-gray-500">Status: {{ $asset['status_label']['name'] }}</p>
                                </div>
                            </div>
                            <div class="flex justify-between mt-4">
                                <a href="{{ $asset['qr'] }}" class="text-blue-500 hover:underline">View QR</a>
                                <a href="{{ route('asset.details', $asset['id']) }}" class="bg-blue-500 text-white font-semibold py-1 px-3 rounded">Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No assets assigned.</p>
            @endif
        </section>


        <!-- User Accessories -->
        <section class="mb-10">
            <h2 class="text-xl font-semibold">Assigned Accessories</h2>
            @if($accessories->isNotEmpty())
                <ul>
                    @foreach($accessories as $accessory)
                        <li>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No accessories assigned.</p>
            @endif
        </section>

        <!-- User Licenses -->
        <section class="mb-10">
            <h2 class="text-xl font-semibold">Assigned Licenses</h2>
            @if($licenses->isNotEmpty())
                <ul>
                    @foreach($licenses as $license)
                        <li>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No licenses assigned.</p>
            @endif
        </section>
    </div>
@endsection

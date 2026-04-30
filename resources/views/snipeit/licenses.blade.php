@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📄 Snipe-IT Licenses</h1>
        <a href="{{ url()->previous() }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-medium px-4 py-2 rounded-lg shadow-md transition">
            ⬅ Back
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg shadow-md">
        <table class="min-w-full border border-gray-200 bg-white rounded-lg">
            <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700">
                <tr>
                    <th class="px-4 py-3 font-semibold text-left">ID</th>
                    <th class="px-4 py-3  font-semibold text-left">Name</th>
                    <th class="px-4 py-3  font-semibold text-left">License Holder</th>
                    <th class="px-4 py-3  font-semibold text-left">Email</th>
                    <th class="px-4 py-3  font-semibold text-left">Company</th>
                    <th class="px-4 py-3  font-semibold text-left">Category</th>
                    <th class="px-4 py-3  font-semibold text-left">Manufacturer</th>
                    <th class="px-4 py-3  font-semibold text-center">Seats</th>
                    <th class="px-4 py-3  font-semibold text-center">Free Seats</th>
                    <th class="px-4 py-3  font-semibold text-center">Reassignable</th>
                    <th class="px-4 py-3  font-semibold text-center">Maintained</th>
                    <th class="px-4 py-3  font-semibold text-center">Purchase Date</th>
                    <th class="px-4 py-3  font-semibold text-center">Expiration</th>
                    <th class="px-4 py-3  font-semibold text-left">Notes</th>
                    <th class="px-4 py-3  font-semibold text-center">Created At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                @forelse ($licenses as $license)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">{{ $license['id'] }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $license['name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $license['license_name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $license['license_email'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $license['company']['name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $license['category']['name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $license['manufacturer']['name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-center">{{ $license['seats'] ?? 0 }}</td>
                    <td class="px-4 py-3 text-center">{{ $license['free_seats_count'] ?? 0 }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $license['reassignable'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $license['reassignable'] ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $license['maintained'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $license['maintained'] ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">{{ $license['purchase_date'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($license['expiration_date'])
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ \Carbon\Carbon::parse($license['expiration_date'])->isPast() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $license['expiration_date'] }}
                        </span>
                        @else
                        N/A
                        @endif
                    </td>
                    <td class="px-4 py-3 text-left text-gray-600">{{ $license['notes'] ?: '-' }}</td>
                    <td class="px-4 py-3 text-center">{{ $license['created_at']['formatted'] ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="px-4 py-6 text-center text-gray-500">No Licenses Found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $licenses->links() }}
    </div>
</div>
@endsection

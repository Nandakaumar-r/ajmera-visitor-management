@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">User Hardware</h1>

    <!-- 🔍 Search Form -->
    <form method="GET" action="{{ route('snipeit.assets.show') }}" class="mb-4">
        <div class="flex space-x-2 items-right justify-end">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}" 
                placeholder="Search by Asset Tag or Name"
                class="border rounded px-3 py-2 w-1/4"
            >
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Search
            </button>
        </div>
    </form>

    @if($hardwareList->count() > 0)
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Image</th>
                        <th class="px-4 py-2 border">Asset Tag</th>
                        <th class="px-4 py-2 border">Serial</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Category</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Notes</th>
                        <th class="px-4 py-2 border">QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hardwareList as $hardware)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border text-center">
                                @if($hardware['image'])
                                    <img src="{{ $hardware['image'] }}" 
                                         alt="{{ $hardware['name'] }}" 
                                         class="h-12 w-12 object-cover mx-auto rounded">
                                @else
                                    <span class="text-gray-400 italic">No Image</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border">{{ $hardware['asset_tag'] }}</td>
                            <td class="px-4 py-2 border">{{ $hardware['serial'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border">{{ $hardware['name'] }}</td>
                            <td class="px-4 py-2 border">{{ $hardware['category']['name'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border">
                                @if(isset($hardware['status_label']['name']))
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                        {{ $hardware['status_label']['name'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Unknown</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border whitespace-pre-line text-sm">
                                {{ $hardware['notes'] ?? '—' }}
                            </td>
                            <td class="px-4 py-2 border text-center">
                                @if($hardware['qr'])
                                    <img src="{{ $hardware['qr'] }}" 
                                         alt="QR {{ $hardware['asset_tag'] }}" 
                                         class="h-12 w-12 mx-auto">
                                @else
                                    <span class="text-gray-400 italic">No QR</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 📄 Pagination -->
        <div class="mt-4">
            {{ $hardwareList->links() }}
        </div>
    @else
        <div class="text-center text-gray-500 py-6">
            No hardware found.
        </div>
    @endif
</div>
@endsection

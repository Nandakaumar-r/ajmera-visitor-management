@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">User Accessories</h1>

    @if(count($accessoriesList) > 0)
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Image</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Model Number</th>
                        <th class="px-4 py-2 border">Qty</th>
                        <th class="px-4 py-2 border">Remaining</th>
                        <th class="px-4 py-2 border">Purchase Cost</th>
                        <th class="px-4 py-2 border">Available</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accessoriesList as $accessory)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border text-center">
                                @if($accessory['image'])
                                    <img src="{{ $accessory['image'] }}" alt="{{ $accessory['name'] }}" class="h-12 w-12 object-cover mx-auto rounded">
                                @else
                                    <span class="text-gray-400 italic">No Image</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border">{{ $accessory['name'] }}</td>
                            <td class="px-4 py-2 border">{{ $accessory['model_number'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border">{{ $accessory['qty'] }}</td>
                            <td class="px-4 py-2 border">{{ $accessory['remaining_qty'] }}</td>
                            <td class="px-4 py-2 border">{{ $accessory['purchase_cost'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border">
                                @if($accessory['user_can_checkout'])
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Yes</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-gray-500 py-6">
            No accessories found.
        </div>
    @endif
</div>
@endsection

@extends('layouts.dashboard')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Asset Collection for {{ $resignation->employee->name }}</h2>

        <!-- Employee Info Card -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Employee ID</p>
                    <p class="font-medium">{{ $resignation->employee->employee_id }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Last Working Day</p>
                    <p class="font-medium">{{ $resignation->manager_last_working_day }}</p>
                </div>
            </div>
        </div>

        @if($licenses['total'] > 0)
        <!-- Licenses List -->
        <div class="my-8">
            <h3 class="text-xl font-semibold mb-4">Assigned Licenses</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Company</th>
                            <th class="p-4">Manufacturer</th>
                            <th class="p-4">Product Key</th>
                            <th class="p-4">Order Number</th>
                            <th class="p-4">Purchase Date</th>
                            <th class="p-4">Termination Date</th>
                            <th class="p-4">Cost</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($licenses['rows'] as $license)
                            <tr class="border-t">
                                <td class="p-4">{{ $license['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $license['company']['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $license['manufacturer']['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $license['product_key'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $license['order_number'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $license['purchase_date']['formatted'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $license['termination_date']['formatted'] ?? 'N/A' }}</td>
                                <td class="p-4">${{ $license['purchase_cost'] ?? '0.00' }}</td>
                                <td class="p-4 space-x-2">
                                    @if($license['available_actions']['checkout'])
                                        <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Checkout</button>
                                    @endif
                                    @if($license['available_actions']['checkin'])
                                        <button class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Checkin</button>
                                    @endif
                                    @if($license['available_actions']['clone'])
                                        <button class="bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">Clone</button>
                                    @endif
                                    @if($license['available_actions']['update'])
                                        <button class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Update</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <hr>

        @if($accessories['total'] > 0)
            <div class="my-8">
                <h3 class="text-xl font-semibold mb-4">Assigned Accessories</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4">Image</th>
                                <th class="p-4">Name</th>
                                <th class="p-4">Company</th>
                                <th class="p-4">Manufacturer</th>
                                <th class="p-4">Supplier</th>
                                <th class="p-4">Model Number</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Location</th>
                                <th class="p-4">Quantity</th>
                                <th class="p-4">Remaining Quantity</th>
                                <th class="p-4">Minimum Quantity</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accessories as $accessory)
                            <tr class="border-t">
                                <td class="p-4">{{ $accessory['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['company'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['manufacturer']['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['supplier']['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['model_number'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['category']['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['location']['name'] ?? 'N/A' }}</td>
                                <td class="p-4">{{ $accessory['qty'] ?? 0 }}</td>
                                <td class="p-4">{{ $accessory['remaining_qty'] ?? 0 }}</td>
                                <td class="p-4">{{ $accessory['min_qty'] ?? 'N/A' }}</td>
                            
                                <td class="p-4 space-x-2">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <hr>

        <!-- Assets List -->
        @if($assets['total'] > 0)
        <div class="my-8">
            <h3 class="text-xl font-semibold mb-4">Assigned Assets</h3>

            <form action="{{ route('asset-collection.collect', $resignation->id) }}" method="POST">
                @csrf

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4">Select</th>
                                <th class="p-4">Asset Tag</th>
                                <th class="p-4">Name</th>
                                <th class="p-4">Serial No.</th>
                                <th class="p-4">Model No</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets['rows'] as $asset)
                            <tr class="border-t">
                                <td class="p-4">
                                    <input type="checkbox"
                                        name="collected_assets[]"
                                        value="{{ $asset['id'] }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="p-4">{{ $asset['asset_tag'] }}</td>
                                <td class="p-4">{{ $asset['name'] }}</td>
                                <td class="p-4">{{ $asset['serial'] }}</td>
                                <td class="p-4">{!! $asset['model']['name'] !!}</td>
                                <td class="p-4">{{ $asset['category']['name'] }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-full text-sm 
                                                {{ $asset['status_label']['status_meta'] === 'deployed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $asset['status_label']['name'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Notes Field -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Collection Notes</label>
                    <textarea id="notes"
                        name="notes"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Mark as Collected
                    </button>
                </div>
            </form>
            <form action="{{ route('asset-collection.noc', $resignation->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 float-end m-3">
                    Generate NOC
                </button>
            </form>
        </div>
        @endif

        <!-- Status Messages -->
        @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
        @endif
    </div>
</div>


@endsection
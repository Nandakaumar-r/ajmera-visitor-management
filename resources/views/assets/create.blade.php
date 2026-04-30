<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Request Asset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
                        @csrf

                        <!-- Asset Category -->
                        <div>
                            <x-input-label for="asset_category_id" :value="__('Asset Category')" />
                            <select id="asset_category_id" name="asset_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a category</option>
                                @foreach($assetCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asset_category_id')" class="mt-2" />
                        </div>

                        <!-- Asset -->
                        <div>
                            <x-input-label for="asset_id" :value="__('Asset')" />
                            <select id="asset_id" name="asset_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select an asset</option>
                            </select>
                            <x-input-error :messages="$errors->get('asset_id')" class="mt-2" />
                        </div>

                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity')" />
                            <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- Justification -->
                        <div>
                            <x-input-label for="justification" :value="__('Justification')" />
                            <textarea id="justification" name="justification" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                            <x-input-error :messages="$errors->get('justification')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Submit Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('asset_category_id').addEventListener('change', function() {
            const categoryId = this.value;
            const assetSelect = document.getElementById('asset_id');
            
            console.log('Selected category ID:', categoryId);
            
            // Clear current options
            assetSelect.innerHTML = '<option value="">Select an asset</option>';
            
            if (categoryId) {
                const url = `/assets/by-category/${categoryId}`;
                console.log('Fetching assets from:', url);
                
                // Fetch assets for selected category
                fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(assets => {
                    console.log('Received assets:', assets);
                    if (assets.length === 0) {
                        assetSelect.innerHTML = '<option value="">No assets available</option>';
                    } else {
                        assets.forEach(asset => {
                            const option = document.createElement('option');
                            option.value = asset.id;
                            // option.textContent = `${asset.name} (${asset.quantity} ${asset.unit} available)`;
                            option.textContent = `${asset.name}`;
                            assetSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    assetSelect.innerHTML = '<option value="">Error loading assets</option>';
                });
            }
        });
    </script>
    @endpush
</x-app-layout>

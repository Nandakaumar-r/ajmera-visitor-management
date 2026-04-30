@props(['name' => 'destination', 'id' => 'destination', 'required' => true])

<div
    x-data="locationAutocomplete"
    x-init="init()"
    class="relative"
>
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $id }}"
        x-model="query"
        @input="searchLocations"
        @keydown.enter.prevent="handleEnter"
        {{ $required ? 'required' : '' }}
        autocomplete="off"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
    />
    
    <div
        x-show="showResults"
        x-cloak
        class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-300 dark:border-gray-700 max-h-60 overflow-y-auto"
    >
        <template x-for="(result, index) in results" :key="index">
            <div
                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300"
                x-text="result.display_name"
                @click="selectLocation(result)"
            ></div>
        </template>
    </div>
</div>

@once
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('locationAutocomplete', () => ({
                query: '',
                results: [],
                showResults: false,
                timeout: null,

                init() {
                    // Close dropdown when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.showResults = false;
                        }
                    });
                },

                async searchLocations() {
                    if (this.query.length < 3) {
                        this.results = [];
                        this.showResults = false;
                        return;
                    }

                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(async () => {
                        try {
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.query)}`
                            );
                            const data = await response.json();
                            this.results = data.slice(0, 5);
                            this.showResults = this.results.length > 0;
                        } catch (error) {
                            console.error('Error fetching locations:', error);
                            this.results = [];
                            this.showResults = false;
                        }
                    }, 300);
                },

                selectLocation(location) {
                    this.query = location.display_name;
                    this.showResults = false;
                },

                handleEnter() {
                    if (this.showResults && this.results.length > 0) {
                        this.selectLocation(this.results[0]);
                    }
                }
            }));
        });
    </script>
    @endpush
@endonce

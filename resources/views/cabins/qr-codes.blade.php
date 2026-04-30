<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Cabin QR Codes') }}
            </h2>
            <div class="flex space-x-4">
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print QR Codes
                </button>
                <a href="{{ route('bookings.index') }}" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Back to Cabins
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 print:grid-cols-2">
                    @foreach($cabins as $cabin)
                    <div class="bg-white p-6 rounded-lg shadow-md print:break-inside-avoid border border-gray-200">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $cabin->name }}</h3>
                            <p class="text-sm text-gray-600">Scan to book this cabin</p>
                        </div>
                        <div class="flex justify-center mb-4">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)
                                ->format('svg')
                                ->style('round')
                                ->eye('circle')
                                ->color(63, 70, 229)
                                ->margin(1)
                                ->generate(route('cabins.book', ['cabin' => $cabin->id, 'code' => $cabin->qr_code])) !!}
                        </div>
                        <div class="text-center text-sm text-gray-600">
                            <p>Location: {{ $cabin->location ?? 'Not specified' }}</p>
                            <p class="mt-1">Capacity: {{ $cabin->capacity ?? 'Not specified' }} people</p>
                            <p class="mt-2 text-xs">Last updated: {{ $cabin->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            /* Hide header and navigation when printing */
            header, nav, .no-print {
                display: none !important;
            }
            
            /* Ensure white background */
            body, .bg-white {
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Remove shadows when printing */
            .shadow-md {
                box-shadow: none !important;
            }

            /* Adjust grid for printing */
            .grid {
                display: grid !important;
                gap: 2rem !important;
            }

            /* Ensure each QR code card fits nicely on paper */
            .print\:break-inside-avoid {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                margin-bottom: 2rem !important;
            }

            /* Reset text colors for printing */
            .text-gray-900, .text-gray-600 {
                color: black !important;
            }

            /* Ensure QR codes are clear */
            svg {
                width: 200px !important;
                height: 200px !important;
            }

            /* Add page margins */
            @page {
                margin: 1cm;
            }
        }

        /* Custom QR code styling */
        .qr-wrapper svg {
            border-radius: 10px;
            padding: 10px;
            background-color: white;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Add keyboard shortcut for printing (Ctrl/Cmd + P)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
    @endpush
</x-app-layout>

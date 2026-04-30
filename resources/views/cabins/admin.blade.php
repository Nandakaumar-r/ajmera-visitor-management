<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Cabin QR Codes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cabins as $cabin)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $cabin->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $cabin->capacity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($cabin->qr_code)
                                                <div id="qr-code-{{ $cabin->id }}"></div>
                                            @else
                                                <span class="text-gray-500">No QR code generated</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button onclick="generateQrCode({{ $cabin->id }})"
                                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ $cabin->qr_code ? 'Regenerate' : 'Generate' }} QR Code
                                            </button>
                                            @if($cabin->qr_code)
                                                <button onclick="printQrCode({{ $cabin->id }})"
                                                    class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Print
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function generateQrCode(cabinId) {
            try {
                const response = await fetch(`/cabins/${cabinId}/qr-code`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                const qrCodeContainer = document.getElementById(`qr-code-${cabinId}`);
                qrCodeContainer.innerHTML = data.qr_image;
                
                // Show success message
                alert('QR code generated successfully!');
            } catch (error) {
                console.error('Error generating QR code:', error);
                alert('Error generating QR code. Please try again.');
            }
        }

        function printQrCode(cabinId) {
            const qrCodeContainer = document.getElementById(`qr-code-${cabinId}`);
            const printWindow = window.open('', '', 'width=600,height=600');
            printWindow.document.write('<html><head><title>Print QR Code</title></head><body>');
            printWindow.document.write(qrCodeContainer.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
    @endpush
</x-app-layout>

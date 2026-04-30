@props(['id' => 'booking-modal'])

<div x-data="{ 
    show: false, 
    booking: null,
    async getBookingDetails(bookingId) {
        try {
            console.log('Fetching booking details for ID:', bookingId);
            const response = await fetch(`{{ route('bookings.details', '') }}/${bookingId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.booking = await response.json();
            console.log('Booking details:', this.booking);
            this.show = true;
        } catch (error) {
            console.error('Error fetching booking details:', error);
        }
    }
}" 
    x-show="show" 
    x-cloak
    @keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    id="{{ $id }}">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity" 
            aria-hidden="true"
            @click="show = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" x-text="booking?.cabin?.name + ' - Booking Details'"></h3>
                        
                        <div class="mt-4 space-y-3" x-show="booking">
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Booked By:</span>
                                <span x-text="booking?.user?.name"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Email:</span>
                                <span x-text="booking?.user?.email"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Start Time:</span>
                                <span x-text="booking?.start_time"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">End Time:</span>
                                <span x-text="booking?.end_time"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Purpose:</span>
                                <span x-text="booking?.purpose"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full" 
                                    :class="{
                                        'bg-green-100 text-green-800': booking?.status === 'confirmed',
                                        'bg-yellow-100 text-yellow-800': booking?.status === 'pending',
                                        'bg-red-100 text-red-800': booking?.status === 'cancelled'
                                    }"
                                    x-text="booking?.status"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Location:</span>
                                <span x-text="booking?.cabin?.location"></span>
                            </div>
                            
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-semibold">Capacity:</span>
                                <span x-text="booking?.cabin?.capacity + ' people'"></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="font-semibold">Booked On:</span>
                                <span x-text="booking?.created_at"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                    @click="show = false">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

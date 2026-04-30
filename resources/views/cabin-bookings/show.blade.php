<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cabin Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Booking Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">{{ __('Meeting Information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Cabin') }}</p>
                                <p class="font-medium">{{ $booking->cabin->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Organizer') }}</p>
                                <p class="font-medium">{{ $booking->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Start Time') }}</p>
                                <p class="font-medium">{{ $booking->start_time->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('End Time') }}</p>
                                <p class="font-medium">{{ $booking->end_time->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Purpose') }}</p>
                                <p class="font-medium">{{ $booking->purpose }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Teams Meeting -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">{{ __('Teams Meeting') }}</h3>
                            @if(!$booking->teams_meeting_link)
                                <form action="{{ route('bookings.create-teams-meeting', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <x-primary-button type="submit">
                                        {{ __('Create Teams Meeting') }}
                                    </x-primary-button>
                                </form>
                            @endif
                        </div>
                        @if($booking->teams_meeting_link)
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="mb-2">{{ __('Join meeting:') }}</p>
                                <a href="{{ $booking->teams_meeting_url }}" target="_blank" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $booking->teams_meeting_url }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Attendees -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">{{ __('Attendees') }}</h3>
                            <x-primary-button
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'add-attendees')"
                            >{{ __('Add Attendees') }}</x-primary-button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($booking->attendees as $attendee)
                                <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium">{{ $attendee->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendee->user->email }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('bookings.remove-attendee', [$booking, $attendee]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <x-icons.trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Meeting Notes -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">{{ __('Meeting Notes') }}</h3>
                        <form action="{{ route('bookings.update-notes', $booking) }}" method="POST" class="mb-4">
                            @csrf
                            @method('PATCH')
                            <div class="mb-4">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <x-textarea-input id="notes" name="notes" class="block mt-1 w-full" rows="4">{{ old('notes', $booking->notes) }}</x-textarea-input>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                            <x-primary-button>{{ __('Save Notes') }}</x-primary-button>
                        </form>
                    </div>

                    <!-- Meeting Minutes -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">{{ __('Meeting Minutes') }}</h3>
                        <form action="{{ route('bookings.update-minutes', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-4">
                                <x-input-label for="meeting_minutes" :value="__('Minutes')" />
                                <x-textarea-input id="meeting_minutes" name="meeting_minutes" class="block mt-1 w-full" rows="6">{{ old('meeting_minutes', $booking->meeting_minutes) }}</x-textarea-input>
                                <x-input-error :messages="$errors->get('meeting_minutes')" class="mt-2" />
                            </div>
                            <x-primary-button>{{ __('Save Minutes') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Attendees Modal -->
    <x-modal name="add-attendees" :show="$errors->userInvitation->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('bookings.add-attendees', $booking) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Add Attendees') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="emails" :value="__('Email Addresses')" />
                <x-textarea-input
                    id="emails"
                    name="emails"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Enter email addresses (one per line)') }}"
                />
                <x-input-error :messages="$errors->userInvitation->get('emails')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Add Attendees') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

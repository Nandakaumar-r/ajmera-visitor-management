<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Cabin Calendar') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('bookings.index') }}" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Back to List View
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex justify-between items-center">
                        <div class="flex space-x-4">
                            <select id="filterCabin" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Cabins</option>
                                @foreach($cabins as $cabin)
                                    <option value="{{ $cabin->id }}">{{ $cabin->name }}</option>
                                @endforeach
                            </select>
                            <select id="filterStatus" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Status</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="flex space-x-4">
                            <button id="todayBtn" class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Today</button>
                            <div class="btn-group">
                                <button id="monthViewBtn" class="px-3 py-1 bg-indigo-600 text-white rounded-l hover:bg-indigo-700">Month</button>
                                <button id="weekViewBtn" class="px-3 py-1 bg-gray-100 text-gray-700 border-x hover:bg-gray-200">Week</button>
                                <button id="dayViewBtn" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-r hover:bg-gray-200">Day</button>
                            </div>
                        </div>
                    </div>
                    <div id='calendar' class="fc-theme-standard"></div>
                </div>
            </div>
        </div>
    </div>

    <x-booking-modal id="booking-modal" />

    @push('scripts')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .fc-theme-standard {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont;
        }
        .fc-event {
            cursor: pointer;
            padding: 2px 4px;
        }
        .fc-daygrid-event {
            white-space: normal;
        }
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600;
        }
        .fc-button {
            background-color: rgb(79 70 229) !important;
            border-color: rgb(79 70 229) !important;
        }
        .fc-button:hover {
            background-color: rgb(67 56 202) !important;
            border-color: rgb(67 56 202) !important;
        }
        .fc-day-today {
            background-color: rgb(238 242 255) !important;
        }
        .fc-list-event:hover td {
            background-color: rgb(238 242 255) !important;
        }
    </style>

    <script>
        // Wait for Alpine.js to load before initializing FullCalendar
        document.addEventListener('alpine:init', () => {
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next',
                        center: 'title',
                        right: ''
                    },
                    events: @json($bookings),
                    eventDidMount: function(info) {
                        info.el.addEventListener('click', function() {
                            console.log('Event clicked:', info.event.id);
                            const modal = document.querySelector('#booking-modal');
                            if (!modal) {
                                console.error('Modal element not found');
                                return;
                            }
                            if (!modal.__x) {
                                console.error('Alpine instance not found on modal');
                                return;
                            }
                            const modalData = modal.__x.$data;
                            console.log('Modal data:', modalData);
                            modalData.getBookingDetails(info.event.id);
                        });
                    },
                    dayMaxEvents: true,
                    eventColor: '#4F46E5',
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    }
                });
                calendar.render();

                // View buttons
                const monthViewBtn = document.getElementById('monthViewBtn');
                const weekViewBtn = document.getElementById('weekViewBtn');
                const dayViewBtn = document.getElementById('dayViewBtn');
                const todayBtn = document.getElementById('todayBtn');
                const filterCabin = document.getElementById('filterCabin');
                const filterStatus = document.getElementById('filterStatus');

                if (monthViewBtn) {
                    monthViewBtn.addEventListener('click', function() {
                        calendar.changeView('dayGridMonth');
                        updateViewButtons('monthViewBtn');
                    });
                }

                if (weekViewBtn) {
                    weekViewBtn.addEventListener('click', function() {
                        calendar.changeView('timeGridWeek');
                        updateViewButtons('weekViewBtn');
                    });
                }

                if (dayViewBtn) {
                    dayViewBtn.addEventListener('click', function() {
                        calendar.changeView('timeGridDay');
                        updateViewButtons('dayViewBtn');
                    });
                }

                if (todayBtn) {
                    todayBtn.addEventListener('click', function() {
                        calendar.today();
                    });
                }

                // Filter handlers
                if (filterCabin) {
                    filterCabin.addEventListener('change', filterEvents);
                }

                if (filterStatus) {
                    filterStatus.addEventListener('change', filterEvents);
                }

                function filterEvents() {
                    const cabinId = filterCabin ? filterCabin.value : '';
                    const status = filterStatus ? filterStatus.value : '';
                    const now = new Date();

                    console.log('Filtering events:', { cabinId, status, now });

                    calendar.getEvents().forEach(event => {
                        let visible = true;

                        if (cabinId && event.extendedProps.cabin_id != cabinId) {
                            console.log('Filtering by cabin:', event.title, event.extendedProps.cabin_id, cabinId);
                            visible = false;
                        }

                        if (status) {
                            const eventStart = new Date(event.start);
                            const eventEnd = new Date(event.end);

                            console.log('Filtering by status:', event.title, { status, eventStart, eventEnd, now });

                            switch(status) {
                                case 'upcoming':
                                    visible = visible && (eventStart > now);
                                    break;
                                case 'ongoing':
                                    visible = visible && (eventStart <= now && eventEnd >= now);
                                    break;
                                case 'completed':
                                    visible = visible && (eventEnd < now);
                                    break;
                            }
                        }

                        console.log('Event visibility:', event.title, visible);
                        event.setProp('display', visible ? 'auto' : 'none');
                    });
                }

                function updateViewButtons(activeBtn) {
                    const buttons = {
                        monthViewBtn: document.getElementById('monthViewBtn'),
                        weekViewBtn: document.getElementById('weekViewBtn'),
                        dayViewBtn: document.getElementById('dayViewBtn')
                    };

                    Object.entries(buttons).forEach(([id, element]) => {
                        if (element) {
                            if (id === activeBtn) {
                                element.classList.remove('bg-gray-100', 'text-gray-700');
                                element.classList.add('bg-indigo-600', 'text-white');
                            } else {
                                element.classList.remove('bg-indigo-600', 'text-white');
                                element.classList.add('bg-gray-100', 'text-gray-700');
                            }
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

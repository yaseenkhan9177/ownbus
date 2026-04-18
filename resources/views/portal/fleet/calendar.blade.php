@extends('portal.layout')

@section('title', 'Fleet Availability Calendar')

@section('content')
<div class="space-y-4 animate-in fade-in duration-500">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Fleet Availability Calendar</h1>
            <p class="text-xs text-slate-500 mt-0.5">Month-view of all vehicle rental blocks and availability gaps</p>
        </div>
        <div class="flex items-center space-x-3">
            {{-- Legend --}}
            <div class="flex items-center space-x-3 text-[10px] font-black uppercase">
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-emerald-500"></span>
                    <span class="text-slate-500">Active</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-rose-500"></span>
                    <span class="text-slate-500">Overdue</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-indigo-500"></span>
                    <span class="text-slate-500">Completed</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded bg-amber-500"></span>
                    <span class="text-slate-500">Pending</span>
                </span>
            </div>
            <a href="{{ route('company.rentals.create') }}"
                class="inline-flex items-center space-x-2 bg-cyan-500 hover:bg-cyan-400 text-white text-xs font-black uppercase tracking-tight px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Rental</span>
            </a>
        </div>
    </div>

    {{-- Calendar Card --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div id="fleet-calendar" class="p-4" style="min-height: 600px;"></div>
    </div>

</div>
@endsection

@push('scripts')
{{-- FullCalendar v6 (Resource Timeline) --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.11/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('fleet-calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercialShareAlike',
            initialView: 'resourceTimelineMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimelineWeek,resourceTimelineMonth'
            },
            resourceAreaHeaderContent: 'Vehicles',
            resourceAreaWidth: '200px',
            height: 'auto',
            slotMinWidth: 40,
            editable: false,
            resourceLabelContent: function(arg) {
                return {
                    html: `<div class="text-xs font-bold py-1">${arg.resource.title}</div>`
                };
            },
            eventContent: function(arg) {
                return {
                    html: `<div class="px-1.5 py-0.5 text-[10px] font-bold truncate">
                    ${arg.event.title}
                    <span class="opacity-70 ml-1">${arg.event.extendedProps.amount}</span>
                </div>`
                };
            },
            eventClick: function(info) {
                const p = info.event.extendedProps;
                alert(`Rental #${p.uuid}\nCustomer: ${info.event.title}\nAmount: ${p.amount}\nStatus: ${p.status}`);
            },
            events: function(info, successCallback, failureCallback) {
                fetch(`{{ route('company.fleet.calendar.events') }}?start=${info.startStr}&end=${info.endStr}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        calendar.setOption('resources', data.resources);
                        successCallback(data.events);
                    })
                    .catch(failureCallback);
            },
            // Dark mode theming
            themeSystem: 'standard',
        });

        calendar.render();
    });
</script>

<style>
    /* FullCalendar dark-mode overrides */
    .dark .fc {
        color: #e2e8f0;
    }

    .dark .fc-theme-standard td,
    .dark .fc-theme-standard th,
    .dark .fc-theme-standard .fc-scrollgrid {
        border-color: #1e293b !important;
    }

    .dark .fc-datagrid-cell-cushion,
    .dark .fc-col-header-cell-cushion {
        color: #94a3b8 !important;
    }

    .dark .fc-timeline-slot-label {
        color: #64748b;
    }

    .dark .fc-button-primary {
        background: #1e293b;
        border-color: #334155;
        color: #e2e8f0;
    }

    .dark .fc-button-primary:hover {
        background: #0f172a;
    }
</style>
@endpush
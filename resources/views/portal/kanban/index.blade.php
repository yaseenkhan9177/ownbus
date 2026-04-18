@extends('layouts.company')

@section('title', 'Fleet Operations Command')

@push('styles')
<style>
    .kanban-column {
        min-height: calc(100vh - 250px);
        background: rgba(15, 23, 42, 0.4);
    }

    .kanban-card {
        cursor: grab;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .kanban-card:active {
        cursor: grabbing;
    }

    .ghost-card {
        opacity: 0.4;
        border: 2px dashed #06b6d4 !important;
        background: rgba(6, 182, 212, 0.1) !important;
    }

    .neon-glow-cyan {
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.2);
    }

    .neon-border-cyan {
        border-color: rgba(6, 182, 212, 0.3);
    }

    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@section('header_title')
<div class="flex items-center space-x-3">
    <div class="w-2 h-8 bg-cyan-500 rounded-full shadow-[0_0_15px_rgba(6,182,212,0.5)]"></div>
    <h1 class="text-2xl font-black text-white tracking-tighter uppercase italic">
        Operational <span class="text-cyan-400">Kanban</span>
    </h1>
</div>
@endsection

@section('content')
<div class="h-full overflow-x-auto pb-4" x-data="kanbanBoard()">
    <div class="flex space-x-6 min-w-max px-2">
        @foreach($stages as $status => $label)
        <div class="w-80 shrink-0">
            <!-- Column Header -->
            <div class="flex items-center justify-between mb-4 px-2">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-500 shadow-[0_0_5px_#06b6d4]"></span>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $label }}</h3>
                </div>
                <span class="text-[10px] font-mono text-cyan-500 bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20">
                    {{ count($rentalsByStage[$status]) }}
                </span>
            </div>

            <!-- Drop Zone -->
            <div id="col-{{ $status }}"
                data-status="{{ $status }}"
                class="kanban-column rounded-3xl p-3 border border-slate-800/50 backdrop-blur-sm space-y-4 transition-colors duration-300">

                @foreach($rentalsByStage[$status] as $rental)
                <div class="kanban-card bg-slate-900 border border-slate-800/80 rounded-2xl p-4 shadow-xl hover:neon-border-cyan hover:neon-glow-cyan group relative overflow-hidden"
                    data-id="{{ $rental->id }}">

                    <!-- Top Info -->
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[10px] font-mono text-cyan-400/70">#{{ $rental->contract_number }}</span>
                        <div class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-pulse"></div>
                    </div>

                    <!-- Customer -->
                    <div class="mb-4">
                        <h4 class="text-white font-bold text-sm tracking-tight truncate">{{ $rental->customer->name }}</h4>
                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ $rental->pickup_location }}</p>
                    </div>

                    <!-- Details Row -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-slate-800/50 p-2 rounded-lg border border-slate-700/50">
                            <p class="text-[8px] text-gray-500 uppercase font-black mb-1">Vehicle</p>
                            <p class="text-[10px] text-white font-bold truncate">{{ $rental->bus->plate_number ?? 'UNASSIGNED' }}</p>
                        </div>
                        <div class="bg-slate-800/50 p-2 rounded-lg border border-slate-700/50">
                            <p class="text-[8px] text-gray-500 uppercase font-black mb-1">Driver</p>
                            <p class="text-[10px] text-white font-bold truncate">{{ $rental->driver->name ?? 'UNASSIGNED' }}</p>
                        </div>
                    </div>

                    <!-- Time Track -->
                    <div class="flex items-center space-x-2 text-[10px] text-gray-400 font-mono">
                        <svg class="h-3 w-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $rental->start_date->format('H:i') }} - {{ $rental->end_date->format('H:i') }}</span>
                    </div>

                    <!-- Hover Actions -->
                    <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="openDriverModal({{ $rental->id }})" class="p-1.5 bg-cyan-500/10 text-cyan-400 rounded-lg border border-cyan-500/20 hover:bg-cyan-500 hover:text-white transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Driver Suggestion Modal -->
    <div x-show="driverModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
        x-cloak>
        <div class="bg-slate-900 border border-cyan-500/30 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden" @click.away="driverModalOpen = false">
            <div class="p-6 border-b border-slate-800">
                <h3 class="text-xl font-black text-white tracking-widest uppercase">Driver Intelligence</h3>
                <p class="text-sm text-cyan-400 font-mono italic">Scanning for compatible dispatchers...</p>
            </div>

            <div class="p-6 max-h-96 overflow-y-auto space-y-3">
                <template x-if="loadingDrivers">
                    <div class="flex justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-cyan-500"></div>
                    </div>
                </template>

                <template x-for="driver in suggestedDrivers" :key="driver.id">
                    <div class="flex items-center justify-between p-4 bg-slate-800/40 rounded-2xl border border-slate-700/50 hover:border-cyan-500/50 transition-all group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-black" x-text="driver.name[0]"></div>
                            <div>
                                <p class="text-sm font-bold text-white uppercase tracking-tight" x-text="driver.name"></p>
                                <p class="text-[10px] text-emerald-400 font-mono">STATUS: AVAILABLE_FOR_DISPATCH</p>
                            </div>
                        </div>
                        <button @click="assignDriver(driver.id)" class="px-4 py-2 bg-cyan-500/10 text-cyan-500 rounded-lg text-xs font-black border border-cyan-500/20 hover:bg-cyan-500 hover:text-white transition-all">
                            ASSIGN
                        </button>
                    </div>
                </template>

                <template x-if="!loadingDrivers && suggestedDrivers.length === 0">
                    <div class="text-center py-8">
                        <p class="text-sm text-rose-500 italic font-mono uppercase tracking-widest">0_COMPATIBLE_DRIVERS_FOUND</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    function kanbanBoard() {
        return {
            driverModalOpen: false,
            loadingDrivers: false,
            suggestedDrivers: [],
            activeRentalId: null,
            companyId: '{{ auth()->user()->company_id }}',

            init() {
                const columns = ['confirmed', 'assigned', 'dispatched', 'active', 'completed'];
                columns.forEach(status => {
                    const el = document.getElementById('col-' + status);
                    new Sortable(el, {
                        group: 'rentals',
                        ghostClass: 'ghost-card',
                        animation: 150,
                        onEnd: (evt) => {
                            const rentalId = evt.item.dataset.id;
                            const toStatus = evt.to.dataset.status;
                            this.updateRentalStatus(rentalId, toStatus);
                        }
                    });
                });

                // Real-time Sync
                if (window.Echo) {
                    window.Echo.private(`company.${this.companyId}`)
                        .listen('RentalStatusUpdated', (e) => {
                            console.log('Operational sync received:', e);
                            this.handleRemoteUpdate(e.rental);
                        });
                }
            },

            handleRemoteUpdate(rental) {
                const card = document.querySelector(`.kanban-card[data-id="${rental.id}"]`);
                const targetCol = document.getElementById(`col-${rental.status}`);

                if (card && targetCol) {
                    if (card.parentElement !== targetCol) {
                        targetCol.appendChild(card);
                        card.classList.add('neon-glow-cyan');
                        setTimeout(() => card.classList.remove('neon-glow-cyan'), 2000);
                    }

                    const driverSection = card.querySelectorAll('.bg-slate-800\\/50 p.text-white')[1];
                    if (driverSection) driverSection.textContent = rental.driver ? rental.driver.name : 'UNASSIGNED';
                } else if (!card && targetCol) {
                    window.location.reload();
                }
            },

            async updateRentalStatus(id, status) {
                try {
                    const response = await fetch(`/portal/company/kanban/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            to_status: status
                        })
                    });

                    const data = await response.json();
                    if (!data.success) {
                        alert(data.message);
                        window.location.reload(); // Revert on failure
                    }
                } catch (e) {
                    console.error('Update failed', e);
                    window.location.reload();
                }
            },

            async openDriverModal(id) {
                this.activeRentalId = id;
                this.driverModalOpen = true;
                this.loadingDrivers = true;
                this.suggestedDrivers = [];

                try {
                    const response = await fetch(`/portal/company/kanban/${id}/suggest-drivers`);
                    const data = await response.json();
                    this.suggestedDrivers = data.drivers;
                } catch (e) {
                    console.error('Failed to load drivers', e);
                } finally {
                    this.loadingDrivers = false;
                }
            },

            async assignDriver(driverId) {
                try {
                    const response = await fetch(`/portal/company/kanban/${this.activeRentalId}/assign-driver`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            driver_id: driverId
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.driverModalOpen = false;
                        window.location.reload(); // Refresh to show new state
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    console.error('Assignment failed', e);
                }
            }
        }
    }
</script>
@endpush
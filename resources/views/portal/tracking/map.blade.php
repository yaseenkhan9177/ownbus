@extends('portal.layout')

@section('title', 'Live Fleet Tracking | OwnBus')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Outfit', sans-serif; }
    #map { height: calc(100-vh - 200px); min-height: 600px; border-radius: 1.5rem; }
    .vehicle-marker { transition: all 1s linear; }
    .status-badge { font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 99px; text-transform: uppercase; letter-spacing: 0.05em; }
    .custom-popup .leaflet-popup-content-wrapper { border-radius: 1rem; padding: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .custom-popup .leaflet-popup-content { margin: 0; }
</style>
@endpush

@section('content')
<div class="px-6 py-8" x-data="fleetTracker()">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Live Fleet <span class="text-indigo-600">Tracking</span></h1>
            <p class="text-slate-500 text-sm">Real-time GPS telemetry from across the UAE</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">System Live</span>
            </div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400">
                <span>Active: <span class="text-indigo-600" x-text="activeVehicles">0</span></span>
                <span>/</span>
                <span>Total: <span x-text="totalVehicles">0</span></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar: Vehicle List -->
        <div class="lg:col-span-1 space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
            <template x-for="vehicle in vehicles" :key="vehicle.id">
                <div @click="focusVehicle(vehicle)" 
                     class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm cursor-pointer transition-all hover:border-indigo-400 hover:shadow-md group"
                     :class="focusedVehicleId === vehicle.id ? 'border-indigo-500 ring-2 ring-indigo-100' : ''">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-slate-800 text-sm" x-text="vehicle.vehicle_number"></h4>
                        <span class="status-badge" 
                              :class="vehicle.tracking_status === 'live' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                              x-text="vehicle.tracking_status"></span>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3" x-text="vehicle.name"></p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="text-[10px] font-bold text-slate-700">
                                <span x-text="vehicle.location ? vehicle.location.speed : 0"></span> km/h
                            </span>
                            <div class="flex items-center" x-show="vehicle.location && vehicle.location.ignition">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                                <span class="text-[9px] font-bold uppercase text-emerald-600">Ignition On</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </div>
            </template>
        </div>

        <!-- Main Map -->
        <div class="lg:col-span-3 relative">
            <div id="map" class="shadow-2xl border border-slate-200"></div>
            
            <!-- Map Overlay: Search -->
            <div class="absolute top-4 left-4 z-[1000] w-64">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" x-model="search" class="w-full bg-white/90 backdrop-blur-md border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs font-bold focus:outline-none focus:border-indigo-500 shadow-lg" placeholder="Search vehicle #...">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Laravel Echo/Reverb Setup -->
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>

<script>
    function fleetTracker() {
        return {
            map: null,
            vehicles: [],
            markers: {},
            search: '',
            focusedVehicleId: null,
            companyId: {{ auth()->user()->company_id }},

            get activeVehicles() {
                return this.vehicles.filter(v => v.tracking_status === 'live').length;
            },
            get totalVehicles() {
                return this.vehicles.length;
            },

            async init() {
                this.initMap();
                await this.fetchFleet();
                this.initEcho();
                
                // Refresh occasionally to catch offline status changes
                setInterval(() => this.fetchFleet(), 30000);
            },

            initMap() {
                this.map = L.map('map', {
                    zoomControl: false
                }).setView([25.2048, 55.2708], 11); // Dubai Center

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);

                L.control.zoom({ position: 'bottomright' }).addTo(this.map);
            },

            async fetchFleet() {
                try {
                    const response = await fetch(`/api/v1/gps/fleet/${this.companyId}`);
                    const data = await response.json();
                    this.vehicles = data.vehicles;
                    this.updateMarkers();
                } catch (e) {
                    console.error('Failed to fetch fleet status');
                }
            },

            updateMarkers() {
                this.vehicles.forEach(vehicle => {
                    if (!vehicle.location) return;

                    const pos = [vehicle.location.lat, vehicle.location.lng];
                    
                    if (this.markers[vehicle.id]) {
                        this.markers[vehicle.id].setLatLng(pos);
                    } else {
                        const marker = L.marker(pos, {
                            icon: L.divIcon({
                                className: 'custom-marker',
                                html: `<div class="w-8 h-8 bg-indigo-600 rounded-full border-4 border-white shadow-lg flex items-center justify-center transform rotate-${vehicle.location.heading || 0}">
                                         <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                                       </div>`
                            })
                        }).addTo(this.map);

                        marker.bindPopup(`
                            <div class="p-4 w-48 custom-popup">
                                <h4 class="font-black text-slate-800 uppercase text-sm mb-1">${vehicle.vehicle_number}</h4>
                                <p class="text-[10px] text-slate-500 mb-3">${vehicle.name}</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-[11px]">
                                        <span class="text-slate-400">Speed</span>
                                        <span class="font-bold text-indigo-600">${vehicle.location.speed} km/h</span>
                                    </div>
                                    <div class="flex justify-between text-[11px]">
                                        <span class="text-slate-400">Heading</span>
                                        <span class="font-bold">${vehicle.location.heading}°</span>
                                    </div>
                                </div>
                            </div>
                        `);

                        this.markers[vehicle.id] = marker;
                    }
                });
            },

            focusVehicle(vehicle) {
                if (!vehicle.location) return;
                this.focusedVehicleId = vehicle.id;
                this.map.flyTo([vehicle.location.lat, vehicle.location.lng], 15);
                this.markers[vehicle.id].openPopup();
            },

            initEcho() {
                // Configure echo according to .env reverb setup
                window.Pusher = Pusher;
                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: '{{ env("REVERB_APP_KEY") }}',
                    wsHost: '{{ env("REVERB_HOST") }}',
                    wsPort: {{ env("REVERB_PORT") }},
                    wssPort: {{ env("REVERB_PORT") }},
                    forceTLS: false, // Set to true if using HTTPS in prod
                    enabledTransports: ['ws', 'wss'],
                });

                window.Echo.channel('fleet-tracking')
                    .listen('.location.updated', (event) => {
                        this.handleRealtimeUpdate(event.location);
                    });
            },

            handleRealtimeUpdate(newLoc) {
                const vehicle = this.vehicles.find(v => v.id === newLoc.vehicle_id);
                if (vehicle) {
                    vehicle.location = {
                        lat: newLoc.latitude,
                        lng: newLoc.longitude,
                        speed: newLoc.speed,
                        heading: newLoc.heading,
                        ignition: newLoc.ignition_status,
                        updated_at: newLoc.recorded_at
                    };
                    vehicle.tracking_status = 'live';
                    this.updateMarkers();
                    
                    if (this.focusedVehicleId === vehicle.id) {
                        this.map.flyTo([newLoc.latitude, newLoc.longitude]);
                    }
                }
            }
        }
    }
</script>
@endpush

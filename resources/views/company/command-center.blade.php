<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WAR ROOM | HQ COMMAND CENTER</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap');

        body {
            font-family: 'JetBrains Mono', monospace;
        }

        .glow-red {
            box-shadow: 0 0 20px rgba(244, 63, 94, 0.4);
        }

        .glow-cyan {
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.4);
        }

        .emergency-pulse {
            animation: pulse 1s infinite alternate;
        }

        @keyframes pulse {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0.4;
                transform: scale(1.1);
            }
        }

        #map {
            height: 100%;
            width: 100%;
            filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
        }

        /* Custom scrollbar for dark theme */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }
    </style>
</head>

<body class="h-full antialiased" x-data="commandCenter()">

    <!-- ═══════════════════════════════════════════════
         TOP HUD: GLOBAL STATUS & EMERGENCY TRIGGER
    ═══════════════════════════════════════════════ -->
    <header class="absolute top-0 left-0 right-0 z-50 pt-6 px-10 flex items-center justify-between pointer-events-none">
        <div class="flex items-center gap-6 pointer-events-auto">
            <div class="flex flex-col">
                <h1 class="text-2xl font-black tracking-tighter text-white flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyan-500 animate-pulse"></span>
                    HQ COMMAND CENTER
                </h1>
                <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase">Live Operational Intelligence — UAE Fleet</p>
            </div>

            <div class="h-8 w-px bg-slate-800"></div>

            <div class="flex items-center gap-8">
                <template x-for="stat in getFleetStatsSummary()">
                    <div class="text-center">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest" x-text="stat.label"></p>
                        <p class="text-lg font-black leading-none mt-1" :class="stat.color" x-text="stat.value"></p>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center gap-4 pointer-events-auto">
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Local Time (GST)</p>
                <p class="text-lg font-black text-white leading-none mt-1" x-text="currentTime"></p>
            </div>

            <button @click="toggleCrisisMode()"
                :class="crisisMode ? 'bg-rose-600 border-rose-400 glow-red' : 'bg-slate-900 border-slate-700 hover:bg-slate-800'"
                class="ml-4 px-6 py-3 border-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 group">
                <span class="w-2 h-2 rounded-full" :class="crisisMode ? 'bg-white animate-ping' : 'bg-rose-500'"></span>
                <span x-text="crisisMode ? 'STBY CRISIS MODE ACTIVE' : 'ACTIVATE CRISIS MODE'"></span>
            </button>
        </div>
    </header>

    <!-- ═══════════════════════════════════════════════
         MAIN TACTICAL MAP (CENTERPIECE)
    ═══════════════════════════════════════════════ -->
    <div class="fixed inset-0 z-0">
        <div id="map"></div>
    </div>

    <!-- ═══════════════════════════════════════════════
         LEFT SIDE: CRITICAL ALERTS PANEL
    ═══════════════════════════════════════════════ -->
    <aside class="absolute top-24 left-6 bottom-32 w-80 z-50 pointer-events-none">
        <div class="h-full flex flex-col gap-4 pointer-events-auto">
            <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl overflow-hidden flex flex-col h-full">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">⚠️ Critical Alerts</h3>
                    <span class="text-[9px] font-black bg-rose-500 text-white px-1.5 py-0.5 rounded" x-text="data.critical_alerts.length"></span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <template x-for="alert in data.critical_alerts">
                        <div class="p-3 border rounded-xl transition-all"
                            :class="alert.level === '🔴' ? 'bg-rose-500/10 border-rose-500/30' : 'bg-slate-800/50 border-slate-700'">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[9px] font-black uppercase tracking-tighter" :class="alert.level === '🔴' ? 'text-rose-400' : 'text-amber-400'" x-text="alert.category"></span>
                                <span class="text-[10px] font-black text-slate-500" x-text="alert.meta"></span>
                            </div>
                            <p class="text-xs font-bold text-slate-100" x-text="alert.message"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Sound Control Toggle -->
            <button @click="mute = !mute" class="bg-slate-900/80 backdrop-blur-lg border border-slate-800 rounded-xl p-3 flex items-center gap-3 transition-colors hover:bg-slate-800">
                <span x-text="mute ? '🔇' : '🔊'"></span>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="mute ? 'Alert Sound Muted' : 'Sound Active'"></span>
            </button>
        </div>
    </aside>

    <!-- ═══════════════════════════════════════════════
         RIGHT SIDE: REVENUE MONITOR & TIMELINE
    ═══════════════════════════════════════════════ -->
    <aside class="absolute top-24 right-6 bottom-32 w-80 z-50 pointer-events-none">
        <div class="h-full flex flex-col gap-4 pointer-events-auto">
            <!-- Revenue Monitor -->
            <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-800 rounded-2xl p-5 glow-cyan">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">💰 Revenue Monitor</h3>
                    <span class="text-[9px] font-black text-cyan-400 uppercase tracking-widest">Live Flow</span>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Today</p>
                        <p class="text-2xl font-black text-white" x-text="'AED ' + formatNumber(data.revenue.today)"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">AI Lift</p>
                        <p class="text-xl font-black text-emerald-400" x-text="'+AED ' + formatNumber(data.revenue.ai_lift_today)"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">This Week</p>
                        <p class="text-lg font-black text-slate-300" x-text="'AED ' + formatNumber(data.revenue.week)"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Est Margin</p>
                        <p class="text-lg font-black text-white" x-text="data.revenue.margin_pct + '%'"></p>
                    </div>
                </div>
            </div>

            <!-- Incident Feed -->
            <div class="flex-1 bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl overflow-hidden flex flex-col">
                <div class="p-4 border-b border-slate-800">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest italic">/// Incident Timeline Feed</h3>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <template x-for="event in data.timeline">
                        <div class="flex gap-3 relative before:content-[''] before:absolute before:left-2 before:top-6 before:bottom-0 before:w-px before:bg-slate-800 last:before:hidden">
                            <span class="text-xs z-10" x-text="event.icon"></span>
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest" x-text="event.time"></p>
                                <p class="text-xs font-bold text-slate-200 mt-0.5" x-text="event.event"></p>
                                <p class="text-[9px] text-slate-500 italic mt-0.5" x-text="event.meta"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </aside>

    <!-- ═══════════════════════════════════════════════
         BOTTOM BAR: OPERATIONAL EFFICIENCY GAUGES
    ═══════════════════════════════════════════════ -->
    <footer class="absolute bottom-6 left-6 right-6 z-50 pointer-events-none">
        <div class="bg-slate-900/95 backdrop-blur-2xl border border-white/5 rounded-2xl p-4 pointer-events-auto">
            <div class="grid grid-cols-5 gap-8 items-center">
                <div class="flex flex-col">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Fleet Utilization %</p>
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-1000 bg-cyan-500" :style="'width: ' + data.efficiency.utilization + '%'"></div>
                    </div>
                    <p class="text-lg font-black mt-1" x-text="data.efficiency.utilization + '%'"></p>
                </div>

                <div class="flex flex-col">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Downtime Ratio</p>
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-1000 bg-rose-500" :style="'width: ' + data.efficiency.downtime + '%'"></div>
                    </div>
                    <p class="text-lg font-black mt-1" x-text="data.efficiency.downtime + '%'"></p>
                </div>

                <div class="flex flex-col">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Avg Driver Risk</p>
                    <p class="text-lg font-black" :class="data.efficiency.avg_driver_risk < 60 ? 'text-rose-500' : 'text-emerald-500'" x-text="data.efficiency.avg_driver_risk"></p>
                </div>

                <div class="flex flex-col">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Escalation Trend</p>
                    <p class="text-lg font-black text-white" x-text="data.efficiency.maint_trend"></p>
                </div>

                <div class="flex flex-col">
                    <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mb-1.5">Replacement Risk</p>
                    <p class="text-lg font-black text-rose-600" x-text="data.efficiency.replacement_count + ' UNITS'"></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function commandCenter() {
            return {
                data: @json($data),
                currentTime: '',
                mute: false,
                crisisMode: false,
                map: null,
                markers: [],

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    this.initMap();
                    
                    // Auto-refresh every 30 seconds
                    setInterval(() => this.fetchSnapshot(), 30000);

                    // Reverb / Echo Listeners
                    if (window.Echo) {
                        window.Echo.channel('fleet.status')
                            .listen('.App\\Events\\Intelligence\\FleetStatusUpdated', (e) => {
                                console.log('Fleet update received', e);
                                this.fetchSnapshot();
                            });

                        window.Echo.channel('intelligence.alerts')
                            .listen('.App\\Events\\Intelligence\\AlertTriggered', (e) => {
                                this.data.critical_alerts.unshift(e.alert);
                                if (!this.mute) this.playAlertSound();
                            });
                    }
                },

                initMap() {
                    this.map = L.map('map', {
                        zoomControl: false,
                        attributionControl: false
                    }).setView([25.2048, 55.2708], 11);

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(this.map);

                    this.renderMarkers();
                },

                renderMarkers() {
                    // Clear existing
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];

                    this.data.map_markers.forEach(v => {
                        const iconColor = this.getStatusColor(v.status);
                        const icon = L.divIcon({
                            className: 'custom-div-icon',
                            html: `<div class="w-4 h-4 rounded-full border-2 border-white ${v.is_emergency ? 'emergency-pulse bg-rose-600 scale-125' : 'bg-'+iconColor+'-500'}"></div>`,
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        });

                        const marker = L.marker([v.lat, v.lng], {
                                icon
                            })
                            .bindPopup(`<div class="bg-slate-900 text-white p-2"><b>${v.number}</b><br>${v.name}<br>Status: ${v.status}</div>`)
                            .addTo(this.map);

                        this.markers.push(marker);
                    });
                },

                getStatusColor(status) {
                    switch (status) {
                        case 'rented':
                            return 'emerald';
                        case 'maintenance':
                            return 'rose';
                        case 'available':
                            return 'amber';
                        default:
                            return 'slate';
                    }
                },

                async fetchSnapshot() {
                    try {
                        const response = await fetch('{{ route("company.command-center.api") }}');
                        this.data = await response.json();
                        this.renderMarkers();
                        if (this.crisisMode) {
                            this.handleCrisisAutoZoom();
                        }
                    } catch (e) {
                        console.error("Failed to fetch command center snapshot", e);
                    }
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('en-US', {
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                },

                toggleCrisisMode() {
                    this.crisisMode = !this.crisisMode;
                    if (this.crisisMode) {
                        this.handleCrisisAutoZoom();
                    } else {
                        this.map.setView([25.2048, 55.2708], 11);
                    }
                },

                handleCrisisAutoZoom() {
                    const emergencyMarkers = this.data.map_markers.filter(m => m.is_emergency);
                    if (emergencyMarkers.length > 0) {
                        const bounds = L.latLngBounds(emergencyMarkers.map(m => [m.lat, m.lng]));
                        this.map.fitBounds(bounds, {
                            padding: [100, 100],
                            maxZoom: 15
                        });
                    }
                },

                getFleetStatsSummary() {
                    return [
                        { label: 'Total Units', value: this.data.fleet_stats.total, color: 'text-white' },
                        { label: 'Active', value: this.data.fleet_stats.active, color: 'text-emerald-400' },
                        { label: 'Maintenance', value: this.data.fleet_stats.maintenance, color: 'text-rose-400' },
                        { label: 'Idle', value: this.data.fleet_stats.idle, color: 'text-amber-400' },
                        { label: 'Offline', value: this.data.fleet_stats.offline, color: 'text-slate-500' }
                    ];
                },

                playAlertSound() {
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play();
                },

                formatNumber(num) {
                    return new Intl.NumberFormat().format(num);
                }
            }
        }
    </script>
</body>

</html>
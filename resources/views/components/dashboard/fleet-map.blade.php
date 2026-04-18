{{--
    Fleet Live Map Component
    Usage: <x-dashboard.fleet-map :companyId="$company->id" />

    - Leaflet.js with CartoDB Dark Matter tiles (no API key needed)
    - AJAX polls /api/v1/gps/fleet/{companyId} every 30s
    - Color-coded markers: 🟢 Live · 🔴 Offline · 🟡 Available (no active ping)
    - Speed + heading in popup
    - "Last updated" badge refreshes with each poll
--}}

<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
    {{-- Map Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-800">
        <div>
            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">GPS Intelligence</h3>
            <p class="text-base font-bold text-gray-900 dark:text-white mt-0.5">Live Fleet Map</p>
        </div>
        <div class="flex items-center space-x-3">
            {{-- Live pulse indicator --}}
            <span id="gps-live-badge" class="flex items-center space-x-1.5 text-[11px] font-bold text-emerald-500">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span id="gps-last-updated">CONNECTING…</span>
            </span>
            {{-- KPI mini strip --}}
            <div class="flex items-center divide-x divide-gray-100 dark:divide-slate-700">
                <div class="px-3 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Live</p>
                    <p id="gps-count-live" class="text-sm font-black text-emerald-500">—</p>
                </div>
                <div class="px-3 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Offline</p>
                    <p id="gps-count-offline" class="text-sm font-black text-rose-500">—</p>
                </div>
                <div class="px-3 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase">No GPS</p>
                    <p id="gps-count-unknown" class="text-sm font-black text-slate-400">—</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Leaflet Map Container --}}
    <div id="fleet-map" style="height: 380px; width: 100%;"></div>

    {{-- Offline Alert Strip --}}
    <div id="offline-alert-strip" class="hidden px-5 py-3 bg-rose-50 dark:bg-rose-900/20 border-t border-rose-100 dark:border-rose-800">
        <p class="text-[11px] font-bold text-rose-600 dark:text-rose-400">
            ⚠ <span id="offline-count">0</span> vehicle(s) offline — last known positions shown in red
        </p>
    </div>
</div>

{{-- Leaflet CSS and JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    (function() {
        // ── Config ────────────────────────────────────────────────
        const COMPANY_ID = {
            {
                $companyId
            }
        };
        const POLL_INTERVAL = 30000; // 30 seconds
        const UAE_CENTER = [25.2048, 55.2708]; // Dubai
        const FLEET_URL = `/api/v1/gps/fleet/${COMPANY_ID}`;
        const AUTH_TOKEN = document.querySelector('meta[name="api-token"]')?.content ?? '';

        // ── Marker colours ────────────────────────────────────────
        const MARKER_COLORS = {
            live: '#10b981', // emerald
            offline: '#f43f5e', // rose
            unknown: '#94a3b8', // slate
        };

        function makeIcon(color, heading) {
            const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                <circle cx="14" cy="14" r="10" fill="${color}" opacity="0.9"/>
                <circle cx="14" cy="14" r="4"  fill="white"/>
                <polygon points="14,2 17,9 14,7 11,9" fill="${color}" transform="rotate(${heading ?? 0},14,14)"/>
            </svg>`;
            return L.divIcon({
                html: svg,
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                className: '',
            });
        }

        // ── Init Leaflet ──────────────────────────────────────────
        const map = L.map('fleet-map', {
            center: UAE_CENTER,
            zoom: 11,
            zoomControl: true,
            attributionControl: false,
        });

        L.tileLayer(
            'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                subdomains: 'abcd',
                maxZoom: 19
            }
        ).addTo(map);

        // ── Marker Registry ───────────────────────────────────────
        const markers = {};

        // ── Poll Function ─────────────────────────────────────────
        async function refreshFleet() {
            try {
                const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const headers = {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };

                if (AUTH_TOKEN) headers['Authorization'] = `Bearer ${AUTH_TOKEN}`;
                if (CSRF_TOKEN) headers['X-CSRF-TOKEN'] = CSRF_TOKEN;

                const res = await fetch(FLEET_URL, {
                    headers
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();

                let liveCnt = 0,
                    offlineCnt = 0,
                    unknownCnt = 0;

                (data.vehicles ?? []).forEach(vehicle => {
                    const status = vehicle.tracking_status ?? 'unknown';
                    const color = MARKER_COLORS[status] ?? MARKER_COLORS.unknown;

                    if (status === 'live') liveCnt++;
                    if (status === 'offline') offlineCnt++;
                    if (status === 'unknown') unknownCnt++;

                    if (!vehicle.lat || !vehicle.lng) return; // no position yet

                    const latlng = [parseFloat(vehicle.lat), parseFloat(vehicle.lng)];
                    const popup = `
                    <div style="font-family:monospace;font-size:12px;min-width:160px">
                        <b style="color:${color}">${vehicle.vehicle_number}</b><br>
                        Status: <b>${vehicle.fleet_status}</b><br>
                        GPS: <b>${status.toUpperCase()}</b><br>
                        Speed: <b>${vehicle.speed ?? 0} km/h</b><br>
                        Last ping: ${vehicle.last_ping}<br>
                    </div>`;

                    if (markers[vehicle.vehicle_id]) {
                        // Update existing marker position + icon
                        markers[vehicle.vehicle_id]
                            .setLatLng(latlng)
                            .setIcon(makeIcon(color, vehicle.heading))
                            .getPopup()?.setContent(popup);
                    } else {
                        // Create new marker
                        markers[vehicle.vehicle_id] = L.marker(latlng, {
                                icon: makeIcon(color, vehicle.heading)
                            })
                            .bindPopup(popup)
                            .addTo(map);
                    }
                });

                // Update KPI strip
                document.getElementById('gps-count-live').textContent = liveCnt;
                document.getElementById('gps-count-offline').textContent = offlineCnt;
                document.getElementById('gps-count-unknown').textContent = unknownCnt;

                // Offline alert banner
                const strip = document.getElementById('offline-alert-strip');
                document.getElementById('offline-count').textContent = offlineCnt;
                strip.classList.toggle('hidden', offlineCnt === 0);

                // Last updated badge
                const now = new Date();
                document.getElementById('gps-last-updated').textContent =
                    `LIVE · ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}:${now.getSeconds().toString().padStart(2,'0')}`;

            } catch (err) {
                console.warn('[FleetMap] Poll failed:', err.message);
                document.getElementById('gps-last-updated').textContent = 'OFFLINE';
            }
        }

        // ── Boot ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            refreshFleet(); // Initial load immediately
            setInterval(refreshFleet, POLL_INTERVAL); // Then every 30s
        });

    })();
</script>
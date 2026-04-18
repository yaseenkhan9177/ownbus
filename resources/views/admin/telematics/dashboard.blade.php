@extends('layouts.company')

@section('title', 'Live Telematics Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i> Real-time Fleet Tracking
                </h5>
                <div>
                    <span class="badge bg-light text-primary me-2"><i class="fas fa-bus"></i> <span id="active-buses-count">0</span> Active Buses</span>
                    <span class="badge bg-success" id="connection-status">Connecting...</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="map" style="height: 600px; width: 100%;"></div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted"><i class="fas fa-info-circle"></i> Map updates automatically every 10 seconds.</small>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Google Maps SDK -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap" async defer></script>

<script>
    let map;
    let markers = {}; // Store markers by vehicle ID
    const companyId = {
        {
            $companyId ?? 0
        }
    };

    function initMap() {
        // Initial center focusing generically on UAE (Dubai) for testing
        // You would dynamically set this based on fleet location or company settings
        const initialLocation = {
            lat: 25.2048,
            lng: 55.2708
        };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: initialLocation,
            mapTypeId: 'roadmap',
            styles: [{
                    featureType: "poi.business",
                    stylers: [{
                        visibility: "off"
                    }],
                },
                {
                    featureType: "transit",
                    elementType: "labels.icon",
                    stylers: [{
                        visibility: "off"
                    }],
                },
            ],
        });

        console.log("Map Initialized.");
        connectToTelematicsStream();
    }

    function connectToTelematicsStream() {
        if (typeof Echo === 'undefined') {
            document.getElementById('connection-status').innerText = 'Echo Not Found';
            document.getElementById('connection-status').className = 'badge bg-danger';
            return;
        }

        document.getElementById('connection-status').innerText = 'Connected';
        document.getElementById('connection-status').className = 'badge bg-success';

        // Connect to Laravel Reverb via Echo
        Echo.private(`company.${companyId}.telematics`)
            .listen('.App\\Events\\Telematics\\VehicleLocationUpdated', (e) => {
                console.log("GPS Ping Received:", e);
                updateVehicleLocation(e);
            });
    }

    function updateVehicleLocation(payload) {
        const vehicleId = payload.vehicle_id;
        const position = {
            lat: parseFloat(payload.latitude),
            lng: parseFloat(payload.longitude)
        };

        if (markers[vehicleId]) {
            // Update existing marker
            markers[vehicleId].setPosition(position);

            // Optional: Animate movement smoothly instead of jumping
            // This requires more complex math or a maps animation library
        } else {
            // Create new marker
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: `Bus ${vehicleId} - IMEI: ${payload.imei}`,
                icon: {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 5,
                    rotation: payload.heading || 0, // Requires heading in payload
                    fillColor: '#FF0000',
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: '#FFFFFF'
                }
            });

            // Add InfoWindow on click
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h6 class="mb-2">Bus ID: ${vehicleId}</h6>
                        <table class="table table-sm mb-0">
                            <tr><th>Speed</th><td>${payload.speed} km/h</td></tr>
                            <tr><th>Engine</th><td>${payload.ignition_status ? '<span class="text-success">ON</span>' : '<span class="text-danger">OFF</span>'}</td></tr>
                            <tr><th>Updated</th><td>${new Date(payload.timestamp).toLocaleTimeString()}</td></tr>
                        </table>
                    </div>
                `
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });

            markers[vehicleId] = marker;
            updateActiveCount();
        }
    }

    function updateActiveCount() {
        const count = Object.keys(markers).length;
        document.getElementById('active-buses-count').innerText = count;
    }
</script>
@endsection
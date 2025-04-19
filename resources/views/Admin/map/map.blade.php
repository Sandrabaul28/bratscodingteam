@extends('layouts.Admin.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Map Location Section -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 text-success"><span class="font-weight-bold">MAP LOCATION</span></h6>
                </div>
                <div class="card-body">
                    <div id="map" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
        </div>

        <!-- Data Preview Section -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 text-success"><span class="font-weight-bold">Data Preview</span></h6>
                    <button id="downloadCsv" class="btn btn-primary"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Download CSV</button>
                </div>
                <div class="card-body" style="height: 500px; overflow-y: auto;">
                    <table id="previewTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Plant</th>
                                <th>Barangay</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Tree/Hills</th>
                                <th>Area (ha)</th>
                                <th>Encoded Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be inserted here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet & MarkerCluster Scripts -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>
    const locations = @json($locations);
    const map = L.map('map').setView([10.3763, 124.9825], 13);

    const streetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    });

    const satelliteMap = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    const labelLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    streetMap.addTo(map);
    L.control.layers({
        "🗺 OpenStreetMap": streetMap,
        "🛰 Google Satellite": satelliteMap,
        "🏷 Google Labels": labelLayer
    }).addTo(map);

    const markers = L.markerClusterGroup({ maxClusterRadius: 20, disableClusteringAtZoom: 10 });
    const usedCoordinates = {};
    const markerMap = [];

    locations.forEach((location, index) => {
        let lat = Number(location.latitude);
        let lng = Number(location.longitude);
        const imageUrl = location.image_path ? `/storage/${location.image_path}` : 'https://via.placeholder.com/150?text=No+Image';

        if (!isNaN(lat) && !isNaN(lng) && lng > 100) {
            // Avoid marker overlap with small jitter
            const key = `${lat.toFixed(6)},${lng.toFixed(6)}`;
            if (usedCoordinates[key]) {
                const jitter = 0.00005 + Math.random() * 0.0001;
                lat += jitter;
                lng += jitter;
            } else {
                usedCoordinates[key] = true;
            }

            const formattedDate = new Date(location.created_at).toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const popupContent = `
                <b>${location.first_name ?? ''} ${location.middle_name ?? ''} ${location.last_name ?? ''}</b><br>
                <b>Plant:</b> ${location.name_of_plants ?? ''}<br>
                <b>Barangay:</b> ${location.name_of_barangay ?? ''}<br>
                <b>Latitude:</b> ${location.latitude}<br>
                <b>Longitude:</b> ${location.longitude}<br>
                <b>Tree/Hills:</b> ${location.total ?? 'not available'}<br>
                <b>Area (ha):</b> ${location.total_planted_area ?? 'not available'}<br>
                <a href="${imageUrl}" target="_blank"><img src="${imageUrl}" width="150" height="100" style="margin-top:5px; border-radius:5px;"></a>
            `;

            const marker = L.marker([lat, lng]).bindPopup(popupContent);
            markerMap[index] = marker;
            markers.addLayer(marker);

            // Add data to table
            document.querySelector("#previewTable tbody").insertAdjacentHTML('beforeend', `
                <tr data-index="${index}" style="cursor:pointer;">
                    <td>${location.first_name ?? ''} ${location.middle_name ?? ''} ${location.last_name ?? ''}</td>
                    <td>${location.name_of_plants ?? ''}</td>
                    <td>${location.name_of_barangay ?? ''}</td>
                    <td>${location.latitude}</td>
                    <td>${location.longitude}</td>
                    <td>${location.total ?? 'not available'}</td>
                    <td>${location.total_planted_area ?? 'not available'}</td>
                    <td>${formattedDate}</td>
                </tr>
            `);
        }
    });

    map.addLayer(markers);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            L.marker([userLat, userLng], {
                icon: L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                    iconSize: [30, 30]
                })
            }).addTo(map).bindPopup("<b>You're here!</b>").openPopup();
            map.setView([userLat, userLng], 14);
        });
    }

    // Table row click
    document.querySelector("#previewTable tbody").addEventListener("click", e => {
        const row = e.target.closest("tr");
        if (!row) return;
        const index = row.getAttribute("data-index");
        const marker = markerMap[index];
        if (marker) {
            map.setView(marker.getLatLng(), 15);
            marker.openPopup();
        }
    });

    // CSV Download
    document.getElementById("downloadCsv").addEventListener("click", () => {
        const rows = document.querySelectorAll("#previewTable tr");
        const csvData = [];

        rows.forEach(row => {
            const cells = row.querySelectorAll("th, td");
            const line = Array.from(cells).map(cell => {
                let text = cell.textContent.trim();
                return text.includes(",") ? `"${text}"` : text;
            }).join(",");
            csvData.push(line);
        });

        const blob = new Blob([csvData.join("\n")], { type: 'text/csv' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "map_data_export.csv";
        link.click();
    });
</script>
@endsection

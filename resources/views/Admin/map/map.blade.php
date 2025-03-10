@extends('layouts.Admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 text-success"><span class="font-weight-bold">MAP LOCATION</span></h6>
        </div>
        <div class="card-body">
            <input type="text" id="searchBox" placeholder="🔍 Search barangay or farmer..." class="form-control mb-2">
            <div id="map" style="width: 100%; height: 500px;"></div>
        </div>
    </div>
</div>

<!-- Include Leaflet.js and Plugins -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Leaflet Plugins -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

<script>
    var locations = @json($locations);
    console.log("📍 Coordinates from Database:", locations);

    if (locations.length === 0) {
        console.error("❌ WALANG LUMABAS NA DATA! CHECK DATABASE!");
    }

    // ✅ Initial Map Setup
    var map = L.map('map').setView([10.3763, 124.9825], 13);

    // ✅ Base Layers (Map Options)
    var streetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    });

    var satelliteMap = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Satellite'
    });

    var labelLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Labels'
    });

    // ✅ Default Map: OpenStreetMap
    streetMap.addTo(map);

    // ✅ Layer Control (User Choice)
    var baseMaps = {
        "🗺 OpenStreetMap": streetMap,
        "🛰 Google Satellite": satelliteMap,
        "🏷 Google Labels": labelLayer
    };

    L.control.layers(baseMaps).addTo(map);

    // 📌 Clustering Markers
    var markers = L.markerClusterGroup();
    
    // 🔥 Heatmap Data
    var heatmapData = [];

    locations.forEach(function (location) {
        let lat = Number(location.latitude); 
        let lng = Number(location.longitude);
        let imageUrl = location.image_path ? `/storage/${location.image_path}` : 'https://via.placeholder.com/150?text=No+Image';

        if (!isNaN(lat) && !isNaN(lng) && lng > 100) {
            console.log("✅ Adding marker at:", lat, lng);

            // ✅ Clickable Image
            let imageTag = `
                <br><a href="${imageUrl}" target="_blank">
                    <img src="${imageUrl}" width="150" height="100" style="margin-top: 5px; border-radius: 5px; cursor: pointer;">
                </a>
            `;

            var marker = L.marker([lat, lng], { draggable: true })
                .bindPopup(`
                    <b>${location.first_name} ${location.last_name}</b><br>
                    <b>Plant:</b> ${location.name_of_plants}<br>
                    <b>Coordinates:</b> ${lat}, ${lng}<br>
                    ${imageTag}
                `);

            markers.addLayer(marker);
            heatmapData.push([lat, lng, 1]); // Add to heatmap

            // 📌 Update coordinates when dragged
            marker.on('dragend', function (e) {
                let newLatLng = marker.getLatLng();
                console.log("📍 New Position:", newLatLng.lat, newLatLng.lng);
            });
        } else {
            console.error("⚠️ Invalid coordinates detected:", location);
        }
    });

    map.addLayer(markers);

    // 🔥 Heatmap Layer
    var heat = L.heatLayer(heatmapData, {radius: 25, blur: 15, maxZoom: 17}).addTo(map);

    // 📍 Geolocation (User Location)
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            L.marker([userLat, userLng], {icon: L.icon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', iconSize: [30, 30]})})
                .addTo(map)
                .bindPopup("<b>You're here!</b>").openPopup();

            map.setView([userLat, userLng], 14);
        }, function(error) {
            console.error("❌ Geolocation Error:", error.message);
        });
    } else {
        console.error("❌ Geolocation is not supported by this browser.");
    }

    // 🔍 Search Functionality
    document.getElementById("searchBox").addEventListener("keyup", function () {
        var searchText = this.value.toLowerCase();
        var found = false;

        locations.forEach(function (location) {
            if (location.first_name.toLowerCase().includes(searchText) || 
                location.last_name.toLowerCase().includes(searchText) || 
                location.name_of_plants.toLowerCase().includes(searchText)) {
                
                map.setView([location.latitude, location.longitude], 15);

                L.circle([location.latitude, location.longitude], {
                    color: "red",
                    fillColor: "#f03",
                    fillOpacity: 0.5,
                    radius: 100
                }).addTo(map).bindPopup(`<b>${location.first_name} ${location.last_name}</b>`).openPopup();

                found = true;
            }
        });

        if (!found) {
            console.warn("🔍 No results found!");
        }
    });

    // 📏 Drawing & Measuring Tools
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: { featureGroup: drawnItems },
        draw: { polygon: true, polyline: true, rectangle: true, circle: false, marker: true }
    });
    map.addControl(drawControl);

    map.on('draw:created', function (e) {
        drawnItems.addLayer(e.layer);
        console.log("📝 New Shape Drawn:", e.layer.getLatLngs());
    });

</script>
   


@endsection

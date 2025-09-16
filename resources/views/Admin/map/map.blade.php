@extends('layouts.Admin.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Map Location Section -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 text-success"><span class="font-weight-bold">MAP LOCATION</span></h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleBlinking()" id="blinkingBtn">
                            <i class="fas fa-eye"></i> Toggle Blinking
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="resetMapView()">
                            <i class="fas fa-home"></i> Reset View
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showAllMarkers()">
                            <i class="fas fa-globe"></i> Show All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="fitBounds()">
                            <i class="fas fa-expand-arrows-alt"></i> Fit Bounds
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="map" style="width: 100%; height: 600px;"></div>
                </div>
            </div>
        </div>

        <!-- Legend and Controls Section -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 text-info"><span class="font-weight-bold">LEGEND & CONTROLS</span></h6>
                </div>
                <div class="card-body">
                    <!-- Legend -->
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-dark">Commodity Types</h6>
                        <div id="legend" class="legend-container">
                            <!-- Legend items will be populated dynamically -->
                        </div>
                    </div>
                    
                    <!-- Map Controls -->
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-dark">Map Controls</h6>
                        <div class="form-group">
                            <label for="commodityFilter">Filter by Commodity:</label>
                            <select id="commodityFilter" class="form-control form-control-sm">
                                <option value="">All Commodities</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="barangayFilter">Filter by Barangay:</label>
                            <select id="barangayFilter" class="form-control form-control-sm">
                                <option value="">All Barangays</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zoomLevel">Zoom Level:</label>
                            <input type="range" id="zoomLevel" class="form-control-range" min="8" max="18" value="11" onchange="setZoomLevel(this.value)">
                            <small class="text-muted">Current: <span id="currentZoom">11</span></small>
                        </div>
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showAllMarkers()">
                                <i class="fas fa-globe"></i> Show All Markers
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fitBounds()">
                                <i class="fas fa-expand-arrows-alt"></i> Fit to Markers
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-dark">Statistics</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h5 class="text-primary mb-0" id="totalLocations">0</h5>
                                    <small class="text-muted">Total Locations</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h5 class="text-success mb-0" id="totalCommodities">0</h5>
                                    <small class="text-muted">Commodity Types</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Enhanced Data Display Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 text-success">
                        <span class="font-weight-bold">📍 BONTOC AGRICULTURAL DATA</span>
                        <span class="badge badge-info ml-2" id="totalValidLocations">0</span>
                    </h6>
                    <div class="btn-group" role="group">
                        <button id="downloadCsv" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Download CSV
                        </button>
                        <button id="exportMap" class="btn btn-success btn-sm">
                            <i class="fas fa-map-marked-alt"></i> Export Map
                        </button>
                        <button id="refreshData" class="btn btn-info btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title" id="totalFarmers">0</h5>
                                    <p class="card-text">Total Farmers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title" id="totalCommoditiesCount">0</h5>
                                    <p class="card-text">Commodity Types</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title" id="totalBarangays">0</h5>
                                    <p class="card-text">Barangays</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title" id="totalArea">0</h5>
                                    <p class="card-text">Total Area (ha)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div style="height: 500px; overflow-y: auto;">
                        <table id="previewTable" class="table table-striped table-hover table-sm">
                            <thead class="thead-dark sticky-top">
                                <tr>
                                    <th style="width: 50px;">Icon</th>
                                    <th style="width: 200px;">Farmer Name</th>
                                    <th style="width: 150px;">Commodity</th>
                                    <th style="width: 150px;">Barangay</th>
                                    <th style="width: 200px;">Coordinates</th>
                                    <th style="width: 100px;">Tree/Hills</th>
                                    <th style="width: 100px;">Area (ha)</th>
                                    <th style="width: 120px;">Encoded Date</th>
                                    <th style="width: 100px;">Actions</th>
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
</div>

<!-- Enhanced Leaflet & MarkerCluster Scripts -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet-image/leaflet-image.js"></script>

<!-- Custom CSS for blinking animation and legend -->
<style>
    .blinking {
        animation: blink 1.5s infinite;
    }
    
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0.3; }
    }
    
    .legend-container {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        padding: 5px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    
    .legend-icon {
        width: 24px;
        height: 24px;
        margin-right: 10px;
        border-radius: 50%;
    }
    
    .legend-text {
        font-size: 12px;
        font-weight: 500;
    }
    
    .legend-count {
        margin-left: auto;
        background-color: #007bff;
        color: white;
        border-radius: 10px;
        padding: 2px 8px;
        font-size: 10px;
    }
    
    .custom-popup {
        max-width: 300px;
    }
    
    .popup-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
        margin-top: 10px;
    }
    
    .popup-header {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        padding: 10px;
        margin: -10px -10px 10px -10px;
        border-radius: 5px 5px 0 0;
    }
    
    .popup-content {
        font-size: 14px;
    }
    
    .popup-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 10px;
    }
    
    .stat-item {
        text-align: center;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 3px;
    }
    
    .stat-value {
        font-weight: bold;
        color: #007bff;
    }
    
    .stat-label {
        font-size: 11px;
        color: #6c757d;
    }
</style>

<script>
    // Global variables
    const locations = @json($locations);
    let map, markers, markerMap = [], blinkingEnabled = false;
    let commodityStats = {}, barangayStats = {};
    
    // Custom icon definitions for different commodities
    const commodityIcons = {
        'rice': { icon: '🌾', color: '#28a745', name: 'Rice' },
        'corn': { icon: '🌽', color: '#ffc107', name: 'Corn' },
        'vegetables': { icon: '🥬', color: '#20c997', name: 'Vegetables' },
        'fruits': { icon: '🍎', color: '#fd7e14', name: 'Fruits' },
        'coconut': { icon: '🥥', color: '#6f42c1', name: 'Coconut' },
        'banana': { icon: '🍌', color: '#ffc107', name: 'Banana' },
        'mango': { icon: '🥭', color: '#fd7e14', name: 'Mango' },
        'squash': { icon: '🎃', color: '#e83e8c', name: 'Squash' },
        'cucumber': { icon: '🥒', color: '#20c997', name: 'Cucumber' },
        'gabi': { icon: '🍠', color: '#6c757d', name: 'Gabi' },
        'polesitao': { icon: '🌿', color: '#28a745', name: 'Polesitao' },
        'yautia': { icon: '🌱', color: '#17a2b8', name: 'Yautia' },
        'default': { icon: '🌱', color: '#6c757d', name: 'Other' }
    };

    // Initialize map
    function initializeMap() {
        // Philippines center coordinates (Leyte area)
        map = L.map('map', {
            center: [10.3763, 124.9825], // Leyte, Philippines
            zoom: 11,
            zoomControl: true,
            maxBounds: [
                [4.0, 116.0], // Southwest corner of Philippines
                [21.0, 127.0]  // Northeast corner of Philippines
            ],
            maxBoundsViscosity: 1.0 // Prevent panning outside Philippines
        });

        // Tile layers
        const streetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            crossOrigin: true
        });

        const satelliteMap = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google'
        });

        const terrainMap = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google'
        });

        streetMap.addTo(map);
        
        L.control.layers({
            "🗺 Street Map": streetMap,
            "🛰 Satellite": satelliteMap,
            "🏔 Terrain": terrainMap
        }).addTo(map);

        // Use a regular LayerGroup so individual pins stay visible at all zoom levels
        markers = L.layerGroup();
        
        // Add zoom event listener
        map.on('zoomend', updateZoomDisplay);
    }

    // Get commodity icon and color
    function getCommodityIcon(plantName) {
        const name = plantName.toLowerCase();
        for (const [key, value] of Object.entries(commodityIcons)) {
            if (name.includes(key)) {
                return value;
            }
        }
        return commodityIcons.default;
    }

    // Create custom marker icon
    function createCustomIcon(commodity) {
        const iconData = getCommodityIcon(commodity);
        return L.divIcon({
            html: `<div style="
                background-color: ${iconData.color};
                border: 3px solid white;
                border-radius: 50%;
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                cursor: pointer;
            ">${iconData.icon}</div>`,
            className: 'custom-marker',
            iconSize: [35, 35],
            iconAnchor: [17, 17],
            popupAnchor: [0, -17]
        });
    }

    // Create enhanced popup content
    function createPopupContent(location) {
        const imageUrl = location.image_path ? `/storage/${location.image_path}` : 'https://via.placeholder.com/200x150?text=No+Image';
        const commodity = getCommodityIcon(location.name_of_plants);
        const formattedDate = new Date(location.created_at).toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        return `
            <div class="custom-popup">
                <div class="popup-header">
                    <h6 class="mb-1">${location.first_name ?? ''} ${location.middle_name ?? ''} ${location.last_name ?? ''}</h6>
                    <small>${location.name_of_barangay ?? ''}</small>
                </div>
                <div class="popup-content">
                    <div class="d-flex align-items-center mb-2">
                        <span style="font-size: 20px; margin-right: 8px;">${commodity.icon}</span>
                        <strong>${location.name_of_plants ?? ''}</strong>
                    </div>
                    <div class="popup-stats">
                        <div class="stat-item">
                            <div class="stat-value">${location.total ?? 'N/A'}</div>
                            <div class="stat-label">Tree/Hills</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${location.total_planted_area ?? 'N/A'}</div>
                            <div class="stat-label">Area (ha)</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt"></i> 
                            ${location.latitude}, ${location.longitude}
                        </small><br>
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> 
                            ${formattedDate}
                        </small>
                    </div>
                    <a href="${imageUrl}" target="_blank">
                        <img src="${imageUrl}" class="popup-image" alt="Location Image">
                    </a>
                </div>
            </div>
        `;
    }

    // Validate if coordinates are within Philippines
    function isWithinPhilippines(lat, lng) {
        // Philippines bounding box
        const philippinesBounds = {
            north: 21.0,
            south: 4.0,
            east: 127.0,
            west: 116.0
        };
        
        return lat >= philippinesBounds.south && 
               lat <= philippinesBounds.north && 
               lng >= philippinesBounds.west && 
               lng <= philippinesBounds.east;
    }

    // Add markers to map
    function addMarkers() {
        const usedCoordinates = {};
        let validLocations = 0;
        let invalidLocations = 0;
        
        locations.forEach((location, index) => {
            let lat = Number(location.latitude);
            let lng = Number(location.longitude);

            // Validate coordinates and check if within Philippines
            if (!isNaN(lat) && !isNaN(lng) && isWithinPhilippines(lat, lng)) {
                validLocations++;
                // Avoid marker overlap
                const key = `${lat.toFixed(6)},${lng.toFixed(6)}`;
                if (usedCoordinates[key]) {
                    const jitter = 0.0001 + Math.random() * 0.0002;
                    lat += jitter;
                    lng += jitter;
                } else {
                    usedCoordinates[key] = true;
                }

                // Create marker with custom icon
                const marker = L.marker([lat, lng], {
                    icon: createCustomIcon(location.name_of_plants)
                }).bindPopup(createPopupContent(location));

                markerMap[index] = marker;
                markers.addLayer(marker);

                // Update statistics
                const commodity = location.name_of_plants?.toLowerCase() || 'other';
                const barangay = location.name_of_barangay || 'Unknown';
                
                commodityStats[commodity] = (commodityStats[commodity] || 0) + 1;
                barangayStats[barangay] = (barangayStats[barangay] || 0) + 1;

                // Add to table
                addToTable(location, index, commodity);
            } else {
                invalidLocations++;
                console.warn(`Invalid coordinates for location ${index}:`, lat, lng);
            }
        });

        map.addLayer(markers);
        updateLegend();
        updateStatistics();
        updateFilters();
        
        // Show validation results
        if (invalidLocations > 0) {
            console.warn(`${invalidLocations} locations have invalid coordinates and were not displayed on the map.`);
        }
        console.log(`Successfully loaded ${validLocations} valid locations within Philippines.`);
    }

    // Add row to data table
    function addToTable(location, index, commodity) {
        const iconData = getCommodityIcon(location.name_of_plants);
        const formattedDate = new Date(location.created_at).toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        const row = document.createElement('tr');
        row.setAttribute('data-index', index);
        row.style.cursor = 'pointer';
        row.innerHTML = `
            <td style="text-align: center; font-size: 18px;" title="${iconData.name}">${iconData.icon}</td>
            <td>
                <strong>${location.first_name ?? ''} ${location.middle_name ?? ''} ${location.last_name ?? ''}</strong>
                <br><small class="text-muted">${location.name_of_association ?? 'No Association'}</small>
            </td>
            <td>
                <span class="badge" style="background-color: ${iconData.color}; color: white; font-size: 11px;">
                    ${location.name_of_plants ?? 'Unknown'}
                </span>
            </td>
            <td>
                <strong>${location.name_of_barangay ?? 'Unknown'}</strong>
                <br><small class="text-muted">Philippines</small>
            </td>
            <td>
                <small class="text-primary">
                    <i class="fas fa-map-marker-alt"></i> ${parseFloat(location.latitude).toFixed(6)}
                </small>
                <br><small class="text-primary">
                    <i class="fas fa-map-marker-alt"></i> ${parseFloat(location.longitude).toFixed(6)}
                </small>
            </td>
            <td class="text-center">
                <strong class="text-success">${location.total ?? 'N/A'}</strong>
                <br><small class="text-muted">trees/hills</small>
            </td>
            <td class="text-center">
                <strong class="text-info">${location.total_planted_area ? parseFloat(location.total_planted_area).toFixed(2) : 'N/A'}</strong>
                <br><small class="text-muted">hectares</small>
            </td>
            <td>
                <small class="text-muted">${formattedDate}</small>
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary" onclick="focusOnMarker(${index})" title="Focus on Map">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="showLocationDetails(${index})" title="View Details">
                    <i class="fas fa-info-circle"></i>
                </button>
            </td>
        `;
        
        document.querySelector("#previewTable tbody").appendChild(row);
    }

    // Update legend
    function updateLegend() {
        const legendContainer = document.getElementById('legend');
        legendContainer.innerHTML = '';

        Object.entries(commodityStats).forEach(([commodity, count]) => {
            const iconData = getCommodityIcon(commodity);
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';
            legendItem.innerHTML = `
                <div class="legend-icon" style="background-color: ${iconData.color}; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                    ${iconData.icon}
                </div>
                <div class="legend-text">${iconData.name}</div>
                <div class="legend-count">${count}</div>
            `;
            legendContainer.appendChild(legendItem);
        });
    }

    // Update statistics
    function updateStatistics() {
        const validLocations = locations.filter(loc => {
            const lat = Number(loc.latitude);
            const lng = Number(loc.longitude);
            return !isNaN(lat) && !isNaN(lng) && isWithinPhilippines(lat, lng);
        });
        
        const uniqueFarmers = new Set(validLocations.map(loc => loc.farmer_id || (loc.first_name + ' ' + loc.last_name))).size;
        const uniqueBarangays = new Set(validLocations.map(loc => loc.name_of_barangay).filter(Boolean)).size;
        const totalArea = validLocations.reduce((sum, loc) => {
            const area = parseFloat(loc.total_planted_area) || 0;
            return sum + area;
        }, 0);
        
        // Update summary cards
        document.getElementById('totalValidLocations').textContent = validLocations.length;
        document.getElementById('totalFarmers').textContent = uniqueFarmers;
        document.getElementById('totalCommoditiesCount').textContent = Object.keys(commodityStats).length;
        document.getElementById('totalBarangays').textContent = uniqueBarangays;
        document.getElementById('totalArea').textContent = totalArea.toFixed(2);
        
        // Update legend statistics
        document.getElementById('totalLocations').textContent = validLocations.length;
        document.getElementById('totalCommodities').textContent = Object.keys(commodityStats).length;
    }

    // Update filter dropdowns
    function updateFilters() {
        const commodityFilter = document.getElementById('commodityFilter');
        const barangayFilter = document.getElementById('barangayFilter');

        // Clear existing options
        commodityFilter.innerHTML = '<option value="">All Commodities</option>';
        barangayFilter.innerHTML = '<option value="">All Barangays</option>';

        // Add commodity options
        Object.entries(commodityStats).forEach(([commodity, count]) => {
            const iconData = getCommodityIcon(commodity);
            const option = document.createElement('option');
            option.value = commodity;
            option.textContent = `${iconData.icon} ${iconData.name} (${count})`;
            commodityFilter.appendChild(option);
        });

        // Add barangay options
        Object.entries(barangayStats).forEach(([barangay, count]) => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = `${barangay} (${count})`;
            barangayFilter.appendChild(option);
        });
    }

    // Toggle blinking animation
    function toggleBlinking() {
        blinkingEnabled = !blinkingEnabled;
        const button = event.target;
        
        if (blinkingEnabled) {
            button.innerHTML = '<i class="fas fa-eye-slash"></i> Stop Blinking';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-primary');
            
            // Add blinking class to all markers using CSS animation
            markerMap.forEach(marker => {
                if (marker) {
                    // Create a blinking effect by toggling marker visibility
                    let isVisible = true;
                    const blinkInterval = setInterval(() => {
                        if (blinkingEnabled) {
                            marker.setOpacity(isVisible ? 0.3 : 1);
                            isVisible = !isVisible;
                        } else {
                            clearInterval(blinkInterval);
                            marker.setOpacity(1);
                        }
                    }, 750);
                    
                    // Store interval ID for cleanup
                    marker._blinkInterval = blinkInterval;
                }
            });
        } else {
            button.innerHTML = '<i class="fas fa-eye"></i> Toggle Blinking';
            button.classList.remove('btn-primary');
            button.classList.add('btn-outline-primary');
            
            // Remove blinking effect from all markers
            markerMap.forEach(marker => {
                if (marker && marker._blinkInterval) {
                    clearInterval(marker._blinkInterval);
                    marker._blinkInterval = null;
                    marker.setOpacity(1);
                }
            });
        }
    }

    // Reset map view to Philippines
    function resetMapView() {
        map.setView([10.3763, 124.9825], 11); // Leyte, Philippines
    }
    
    // Show all markers (clear filters)
    function showAllMarkers() {
        // Clear filter dropdowns
        document.getElementById('commodityFilter').value = '';
        document.getElementById('barangayFilter').value = '';
        
        // Show all markers
        markers.clearLayers();
        locations.forEach((location, index) => {
            if (markerMap[index]) {
                const lat = Number(location.latitude);
                const lng = Number(location.longitude);
                
                if (!isNaN(lat) && !isNaN(lng) && isWithinPhilippines(lat, lng)) {
                    markers.addLayer(markerMap[index]);
                }
            }
        });
        
        // Show all table rows
        const tableRows = document.querySelectorAll("#previewTable tbody tr");
        tableRows.forEach(row => {
            row.style.display = '';
        });
        
        // Update statistics
        updateStatistics();
    }
    
    // Fit map bounds to show all markers
    function fitBounds() {
        if (markers.getLayers && markers.getLayers().length === 0) {
            alert('No markers to fit bounds. Please show all markers first.');
            return;
        }
        
        const group = new L.featureGroup(markers.getLayers ? markers.getLayers() : markers.getLayers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
    
    // Set zoom level
    function setZoomLevel(level) {
        const zoom = parseInt(level);
        map.setZoom(zoom);
        document.getElementById('currentZoom').textContent = zoom;
    }
    
    // Update zoom level display when map zoom changes
    function updateZoomDisplay() {
        const currentZoom = map.getZoom();
        document.getElementById('zoomLevel').value = currentZoom;
        document.getElementById('currentZoom').textContent = currentZoom;
    }

    // Show location details in a modal
    function showLocationDetails(index) {
        const location = locations[index];
        if (!location) return;
        
        const commodity = getCommodityIcon(location.name_of_plants);
        const formattedDate = new Date(location.created_at).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const modalContent = `
            <div class="modal fade" id="locationDetailsModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(45deg, ${commodity.color}, #20c997); color: white;">
                            <h5 class="modal-title">
                                <span style="font-size: 24px; margin-right: 10px;">${commodity.icon}</span>
                                ${location.first_name ?? ''} ${location.middle_name ?? ''} ${location.last_name ?? ''}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-seedling text-success"></i> Commodity Information</h6>
                                    <p><strong>Type:</strong> ${location.name_of_plants ?? 'Unknown'}</p>
                                    <p><strong>Tree/Hills:</strong> ${location.total ?? 'N/A'}</p>
                                    <p><strong>Area:</strong> ${location.total_planted_area ? parseFloat(location.total_planted_area).toFixed(2) + ' hectares' : 'N/A'}</p>
                                    
                                    <h6 class="mt-3"><i class="fas fa-map-marker-alt text-primary"></i> Location Details</h6>
                                    <p><strong>Barangay:</strong> ${location.name_of_barangay ?? 'Unknown'}</p>
                                    <p><strong>Association:</strong> ${location.name_of_association ?? 'No Association'}</p>
                                    <p><strong>Country:</strong> Philippines 🇵🇭</p>
                                    <p><strong>Coordinates:</strong><br>
                                        <small class="text-muted">Lat: ${parseFloat(location.latitude).toFixed(6)}</small><br>
                                        <small class="text-muted">Lng: ${parseFloat(location.longitude).toFixed(6)}</small>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-calendar text-info"></i> Record Information</h6>
                                    <p><strong>Encoded Date:</strong> ${formattedDate}</p>
                                    <p><strong>Status:</strong> <span class="badge badge-success">Active</span></p>
                                    
                                    ${location.image_path ? `
                                        <h6 class="mt-3"><i class="fas fa-image text-warning"></i> Location Image</h6>
                                        <img src="/storage/${location.image_path}" class="img-fluid rounded" alt="Location Image" style="max-height: 200px;">
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="focusOnMarker(${index}); $('#locationDetailsModal').modal('hide');">
                                <i class="fas fa-map-marker-alt"></i> Show on Map
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('locationDetailsModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalContent);
        
        // Show modal
        $('#locationDetailsModal').modal('show');
    }

    // Focus on specific marker
    function focusOnMarker(index) {
        const marker = markerMap[index];
        if (marker) {
            map.setView(marker.getLatLng(), 16);
            marker.openPopup();
        }
    }

    // Filter markers
    function filterMarkers() {
        const commodityFilter = document.getElementById('commodityFilter').value;
        const barangayFilter = document.getElementById('barangayFilter').value;
        
        // Clear all markers from the cluster group
        markers.clearLayers();
        
        // Filter and add markers back
        locations.forEach((location, index) => {
            const commodity = location.name_of_plants?.toLowerCase() || 'other';
            const barangay = location.name_of_barangay || 'Unknown';
            
            let showMarker = true;
            
            // Check commodity filter
            if (commodityFilter && !commodity.includes(commodityFilter)) {
                showMarker = false;
            }
            
            // Check barangay filter
            if (barangayFilter && barangay !== barangayFilter) {
                showMarker = false;
            }
            
            // Add marker if it passes filters and is within Philippines
            if (showMarker && markerMap[index]) {
                const lat = Number(location.latitude);
                const lng = Number(location.longitude);
                
                if (!isNaN(lat) && !isNaN(lng) && isWithinPhilippines(lat, lng)) {
                    markers.addLayer(markerMap[index]);
                }
            }
        });
        
        // Update table visibility
        updateTableVisibility(commodityFilter, barangayFilter);
    }
    
    // Update table visibility based on filters
    function updateTableVisibility(commodityFilter, barangayFilter) {
        const tableRows = document.querySelectorAll("#previewTable tbody tr");
        
        tableRows.forEach(row => {
            const index = parseInt(row.getAttribute("data-index"));
            const location = locations[index];
            
            if (!location) {
                row.style.display = 'none';
                return;
            }
            
            const commodity = location.name_of_plants?.toLowerCase() || 'other';
            const barangay = location.name_of_barangay || 'Unknown';
            
            let showRow = true;
            
            // Check commodity filter
            if (commodityFilter && !commodity.includes(commodityFilter)) {
                showRow = false;
            }
            
            // Check barangay filter
            if (barangayFilter && barangay !== barangayFilter) {
                showRow = false;
            }
            
            // Show/hide row
            row.style.display = showRow ? '' : 'none';
        });
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        addMarkers();
        
        // Add user location if available
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                
                L.marker([userLat, userLng], {
                    icon: L.icon({
                        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                        iconSize: [30, 30]
                    })
                }).addTo(map).bindPopup("<b>📍 You're here!</b>").openPopup();
            });
        }

        // Table row click events
        document.querySelector("#previewTable tbody").addEventListener("click", e => {
            const row = e.target.closest("tr");
            if (!row) return;
            const index = parseInt(row.getAttribute("data-index"));
            focusOnMarker(index);
        });

        // Filter events
        document.getElementById('commodityFilter').addEventListener('change', filterMarkers);
        document.getElementById('barangayFilter').addEventListener('change', filterMarkers);

        // CSV Download
        document.getElementById("downloadCsv").addEventListener("click", () => {
            try {
                const csvData = [];
                
                // Add header row
                csvData.push([
                    'Icon',
                    'Farmer Name',
                    'Commodity',
                    'Barangay',
                    'Latitude',
                    'Longitude',
                    'Tree/Hills',
                    'Area (ha)',
                    'Encoded Date',
                    'Association'
                ].join(','));
                
                // Add data rows (only visible rows)
                const visibleRows = document.querySelectorAll("#previewTable tbody tr:not([style*='display: none'])");
                visibleRows.forEach(row => {
                    const cells = row.querySelectorAll("td");
                    if (cells.length >= 9) {
                        const line = [
                            cells[0].textContent.trim(), // Icon
                            cells[1].textContent.trim().replace(/\n/g, ' '), // Farmer Name
                            cells[2].textContent.trim(), // Commodity
                            cells[3].textContent.trim().replace(/\n/g, ' '), // Barangay
                            cells[4].textContent.trim().split('\n')[0].replace('📍 ', ''), // Latitude
                            cells[4].textContent.trim().split('\n')[1].replace('📍 ', ''), // Longitude
                            cells[5].textContent.trim().split('\n')[0], // Tree/Hills
                            cells[6].textContent.trim().split('\n')[0], // Area
                            cells[7].textContent.trim(), // Date
                            cells[1].textContent.trim().split('\n')[1] || 'No Association' // Association
                        ].map(field => {
                            // Escape commas and quotes
                            if (field.includes(',') || field.includes('"') || field.includes('\n')) {
                                return `"${field.replace(/"/g, '""')}"`;
                            }
                            return field;
                        }).join(',');
                        
                        csvData.push(line);
                    }
                });

                const blob = new Blob([csvData.join("\n")], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = `philippines_agricultural_data_${new Date().toISOString().split('T')[0]}.csv`;
                link.click();
                
                // Show success message
                alert(`CSV exported successfully! ${visibleRows.length} records exported.`);
            } catch (error) {
                console.error('CSV export error:', error);
                alert('Error exporting CSV. Please try again.');
            }
        });

        // Map Export (screenshot)
        document.getElementById("exportMap").addEventListener("click", () => {
            try {
                // Force a CORS-friendly base layer for export
                const wasStreet = map.hasLayer(streetMap);
                const wasSat = map.hasLayer(satelliteMap);
                const wasTer = map.hasLayer(terrainMap);

                if (!wasStreet) {
                    map.addLayer(streetMap);
                }
                if (wasSat) map.removeLayer(satelliteMap);
                if (wasTer) map.removeLayer(terrainMap);

                const render = () => {
                    leafletImage(map, function(err, mapCanvas) {
                        // Restore previous base layer after rendering
                        if (!wasStreet) map.removeLayer(streetMap);
                        if (wasSat) map.addLayer(satelliteMap);
                        if (wasTer) map.addLayer(terrainMap);

                        if (err || !mapCanvas) {
                            console.error('leaflet-image error:', err);
                            alert('Unable to render the map for export. Try again.');
                            return;
                        }

                        // Compose final canvas with title and stats above the map image
                        const paddingTop = 90; // space for header text
                        const paddingBottom = 30; // space for footer
                        const outCanvas = document.createElement('canvas');
                        outCanvas.width = mapCanvas.width;
                        outCanvas.height = mapCanvas.height + paddingTop + paddingBottom;
                        const ctx = outCanvas.getContext('2d');

                        // Background
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, outCanvas.width, outCanvas.height);

                        // Header text
                        ctx.fillStyle = '#000';
                        ctx.font = 'bold 24px Arial';
                        ctx.textAlign = 'center';
                        ctx.fillText('Philippines Agricultural Map', outCanvas.width / 2, 35);
                        ctx.font = '16px Arial';
                        ctx.fillStyle = '#666';
                        ctx.fillText(`Generated on ${new Date().toLocaleDateString()}`, outCanvas.width / 2, 60);

                        // Stats (left aligned)
                        ctx.font = '14px Arial';
                        ctx.fillStyle = '#333';
                        ctx.textAlign = 'left';
                        ctx.fillText(`Total Locations: ${document.getElementById('totalValidLocations').textContent}`, 20, 85);
                        ctx.fillText(`Total Farmers: ${document.getElementById('totalFarmers').textContent}`, 280, 85);
                        ctx.fillText(`Commodity Types: ${document.getElementById('totalCommoditiesCount').textContent}`, 480, 85);
                        ctx.fillText(`Total Area: ${document.getElementById('totalArea').textContent} ha`, 700, 85);

                        // Draw the map image
                        ctx.drawImage(mapCanvas, 0, paddingTop);

                        // Footer note
                        ctx.font = '12px Arial';
                        ctx.fillStyle = '#999';
                        ctx.textAlign = 'center';
                        ctx.fillText('Generated from the interactive map', outCanvas.width / 2, outCanvas.height - 10);

                        // Download the composed image
                        const link = document.createElement('a');
                        link.download = `philippines_agricultural_map_${new Date().toISOString().split('T')[0]}.png`;
                        link.href = outCanvas.toDataURL('image/png');
                        link.click();
                    });
                };

                // Try to wait for tiles if we just switched base layer
                streetMap.once('load', render);
                setTimeout(render, 800);
            } catch (error) {
                console.error('Map export error:', error);
                alert('Error exporting map. Please try again.');
            }
        });
    });
</script>
@endsection

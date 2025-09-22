@extends('layouts.Admin.app')

@section('content')
<style>
    
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }
    
    #recordedCount, #uploadedCount {
        font-weight: bold;
        font-size: 1em;
    }
    
    .count-display {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border-radius: 20px;
        padding: 0.5em 1em;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .pagination-info {
        color: #6c757d;
        font-size: 0.9em;
        padding: 0.5em 0;
    }
    
    /* Enhanced Pagination Styling */
    .pagination {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #4e73df;
        background-color: #fff;
        border: 1px solid #dddfeb;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 0.35rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
    }
    
    .pagination .page-link:hover {
        color: #224abe;
        background-color: #eaecf4;
        border-color: #dddfeb;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .pagination .page-item.active .page-link {
        color: #fff;
        background-color: #4e73df;
        border-color: #4e73df;
        box-shadow: 0 2px 4px rgba(78, 115, 223, 0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #858796;
        background-color: #fff;
        border-color: #dddfeb;
        cursor: not-allowed;
    }
    
    .pagination .page-item:first-child .page-link {
        border-top-left-radius: 0.35rem;
        border-bottom-left-radius: 0.35rem;
    }
    
    .pagination .page-item:last-child .page-link {
        border-top-right-radius: 0.35rem;
        border-bottom-right-radius: 0.35rem;
    }
</style>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 text-success">
                <span class="font-weight-bold">INVENTORY VALUED CROPS</span>
            </h6>
            <div class="d-flex">
                <select id="month" class="form-control mr-2">
                    @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                    @endforeach
                </select>

                <select id="year" class="form-control mr-2">
                    @foreach (range(date('Y'), date('Y', strtotime('-10 years'))) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <a href="#" id="export-button" class="btn btn-success">
                    <i class="fa fa-th" aria-hidden="true"></i><br>Excel
                </a>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.getElementById('export-button').onclick = function(event) {
                event.preventDefault(); // Prevent the default action of the link

                var month = document.getElementById('month').value;
                var year = document.getElementById('year').value;

                // Construct URLs for preview and download
                var previewUrl = "{{ route('admin.inventory.previewMonthlyInventory', ['month' => '__month__', 'year' => '__year__']) }}";
                var downloadUrl = "{{ route('admin.inventory.exportMonthlyInventoryExcel', ['month' => '__month__', 'year' => '__year__']) }}";

                // Replace the placeholders in the URLs
                previewUrl = previewUrl.replace('__month__', month).replace('__year__', year);
                downloadUrl = downloadUrl.replace('__month__', month).replace('__year__', year);

                fetch(previewUrl)
                    .then(response => response.text())
                    .then(html => {
                        // Ensure the HTML is loaded into the modal
                        document.getElementById('previewContent').innerHTML = html;

                        // Set the download URL for the Excel file
                        document.getElementById('download-button').href = downloadUrl;

                        // Show the modal
                        $('#previewModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error fetching preview:', error);
                        alert('There was an error loading the preview.');
                    });

            };
        </script>

        <!-- Modal for Previewing Inventory Data -->
        <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">Preview Inventory Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="previewContent" style="max-height: 400px; overflow:auto; white-space: nowrap;">
                            <!-- Data will be loaded here dynamically -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="#" id="download-button" class="btn btn-success">Download Excel</a>
                    </div>
                </div>
            </div>
        </div>


        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success message -->
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" id="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Inventory form -->
            <form action="{{ route('admin.inventory.store') }}" method="POST">
                @csrf

                <div id="inventory-cards">
                    <div class="inventory-card card mb-3">
                        <div class="card-body">
                            <div class="form-row">
                                <!-- Farmer Selection -->
                                <div class="col-md-4 mb-3">
                                    <label for="farmer_name">Farmer <span style="color: red;">*</span></label>
                                    <input list="farmer_list" id="farmer_name" name="farmer_name" class="form-control @error('farmer_name') is-invalid @enderror" required placeholder="Select Farmer">
                                    <datalist id="farmer_list">
                                        @foreach($farmers as $farmer)
                                            <option 
                                                value="{{ $farmer->first_name }} {{ $farmer->last_name }}"
                                                data-id="{{ $farmer->id }}"
                                                data-affiliation-id="{{ $farmer->affiliation->id ?? '' }}"
                                                data-affiliation-name="{{ $farmer->affiliation->name_of_barangay ?? '' }} - {{ $farmer->affiliation->name_of_association ?? '' }}">
                                            </option>
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="farmer_id" id="farmer_id">
                                    @error('farmer_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Plant Selection -->
                                <div class="col-md-4 mb-3">
                                    <label for="plant_name">Commodity <span style="color: red;">*</span></label>
                                    <input list="plant_list" id="plant_name" name="plant_name" class="form-control @error('plant_name') is-invalid @enderror" required placeholder="Select Commodity">
                                    <datalist id="plant_list">
                                        @foreach($plants as $plant)
                                            <option value="{{ $plant->name_of_plants }}" data-id="{{ $plant->id }}"></option>
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="plant_id" id="plant_id">
                                    @error('plant_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Affiliation Selection (Auto-filled) -->
                                <div class="col-md-4 mb-3">
                                    <label for="affiliation_name">Affiliation</label>
                                    <input type="text" id="affiliation_name" name="affiliation_name" class="form-control @error('affiliation_name') is-invalid @enderror" readonly placeholder="Affiliation will be auto-filled">
                                    <input type="hidden" name="affiliation_id" id="affiliation_id">
                                    @error('affiliation_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Script to Auto-Fill IDs and Affiliation -->
                                <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const farmerInput = document.getElementById('farmer_name');
                                    const farmerIdField = document.getElementById('farmer_id');
                                    const plantInput = document.getElementById('plant_name');
                                    const plantIdField = document.getElementById('plant_id');
                                    const affiliationInput = document.getElementById('affiliation_name');
                                    const affiliationIdField = document.getElementById('affiliation_id');

                                    farmerInput.addEventListener('input', function () {
                                        const selectedFarmer = Array.from(document.querySelectorAll('#farmer_list option')).find(option => option.value === farmerInput.value);
                                        if (selectedFarmer) {
                                            farmerIdField.value = selectedFarmer.getAttribute('data-id');
                                            const affId = selectedFarmer.getAttribute('data-affiliation-id');
                                            const affName = selectedFarmer.getAttribute('data-affiliation-name');
                                            
                                            affiliationInput.value = affName;
                                            affiliationIdField.value = affId;
                                        } else {
                                            farmerIdField.value = '';
                                            affiliationInput.value = '';
                                            affiliationIdField.value = '';
                                        }
                                    });

                                    plantInput.addEventListener('input', function () {
                                        const selectedPlant = Array.from(document.querySelectorAll('#plant_list option')).find(option => option.value === plantInput.value);
                                        if (selectedPlant) {
                                            plantIdField.value = selectedPlant.getAttribute('data-id');
                                        } else {
                                            plantIdField.value = '';
                                        }
                                    });
                                });
                                </script>



                                <div class="col-md-2 mb-3">
                                    <label for="planting_density">Planting Density(ha)<span style="color: red;">*</span></label>
                                    <input type="number" name="planting_density" class="form-control @error('planting_density') is-invalid @enderror" required onchange="calculateAreaHarvested(this)">
                                    @error('planting_density')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="production_volume">Prodn. Vol/ Hectare<span style="color: red;">*</span></label>
                                    <input type="number" name="production_volume" class="form-control @error('production_volume') is-invalid @enderror" required onchange="calculateProductionVolume(this)">
                                    @error('production_volume')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="newly_planted">Newly Planted</label>
                                    <input type="number" name="newly_planted" class="form-control" onchange="calculateTotals(this)">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="vegetative">Vegetative</label>
                                    <input type="number" name="vegetative" class="form-control" onchange="calculateTotals(this)">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="reproductive">Reproductive</label>
                                    <input type="number" name="reproductive" class="form-control" onchange="calculateTotals(this)">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="maturity_harvested">Maturity/Harvested</label>
                                    <input type="number" name="maturity_harvested" class="form-control" onchange="calculateAreaHarvested(this); calculateTotals(this)">
                                </div>

                                <!-- Newly planted divide -->
                                 <div class="col-md-2 mb-3">
                                    <label for="newly_planted_divided">Newly Planted</label>
                                    <input type="number" name="newly_planted_divided" class="form-control" readonly>
                                </div>
                                <!-- Vegetative divide -->

                                 <div class="col-md-2 mb-3">
                                    <label for="vegetative_divided">Vegetative</label>
                                    <input type="number" name="vegetative_divided" class="form-control" readonly>
                                </div>

                                <!-- Reproduction divide -->

                                 <div class="col-md-2 mb-3">
                                    <label for="reproductive_divided">Reproductive</label>
                                    <input type="number" name="reproductive_divided" class="form-control" readonly>
                                </div>

                                <!-- Maturity divide -->

                                 <div class="col-md-2 mb-3">
                                    <label for="maturity_harvested_divided">Maturity/Harvested</label>
                                    <input type="number" name="maturity_harvested_divided" class="form-control" readonly>
                                </div>


                                <div class="col-md-2 mb-3">
                                    <label for="area_harvested">Area Harvested (ha)</label>
                                    <input type="number" name="area_harvested" class="form-control" readonly>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="final_production_volume">Production Volume(MT)</label>
                                    <input type="number" name="final_production_volume" class="form-control" readonly>
                                </div>

                                <!-- Total/ Planted Area -->
                                <div class="col-md-2 mb-3">
                                    <label for="total_planted_area">Total/ Planted Area</label>
                                    <input type="number" id="total_planted_area" name="total_planted_area" step="0.0001" class="form-control" readonly>
                                </div>

                                <!-- Total/ #Hill/Puno -->
                                <div class="col-md-2 mb-3">
                                    <label for="total">Total/ #Hill/Puno</label>
                                    <input type="number" id="total" name="total" class="form-control" step="1" readonly>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Submit Inventory</button>
            </form>
        </div>
    </div>

    <!-- Excel Upload Section -->
    <div class="card shadow mb-4 mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 text-primary">
                <span class="font-weight-bold">UPLOAD EXCEL FILE</span>
            </h6>
        </div>
        
        <!-- Upload Instructions and Warnings -->
        <div class="alert alert-info mx-3 mt-3 mb-0">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle"></i> Upload Instructions & Requirements
            </h6>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success"><i class="fas fa-check-circle"></i> File Requirements:</h6>
                    <ul class="mb-2">
                        <li><strong>Format:</strong> .xlsx or .xls files only</li>
                        <li><strong>Size:</strong> Maximum 50MB</li>
                        <li><strong>Rows:</strong> Can handle up to 10,000+ records</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Common Errors to Avoid:</h6>
                    <ul class="mb-2">
                        <li>Don't upload .csv files (use .xlsx instead)</li>
                        <li>Ensure first row contains headers</li>
                        <li>Don't leave required columns empty</li>
                        <li>Check file isn't corrupted or password-protected</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h6 class="text-primary"><i class="fas fa-table"></i> Expected Excel Format:</h6>
                    <small class="text-muted">
                        Column A: Barangay | Column B: Commodity | Column C: Farmer | Column D: Planting Density | 
                        Column E: Production Vol/Hectare | Column F: Newly Planted | Column G: Vegetative | 
                        Column H: Reproductive | Column I: Maturity/Harvested | Column J: Total
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventory.uploadExcel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="excel_file">Select Excel File (.xlsx)</label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            @error('excel_file')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Upload an Excel file with the same format as the sample. Maximum file size: 50MB.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-upload"></i> Upload Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<!-- Recorded inventory table -->
<div class="card shadow mb-4 mt-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col-md-6">
                <h6 class="m-0 text-success"><span class="font-weight-bold">RECORDED INVENTORY</span></h6>
            </div>
            <div class="col-md-6 text-right">
                <span class="badge badge-primary count-display" id="recordedCount">Total Records: {{ $inventories->total() }}</span>
            </div>
        </div>
        <br>
        <!-- Search Bar -->
        <input type="text" id="searchBar" class="form-control form-control-sm" placeholder="Search by Barangay, Association, Plant Name, Last Name, or First Name" style="max-width: 300px;" onkeyup="filterInventoryTable()">
                <!-- JavaScript for Filtering Recorded Inventory Table -->
                <!-- JavaScript for Filtering Inventory Table -->
        <script>
            function filterInventoryTable() {
                const query = document.getElementById("searchBar").value.toLowerCase();
                const table = document.getElementById("recorded-inventory-table");
                const rows = table.getElementsByTagName("tr");
                let visibleCount = 0;

                for (let i = 1; i < rows.length; i++) {
                    const barangayCell = rows[i].getElementsByTagName("td")[1];
                    const associationCell = rows[i].getElementsByTagName("td")[2];
                    const plantCell = rows[i].getElementsByTagName("td")[3];
                    const surnameCell = rows[i].getElementsByTagName("td")[4];
                    const firstnameCell = rows[i].getElementsByTagName("td")[5];

                    if (barangayCell && associationCell && plantCell && surnameCell && firstnameCell) {
                        const barangay = barangayCell.textContent.toLowerCase();
                        const association = associationCell.textContent.toLowerCase();
                        const plant = plantCell.textContent.toLowerCase();
                        const surname = surnameCell.textContent.toLowerCase();
                        const firstname = firstnameCell.textContent.toLowerCase();

                        // Check if the query matches any of the fields
                        if (
                            barangay.includes(query) || 
                            association.includes(query) || 
                            plant.includes(query) || 
                            surname.includes(query) || 
                            firstname.includes(query)
                        ) {
                            rows[i].style.display = ""; // Show row
                            visibleCount++;
                        } else {
                            rows[i].style.display = "none"; // Hide row
                        }
                    }
                }
                
                // Update the count display
                const totalRecords = {{ $inventories->total() }};
                const countElement = document.getElementById('recordedCount');
                if (query === '') {
                    countElement.textContent = `Total Records: ${totalRecords}`;
                } else {
                    countElement.textContent = `Showing: ${visibleCount} of ${totalRecords} records`;
                }
            }
        </script>
    </div>
        

    <!-- JavaScript for Filtering Uploaded Inventory Table -->
    <script>
        function filterUploadedTable() {
            const query = document.getElementById("searchUploadedBar").value.toLowerCase();
            const table = document.getElementById("uploaded-inventory-table");
            const rows = table.getElementsByTagName("tr");
            let visibleCount = 0;

            for (let i = 1; i < rows.length; i++) {
                const barangayCell = rows[i].getElementsByTagName("td")[1]; // Updated index for checkbox column
                const commodityCell = rows[i].getElementsByTagName("td")[2];
                const farmerCell = rows[i].getElementsByTagName("td")[3];

                if (barangayCell && commodityCell && farmerCell) {
                    const barangay = barangayCell.textContent.toLowerCase();
                    const commodity = commodityCell.textContent.toLowerCase();
                    const farmer = farmerCell.textContent.toLowerCase();

                    // Check if the query matches any of the fields
                    if (
                        barangay.includes(query) || 
                        commodity.includes(query) || 
                        farmer.includes(query)
                    ) {
                        rows[i].style.display = ""; // Show row
                        visibleCount++;
                    } else {
                        rows[i].style.display = "none"; // Hide row
                    }
                }
            }
            
            // Update the count display
            const totalRecords = {{ $uploadedInventories->total() }};
            const countElement = document.getElementById('uploadedCount');
            if (query === '') {
                countElement.textContent = `Total Records: ${totalRecords}`;
            } else {
                countElement.textContent = `Showing: ${visibleCount} of ${totalRecords} records`;
            }
        }

    </script>

    <div class="card-body">
        <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
            <table class="table table-bordered" id="recorded-inventory-table">
                <thead>
                    <tr>
                        <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Barangay</th>
                        <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Association</th>
                        <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Commodity</th>
                        <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Farmers</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Planting Density (ha)</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Production Vol/ Hectare (MT)</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity Harvested</th>
                        <th style="position: sticky; top: 0; background: greenyellow; z-index: 1;">Total/ #hill/puno</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted/ha</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative/ha</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive/ha</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity Harvested/ha</th>
                        <th style="position: sticky; top: 0; background: greenyellow; z-index: 1;">Total/ Planted Area</th>
                        <th style="position: sticky; top: 0; background: orange; z-index: 1;">Area Harvested</th>
                        <th style="position: sticky; top: 0; background: orange; z-index: 1;">Production Volume (MT)</th>
                        <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventories as $inventory)
                        <tr>
                            <td style="background: lightyellow;">{{ $inventory->affiliation->name_of_barangay }}</td>
                            <td style="background: lightyellow;">{{ $inventory->affiliation->name_of_association }}</td>
                            <td style="background: lightyellow;">{{ $inventory->plant->name_of_plants }}</td>
                            <td style="background: lightyellow;">{{ $inventory->farmer->first_name }} {{ $inventory->farmer->last_name }}</td>
                            <td>{{ number_format($inventory->planting_density) }}</td>
                            <td>{{ number_format($inventory->production_volume) }}</td>
                            <td>{{ number_format($inventory->newly_planted, 0) }}</td>
                            <td>{{ number_format($inventory->vegetative, 0) }}</td>
                            <td>{{ number_format($inventory->reproductive, 0) }}</td>
                            <td>{{ number_format($inventory->maturity_harvested, 0) }}</td>
                            <td style="background: lightgreen;">{{ number_format($inventory->total, 0) }}</td>

                            <!-- Display newly planted divided field -->
                            <td>{{ number_format($inventory->newly_planted_divided, 4) }}</td>
                            <!-- Display vegetative divided field -->
                            <td>{{ number_format($inventory->vegetative_divided, 4) }}</td>
                            <!-- Display reproductive divided field -->
                            <td>{{ number_format($inventory->reproductive_divided, 4) }}</td>
                            <!-- Display maturity harvested divided field -->
                            <td>{{ number_format($inventory->maturity_harvested_divided, 4) }}</td>
                            <td style="background: lightgreen;">{{ number_format($inventory->total_planted_area, 4) }}</td>


                            <td style="background: #ffb768;">{{ number_format($inventory->area_harvested, 4) }}</td>
                            <td style="background: #ffb768;">{{ number_format($inventory->final_production_volume, 4) }}</td>

                            <td>
                                <button class="btn btn-warning" onclick="loadEditModal({{ $inventory->id }})"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $inventory->id }}"><i class="fas fa-trash"></i></button>
                            </td>




                                <!-- ... -->
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $inventory->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this inventory record?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('admin.inventory.destroy', $inventory->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    <small class="text-muted">
                        Showing {{ $inventories->firstItem() ?? 0 }} to {{ $inventories->lastItem() ?? 0 }} of {{ $inventories->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $inventories->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Uploaded Inventory Data Table -->
@if($uploadedInventories->count() > 0)
<div class="card shadow mb-4 mt-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col-md-6">
                <h6 class="m-0 text-info">
                    <span class="font-weight-bold">UPLOADED INVENTORY DATA</span>
                </h6>
            </div>
            <div class="col-md-6 text-right">
                <span class="badge badge-info count-display" id="uploadedCount">Total Records: {{ $uploadedInventories->total() }}</span>
            </div>
        </div>
        <br>
        <!-- Search Bar for Uploaded Data -->
        <input type="text" id="searchUploadedBar" class="form-control form-control-sm" 
               placeholder="Search uploaded data by Barangay, Commodity, or Farmer" 
               style="max-width: 300px;" onkeyup="filterUploadedTable()">
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
            <table class="table table-bordered" id="uploaded-inventory-table">
                <thead>
                    <tr>
                        <th style="position: sticky; top: 0; background: lightblue; z-index: 1;">Barangay</th>
                        <th style="position: sticky; top: 0; background: lightblue; z-index: 1;">Commodity</th>
                        <th style="position: sticky; top: 0; background: lightblue; z-index: 1;">Farmer</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Planting Density (ha)</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Production Vol/ Hectare (MT)</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity</th>
                        <th style="position: sticky; top: 0; background: lightgreen; z-index: 1;">Total</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted/ha</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative/ha</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive/ha</th>
                        <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity/ha</th>
                        <th style="position: sticky; top: 0; background: lightgreen; z-index: 1;">Total Planted Area</th>
                        <th style="position: sticky; top: 0; background: orange; z-index: 1;">Area Harvested</th>
                        <th style="position: sticky; top: 0; background: orange; z-index: 1;">Production Volume (MT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($uploadedInventories as $uploaded)
                        <tr>
                            <td style="background: lightcyan;">{{ $uploaded->barangay }}</td>
                            <td style="background: lightcyan;">{{ $uploaded->commodity }}</td>
                            <td style="background: lightcyan;">{{ $uploaded->farmer }}</td>
                            <td>{{ number_format($uploaded->planting_density) }}</td>
                            <td>{{ number_format($uploaded->production_vol_hectare) }}</td>
                            <td>{{ number_format($uploaded->newly_planted, 0) }}</td>
                            <td>{{ number_format($uploaded->vegetative, 0) }}</td>
                            <td>{{ number_format($uploaded->reproductive, 0) }}</td>
                            <td>{{ number_format($uploaded->maturity, 0) }}</td>
                            <td style="background: lightgreen;">{{ number_format($uploaded->total, 0) }}</td>
                            <td>{{ number_format($uploaded->planted_area_newly, 4) }}</td>
                            <td>{{ number_format($uploaded->planted_area_vegetative, 4) }}</td>
                            <td>{{ number_format($uploaded->planted_area_reproductive, 4) }}</td>
                            <td>{{ number_format($uploaded->planted_area_maturity, 4) }}</td>
                            <td style="background: lightgreen;">{{ number_format($uploaded->planted_total, 4) }}</td>
                            <td style="background: #ffb768;">{{ number_format($uploaded->area_harvested, 4) }}</td>
                            <td style="background: #ffb768;">{{ number_format($uploaded->production_volume_mt, 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination for Uploaded Data -->
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    <small class="text-muted">
                        Showing {{ $uploadedInventories->firstItem() ?? 0 }} to {{ $uploadedInventories->lastItem() ?? 0 }} of {{ $uploadedInventories->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $uploadedInventories->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const plantingDensityInput = document.querySelector('input[name="planting_density"]');
        const newlyPlantedInput = document.querySelector('input[name="newly_planted"]');
        const vegetativeInput = document.querySelector('input[name="vegetative"]');
        const reproductiveInput = document.querySelector('input[name="reproductive"]');
        const maturityHarvestedInput = document.querySelector('input[name="maturity_harvested"]');

        const readonlyNewlyPlanted = document.querySelector('input[name="newly_planted_divided"][readonly]');
        const readonlyVegetative = document.querySelector('input[name="vegetative_divided"][readonly]');
        const readonlyReproductive = document.querySelector('input[name="reproductive_divided"][readonly]');
        const readonlyMaturityHarvested = document.querySelector('input[name="maturity_harvested_divided"][readonly]');
        
        const totalPlantedAreaInput = document.querySelector('input[name="total_planted_area"]');
        const totalInput = document.querySelector('input[name="total"]');

        function calculateDivide() {
            const plantingDensity = parseFloat(plantingDensityInput.value) || 1; // Default to 1 to avoid division by 0

            // Calculate and set read-only values, defaulting to 0 if the input is empty or invalid
            readonlyNewlyPlanted.value = (parseFloat(newlyPlantedInput.value) || 0) / plantingDensity || 0;
            readonlyVegetative.value = (parseFloat(vegetativeInput.value) || 0) / plantingDensity || 0;
            readonlyReproductive.value = (parseFloat(reproductiveInput.value) || 0) / plantingDensity || 0;
            readonlyMaturityHarvested.value = (parseFloat(maturityHarvestedInput.value) || 0) / plantingDensity || 0;

            // Calculate total planted area (sum of readonly fields)
            const totalPlantedArea = 
                parseFloat(readonlyNewlyPlanted.value) + 
                parseFloat(readonlyVegetative.value) + 
                parseFloat(readonlyReproductive.value) + 
                parseFloat(readonlyMaturityHarvested.value);

            // Set the total planted area with 4 decimal places
            totalPlantedAreaInput.value = totalPlantedArea.toFixed(4); // Use toFixed for decimal

            // Calculate total hills (if this represents the total count of new plants added)
            const totalHills = 
                (parseFloat(newlyPlantedInput.value) || 0) + 
                (parseFloat(vegetativeInput.value) || 0) + 
                (parseFloat(reproductiveInput.value) || 0) + 
                (parseFloat(maturityHarvestedInput.value) || 0);

            // Set the total hills
            totalInput.value = totalHills.toFixed(0); // Use toFixed for whole numbers
        }

        // Attach event listeners to all input fields
        plantingDensityInput.addEventListener('input', calculateDivide);
        newlyPlantedInput.addEventListener('input', calculateDivide);
        vegetativeInput.addEventListener('input', calculateDivide);
        reproductiveInput.addEventListener('input', calculateDivide);
        maturityHarvestedInput.addEventListener('input', calculateDivide);

    });


    // Automatically calculate Area Harvested
    function calculateAreaHarvested(element) {
        const card = element.closest('.inventory-card');
        const maturityHarvested = parseInt(card.querySelector('input[name^="maturity_harvested"]').value) || 0;
        const plantingDensity = parseInt(card.querySelector('input[name^="planting_density"]').value) || 0;

        const areaHarvested = maturityHarvested / plantingDensity;
        card.querySelector('input[name^="area_harvested"]').value = areaHarvested.toFixed(4); // rounded to 4 decimal places

        calculateProductionVolume(card);
    }

    // Automatically calculate Production Volume (MT)
    function calculateProductionVolume(card) {
        const areaHarvested = parseFloat(card.querySelector('input[name^="area_harvested"]').value) || 0;
        const productionVolPerHa = parseFloat(card.querySelector('input[name^="production_volume"]').value) || 0;

        const productionVolumeMT = (areaHarvested * productionVolPerHa) / 1000;
        card.querySelector('input[name^="final_production_volume"]').value = productionVolumeMT.toFixed(4); // rounded to 4 decimal places
    }

    // Success message fade-out effect
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.classList.add('fade');
            setTimeout(() => successMessage.remove(), 500);
        }
    }, 5000);
</script>

<!-- Single Edit Modal (loaded via AJAX) -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Inventory Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="editModalBody">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading edit form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// AJAX function to load edit modal
function loadEditModal(inventoryId) {
    $('#editModal').modal('show');
    
    // Show loading state
    $('#editModalBody').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading edit form...</p>
        </div>
    `);
    
    // Fetch edit form via AJAX
    fetch(`/admin/inventory/${inventoryId}/edit-form`)
        .then(response => response.text())
        .then(html => {
            $('#editModalBody').html(html);
            // Re-initialize any JavaScript for the loaded form
            initializeEditForm();
        })
        .catch(error => {
            console.error('Error loading edit form:', error);
            $('#editModalBody').html(`
                <div class="alert alert-danger">
                    <h6>Error Loading Form</h6>
                    <p>Unable to load the edit form. Please try again.</p>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            `);
        });
}

// Initialize edit form calculations
function initializeEditForm() {
    const inventoryId = $('input[name="inventory_id"]').val();
    if (!inventoryId) return;
    
    function updateCalculations() {
        const plantingDensity = parseFloat($('#planting_density_' + inventoryId).val().replace(/,/g, '')) || 0;
        const maturityHarvested = parseFloat($('#maturity_harvested_' + inventoryId).val()) || 0;
        const productionVolume = parseFloat($('#production_volume_' + inventoryId).val()) || 0;
        const newlyPlanted = parseFloat($('#newly_planted_' + inventoryId).val()) || 0;
        const vegetative = parseFloat($('#vegetative_' + inventoryId).val()) || 0;
        const reproductive = parseFloat($('#reproductive_' + inventoryId).val()) || 0;

        // Calculate divided values
        const newlyPlantedDivided = (plantingDensity > 0) ? (newlyPlanted / plantingDensity) : 0;
        const vegetativeDivided = (plantingDensity > 0) ? (vegetative / plantingDensity) : 0;
        const reproductiveDivided = (plantingDensity > 0) ? (reproductive / plantingDensity) : 0;
        const maturityHarvestedDivided = (plantingDensity > 0) ? (maturityHarvested / plantingDensity) : 0;

        $('#newly_planted_divided_' + inventoryId).val(newlyPlantedDivided.toFixed(4));
        $('#vegetative_divided_' + inventoryId).val(vegetativeDivided.toFixed(4));
        $('#reproductive_divided_' + inventoryId).val(reproductiveDivided.toFixed(4));
        $('#maturity_harvested_divided_' + inventoryId).val(maturityHarvestedDivided.toFixed(4));

        // Calculations for area harvested, total, total planted area, and final production volume
        const areaHarvested = maturityHarvestedDivided;
        const total = newlyPlanted + vegetative + reproductive + maturityHarvested;
        const totalPlantedArea = newlyPlantedDivided + vegetativeDivided + reproductiveDivided + maturityHarvestedDivided;
        const finalProductionVolume = (areaHarvested * productionVolume) / 1000;

        // Update fields
        $('#area_harvested_' + inventoryId).val(areaHarvested.toFixed(4));
        $('#total_' + inventoryId).val(total);
        $('#total_planted_area_' + inventoryId).val(totalPlantedArea.toFixed(4));
        $('#final_production_volume_' + inventoryId).val(finalProductionVolume.toFixed(4));
    }

    // Attach event listeners to input fields
    $('#planting_density_' + inventoryId).on('input', updateCalculations);
    $('#maturity_harvested_' + inventoryId).on('input', updateCalculations);
    $('#production_volume_' + inventoryId).on('input', updateCalculations);
    $('#newly_planted_' + inventoryId).on('input', updateCalculations);
    $('#vegetative_' + inventoryId).on('input', updateCalculations);
    $('#reproductive_' + inventoryId).on('input', updateCalculations);
}
</script>

@endsection

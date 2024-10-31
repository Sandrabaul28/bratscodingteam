@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 text-success">
                <span class="font-weight-bold">INVENTORY VALUED CROPS</span>
            </h6>
            <a href="{{ route('admin.inventory.exportMonthlyInventoryExcel') }}" class="btn btn-success">Export to Excel</a>
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
                                            <option value="{{ $farmer->first_name }} {{ $farmer->last_name }}" data-id="{{ $farmer->id }}"></option>
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

                                <!-- Affiliation Selection -->
                                <div class="col-md-4 mb-3">
                                    <label for="affiliation_name">Affiliation <span style="color: red;">*</span></label>
                                    <input list="affiliation_list" id="affiliation_name" name="affiliation_name" class="form-control @error('affiliation_name') is-invalid @enderror" required placeholder="Select Affiliation">
                                    <datalist id="affiliation_list">
                                        @foreach($affiliations as $affiliation)
                                            <option value="{{ $affiliation->name_of_barangay}} - {{ $affiliation->name_of_association }} " data-id="{{ $affiliation->id }}"></option>
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="affiliation_id" id="affiliation_id">
                                    @error('affiliation_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <script >
                                    document.addEventListener('DOMContentLoaded', function() {
                                    const farmerInput = document.getElementById('farmer_name');
                                    const farmerIdField = document.getElementById('farmer_id');
                                    const plantInput = document.getElementById('plant_name');
                                    const plantIdField = document.getElementById('plant_id');
                                    const affiliationInput = document.getElementById('affiliation_name');
                                    const affiliationIdField = document.getElementById('affiliation_id');

                                    farmerInput.addEventListener('input', function() {
                                        const selectedFarmer = Array.from(document.querySelectorAll('#farmer_list option')).find(option => option.value === farmerInput.value);
                                        if (selectedFarmer) {
                                            farmerIdField.value = selectedFarmer.getAttribute('data-id');
                                        } else {
                                            farmerIdField.value = '';
                                        }
                                    });

                                    plantInput.addEventListener('input', function() {
                                        const selectedPlant = Array.from(document.querySelectorAll('#plant_list option')).find(option => option.value === plantInput.value);
                                        if (selectedPlant) {
                                            plantIdField.value = selectedPlant.getAttribute('data-id');
                                        } else {
                                            plantIdField.value = '';
                                        }
                                    });

                                    affiliationInput.addEventListener('input', function() {
                                        const selectedAffiliation = Array.from(document.querySelectorAll('#affiliation_list option')).find(option => option.value === affiliationInput.value);
                                        if (selectedAffiliation) {
                                            affiliationIdField.value = selectedAffiliation.getAttribute('data-id');
                                        } else {
                                            affiliationIdField.value = '';
                                        }
                                    });
                                });

                                </script>


                                <div class="col-md-2 mb-3">
                                    <label for="planting_density">Planting Density(ha) <span style="color: red;">*</span></label>
                                    <input type="number" name="planting_density" class="form-control @error('planting_density') is-invalid @enderror" required onchange="calculateAreaHarvested(this)">
                                    @error('planting_density')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="production_volume">Prodn. Vol/ Hectare(mt)<span style="color: red;">*</span></label>
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
                                    <label for="final_production_volume">Production Volume (MT)</label>
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

    <!-- Recorded inventory table -->
<div class="card shadow mb-4 mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success"><span class="font-weight-bold">RECORDED INVENTORY</span></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered" id="recorded-inventory-table">
                <thead>
                    <tr>
                        <th style="background: yellow;">Barangay</th>
                        <th style="background: yellow;">Commodity</th>
                        <th style="background: yellow;">Farmers</th>
                        <th>Planting Density (ha)</th>
                        <th>Production Vol/ Hectare (MT)</th>
                        <th>Newly Planted</th>
                        <th>Vegetative</th>
                        <th>Reproductive</th>
                        <th>Maturity Harvested</th>
                        <th style="background: greenyellow;">Total/ #hill/puno</th>
                        <!--  -->
                        <th>Newly Planted/ha</th> <!-- Newly Planted divided field -->
                        <th>Vegetative/ha</th>    <!-- Vegetative divided field -->
                        <th>Reproductive/ha</th>  <!-- Reproductive divided field -->
                        <th>Maturity Harvested/ha</th> <!-- Maturity Harvested divided field -->
                        <th style="background: greenyellow;">Total/ Planted Area</th>
                        <th style="background: orange;">Area Harvested</th>
                        <th style="background: orange;">Production Volume (MT)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventories as $inventory)
                        <tr>
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
                                <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $inventory->id }}"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $inventory->id }}"><i class="fas fa-trash"></i></button>
                            </td>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $inventory->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $inventory->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $inventory->id }}">Edit Inventory Record</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="farmer_id">Farmer</label>
                                                        <select class="form-control" name="farmer_id" id="farmer_id" required>
                                                            @foreach($farmers as $farmer)
                                                                <option value="{{ $farmer->id }}" {{ $inventory->farmer_id == $farmer->id ? 'selected' : '' }}>
                                                                    {{ $farmer->first_name }} {{ $farmer->last_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="plant_id">Plant</label>
                                                        <select class="form-control" name="plant_id" id="plant_id" required>
                                                            @foreach($plants as $plant)
                                                                <option value="{{ $plant->id }}" {{ $inventory->plant_id == $plant->id ? 'selected' : '' }}>
                                                                    {{ $plant->name_of_plants }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="affiliation_id">Affiliation</label>
                                                        <select class="form-control" name="affiliation_id" id="affiliation_id" required>
                                                            @foreach($affiliations as $affiliation)
                                                                <option value="{{ $affiliation->id }}" {{ $inventory->affiliation_id == $affiliation->id ? 'selected' : '' }}>
                                                                    {{ $affiliation->name_of_association }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="planting_density">Planting Density</label>
                                                        <input type="number" class="form-control" name="planting_density" id="planting_density" value="{{ round($inventory->planting_density, 4) }}" step="0.0001" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="production_volume">Production Volume</label>
                                                        <input type="number" class="form-control" name="production_volume" id="production_volume" value="{{ round($inventory->production_volume, 4) }}" step="0.0001" required>
                                                    </div>

                                                    <!-- Remaining Fields -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="newly_planted">Newly Planted</label>
                                                        <input type="number" class="form-control" name="newly_planted" id="newly_planted" value="{{ $inventory->newly_planted }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="vegetative">Vegetative</label>
                                                        <input type="number" class="form-control" name="vegetative" id="vegetative" value="{{ $inventory->vegetative }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="reproductive">Reproductive</label>
                                                        <input type="number" class="form-control" name="reproductive" id="reproductive" value="{{ $inventory->reproductive }}">
                                                    </div>


                                                    <div class="col-md-6 mb-3">
                                                        <label for="maturity_harvested">Maturity Harvested</label>
                                                        <input type="number" class="form-control" name="maturity_harvested" id="maturity_harvested" value="{{ $inventory->maturity_harvested }}">
                                                    </div>


                                                    <!-- Calculated Fields -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="area_harvested">Area Harvested (HAS)</label>
                                                        <input type="text" class="form-control" name="area_harvested" id="area_harvested" value="{{ number_format($inventory->area_harvested, 4) }}" readonly>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="final_production_volume">Production Volume (MT)</label>
                                                        <input type="text" class="form-control" name="final_production_volume" id="final_production_volume" value="{{ number_format($inventory->final_production_volume, 4) }}" readonly>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="total">Total</label>
                                                        <input type="text" class="form-control" name="total" id="total" value="{{ number_format($inventory->total) }}" readonly>
                                                    </div>

                                                </div>
                                            </div>
                                            <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                                // Function to calculate and update the fields
                                                function updateCalculations() {
                                                    // Get input values and parse as floats
                                                    const plantingDensity = parseFloat(document.getElementById('planting_density').value.replace(/,/g, '')) || 0;
                                                    const maturityHarvested = parseFloat(document.getElementById('maturity_harvested').value) || 0;
                                                    const productionVolume = parseFloat(document.getElementById('production_volume').value.replace(/,/g, '')) || 0;

                                                    // Calculate Area Harvested (HAS)
                                                    const areaHarvested = (plantingDensity > 0) ? (maturityHarvested / plantingDensity) : 0;
                                                    document.getElementById('area_harvested').value = areaHarvested.toFixed(4); // Round to 4 decimals

                                                    // Calculate Final Production Volume (MT)
                                                    const finalProductionVolume = (areaHarvested * productionVolume) / 1000; // Convert to MT
                                                    document.getElementById('final_production_volume').value = finalProductionVolume.toFixed(4); // Round to 4 decimals

                                                    // Calculate Total
                                                    const newlyPlanted = parseFloat(document.getElementById('newly_planted').value) || 0;
                                                    const vegetative = parseFloat(document.getElementById('vegetative').value) || 0;
                                                    const reproductive = parseFloat(document.getElementById('reproductive').value) || 0;

                                                    const total = newlyPlanted + vegetative + reproductive + maturityHarvested;
                                                    document.getElementById('total').value = total; // Round to 4 decimals
                                                }

                                                // Add event listeners for input fields
                                                const inputIds = [
                                                    'planting_density',
                                                    'maturity_harvested',
                                                    'production_volume',
                                                    'newly_planted',
                                                    'vegetative',
                                                    'reproductive'
                                                ];

                                                inputIds.forEach(id => {
                                                    document.getElementById(id).addEventListener('input', updateCalculations);
                                                });
                                            });
                                        </script>



                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


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
    </div>
</div>
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

            // Calculate and set read-only values
            readonlyNewlyPlanted.value = (parseFloat(newlyPlantedInput.value) / plantingDensity).toFixed(4);
            readonlyVegetative.value = (parseFloat(vegetativeInput.value) / plantingDensity).toFixed(4);
            readonlyReproductive.value = (parseFloat(reproductiveInput.value) / plantingDensity).toFixed(4);
            readonlyMaturityHarvested.value = (parseFloat(maturityHarvestedInput.value) / plantingDensity).toFixed(4);

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
                parseFloat(newlyPlantedInput.value) + 
                parseFloat(vegetativeInput.value) + 
                parseFloat(reproductiveInput.value) + 
                parseFloat(maturityHarvestedInput.value);

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

@endsection

@extends('layouts.admin.app')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 text-success"><span class="font-weight-bold">HIGH VALUED CROPS</span></h6>
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

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" id="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.hvcdp.store') }}" method="POST" enctype="multipart/form-data" id="crop-form">
    @csrf
    <div class="form-row">
        <!-- Farmer input -->
        <div class="form-group col-md-4">
            <label for="farmer_name">Name of Farmer <span style="color: red;">*</span></label>
            <input list="farmers" id="farmer_name" name="farmer_name" class="form-control" value="{{ old('farmer_name') }}" placeholder="Enter Farmer's name" required>
            <datalist id="farmers">
                @foreach($farmers as $farmer)
                    <option value="{{ $farmer->first_name }} {{ $farmer->last_name }}" data-id="{{ $farmer->id }}"></option>
                @endforeach
            </datalist>
            <input type="hidden" name="farmer_id" id="farmer_id" value="{{ old('farmer_id') }}">
        </div>

        <!-- Plant input -->
        <div class="form-group col-md-4">
            <label for="plant_name">Name of Plant <span style="color: red;">*</span></label>
            <input list="plants" id="plant_name" name="plant_name" class="form-control" value="{{ old('plant_name') }}" placeholder="Enter Plant/Crop name" required>
            <datalist id="plants">
                @foreach($plants as $plant)
                    <option value="{{ $plant->name_of_plants }}" data-id="{{ $plant->id }}"></option>
                @endforeach
            </datalist>
            <input type="hidden" name="plant_id" id="plant_id" value="{{ old('plant_id') }}">
        </div>

        <!-- Count input -->
        <div class="form-group col-md-4">
            <label for="count">Count <span style="color: red;">*</span></label>
            <input type="number" name="count" class="form-control" value="{{ old('count') }}" placeholder="Enter Count" required>
        </div>
    </div>

    <div class="form-row">
        <!-- Latitude input -->
        <div class="form-group col-md-4">
            <label for="latitude">Latitude (Optional)</label>
            <input type="text" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="Enter Latitude">
        </div>

        <!-- Longitude input -->
        <div class="form-group col-md-4">
            <label for="longitude">Longitude (Optional)</label>
            <input type="text" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="Enter Longitude">
        </div>

        
        <!-- Image upload field -->
        <div class="form-group col-md-4">
            <label for="image">Upload Image (Optional)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>
    </div>



    <!-- Submit button -->
    <button type="submit" class="btn btn-success">Add</button>
</form>



            <script>
                const farmerInput = document.getElementById('farmer_name');
                const farmerIdField = document.getElementById('farmer_id');
                const plantInput = document.getElementById('plant_name');
                const plantIdField = document.getElementById('plant_id');

                farmerInput.addEventListener('input', function() {
                    const selectedFarmer = Array.from(document.querySelectorAll('#farmers option')).find(option => option.value === farmerInput.value);
                    if (selectedFarmer) {
                        farmerIdField.value = selectedFarmer.getAttribute('data-id');
                    } else {
                        farmerIdField.value = '';
                    }
                });

                plantInput.addEventListener('input', function() {
                    const selectedPlant = Array.from(document.querySelectorAll('#plants option')).find(option => option.value === plantInput.value);
                    if (selectedPlant) {
                        plantIdField.value = selectedPlant.getAttribute('data-id');
                    } else {
                        plantIdField.value = '';
                    }
                });
            </script>

            <hr> 

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 text-success"><span class="font-weight-bold">Recorded Inventory</span></h6>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <div class="card-body">
                                <!-- Search Bar for Filtering -->
                                <div class="mb-3">
                                    <input type="text" id="searchBar" class="form-control form-control-sm" placeholder="Search by Name, Barangay, or Association" onkeyup="filterTable()" style="max-width: 300px;">
                                </div>

                                <!-- JavaScript for Filtering -->
                                <script>
                                    function filterTable() {
                                        const input = document.getElementById('searchBar');
                                        const filter = input.value.toLowerCase();
                                        const table = document.getElementById('inventory-table');
                                        const rows = table.getElementsByTagName('tr');
                                        
                                        // Loop through all table rows, and hide those that don't match the search query
                                        for (let i = 1; i < rows.length; i++) {
                                            const row = rows[i];
                                            const cells = row.getElementsByTagName('td');
                                            
                                            let matchFound = false;
                                            // Check if any of the cells in this row match the search term
                                            for (let j = 0; j < cells.length; j++) {
                                                if (cells[j]) {
                                                    const cellText = cells[j].textContent || cells[j].innerText;
                                                    
                                                    // Adjust the logic here to specifically match columns for Name, Barangay, and Association
                                                    if (j === 0 || j === 1 || j === 2) { // Assuming Name is in the first column, Barangay in the second, and Association in the third
                                                        if (cellText.toLowerCase().indexOf(filter) > -1) {
                                                            matchFound = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }

                                            if (matchFound) {
                                                row.style.display = ''; // Show row
                                            } else {
                                                row.style.display = 'none'; // Hide row
                                            }
                                        }
                                    }
                                </script>


                                <div class="table-responsive">
                                    <table class="table table-bordered" id="inventory-table">
                        <thead>
                            <tr>
                                <th>Name of Farmer</th>
                                <th>Barangay</th>
                                <th>Association</th>
                                <th>Plants (Name and Count)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedInventory = [];
                            @endphp

                            @foreach($inventoryCrops as $crop)
                                @php
                                    if (!isset($groupedInventory[$crop->farmer_id])) {
                                        $groupedInventory[$crop->farmer_id] = [
                                            'farmer_name' => $crop->first_name . ' ' . $crop->last_name,
                                            'barangay' => $crop->name_of_barangay, // Show barangay separately
                                            'association' => $crop->name_of_association, // Show association separately
                                            'added_by_first_name' => $crop->added_by_first_name,
                                            'added_by_last_name' => $crop->added_by_last_name,
                                            'plants' => []
                                        ];
                                    }
                                    $groupedInventory[$crop->farmer_id]['plants'][] = $crop;
                                @endphp
                            @endforeach

                            @foreach($groupedInventory as $farmerId => $group)
                                <tr>
                                    <td>{{ $group['farmer_name'] }}</td>
                                    <td>{{ $group['barangay'] }}</td> <!-- Display barangay here -->
                                    <td>{{ $group['association'] ?? 'N/A' }}</td> <!-- Display association here (or 'N/A' if not available) -->
                                    <td>
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-{{ $farmerId }}" aria-expanded="false" aria-controls="collapse-{{ $farmerId }}">
                                            <i class="fa fa-arrow-circle-down" aria-hidden="true"></i> View Plants
                                        </button>
                                    </td>
                                </tr>

                                <!-- Collapsible rows for plant details -->
                                <tr id="collapse-{{ $farmerId }}" class="collapse">
                                    <td colspan="4"> <!-- Adjusted colspan to 4 -->
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Plant Name</th>
                                                    <th>Count</th>
                                                    <th>Latitude</th>
                                                    <th>Longitude</th>
                                                    <th>Date Added</th>
                                                    <th>Added by</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($group['plants'] as $plant)
                                                    <tr>
                                                        <td>{{ $plant->name_of_plants }}</td>
                                                        <td>{{ $plant->count }}</td>
                                                        <td>{{ $plant->latitude }}</td>
                                                        <td>{{ $plant->longitude }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($plant->created_at)->format('F d, Y') }}</td>
                                                        <td>{{ $plant->added_by_first_name }} {{ $plant->added_by_last_name }} / {{ $plant->role_name }}</td>
                                                        <td>

                                                            <!-- View Button -->
                                                            <!-- button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal-{{ $plant->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button> -->

                                                            <!-- Edit Button -->
                                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal-{{ $plant->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>

                                                            <!-- Delete Button -->
                                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $plant->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- View Modal -->
                                                    <div class="modal fade" id="viewModal-{{ $plant->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="viewModalLabel">Plant Details</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Plant Name:</strong> {{ $plant->name_of_plants }}</p>
                                                                    <p><strong>Count:</strong> {{ $plant->count }}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Edit Modal -->
<!-- Edit Modal -->
<div class="modal fade" id="editModal-{{ $plant->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Plant Count</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.count.update', $plant->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="edit_plant_name_{{ $plant->id }}">Plant Name</label>
                        <input list="plants" id="edit_plant_name_{{ $plant->id }}" name="plant_name" value="{{ $plant->name_of_plants }}" class="form-control">
                        <datalist id="plants">
                            @foreach($plants as $plantOption)
                                <option value="{{ $plantOption->name_of_plants }}" data-id="{{ $plantOption->id }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="edit_count_{{ $plant->id }}">Count</label>
                        <input type="number" id="edit_count_{{ $plant->id }}" name="count" value="{{ $plant->count }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="edit_latitude_{{ $plant->id }}">Latitude</label>
                        <input type="text" id="edit_latitude_{{ $plant->id }}" name="latitude" value="{{ $plant->latitude }}" class="form-control">
                        <small class="form-text text-muted">Format: -90 to 90</small>
                    </div>

                    <div class="form-group">
                        <label for="edit_longitude_{{ $plant->id }}">Longitude</label>
                        <input type="text" id="edit_longitude_{{ $plant->id }}" name="longitude" value="{{ $plant->longitude }}" class="form-control">
                        <small class="form-text text-muted">Format: -180 to 180</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal-{{ $plant->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete this plant record?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <form action="{{ route('admin.count.destroy', $plant->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

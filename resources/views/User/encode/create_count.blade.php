@extends('layouts.user.app')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 text-success"><span class="font-weight-bold">HIGH VALUED CROPS DEVELOPMENT PROGRAM</span></h6>
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

            <form action="{{ route('user.count.store') }}" method="POST" id="crop-form">
                @csrf
                <div class="form-row">
                    <!-- Farmer input with datalist -->
                    <div class="form-group col-md-4">
                        <label for="farmer_name">Name of Farmer <span style="color: red;">*</span></label>
                        
                        <!-- Farmer name input -->
                        <input type="text" id="farmer_name" name="farmer_name" class="form-control" list="farmers" autocomplete="off" required>
                        <datalist id="farmers">
                            <!-- Options will be loaded via Ajax -->
                        </datalist>
                        
                        <!-- Hidden input for farmer ID -->
                        <input type="hidden" name="farmer_id" id="farmer_id" value="{{ old('farmer_id') }}">
                        <input type="hidden" id="affiliation_id" value="{{ Auth::user()->affiliation_id }}"> 

                        <!-- Error message display -->
                        @if ($errors->has('farmer_id'))
                            <span class="text-danger">{{ $errors->first('farmer_id') }}</span>
                        @endif
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const farmerNameInput = document.getElementById('farmer_name');
                        const farmerIdInput = document.getElementById('farmer_id');
                        const farmersDatalist = document.getElementById('farmers');

                        farmerNameInput.addEventListener('input', function () {
                            const query = this.value;
                            const affiliationIdInput = document.getElementById('affiliation_id');
                            const affiliationId = affiliationIdInput ? affiliationIdInput.value : null; // Safely get the value

                            if (query.length > 1 && affiliationId) {
                                fetch(`/user/hvcdp/fetch-farmers?query=${query}&affiliation_id=${affiliationId}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        farmersDatalist.innerHTML = ''; // Clear existing options

                                        data.forEach(farmer => {
                                            const option = document.createElement('option');
                                            option.value = `${farmer.first_name} ${farmer.last_name}`;
                                            option.dataset.id = farmer.id;
                                            farmersDatalist.appendChild(option);
                                        });
                                    })
                                    .catch(error => console.error('Error fetching farmers:', error)); // Handle errors
                            }
                        }); 


                        // Set farmer ID when selecting from datalist
                        farmerNameInput.addEventListener('change', function () {
                            const selectedOption = [...farmersDatalist.options].find(option => option.value === this.value);
                            farmerIdInput.value = selectedOption ? selectedOption.dataset.id : '';
                        });
                    });
                    </script>


                    <!-- Plant input -->
                    <div class="form-group col-md-4">
                        <label for="plant_name">Name of Plant <span style="color: red;">*</span></label>
                        <input list="plants" id="plant_name" name="plant_name" class="form-control" required>
                        <datalist id="plants">
                            @foreach($plants as $plant)
                                <option value="{{ $plant->name_of_plants }}" data-id="{{ $plant->id }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="plant_id" id="plant_id">
                    </div>

                    <!-- Count input -->
                    <div class="form-group col-md-4">
                        <label for="count">Count <span style="color: red;">*</span></label>
                        <input type="number" name="count" class="form-control" required>
                    </div>

                     <!-- Latitude input -->
                    <div class="form-group col-md-4">
                        <label for="latitude">Latitude <span style="color: red;">*</span></label>
                        <input type="text" name="latitude" class="form-control" required pattern="-?\d+(\.\d+)?">
                        <small class="form-text text-muted">Format: -90 to 90</small>
                    </div>

                    <!-- Longitude input -->
                    <div class="form-group col-md-4">
                        <label for="longitude">Longitude <span style="color: red;">*</span></label>
                        <input type="text" name="longitude" class="form-control" required pattern="-?\d+(\.\d+)?">
                        <small class="form-text text-muted">Format: -180 to 180</small>
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
                <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered" id="inventory-table">
                        <thead>
                            <tr>
                            <th>Farmer</th>
                            <th>Plant</th>
                            <th>Count</th>
                            <th>Latitude</th>  <!-- Column for Latitude -->
                            <th>Longitude</th> <!-- Column for Longitude -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryCrops->groupBy('farmer_id') as $farmerId => $crops)
                            <!-- Loop through each crop for the farmer -->
                            @foreach($crops as $index => $crop)
                                <tr>
                                    <!-- Display Farmer's Name only on the first row of each farmer group -->
                                    @if($index === 0)
                                        <td rowspan="{{ $crops->count() }}">
                                            {{ $crop->farmer->first_name }} {{ $crop->farmer->last_name }}
                                        </td>
                                    @endif

                                    <!-- Plant Name -->
                                    <td>{{ $crop->plant->name_of_plants }}</td>

                                    <!-- Plant Count -->
                                    <td>{{ $crop->count }}</td>

                                    <!-- Latitude -->
                                    <td>{{ $crop->latitude }}</td>

                                    <!-- Longitude -->
                                    <td>{{ $crop->longitude }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal-{{ $crops->first()->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <!-- 
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $crop->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button> 
                                        -->
                                    </td>
                                </tr>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal-{{ $crop->first()->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Crop Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('user.count.update', $crop->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <!-- Loop through inventory crops -->
                                                    @foreach($inventoryCrops as $inventoryCrop)
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label for="plant_name_{{ $inventoryCrop->id }}">Plant Name</label>
                                                                <!-- Use a datalist for plant selection -->
                                                                <input 
                                                                    list="plantOptions" 
                                                                    name="name_of_plants[{{ $inventoryCrop->id }}]" 
                                                                    id="plant_name_{{ $inventoryCrop->id }}" 
                                                                    value="{{ $inventoryCrop->plant->name_of_plants }}" 
                                                                    class="form-control"
                                                                >
                                                                <!-- Datalist with available plants -->
                                                                <datalist id="plantOptions">
                                                                    @foreach($plants as $plant)
                                                                        <option value="{{ $plant->name_of_plants }}">{{ $plant->name_of_plants }}</option>
                                                                    @endforeach
                                                                </datalist>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="count_{{ $inventoryCrop->id }}">Count</label>
                                                                <input 
                                                                    type="number" 
                                                                    name="count[{{ $inventoryCrop->id }}]" 
                                                                    id="count_{{ $inventoryCrop->id }}" 
                                                                    value="{{ $inventoryCrop->count }}" 
                                                                    class="form-control" 
                                                                    min="0"
                                                                >
                                                            </div>
                                                        </div>

                                                        <!-- Latitude input -->
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label for="latitude_{{ $inventoryCrop->id }}">Latitude</label>
                                                                <input 
                                                                    type="text" 
                                                                    name="latitude[{{ $inventoryCrop->id }}]" 
                                                                    id="latitude_{{ $inventoryCrop->id }}" 
                                                                    value="{{ $inventoryCrop->latitude ?? '' }}" 
                                                                    class="form-control" 
                                                                    pattern="-?\d+(\.\d+)?"
                                                                >
                                                                <small class="form-text text-muted">Format: -90 to 90</small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="longitude_{{ $inventoryCrop->id }}">Longitude</label>
                                                                <input 
                                                                    type="text" 
                                                                    name="longitude[{{ $inventoryCrop->id }}]" 
                                                                    id="longitude_{{ $inventoryCrop->id }}" 
                                                                    value="{{ $inventoryCrop->longitude ?? '' }}" 
                                                                    class="form-control" 
                                                                    required 
                                                                    pattern="-?\d+(\.\d+)?"
                                                                >
                                                                <small class="form-text text-muted">Format: -180 to 180</small>
                                                            </div>
                                                        </div>

                                                        <!-- Hidden input to send crop ID -->
                                                        <input type="hidden" name="crop_ids[]" value="{{ $inventoryCrop->id }}">
                                                    @endforeach

                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <!-- Delete Modal -->
                                <!-- <div class="modal fade" id="deleteModal-{{ $crop->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the record with commodity <strong>{{ $crop->plant->name_of_plants }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('user.count.destroy', $crop->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fade out success message after 5 seconds
    setTimeout(() => {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.transition = "opacity 1s";
            successMessage.style.opacity = 0;
            setTimeout(() => successMessage.remove(), 1000);
        }
    }, 5000);
</script>
@endsection

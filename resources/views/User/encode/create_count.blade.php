@extends('layouts.User.app')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 text-success"><span class="font-weight-bold">HIGH VALUED CROPS </span></h6>
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

                <form action="{{ route('user.count.store') }}" method="POST" enctype="multipart/form-data" id="crop-form">
                @csrf
                <div class="form-row">
                    <!-- Fixed Farmer Name Input -->
                    <div class="form-group col-md-4">
                        <label for="farmer_name">Name of Farmer <span style="color: red;">*</span></label>
                        <input type="text" id="farmer_name" name="farmer_name" class="form-control" value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}" readonly>
                        <input type="hidden" name="farmer_id" id="farmer_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" id="affiliation_id" value="{{ Auth::user()->affiliation_id }}"> 
                        @if ($errors->has('farmer_id'))
                            <span class="text-danger">{{ $errors->first('farmer_id') }}</span>
                        @endif
                    </div>

                    <!-- Plant input -->
                    <div class="form-group col-md-4">
                        <label for="plant_name">Name of Plant <span style="color: red;">*</span></label>
                        <input list="plants" id="plant_name" name="plant_name" placeholder="Enter Plant" class="form-control" required>
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
                        <input type="number" name="count" placeholder="Enter Count" class="form-control" required>
                    </div>

                    <!-- Latitude input -->
                    <div class="form-group col-md-4">
                        <label for="latitude">Latitude (Optional)</label>
                        <input type="text" name="latitude" id="latitude" placeholder="Enter Latitude" class="form-control">
                    </div>

                    <!-- Longitude input -->
                    <div class="form-group col-md-4">
                        <label for="longitude">Longitude (Optional)</label>
                        <input type="text" name="longitude" id="longitude" placeholder="Enter Longitude" class="form-control">
                    </div>

                    <!-- Image upload field -->
                    <div class="form-group col-md-4">
                        <label for="image">Upload Image (Optional)</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*" >
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
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Date & Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryCrops->groupBy('farmer_id') as $farmerId => $crops)
                            @foreach($crops as $index => $crop)
                                <tr>
                                    @if($index === 0)
                                        <td rowspan="{{ $crops->count() }}">
                                            {{ $crop->farmer->first_name }} {{ $crop->farmer->last_name }}
                                        </td>
                                    @endif
                                    <td>{{ $crop->plant->name_of_plants }}</td>
                                    <td>{{ $crop->count }}</td>
                                    <td>{{ $crop->latitude }}</td>
                                    <td>{{ $crop->longitude }}</td>
                                    <td>{{ \Carbon\Carbon::parse($crop->created_at)->timezone('Asia/Manila')->format('F d, Y h:i A') }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal-{{ $crop->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal-{{ $crop->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $crop->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel-{{ $crop->id }}">Edit Crop Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('user.count.update', $crop->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="form-group">
                                                                <label for="plant_name_{{ $crop->id }}">Plant Name</label>
                                                                <input 
                                                                    list="plantOptions" 
                                                                    name="name_of_plants" 
                                                                    id="plant_name_{{ $crop->id }}" 
                                                                    value="{{ $crop->plant->name_of_plants }}" 
                                                                    class="form-control"
                                                                    required
                                                                >
                                                                <datalist id="plantOptions">
                                                                    @foreach($plants as $plant)
                                                                        <option value="{{ $plant->name_of_plants }}">{{ $plant->name_of_plants }}</option>
                                                                    @endforeach
                                                                </datalist>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="count_{{ $crop->id }}">Count</label>
                                                                <input 
                                                                    type="number" 
                                                                    name="count" 
                                                                    id="count_{{ $crop->id }}" 
                                                                    value="{{ $crop->count }}" 
                                                                    class="form-control" 
                                                                    min="0"
                                                                    required
                                                                >
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="latitude_{{ $crop->id }}">Latitude</label>
                                                                <input 
                                                                    type="text" 
                                                                    name="latitude" 
                                                                    id="latitude_{{ $crop->id }}" 
                                                                    value="{{ $crop->latitude }}" 
                                                                    class="form-control" 
                                                                    pattern="-?\d+(\.\d+)?" 
                                                                    required
                                                                >
                                                                <small class="form-text text-muted">Format: -90 to 90</small>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="longitude_{{ $crop->id }}">Longitude</label>
                                                                <input 
                                                                    type="text" 
                                                                    name="longitude" 
                                                                    id="longitude_{{ $crop->id }}" 
                                                                    value="{{ $crop->longitude }}" 
                                                                    class="form-control" 
                                                                    pattern="-?\d+(\.\d+)?" 
                                                                    required
                                                                >
                                                                <small class="form-text text-muted">Format: -180 to 180</small>
                                                            </div>

                                                            <!-- Hidden input to send crop ID -->
                                                            <input type="hidden" name="crop_id" value="{{ $crop->id }}">

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

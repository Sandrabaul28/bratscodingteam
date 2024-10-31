@extends('layouts.admin.app')

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

            <form action="{{ route('admin.hvcdp.store') }}" method="POST" id="crop-form">
                @csrf
                <div class="form-row">
                    <!-- Farmer input -->
                    <div class="form-group col-md-4">
                        <label for="farmer_name">Name of Farmer <span style="color: red;">*</span></label>
                        <input list="farmers" id="farmer_name" name="farmer_name" class="form-control" required>
                        <datalist id="farmers">
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->first_name }} {{ $farmer->last_name }}" data-id="{{ $farmer->id }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="farmer_id" id="farmer_id">
                    </div>

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
                </div>

                <!-- Submit and Add Farmer buttons -->
                <button type="submit" class="btn btn-success">Add</button>
                <a href="{{ route('admin.farmers.create') }}" class="btn btn-warning">Add Farmer</a>
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
                    <div class="table-responsive">
                    <table class="table table-bordered" id="inventory-table">
                        <thead>
                            <tr>
                                <th>Name of Farmer</th>
                                <th>Affiliation</th>
                                <th>Plants (Name and Count)</th>
                                <th>Actions</th>
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
                                            'affiliation' => $crop->name_of_association ? $crop->name_of_association . ' ' . $crop->name_of_barangay : $crop->name_of_barangay,
                                            'plants' => []
                                        ];
                                    }
                                    $groupedInventory[$crop->farmer_id]['plants'][] = $crop;
                                @endphp
                            @endforeach

                            @foreach($groupedInventory as $farmerId => $group)
                                <tr>
                                    <td>{{ $group['farmer_name'] }}</td>
                                    <td>{{ $group['affiliation'] }}</td>
                                    <td>
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-{{ $farmerId }}" aria-expanded="false" aria-controls="collapse-{{ $farmerId }}"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i>View Plants
                                        </button>
                                    </td>
                                    <td>
                                        <!-- Main action buttons for the farmer (if needed) -->
                                        <!-- Delete Button -->
                                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $plant->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                    </td>
                                </tr>

                                <!-- Collapsible rows for plant details -->
                                <tr id="collapse-{{ $farmerId }}" class="collapse">
                                    <td colspan="4">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Plant Name</th>
                                                    <th>Count</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($group['plants'] as $plant)
                                                    <tr>
                                                        <td>{{ $plant->name_of_plants }}</td>
                                                        <td>{{ $plant->count }}</td>
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
                                                                            <label for="edit_count_{{ $plant->id }}">Count</label>
                                                                            <input type="number" id="edit_count_{{ $plant->id }}" name="count" value="{{ $plant->count }}" class="form-control" required>
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

@extends('layouts.Admin.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">PLANT MANAGEMENT / <span class="font-weight-bold">ADD CROPS/ PLANTS LISTS</span></h6>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.plants.store') }}" method="POST">
            @csrf
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <label for="name_of_plants">Name of Plants <span style="color: red;">*</span></label>
                    <input list="plants" name="name_of_plants" placeholder="chayote, carrot, chili, etc." class="form-control form-control-sm" required>
                    <datalist id="plants">
                        <option value="ALUGBATI"></option>
                        <option value="AMPALAYA"></option>
                        <option value="BELL PEPPER"></option>
                        <option value="CABBAGE"></option>
                        <option value="CARROTS"></option>
                        <option value="CHAYOTE"></option>
                        <option value="CHILI"></option>
                        <option value="CHINESE CABBAGE"></option>
                        <option value="CUCUMBER"></option>
                        <option value="EGGPLANT"></option>
                        <option value="GABI"></option>
                        <option value="GINGER"></option>
                        <option value="HOT PEPPER"></option>
                        <option value="KANGKONG"></option>
                        <option value="LABUYO"></option>
                        <option value="LEMON GRASS"></option>
                        <option value="LETTUCE"></option>
                        <option value="MALUNGGAY"></option>
                        <option value="MUNGBEAN"></option>
                        <option value="OKRA"></option>
                        <option value="PAO"></option>
                        <option value="PATOLA"></option>
                        <option value="PEANUT"></option>
                        <option value="PECHAY"></option>
                        <option value="POLESITAO"></option>
                        <option value="SALUYOT"></option>
                        <option value="SNAP BEANS"></option>
                        <option value="SPRING ONIONS"></option>
                        <option value="SQUASH"></option>
                        <option value="STRAWBERRY"></option>
                        <option value="SWEET PEPPER"></option>
                        <option value="SWEET POTATO"></option>
                        <option value="TOMATO"></option>
                        <option value="UPO"></option>
                        <option value="WINGBEAN"></option>
                        <option value="YAM"></option>
                        <option value="YAUTIA"></option>
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label for="variety_name">Variety Name <span style="color: red;">*</span></label>
                    <input type="text" name="variety_name" placeholder="varieties" class="form-control form-control-sm" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
        </form>

    </div>
</div>

<!-- Display the list of plants and varieties -->
<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">PLANT LISTS</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name of Plants</th>
                            <th>Variety Name</th>
                            <th>Actions</th> <!-- Actions Column for Edit and Delete -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plants as $plant)
                            <tr>
                                <td>{{ $plant->name_of_plants }}</td>
                                <td>
                                    @foreach($plant->varieties as $variety)
                                        <span style="font-style: italic;">{{ $variety->variety_name }}</span><br>
                                    @endforeach
                                </td>
                                <td>
                                    <!-- Edit Button to trigger modal -->
                                    <button class="btn btn-warning btn-sm" onclick="editPlant({{ $plant }})"><i class="fas fa-edit"></i> </button>

                                    <!-- Delete Button to trigger modal -->
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $plant->id }})"><i class="fas fa-trash"></i> </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Plant Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Plant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name_of_plants">Name of Plants</label>
                            <input type="text" name="name_of_plants" id="edit_name_of_plants" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_variety_name">Variety Name</label>
                            <input type="text" name="variety_name" id="edit_variety_name" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this plant and its varieties?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle the modals and form submissions -->
<script>
    function editPlant(plant) {
        document.getElementById('edit_name_of_plants').value = plant.name_of_plants;
        document.getElementById('edit_variety_name').value = plant.varieties[0].variety_name; // assuming one variety for simplicity
        document.getElementById('editForm').action = `/admin/plants/${plant.id}`; // Set the form action dynamically
        $('#editModal').modal('show'); // Show the modal
    }

    function confirmDelete(plantId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/plants/${plantId}`; // Set the form action dynamically
        $('#deleteModal').modal('show'); // Show the modal
    }

    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.opacity = 1;
            successMessage.style.transition = 'opacity 0.6s ease-out';
            
            setTimeout(function() {
                successMessage.style.opacity = 0;
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 600);
            }, 5000);
        }
    });
</script>
@endsection

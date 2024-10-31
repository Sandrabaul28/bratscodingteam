@extends('layouts.admin.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">ROLES MANAGEMENT / <span class="font-weight-bold">ADD AFFILIATION / AFFILIATION LISTS</span></h6>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form to create a new affiliation -->
        <form action="{{ route('admin.affiliations.store') }}" method="POST">
            @csrf
            <div class="form-row mb-3">
                <div class="col">
                    <label for="name_of_barangay">Name of Barangay <span style="color: red;">*</span></label>
                    <input list="barangays" name="name_of_barangay" placeholder="Barangay" class="form-control form-control-sm" required>
                    <datalist id="barangays">
                        <option value="Benit"></option>
                        <option value="Buenavista"></option>
                        <option value="Cabugason"></option>
                        <option value="Calogcog"></option>
                        <option value="Cancamares"></option>
                        <option value="Dao"></option>
                        <option value="Divisoria"></option>
                        <option value="Esperanza"></option>
                        <option value="Hilaan"></option>
                        <option value="Himakilo"></option>
                        <option value="Malbago"></option>
                        <option value="Mandamo"></option>
                        <option value="Manháa"></option>
                        <option value="Masaymon"></option>
                        <option value="Pamahawan"></option>
                        <option value="Pasil"></option>
                        <option value="Poblacion"></option>
                        <option value="San Juan"></option>
                        <option value="San Ramon"></option>
                        <option value="Santo Niño"></option>
                        <option value="Santo Rosario"></option>
                        <option value="Talisay"></option>
                        <option value="Tampoong"></option>
                        <option value="Tuburan"></option>
                        <option value="Union"></option>
                        <option value="Malitbogay"></option>
                        <option value="Tagbayaon"></option>
                        <option value="Mahayahay"></option>
                        <option value="San Vicente"></option>
                        <option value="Cagnonocot"></option>
                        <option value="Sampao"></option>
                        <option value="Bagong Silang"></option>
                        <option value="Sillonay"></option>
                        <option value="Matin-ao"></option>
                        <option value="San Pedro"></option>
                        <option value="Pis-ong"></option>
                        <option value="Si-it"></option>
                        <option value="Pio Poblador"></option>
                        <option value="Lawigan"></option>
                        <option value="Matlang"></option>
                    </datalist>
                </div>
                <div class="col">
                    <label for="name_of_association">Name of Association <span style="color: red;">*</span></label>
                    <input type="text" name="name_of_association" placeholder="Association" class="form-control form-control-sm" >
                </div>
            </div>
            <button type="submit" class="btn btn-success">SAVE</button>
            <a href="{{ route('admin.farmers.create') }}" class="btn btn-warning">Add Farmer</a>
        </form>

    </div>
</div>
<hr>

<!-- Display the list of affiliations -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">AFFILIATION LISTS</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Name of Association</th>
                        <th>Name of Barangay</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affiliations as $affiliation)
                        <tr>
                            <td>{{ $affiliation->name_of_association ?? 'NO ASSOCIATION' }}</td>
                            <td>{{ $affiliation->name_of_barangay }}</td>
                            <td>
                                <!-- Edit and Delete buttons -->
                                <button class="btn btn-warning btn-sm" 
                                    data-id="{{ $affiliation->id }}" 
                                    data-name-of-association="{{ $affiliation->name_of_association ?? 'NO ASSOCIATION' }}" 
                                    data-name-of-barangay="{{ $affiliation->name_of_barangay }}" 
                                    onclick="showEditModal(this)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $affiliation->id }})"><i class="fas fa-trash"></i> </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Affiliation Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Affiliation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-row mb-3">
                        <div class="col">
                            <label for="edit_name_of_association">Name of Association</label>
                            <input type="text" name="name_of_association" id="edit_name_of_association" class="form-control form-control-sm">
                        </div>
                        <div class="col">
                            <label for="edit_name_of_barangay">Name of Barangay</label>
                            <input type="text" name="name_of_barangay" id="edit_name_of_barangay" class="form-control form-control-sm">
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
                Are you sure you want to delete this affiliation?
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

<!-- JavaScript to handle edit and delete modals -->
<script>
    function showEditModal(button) {
        const affiliationId = button.getAttribute('data-id');
        const nameOfAssociation = button.getAttribute('data-name-of-association');
        const nameOfBarangay = button.getAttribute('data-name-of-barangay');
        
        document.getElementById('edit_name_of_association').value = nameOfAssociation;
        document.getElementById('edit_name_of_barangay').value = nameOfBarangay;
        document.getElementById('editForm').action = `/admin/affiliations/${affiliationId}`; // Set form action dynamically
        $('#editModal').modal('show'); // Show edit modal
    }

    function confirmDelete(affiliationId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/affiliations/${affiliationId}`; // Set form action dynamically
        $('#deleteModal').modal('show'); // Show delete modal
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

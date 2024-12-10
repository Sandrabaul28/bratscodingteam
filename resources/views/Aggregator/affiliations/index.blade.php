@extends('layouts.aggregator.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success"><span class="font-weight-bold">ADD AFFILIATION / AFFILIATION LISTS</span></h6>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form to create a new affiliation -->
        <form action="{{ route('affiliations.store') }}" method="POST">
            @csrf
            <div class="form-row mb-3">
                <div class="col">
                    <label for="name_of_barangay">Name of Barangay <span style="color: red;">*</span></label>
                    <input list="barangays" name="name_of_barangay" placeholder="Barangay" class="form-control form-control-sm" required>
                    <datalist id="barangays">
                        <option value="Anahao"></option>
                        <option value="Banahao"></option>
                        <option value="Baugo"></option>
                        <option value="Beniton"></option>
                        <option value="Buenavista"></option>
                        <option value="Bunga"></option>
                        <option value="Casao"></option>
                        <option value="Catmon"></option>
                        <option value="Catuogan"></option>
                        <option value="Cawayanan"></option>
                        <option value="Dao"></option>
                        <option value="Divisoria"></option>
                        <option value="Esperanza"></option>
                        <option value="Guinsangaan"></option>
                        <option value="Hibagwan"></option>
                        <option value="Hilaan"></option>
                        <option value="Himakilo"></option>
                        <option value="Hitawos"></option>
                        <option value="Lanao"></option>
                        <option value="Lawgawan"></option>
                        <option value="Mahayahay"></option>
                        <option value="Malbago"></option>
                        <option value="Mauylab"></option>
                        <option value="Olisihan"></option>
                        <option value="Paku"></option>
                        <option value="Pamahawan"></option>
                        <option value="Pamigsian"></option>
                        <option value="Pangi"></option>
                        <option value="Poblacion"></option>
                        <option value="Pong-on"></option>
                        <option value="Sampongon"></option>
                        <option value="San Ramon"></option>
                        <option value="San Vicente"></option>
                        <option value="Santa Cruz"></option>
                        <option value="Sto. Niño"></option>
                        <option value="Taa"></option>
                        <option value="Talisay"></option>
                        <option value="Taytagan"></option>
                        <option value="Tuburan"></option>
                        <option value="Union"></option>
                    </datalist>
                </div>
                <div class="col">
                    <label for="name_of_association">Name of Association <span style="color: red;">*</span></label>
                    <input type="text" name="name_of_association" placeholder="Association" class="form-control form-control-sm">
                </div>
            </div>
            <button type="submit" class="btn btn-success">SAVE</button>
            <a href="{{ route('aggregator.farmers.create') }}" class="btn btn-warning">Add Farmer</a>
        </form>
    </div>
</div>
<hr>
<!-- Display the list of affiliations -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 text-success">AFFILIATION LISTS</h6>
        <input type="text" id="searchInput" class="form-control form-control-sm w-25" placeholder="Search...">
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered mb-4" id="affiliationTable">
                <thead>
                    <tr>
                        <th>Name of Barangay</th>
                        <th>Name of Association</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affiliations as $affiliation)
                        <tr>
                            <!-- Display NO ASSOCIATION if association is null -->
                            <td>{{ $affiliation->name_of_barangay }}</td>

                            <td>{{ $affiliation->name_of_association ?? 'no association' }}</td>
                            <td>
                                <!-- Edit and Delete buttons -->
                                <button class="btn btn-warning btn-sm" onclick="editAffiliation({{ $affiliation }})"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $affiliation->id }})"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#affiliationTable tbody tr');

        rows.forEach(row => {
            let barangay = row.cells[0].textContent.toLowerCase();
            let association = row.cells[1].textContent.toLowerCase();
            if (barangay.includes(filter) || association.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

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
                <form id="editForm" action="{{ route('affiliations.update', $affiliation->id) }}" method="POST">
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
    function editAffiliation(affiliation) {
        document.getElementById('edit_name_of_association').value = affiliation.name_of_association;
        document.getElementById('edit_name_of_barangay').value = affiliation.name_of_barangay;
        document.getElementById('editForm').action = `/aggregator/affiliations/${affiliation.id}`; // Set form action dynamically
        $('#editModal').modal('show'); // Show edit modal
    }

    function confirmDelete(affiliationId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/aggregator/affiliations/${affiliationId}`; // Set form action dynamically
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

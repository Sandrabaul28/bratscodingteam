@extends('layouts.admin.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">HVCDP Records</h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.count.count')}}" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i>Add Record</a>
            <div>
                <!-- Print Button -->
                <a href="{{ route('admin.hvcdp.print', ['from_date' => request('from_date'), 'to_date' => request('to_date'), 'barangay' => request('barangay'), 'inputted_data' => request('inputted_data')]) }}" 
                   class="btn btn-primary" target="_blank">
                   <i class="fa fa-print" aria-hidden="true"></i><br>Print
                </a>
                
                <!-- Export to Excel Button -->
                
            </div>
        </div>

        <div class="mb-4">
            <!-- Filter by Date Form -->
            <form action="{{ route('admin.hvcdp.index') }}" method="GET" class="form-inline mb-3">
                <label for="from_date">From: </label>
                <input type="date" name="from_date" id="from_date" class="form-control mx-2">
                <label for="to_date">To: </label>
                <input type="date" name="to_date" id="to_date" class="form-control mx-2">
                <button type="submit" class="btn btn-success mx-2">Filter</button>
                <a href="{{ route('admin.hvcdp.index') }}" class="btn btn-secondary mx-2">Reset</a>
            </form>

            <!-- Filter by Barangay Form -->
            <form action="{{ route('admin.hvcdp.index') }}" method="GET" class="form-inline">
                <label for="barangay">Filter by Barangay: </label>
                <select name="barangay" id="barangay" class="form-control mx-2">
                    <option value="">-- Select Barangay --</option>
                    @foreach($affiliations as $affiliation)
                        <option value="{{ $affiliation->name_of_barangay }}">{{ $affiliation->name_of_barangay }} - {{ $affiliation->name_of_association }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success mx-2">Filter</button>
                <a href="{{ route('admin.hvcdp.index') }}" class="btn btn-secondary mx-2">Reset</a>
            </form>
            <br>
            <!-- Filter by Inputted Data Form -->
            <form action="{{ route('admin.hvcdp.index') }}" method="GET" class="form-inline mb-3">
                <label>Filter Farmers with Data: </label>
                <div class="form-check form-check-inline mx-2">
                    <input class="form-check-input" type="checkbox" id="withData" name="inputted_data[]" value="yes">
                    <label class="form-check-label" for="withData">With Data</label>
                </div>
                <div class="form-check form-check-inline mx-2">
                    <input class="form-check-input" type="checkbox" id="withoutData" name="inputted_data[]" value="no">
                    <label class="form-check-label" for="withoutData">Without Data</label>
                </div>
                <button type="submit" class="btn btn-success mx-2">Filter</button>
                <a href="{{ route('admin.hvcdp.index') }}" class="btn btn-secondary mx-2">Reset</a>
            </form>


        </div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">DATA LISTS</h6>
    </div>
    <div class="card-body">
         <!-- Search Bar -->
        <input type="text" id="searchBar" class="form-control form-control-sm" placeholder="Search by First Name or Last Name" style="max-width: 300px;" onkeyup="filterFarmersTable()">
        <!-- JavaScript for Filtering Table -->
        <script>
            function filterFarmersTable() {
                // Get input from the search bar
                const query = document.getElementById("searchBar").value.toLowerCase();
                const table = document.getElementById("farmersTable");
                const rows = table.getElementsByTagName("tr");

                // Loop through all table rows (except the header)
                for (let i = 1; i < rows.length; i++) {
                    const surnameCell = rows[i].getElementsByTagName("td")[1];
                    const firstnameCell = rows[i].getElementsByTagName("td")[2];
                    const affiliationCell = rows[i].getElementsByTagName("td")[3];

                    if (surnameCell && firstnameCell && affiliationCell) {
                        const surname = surnameCell.textContent.toLowerCase();
                        const firstname = firstnameCell.textContent.toLowerCase();
                        const affiliation = affiliationCell.textContent.toLowerCase();

                        // Check if the query matches any of the fields
                        if (
                            surname.includes(query) || 
                            firstname.includes(query) || 
                            affiliation.includes(query)
                        ) {
                            rows[i].style.display = ""; // Show row
                        } else {
                            rows[i].style.display = "none"; // Hide row
                        }
                    }
                }
            }
        </script>

        <br>
    <div class="table-responsive">
        <table class="table table-bordered"  id="farmersTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Surname</th>
                    <th>Firstname</th>
                    <th>Affiliation</th>
                    <th>Has Data</th> <!-- Existing column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($farmers as $farmer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $farmer->last_name }}</td>
                    <td>{{ $farmer->first_name }}</td>
                    <td>{{ $farmer->affiliation->name_of_barangay }}</td>
                    <td>{{ $farmer->inventoryValuedCrops->isNotEmpty() ? 'Yes' : 'No' }}</td>
                    <td>
                        <button class="btn btn-info" data-toggle="modal" data-target="#viewModal{{ $farmer->id }}"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $farmer->id }}"><i class="fas fa-edit"></i> </button>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $farmer->id }}"><i class="fas fa-trash"></i> </button>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal{{ $farmer->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $farmer->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel{{ $farmer->id }}">Farmer Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Surname : </strong>{{ $farmer->last_name }}</p>
                                <p><strong>Firstname : </strong>{{ $farmer->first_name }}</p>
                                <p><strong>Affiliation : </strong>{{ $farmer->affiliation->name_of_barangay }} {{ $farmer->affiliation->name_of_association }}</p>
                                <p><strong>Plants :</strong></p>
                                <ul>
                                    @foreach($farmer->inventoryValuedCrops as $inventoryValuedCrop)
                                        <li>{{ $inventoryValuedCrop->plant->name_of_plants }} - {{ $inventoryValuedCrop->count }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $farmer->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $farmer->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('admin.hvcdp.update', $farmer->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $farmer->id }}">Edit Farmer</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="last_name">Surname <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" name="last_name" value="{{ $farmer->last_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="first_name">Firstname <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" name="first_name" value="{{ $farmer->first_name }}">
                                    </div>
                                    <!-- Plants and quantities -->
                                    <div class="form-group">
                                        <label for="plants">Plants</label>
                                        <ul>
                                            @foreach($farmer->inventoryValuedCrops as $inventoryValuedCrop)
                                            <li>
                                                <label>{{ $inventoryValuedCrop->plant->name_of_plants }}</label>
                                                <input type="number" name="plants[{{ $inventoryValuedCrop->plant_id }}]" value="{{ $inventoryValuedCrop->count }}" class="form-control">
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    document.querySelectorAll('.btn-edit').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const modal = document.getElementById('editModal' + id);
                        modal.querySelector('input[name="first_name"]').value = this.getAttribute('data-first_name');
                        modal.querySelector('input[name="last_name"]').value = this.getAttribute('data-last_name');
                        // Populate plant data if needed
                    });
                });

                </script>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $farmer->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $farmer->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $farmer->id }}">Delete Farmer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete {{ $farmer->first_name }} {{ $farmer->last_name }}?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('admin.hvcdp.destroy', $farmer->id) }}" method="POST">
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
        
    </div>
</div>
@endsection

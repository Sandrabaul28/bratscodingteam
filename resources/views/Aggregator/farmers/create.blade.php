@extends('layouts.Aggregator.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">ADD NEW FARMER</h6>
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

        <form action="{{ route('aggregator.farmers.store') }}" method="POST">
            @csrf

            <!-- Farmer Details Section -->
            <div class="form-row mb-3">
                <div class="col-12 col-md-6">                    
                    <label for="first_name">First Name <span style="color: red;">*</span></label>
                    <input type="text" name="first_name" placeholder="First name" class="form-control form-control-sm" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="last_name">Last Name <span style="color: red;">*</span></label>
                    <input type="text" name="last_name" placeholder="Last name" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-row mb-3">
                <div class="col-12 col-md-6">
                    <label for="middle_name">Middle Name (Optional)</label>
                    <input type="text" name="middle_name" placeholder="Middle Initial" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-6">
                    <label for="extension">Extension (e.g., Jr, Sr)</label>
                    <input type="text" name="extension" placeholder="jr, sr, etc." class="form-control form-control-sm">
                </div>
            </div>

            <!-- Control Number and Birthdate -->
            <div class="form-row mb-3">
                <div class="col-12 col-md-6">
                    <label for="control_number">Reference No. / Control No.</label>
                    <input type="text" name="control_number" id="control_number" placeholder="08-64-02-037-000001" 
                        class="form-control form-control-sm" 
                        value="{{ old('control_number', '08-64-02-') }}" 
                        required>            
                </div>
                <div class="col-12 col-md-6">
                    <label for="birthdate">Birthdate</label>
                    <input type="date" name="birthdate" id="birthdate" class="form-control form-control-sm">
                </div>
            </div>

            <!-- Barangay and Association Fields UPDATED-->
            <div class="form-row mb-3">
                <div class="col">
                    <label for="barangay">Barangay</label>
                    <input list="barangays" name="name_of_barangay" placeholder="Select Barangay" class="form-control form-control-sm">
                    <datalist id="barangays">
                        @foreach($uniqueBarangays as $barangay)
                            <option value="{{ $barangay }}">
                            @endforeach
                    </datalist>
                </div>

                <div class="col-12 col-md-6">
                    <label for="association">Association</label>
                    <input type="text" name="name_of_association" id="association" class="form-control form-control-sm" placeholder="Optional Association" list="association-list">
                    <datalist id="association-list">
                        @foreach($uniqueAssociations as $association)
                            <option value="{{ $association }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="form-row mb-3">
                <!-- <div class="col-12 col-md-6">
                    <label for="affiliation">Affiliation</label>
                    <input type="text" id="affiliation" name="affiliation" class="form-control form-control-sm" placeholder="Type to search..." list="affiliations-list" >
                    <datalist id="affiliations-list">
                        @foreach($affiliations as $affiliation)
                            <option value="{{ $affiliation->name_of_association }} - {{ $affiliation->name_of_barangay }}" data-id="{{ $affiliation->id }}"></option>
                        @endforeach
                    </datalist>
                    <input type="hidden" id="affiliation_id" name="affiliation_id">
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const affiliationInput = document.getElementById('affiliation');
                    const affiliationIdInput = document.getElementById('affiliation_id');

                    affiliationInput.addEventListener('input', function () {
                        const value = this.value;

                        const selectedOption = [...document.querySelectorAll('#affiliations-list option')]
                            .find(option => option.value === value);

                        if (selectedOption) {
                            affiliationIdInput.value = selectedOption.dataset.id; // Set the hidden input with the id
                        } else {
                            affiliationIdInput.value = ''; // Clear the hidden input if no match
                        }
                    });
                });
                </script> -->


                <div class="col-12 col-md-6">
                    <label for="email">Email Address (Optional)</label>
                    <input type="email" name="email" placeholder="Email address" class="form-control form-control-sm">
                </div>

                <div class="col-12 col-md-6">
                    <label for="password">Password (Optional)</label>
                    <input type="password" name="password" placeholder="************" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-6">
                    <label for="password_confirmation">Confirm Password (Optional)</label>
                    <input type="password" name="password_confirmation" placeholder="************" class="form-control form-control-sm">
                </div>
                <!-- Hidden role_id field -->
                <input type="hidden" name="role_id" value="2">
            </div>

            <button type="submit" class="btn btn-danger">SAVE</button>
        </form>
    </div>
</div>

<!-- Farmer List Section -->

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">FARMER LISTS</h6>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <input type="text" id="searchBar" class="form-control form-control-sm" placeholder="Search by Name, Barangay, Association, or Control Number" style="max-width: 300px;" onkeyup="filterFarmersTable()">
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
                    const barangayCell = rows[i].getElementsByTagName("td")[3];
                    const associationCell = rows[i].getElementsByTagName("td")[4];
                    const controlNumberCell = rows[i].getElementsByTagName("td")[5];

                    if (surnameCell && firstnameCell && barangayCell && associationCell && controlNumberCell) {
                        const surname = surnameCell.textContent.toLowerCase();
                        const firstname = firstnameCell.textContent.toLowerCase();
                        const barangay = barangayCell.textContent.toLowerCase();
                        const association = associationCell.textContent.toLowerCase();
                        const controlNumber = controlNumberCell.textContent.toLowerCase();

                        // Check if the query matches any of the fields
                        if (
                            surname.includes(query) || 
                            firstname.includes(query) || 
                            barangay.includes(query) || 
                            association.includes(query) || 
                            controlNumber.includes(query)
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
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered" id="farmersTable">
            <thead>
                <tr>
                    <th>id</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Barangay</th>
                    <th>Association</th>
                    <th>Reference no. / Control no.</th>
                    <th>Added by</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    @php
                        $count =1;
                    @endphp
                @foreach($farmers as $farmer)
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{{ $farmer->first_name }}</td>
                        <td>{{ $farmer->last_name }}</td>
                        <td>{{$farmer->affiliation->name_of_barangay}} </td>
                        <td>{{ $farmer->affiliation->name_of_association ?? 'N/A'}}</td>
                        <td>{{ $farmer->control_number }}</td>
                        <td><span style="font-style: normal;">{{ $farmer->addedBy->first_name ?? 'N/A' }}</span> / <span style="font-style: italic;">{{ $farmer->addedBy->role->role_name ?? 'N/A' }}</span></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal-{{ $farmer->id }}"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal-{{ $farmer->id }}"><i class="fas fa-edit"></i></button>
                            <!-- <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $farmer->id }}"><i class="fas fa-trash"></i> </button> -->
                        </td>
                    </tr>
                    <!-- View Modal -->
                    <!-- View Modal -->
                    <div class="modal fade" id="viewModal-{{ $farmer->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewModalLabel">View Farmer Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>First Name:</strong> {{ $farmer->first_name }}</p>
                                    <p><strong>Last Name:</strong> {{ $farmer->last_name }}</p>
                                    <p><strong>Middle Name:</strong> {{ $farmer->middle_name }}</p>
                                    <p><strong>Extension:</strong> {{ $farmer->extension }}</p>
                                    <p><strong>Barangay:</strong> {{ $farmer->affiliation->name_of_barangay ?? 'N/A' }}</p>
                                    <p><strong>Association:</strong> {{ $farmer->affiliation->name_of_association ?? 'N/A' }}</p>
                                    <p><strong>Reference no. / Control no.:</strong> {{ $farmer->control_number }}</p>
                                    <p><strong>Birthdate:</strong> {{ $farmer->birthdate }}</p>

                                    <p><strong>Email:</strong> {{ optional($farmer->user)->email ?? 'N/A' }}</p> <!-- Use optional() to avoid errors -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal-{{ $farmer->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Farmer</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('aggregator.farmers.update', $farmer->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <!-- Farmer Information -->
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <label for="first_name">First Name <span style="color: red;">*</span></label>
                                                <input type="text" name="first_name" value="{{ $farmer->first_name }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col">
                                                <label for="last_name">Last Name <span style="color: red;">*</span></label>
                                                <input type="text" name="last_name" value="{{ $farmer->last_name }}" class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <label for="middle_name">Middle Name (Optional)</label>
                                                <input type="text" name="middle_name" value="{{ $farmer->middle_name }}" class="form-control form-control-sm">
                                            </div>
                                            <div class="col">
                                                <label for="extension">Extension (e.g., Jr, Sr)</label>
                                                <input type="text" name="extension" value="{{ $farmer->extension }}" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <label for="affiliation_id">Affiliation</label>
                                                <select name="affiliation_id" class="form-control form-control-sm">
                                                    @foreach($affiliations as $affiliation)
                                                        <option value="{{ $affiliation->id }}" {{ $affiliation->id == $farmer->affiliation_id ? 'selected' : '' }}>{{ $affiliation->name_of_barangay}} - {{ $affiliation->name_of_association}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="birthdate">Birthdate</label>
                                                <input type="date" name="birthdate" value="{{ $farmer->birthdate }}" class="form-control form-control-sm">
                                            </div>
                                        </div>

                                        <!-- Account Information Toggle -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="addAccount-{{ $farmer->id }}" name="add_account" onchange="toggleAccountFields({{ $farmer->id }})" {{ $farmer->user ? 'checked' : '' }}>
                                            <label class="form-check-label" for="addAccount-{{ $farmer->id }}">Add/Edit Account</label>
                                        </div>

                                        <!-- Account Fields (Show/Hide based on checkbox) -->
                                        <div id="accountFields-{{ $farmer->id }}" class="account-fields {{ $farmer->user ? '' : 'd-none' }}">
                                            <div class="form-row mb-3">
                                                <div class="col">
                                                    <label for="email">Email Address</label>
                                                    <input type="email" name="email" value="{{ $farmer->user->email ?? '' }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="col">
                                                    <label for="password">Password</label>
                                                    <input type="password" name="password" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="form-row mb-3">
                                                <div class="col">
                                                    <label for="password_confirmation">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Update</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function toggleAccountFields(id) {
                            const accountFields = document.getElementById(`accountFields-${id}`);
                            accountFields.classList.toggle('d-none');
                        }
                    </script>


                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal-{{ $farmer->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Delete Farmer</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this farmer?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('aggregator.farmers.destroy', $farmer->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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

<script>
    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 5000);
</script>
@endsection

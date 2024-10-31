@extends('layouts.admin.app')

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


        <form action="{{ route('admin.farmers.store') }}" method="POST">
        @csrf
        <!-- Farmer Details Section -->
        <div class="form-row mb-3">
            <div class="col">
                <label for="first_name">First Name <span style="color: red;">*</span></label>
                <input type="text" name="first_name" placeholder="First name" class="form-control form-control-sm" required>
            </div>
            <div class="col">
                <label for="last_name">Last Name <span style="color: red;">*</span></label>
                <input type="text" name="last_name" placeholder="Last name" class="form-control form-control-sm" required>
            </div>
        </div>

        <div class="form-row mb-3">
            <div class="col">
                <label for="middle_name">Middle Name (Optional)</label>
                <input type="text" name="middle_name" placeholder="Middle Initial" class="form-control form-control-sm">
            </div>
            <div class="col">
                <label for="extension">Extension (e.g., Jr, Sr)</label>
                <input type="text" name="extension" placeholder="jr, sr, etc." class="form-control form-control-sm">
            </div>
        </div>

        <!-- Control Number and Birthdate -->
        <div class="form-row mb-3">
            <div class="col">
                <label for="control_number">Reference No. / Control No.</label>
                <input type="text" name="control_number" id="control_number" placeholder="08-64-02-037-000001" class="form-control form-control-sm">
            </div>
            <div class="col">
                <label for="birthdate">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" class="form-control form-control-sm">
            </div>
        </div>

        <div class="form-row mb-3">
            <div class="col">
                <label for="affiliation_id">Affiliation</label>
                <select name="affiliation_id" class="form-control form-control-sm">
                    @foreach($affiliations as $affiliation)
                        <option value="{{ $affiliation->id }}">{{ $affiliation->name_of_association ?? $affiliation->name_of_barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="email">Email Address (Optional)</label>
                <input type="email" name="email" placeholder="Email address" class="form-control form-control-sm">
            </div>
        </div>

        <div class="form-row mb-3">
            <div class="col">
                <label for="password">Password (Optional)</label>
                <input type="password" name="password" placeholder="************" class="form-control form-control-sm">
            </div>
            <div class="col">
                <label for="password_confirmation">Confirm Password (Optional)</label>
                <input type="password" name="password_confirmation" placeholder="************" class="form-control form-control-sm">
            </div>
            <!-- Hidden role_id field -->
            <input type="hidden" name="role_id" value="2">
        </div>

        <button type="submit" class="btn btn-danger">SAVE</button>
        <a href="{{ route('admin.affiliations.index') }}" class="btn btn-warning">Add Affiliation</a>
    </form>

    </div>
</div>

<div class="d-flex align-items-center mt-2" style="width: 300px;">
    <form action="{{ route('admin.farmers.import') }}" method="POST" enctype="multipart/form-data" class="input-group input-group-sm mr-2">
        @csrf
        <div class="custom-file">
            <input type="file" name="file" class="custom-file-input" id="file" required>
            <label class="custom-file-label" for="file">Choose file...</label>
        </div>
        <div class="input-group-append">
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-file-import"></i> Import
            </button>
        </div>
    </form>
</div>

<script>
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name; // Gamitin ang e.target
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName; // I-update ang label na may file name
    });
</script>

<hr>


<!-- Farmer List Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">FARMER LISTS</h6>
    </div>
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('admin.farmers.index') }}" class="mb-3">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by First or Last Name" list="farmers-list" value="{{ request('search') }}">
                    <datalist id="farmers-list">
                        @foreach($farmers as $farmer)
                            <option value="{{ $farmer->first_name }}"></option>
                            <option value="{{ $farmer->last_name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col">
                    <select name="affiliation_id" class="form-control form-control-sm">
                        <option value="">Select Affiliation</option>
                        @foreach($affiliations as $affiliation)
                            <option value="{{ $affiliation->id }}" {{ request('affiliation_id') == $affiliation->id ? 'selected' : '' }}>
                                {{ $affiliation->name_of_association ?? $affiliation->name_of_barangay }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('admin.farmers.index') }}" class="btn btn-secondary btn-sm">Reset</a> <!-- Reset Button -->
                </div>
            </div>
        </form>

        <!-- Show Entries Dropdown -->
        <div class="mb-3">
            <label for="entries" class="mr-2">Show:</label>
            <select id="entries" class="form-control form-control-sm d-inline-block" style="width: auto;" onchange="location = this.value;">
                <option value="{{ route('admin.farmers.index', ['per_page' => 10]) }}" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="{{ route('admin.farmers.index', ['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="{{ route('admin.farmers.index', ['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="{{ route('admin.farmers.index', ['per_page' => 100]) }}" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>id</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Affiliation</th>
                    <th>Reference No. / Control No.</th>
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
                        <td>{{ $farmer->affiliation->name_of_association ?? $farmer->affiliation->name_of_barangay ?? 'N/A' }}</td>
                        <td>{{ $farmer->control_number }}</td>
                        <td><span style="font-style: italic;">{{ $farmer->addedBy->role->role_name ?? 'N/A' }}</span></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal-{{ $farmer->id }}"><i class="fas fa-eye"></i> </button>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal-{{ $farmer->id }}"><i class="fas fa-edit"></i> </button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $farmer->id }}"> <i class="fas fa-trash"></i> </button>
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
                                    <p><strong>Affiliation:</strong> {{ $farmer->affiliation->name_of_association ?? $farmer->affiliation->name_of_barangay ?? 'N/A' }}</p>
                                    <p><strong>Reference no. / Control no. :</strong> {{ $farmer->control_number }}</p>
                                    <p><strong>Birthdate:</strong> {{ $farmer->birthdate }}</p>

                                    <p><strong>Email:</strong> {{ optional($farmer->user)->email ?? 'N/A' }}</p> <!-- Corrected line -->

                                    <p><strong>Added By:</strong> {{ optional($farmer->addedBy->role)->role_name ?? 'N/A' }}</p> <!-- Use optional() for safety -->
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
                                <form action="{{ route('admin.farmers.update', $farmer->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
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
                                                        <option value="{{ $affiliation->id }}" {{ $affiliation->id == $farmer->affiliation_id ? 'selected' : '' }}>{{ $affiliation->name_of_association ?? $affiliation->name_of_barangay }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="email">Email Address (Optional)</label>
                                                <input type="email" name="email" value="{{ $farmer->user->email ?? '' }}" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <input type="hidden" name="role_id" value="2">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Update</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
                                    <form action="{{ route('admin.farmers.destroy', $farmer->id) }}" method="POST">
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
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');

    filterForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Pigilan ang default form submission

        const formData = new FormData(filterForm); // Kunin ang form data

        fetch(filterForm.action, {
            method: 'GET',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Magbigay ng header para sa AJAX request
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text(); // Kunin ang response bilang text
        })
        .then(data => {
            document.getElementById('farmerTable').innerHTML = data; // Palitan ang nilalaman ng table
        })
        .catch(error => console.error('There was a problem with the fetch operation:', error));
    });
});
</script>

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

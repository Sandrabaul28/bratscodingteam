@extends('layouts.aggregator.app')

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
            <a href="{{ route('affiliations.index')}}" class="btn btn-warning">Add Affiliation</a>
        </form>
    </div>
</div>

<!-- Farmer List Section -->

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">FARMER LISTS</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Affiliation</th>
                    <th>Added by</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($farmers as $farmer)
                    <tr>
                        <td>{{ $farmer->first_name }}</td>
                        <td>{{ $farmer->last_name }}</td>
                        <td>{{ $farmer->affiliation->name_of_association ?? $farmer->affiliation->name_of_barangay ?? 'N/A' }}</td>
                        <td><span style="font-style: italic;">{{ $farmer->addedBy->role->role_name ?? 'N/A' }}</span></td>
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
                                    <p><strong>Affiliation:</strong> {{ $farmer->affiliation->name_of_association ?? $farmer->affiliation->name_of_barangay ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ optional($farmer->user)->email ?? 'N/A' }}</p> <!-- Use optional() to avoid errors -->
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
                                <form action="{{ route('aggregator.farmers.update', $farmer->id) }}" method="POST">
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

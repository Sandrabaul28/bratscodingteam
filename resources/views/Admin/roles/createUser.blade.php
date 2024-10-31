@extends('layouts.admin.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">ROLES MANAGEMENT / <span class="font-weight-bold">ADD NEW USER / USER LISTS </span></h6>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('admin.roles.storeUser') }}" method="POST">
            @csrf

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
                    <label for="middle_name">Middle Name <span style="color: red;">*</span> (Middle Initial)</label>
                    <input type="text" name="middle_name" placeholder="Middle Inital" class="form-control form-control-sm">
                </div>
                <div class="col">
                    <label for="extension">Extension <span style="color: red;">*</span> (e.g., Jr, Sr)</label>
                    <input type="text" name="extension" placeholder="jr. , sr. etc." class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-row mb-3">
                <div class="col">
                    <label for="affiliation_id">Affiliation</label>
                    <select name="affiliation_id" class="form-control form-control-sm">
                        @foreach($affiliations as $affiliation)
                            @if ($affiliation->name_of_association)
                                <option value="{{ $affiliation->id }}">{{ $affiliation->name_of_association }}</option>
                            @endif
                            @if ($affiliation->name_of_barangay)
                                <option value="{{ $affiliation->id }}">{{ $affiliation->name_of_barangay }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
        
                <div class="col">
                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="sample@gmail.com" class="form-control form-control-sm" required>
                </div>

                <div class="col">
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="************" class="form-control form-control-sm @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="************" class="form-control form-control-sm" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role_id" class="form-control form-control-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-danger">Add User</button>
            <a href="{{ route('affiliations.index')}}" class="btn btn-warning">Add Affiliation</a>

        </form>
    </div>
</div>
<hr>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">USER LISTS</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th> <!-- Added Actions Column -->
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->role_name }}</td>
                        <td>
                            <!-- Action buttons with modal triggers -->
                            <button class="btn btn-info btn-sm" onclick="viewUser({{ $user }})"><i class="fas fa-eye"></i> </button>
                            <button class="btn btn-warning btn-sm" onclick="editUser({{ $user }})"><i class="fas fa-edit"></i> </button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">View User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>First Name:</strong> <span id="viewFirstName"></span></p>
                <p><strong>Last Name:</strong> <span id="viewLastName"></span></p>
                <p><strong>Extension:</strong> <span id="viewExtension"></span></p>
                <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                <p><strong>Role:</strong> <span id="viewRole"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editFirstName">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select name="role_id" id="editRole" class="form-control">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
            </div>
            <div class="modal-footer">
                <form action="" method="POST" id="deleteUserForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // View User
    function viewUser(user) {
        document.getElementById('viewFirstName').textContent = user.first_name;
        document.getElementById('viewLastName').textContent = user.last_name;
        document.getElementById('viewEmail').textContent = user.email;
        document.getElementById('viewRole').textContent = user.role.role_name;
        $('#viewUserModal').modal('show');
    }

    // Edit User
    function editUser(user) {
        document.getElementById('editFirstName').value = user.first_name;
        document.getElementById('editLastName').value = user.last_name;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editRole').value = user.role_id;
        document.getElementById('editUserForm').action = '/admin/roles/' + user.id; // Ensure this matches the route
        $('#editUserModal').modal('show');
    }


    // Confirm Delete
    function confirmDelete(userId) {
        document.getElementById('deleteUserForm').action = '/admin/roles/' + userId;
        $('#deleteUserModal').modal('show');
    }

    // Fade-out success message
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

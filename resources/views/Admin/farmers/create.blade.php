@extends('layouts.Admin.app')

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
        <!-- Pangalan Fields -->
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

        <!-- Middle Name at Extension Fields -->
        <div class="form-row mb-3">
            <div class="col-12 col-md-6">
                <label for="middle_name">Middle Name</label>
                <input type="text" name="middle_name" placeholder="Middle Name" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-6">
                <label for="extension">Extension (e.g., Jr, Sr)</label>
                <input type="text" name="extension" placeholder="jr, sr, etc." class="form-control form-control-sm">
            </div>
        </div>

        <!-- Control Number at Birthdate Fields --> 
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

        <!-- Barangay and Association Fields -->
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


        <!-- Email Field -->
        <div class="form-row mb-3">
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
            <input type="hidden" name="role_id" value="2">
        </div>

        <button type="submit" class="btn btn-danger">SAVE</button>
    </form>


    </div>
</div>

<hr>


<!-- Farmer List Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-success">FARMER LISTS</h6><br>
        <div>
            <!-- Print All Button -->
            <button class="btn btn-primary btn-sm" onclick="printAllFarmers()">
                <i class="fas fa-print"></i> Print All
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <input type="text" id="searchBar" class="form-control form-control-sm" placeholder="Search by Last Name, First Name, Barangay, Association, Control Number, or Added By" style="max-width: 300px;" onkeyup="filterFarmersTable()">

        <!-- JavaScript for Filtering Table -->
        <script>
            function filterFarmersTable() {
                // Get input from the search bar
                const query = document.getElementById("searchBar").value.toLowerCase();
                const table = document.getElementById("farmer-list");
                const rows = table.getElementsByTagName("tr");

                // Loop through all table rows (except the header)
                for (let i = 1; i < rows.length; i++) {
                    const surnameCell = rows[i].getElementsByTagName("td")[1];
                    const firstnameCell = rows[i].getElementsByTagName("td")[2];
                    const barangayCell = rows[i].getElementsByTagName("td")[3];
                    const associationCell = rows[i].getElementsByTagName("td")[4];
                    const controlNumberCell = rows[i].getElementsByTagName("td")[5];
                    const addedByCell = rows[i].getElementsByTagName("td")[6];

                    if (surnameCell && firstnameCell && barangayCell && associationCell && controlNumberCell && addedByCell) {
                        const surname = surnameCell.textContent.toLowerCase();
                        const firstname = firstnameCell.textContent.toLowerCase();
                        const barangay = barangayCell.textContent.toLowerCase();
                        const association = associationCell.textContent.toLowerCase();
                        const controlNumber = controlNumberCell.textContent.toLowerCase();
                        const addedBy = addedByCell.textContent.toLowerCase();

                        // Check if the query matches any of the fields
                        if (
                            surname.includes(query) || 
                            firstname.includes(query) || 
                            barangay.includes(query) || 
                            association.includes(query) || 
                            controlNumber.includes(query) || 
                            addedBy.includes(query)
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
        <table id="farmer-list" class="table table-bordered">
            <thead>
                <tr>
                    <th>id</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Barangay</th>
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
                        <tr id="farmer-row-{{ $farmer->id }}" data-middle-name="{{ $farmer->middle_name }}"
                            data-extension="{{ $farmer->extension }}"
                            data-birthdate="{{ $farmer->birthdate }}"
                            data-email="{{ $farmer->email }}">
                        <td>{{ $count++ }}</td>
                        <td>{{ $farmer->first_name }}</td>
                        <td>{{ $farmer->last_name }}</td>
                        <td>{{$farmer->affiliation->name_of_barangay}} </td>
                        <td>{{ $farmer->affiliation->name_of_association ?? 'N/A'}}</td>
                        <td>{{ $farmer->control_number }}</td>
                        <td><span style="font-style: normal;">{{ $farmer->addedBy->first_name ?? 'N/A' }}</span> / <span style="font-style: italic;">{{ $farmer->addedBy->role->role_name ?? 'N/A' }}</span></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal-{{ $farmer->id }}"><i class="fas fa-eye"></i> </button>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal-{{ $farmer->id }}"><i class="fas fa-edit"></i> </button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $farmer->id }}"> <i class="fas fa-trash"></i> </button>
                            <!-- Print Button for Individual Farmer -->
                                <button class="btn btn-primary btn-sm" onclick="printFarmer({{ $farmer->id }})"><i class="fas fa-print"></i> </button>
                        </td>
                        <script>
                            function printFarmer(farmerId) {
                            // Get the table row for the specific farmer
                            const row = document.querySelector(`#farmer-row-${farmerId}`);
                            
                            // Extract the cells in the row (not including 'id', 'actions', or 'added by')
                            const cells = row.querySelectorAll('td');
                            
                            // Fetch missing details from data attributes in the row
                            const middleName = row.getAttribute('data-middle-name') || '-'; // Default to 'N/A' if not available
                            const extension = row.getAttribute('data-extension') || '-'; // Default to 'N/A' if not available
                            const controlNumber = row.getAttribute('data-control-number') || '-'; // Default to 'N/A' if not available
                            const birthdate = row.getAttribute('data-birthdate') || '-'; // Default to 'N/A' if not available
                            const email = row.getAttribute('data-email') || '-'; // Default to 'N/A' if not available

                            // Start building the content for the print
                            let content = '<table border="1" style="width: 100%; border-collapse: collapse;">';
                            content += '<thead><tr><th>First Name</th><th>Last Name</th><th>Middle Name</th><th>Extension</th><th>Barangay</th><th>Affiliation</th><th>Reference No. / Control No.</th><th>Birthdate</th><th>Email</th></tr></thead>';
                            content += '<tbody><tr>';
                            
                            // Add each cell (columns) with extra details
                            content += `<td>${cells[1]?.innerText || ''}</td>`; // First Name
                            content += `<td>${cells[2]?.innerText || ''}</td>`; // Last Name

                            content += `<td>${middleName}</td>`; // Middle Name
                            content += `<td>${extension}</td>`; // Extension
                            content += `<td>${cells[3]?.innerText || ''}</td>`; // Barangay
                            content += `<td>${cells[4]?.innerText || ''}</td>`; // Affiliation
                            content += `<td>${cells[5]?.innerText || ''}</td>`; // Reference No. / Control No.
                            
                            // Add the additional fields
                            content += `<td>${birthdate}</td>`; // Birthdate
                            content += `<td>${email}</td>`; // Email

                            content += '</tr></tbody></table>';

                            // Open a new window for printing
                            const printWindow = window.open('', '', 'height=600,width=800');
                            printWindow.document.write('<html><head><title>Farmer Details</title></head><body>');
                            
                            // Add print-specific CSS inside the <style> tag
                            printWindow.document.write(`
                                <style>
                                    body {
                                        font-family: Arial, sans-serif;
                                    }
                                    @media print {
                                        @page {
                                            margin: 0;
                                        }
                                        body {
                                            margin: 1cm;
                                        }
                                        /* Hide any header or footer in the HTML */
                                        header, footer {
                                            display: none;
                                        }
                                    }
                                </style>
                            </head><body>`);
                            
                            printWindow.document.write(content);
                            printWindow.document.write('</body></html>');
                            printWindow.document.close();

                            // Trigger print once content is fully loaded
                            printWindow.print();
                        }

                        function printAllFarmers() {
                        // Select all visible rows in the farmer table (excluding header)
                        const rows = document.querySelectorAll('table#farmer-list tbody tr');

                        // Start building the table content
                        let content = `
                            <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Middle Name</th>
                                        <th>Extension</th>
                                        <th>Barangay</th>
                                        <th>Affiliation</th>
                                        <th>Reference No. / Control No.</th>
                                        <th>Birthdate</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        rows.forEach(row => {
                            // Ensure the row is visible
                            if (row.style.display !== "none") {
                                // Fetch all table cells from the row
                                const cells = row.querySelectorAll('td');

                                // Skip rows with no data
                                if (cells.length === 0) return;

                                // Fetch data from data attributes or cells
                                const middleName = row.getAttribute('data-middle-name') || ''; // Data attribute for middle name
                                const extension = row.getAttribute('data-extension') || '';   // Data attribute for extension
                                const controlNumber = row.getAttribute('data-control-number') || '';
                                const birthdate = row.getAttribute('data-birthdate') || '';   // Data attribute for birthdate
                                // const email = row.getAttribute('data-email') || '';           // Data attribute for email

                                // Add a row to the content
                                content += `
                                    <tr>
                                        <td>${cells[1]?.innerText.trim() || ''}</td> <!-- First Name -->
                                        <td>${cells[2]?.innerText.trim() || ''}</td> <!-- Last Name -->
                                        <td>${middleName}</td> <!-- Middle Name -->
                                        <td>${extension}</td> <!-- Extension -->
                                        <td>${cells[3]?.innerText.trim() || ''}</td> <!-- Barangay -->
                                        <td>${cells[4]?.innerText.trim() || ''}</td> <!-- Affiliation -->
                                        <td>${cells[5]?.innerText.trim() || ''}</td> <!-- Reference No. / Control No. -->
                                        <td>${birthdate}</td> <!-- Birthdate -->
                                    </tr>
                                `;
                            }
                        });

                        content += `</tbody></table>`;

                        // Open a new window for printing
                        const printWindow = window.open('', '', 'height=600,width=800');
                        printWindow.document.write('<html><head><title>Farmer List</title>');
                        printWindow.document.write(`
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }
                                th, td {
                                    border: 1px solid #000;
                                    padding: 8px;
                                }
                                th {
                                    background-color: #f2f2f2;
                                }
                                @media print {
                                    @page {
                                        margin: 0;
                                    }
                                    body {
                                        margin: 1cm;
                                    }
                                }
                            </style>
                        </head><body>`);
                        printWindow.document.write(content);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();

                        // Trigger the print action once the window content has loaded
                        printWindow.onload = function () {
                            printWindow.print();
                        };
                    }

                        </script>
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
                                    <p><strong>Reference no. / Control no. :</strong> {{ $farmer->control_number }}</p>
                                    <p><strong>Birthdate:</strong> {{ $farmer->birthdate }}</p>

                                    <p><strong>Email:</strong> {{ optional($farmer->user)->email ?? 'N/A' }}</p> <!-- Corrected line -->

                                    <p><strong>Added By:</strong>{{ $farmer->addedBy->first_name ?? 'N/A' }} / {{ optional(optional($farmer->addedBy)->role)->role_name}}</p>

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
                                                <label for="control_number">Reference no. / Control no.</label>
                                                <input type="text" name="control_number" value="{{ $farmer->control_number }}" class="form-control form-control-sm">
                                            </div>
                                            <div class="col">
                                                <label for="birthdate">Birthdate</label>
                                                <input type="date" name="birthdate" value="{{ $farmer->birthdate }}" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <label for="affiliation_id">Affiliation</label>
                                                <select name="affiliation_id" class="form-control form-control-sm">
                                                    @foreach($affiliations as $affiliation)
                                                        <option value="{{ $affiliation->id }}" {{ $affiliation->id == $farmer->affiliation_id ? 'selected' : '' }}>
                                                            {{ $affiliation->name_of_barangay }} - {{ $affiliation->name_of_association }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Account Information -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="addAccount-{{ $farmer->id }}" name="add_account" onchange="toggleAccountFields({{ $farmer->id }})" {{ $farmer->user ? 'checked' : '' }}>
                                            <label class="form-check-label" for="addAccount-{{ $farmer->id }}">Add/Edit Account</label>
                                        </div>

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

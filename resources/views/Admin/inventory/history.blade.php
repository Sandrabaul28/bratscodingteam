@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <!-- Card for Recorded Inventory -->
    <div class="card shadow mb-4 mt-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 text-success">
                <span class="font-weight-bold">INVENTORY VALUED CROPS</span>
            </h6>
            <div class="d-flex">
                <select id="month" class="form-control mr-2">
                    @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                    @endforeach
                </select>

                <select id="year" class="form-control mr-2">
                    @foreach (range(date('Y'), date('Y', strtotime('-10 years'))) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <a href="#" id="export-button" class="btn btn-success">
                    <i class="fa fa-th" aria-hidden="true"></i><br>Excel
                </a>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.getElementById('export-button').onclick = function(event) {
                event.preventDefault(); // Prevent the default action of the link

                var month = document.getElementById('month').value;
                var year = document.getElementById('year').value;

                // Construct URLs for preview and download
                var previewUrl = "{{ route('admin.inventory.previewHistory', ['month' => '__month__', 'year' => '__year__']) }}";
                var downloadUrl = "{{ route('admin.inventory.exportHistory', ['month' => '__month__', 'year' => '__year__']) }}";

                // Replace the placeholders in the URLs
                previewUrl = previewUrl.replace('__month__', month).replace('__year__', year);
                downloadUrl = downloadUrl.replace('__month__', month).replace('__year__', year);

                fetch(previewUrl)
                    .then(response => response.text())
                    .then(html => {
                        // Ensure the HTML is loaded into the modal
                        document.getElementById('previewContent').innerHTML = html;

                        // Set the download URL for the Excel file
                        document.getElementById('download-button').href = downloadUrl;

                        // Show the modal
                        $('#previewModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error fetching preview:', error);
                        alert('There was an error loading the preview.');
                    });

            };
        </script>

        <!-- Modal for Previewing Inventory Data -->
        <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">Preview Inventory Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="previewContent" style="max-height: 400px; overflow:auto; white-space: nowrap;">
                            <!-- Data will be loaded here dynamically -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="#" id="download-button" class="btn btn-success">Download Excel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Search Bar -->

            <!-- Table responsive for Recorded Inventory -->
            <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                <table class="table table-bordered" id="recorded-inventory-table">
                    <thead>
                        <tr>
                            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Farmer</th>
                            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Barangay</th>
                            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Association</th>
                            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records->groupBy('farmer_id') as $farmerRecords)
                        @php
                            $farmer = $farmerRecords->first()->farmer;
                            $barangay = $farmerRecords->first()->affiliation->name_of_barangay ?? 'n/a';
                            $association = $farmerRecords->first()->affiliation->name_of_association ?? 'n/a';
                        @endphp
                        <tr>
                            <!-- Display Farmer and Barangay information -->
                            <td>{{ $farmer->first_name }} {{ $farmer->last_name }}</td>
                            <td>{{ $barangay }}</td>
                            <td>{{ $association }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="toggleDetails({{ $farmer->id }})">View Details</button>
                            </td>
                        </tr>

                        <!-- Monthly records for this farmer -->
                        <tr id="details-{{ $farmer->id }}" class="collapse-row" style="display: none;">
                            <td colspan="3">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Commodity</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Planting Density (ha)</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Production Vol/ Hectare (MT)</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity Harvested</th>
                                            <th style="position: sticky; top: 0; background: greenyellow; z-index: 1;">Total/ #hill/puno</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted/ha</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative/ha</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive/ha</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity Harvested/ha</th>
                                            <th style="position: sticky; top: 0; background: greenyellow; z-index: 1;">Total/ Planted Area</th>
                                            <th style="position: sticky; top: 0; background: orange; z-index: 1;">Area Harvested</th>
                                            <th style="position: sticky; top: 0; background: orange; z-index: 1;">Production Volume (MT)</th>
                                            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($farmerRecords as $record)
                                        <tr>
                                            <td>{{ $record->created_at->format('F Y') }}</td>
                                            <td>{{ $record->plant->name_of_plants ?? 'N/A' }}</td>
                                            <td>{{ $record->planting_density }}</td>
                                            <td>{{ $record->production_volume }}</td>
                                            <td>{{ $record->newly_planted }}</td>
                                            <td>{{ $record->vegetative }}</td>
                                            <td>{{ $record->reproductive }}</td>
                                            <td>{{ $record->maturity_harvested }}</td>
                                            <td>{{ $record->total }}</td>
                                            <td>{{ $record->newly_planted_divided }}</td>
                                            <td>{{ $record->vegetative_divided }}</td>
                                            <td>{{ $record->reproductive_divided }}</td>
                                            <td>{{ $record->maturity_harvested_divided }}</td>
                                            <td>{{ $record->total_planted_area }}</td>
                                            <td>{{ $record->area_harvested }}</td>
                                            <td>{{ $record->final_production_volume }}</td>
                                            <td>
                                                <form action="{{ route('admin.inventory.delete', $record->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Add JavaScript for the dropdown effect -->
            <script>
                function toggleDetails(farmerId) {
                    var row = document.getElementById('details-' + farmerId);
                    row.style.display = row.style.display === 'none' ? '' : 'none';
                }

                // Apply Filters Functionality
                document.getElementById('apply-filters').addEventListener('click', function() {
                    var monthYear = document.getElementById('month-year-filter').value;
                    var affiliationId = document.getElementById('affiliation-filter').value;

                    // Reload or filter your records based on the selected filters
                    window.location.href = "{{ route('admin.inventory.history') }}" + 
                        (monthYear ? '?month_year=' + monthYear : '') +
                        (affiliationId ? '&affiliation_id=' + affiliationId : '');
                });
            </script>

        </div>
    </div>
</div>
@endsection

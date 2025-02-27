@extends('layouts.Admin.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-primary shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TOTAL USERS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Plants Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-success shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                TOTAL PLANTS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPlants }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-danger shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TOTAL BARANGAYS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBarangay }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Affiliation Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-warning shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TOTAL AFFILIATION
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAffiliation }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Plant Distribution and Monthly Data Overview Row -->
    <div class="row">
        <!-- Monthly Data Overview Bar Chart -->
        <div class="col-xl-6 col-md-6 col-sm-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Data Overview</h6>
                </div>
                <div class="card-body">
                    <!-- Wrapping the chart in a scrollable container -->
                    <div class="chart-container" style="overflow-x: auto; width: 100%; max-width: 100%;">
                        <canvas id="monthlyBarChart" width="400" height="500"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plant Distribution Pie Chart -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Plant Distribution</h6>

                    <!-- Month and Year Filters -->
                    <div class="d-flex align-items-center">
                        <form id="exportForm" action="{{ route('admin.exportPlantSummary') }}" method="GET" class="form-inline">
                            <select name="month" id="month" class="form-control mr-2">
                                <option value="">All Months</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endforeach
                            </select>
                            
                            <select name="year" id="year" class="form-control mr-2">
                                <option value="">All Years</option>
                                @foreach(range(date('Y'), 2000) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>

                            <!-- Print Button aligned to the right -->
                            <button type="submit" class="btn btn-primary" id="printSummary">
                                <i class="fa fa-print" aria-hidden="true"></i><br>Excel
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="plantsPieChart" width="100%" height="100%"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    // Pie Chart for Plant Distribution
    const plantsData = @json($plantsData);

    const labels = plantsData.map(item => item.plant_name);
    const totalPlants = plantsData.map(item => item.total_plants);
    const totalFarmers = plantsData.map(item => item.total_farmers);
    const totalBarangays = plantsData.map(item => item.total_barangays);
    const totalSum = totalPlants.reduce((a, b) => a + b, 0); // Total ng lahat ng halaman

    const ctx = document.getElementById('plantsPieChart').getContext('2d');
    const plantsPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total of Plants per Crop',
                data: totalPlants,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                    '#FF7F50', '#00CED1', '#FFD700', '#32CD32', '#8A2BE2', '#FF4500',
                    '#7FFF00', '#FF6347', '#40E0D0', '#9ACD32', '#FF1493', '#C71585',
                    '#B22222', '#008080', '#F08080', '#D2691E', '#DC143C', '#FF8C00',
                    '#6A5ACD', '#98FB98', '#F0E68C', '#DAA520', '#8B0000', '#7B68EE',
                    '#FFB6C1', '#20B2AA', '#228B22', '#ADFF2F', '#D3D3D3', '#A52A2A',
                    '#800080', '#9B30FF'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: (value, ctx) => {
                        let percentage = ((value / totalSum) * 100).toFixed(2);
                        return `${value} (${percentage}%)`; // ✅ Ipakita ang numerical value at percentage
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const plant = totalPlants[index];
                            const farmers = totalFarmers[index];
                            const barangays = totalBarangays[index];
                            const percentage = ((plant / totalSum) * 100).toFixed(2); // Kinukuha ang percentage

                            return `Plant: ${plant} (${percentage}%)\nFarmer: ${farmers}\nBarangay: ${barangays}`;
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        fontSize: 10
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // Dapat idagdag ito para gumana ang datalabels
    });

    // Bar Chart for Monthly Data Overview
    var ctxBar = document.getElementById('monthlyBarChart').getContext('2d');
    var monthlyBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: [
                @foreach($monthlyData as $data)
                    "{{ date('F', mktime(0, 0, 0, $data->month, 1)) }} {{ $data->year }}",
                @endforeach
            ],
            datasets: [
                {
                    label: 'Total',
                    data: [
                        @foreach($monthlyData as $data)
                            {{ $data->total }},
                        @endforeach
                    ],
                    backgroundColor: 'green',
                    borderColor: 'green',
                    borderWidth: 1
                },
                {
                    label: 'Total Planted Area',
                    data: [
                        @foreach($monthlyData as $data)
                            {{ $data->total_planted_area }},
                        @endforeach
                    ],
                    backgroundColor: 'yellow',
                    borderColor: 'yellow',
                    borderWidth: 1
                },
                {
                    label: 'Production Volume',
                    data: [
                        @foreach($monthlyData as $data)
                            {{ $data->final_production_volume }},
                        @endforeach
                    ],
                    backgroundColor: 'grey',
                    borderColor: 'grey',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month and Year'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total'
                    },
                    beginAtZero: true,
                    suggestedMin: 0,
                    ticks: {
                        stepSize: 1000,
                        min: 1,
                        max: 10000
                    },
                    type: 'logarithmic'
                }
            }
        }
    });

    // // Print summary functionality
    // document.getElementById('printSummary').addEventListener('click', function() {
    //     var summary = '<h3>Plant Distribution Summary</h3>';
    //     summary += '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
    //     summary += '<thead><tr><th>Plant</th><th>Total Plants</th><th>Total Farmers</th><th>Total Barangays</th></tr></thead>';
    //     summary += '<tbody>';

    //     plantsData.forEach(function(item) {
    //         summary += `<tr>
    //                         <td>${item.plant_name}</td>
    //                         <td>${item.total_plants}</td>
    //                         <td>${item.total_farmers}</td>
    //                         <td>${item.total_barangays}</td>
    //                     </tr>`;
    //     });

    //     summary += '</tbody></table>';
        
    //     // Open the print dialog with the summary table
    //     var printWindow = window.open('', '_blank', 'width=800,height=600');
    //     printWindow.document.write('<html><head><title>Print Summary</title></head><body>');
    //     printWindow.document.write(summary);
    //     printWindow.document.write('</body></html>');
    //     printWindow.document.close();
        
    //     // Trigger the print dialog
    //     printWindow.print();
    // });
</script>

@endsection

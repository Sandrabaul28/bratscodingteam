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

    <!-- Additional Insights Row -->
    <div class="row">
        <!-- Total Records Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-info shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                TOTAL RECORDS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRecords }}</div>
                            <div class="text-xs text-muted">
                                Manual: {{ $totalManualRecords }} | Uploaded: {{ $totalUploadedRecords }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Production Volume Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-success shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                TOTAL PRODUCTION (MT)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($combinedTotalData->final_production_volume, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Planted Area Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-warning shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                TOTAL PLANTED AREA (HA)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($combinedTotalData->total_planted_area, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-seedling fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Sources Card -->
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="card border-left-secondary shadow h-20 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                DATA SOURCES
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2 Sources</div>
                            <div class="text-xs text-muted">
                                Manual Entry + Excel Upload
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
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
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>Monthly Data Overview
                    </h6>
                    <div class="text-muted small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Combined Data from Manual & Excel Upload
                    </div>
                </div>
                <div class="card-body">
                    <!-- Wrapping the chart in a scrollable container -->
                    <div class="chart-container" style="overflow-x: auto; width: 100%; max-width: 100%;">
                        <canvas id="monthlyBarChart" width="400" height="500"></canvas>
                    </div>
                    <!-- Chart Legend -->
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <span class="badge badge-success p-2">
                                    <i class="fas fa-seedling mr-1"></i>Total Plants
                                </span>
                            </div>
                            <div class="col-4">
                                <span class="badge badge-warning p-2">
                                    <i class="fas fa-map mr-1"></i>Planted Area (HA)
                                </span>
                            </div>
                            <div class="col-4">
                                <span class="badge badge-info p-2">
                                    <i class="fas fa-weight mr-1"></i>Production (MT)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plant Distribution Pie Chart -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Plant Distribution by Count
                    </h6>

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
                            <button type="submit" class="btn btn-primary btn-sm" id="printSummary">
                                <i class="fa fa-download mr-1"></i>Excel
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="plantsPieChart" width="100%" height="100%"></canvas>
                    <!-- Chart Info -->
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing combined data from manual entries and Excel uploads
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plant Distribution Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-seedling mr-2"></i>Plant Distribution Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Total Plant Counts by Commodity</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Commodity</th>
                                            <th class="text-right">Plant Count</th>
                                            <th class="text-right">Percentage</th>
                                            <th class="text-right">Farmers</th>
                                            <th class="text-right">Barangays</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalPlantCount = $combinedPlantsData->sum('total_plants');
                                        @endphp
                                        @foreach($combinedPlantsData->sortByDesc('total_plants') as $plant)
                                            @php
                                                $percentage = $totalPlantCount > 0 ? ($plant->total_plants / $totalPlantCount) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $plant->plant_name }}</strong></td>
                                                <td class="text-right">{{ number_format($plant->total_plants) }}</td>
                                                <td class="text-right">
                                                    <span class="badge badge-primary">{{ number_format($percentage, 1) }}%</span>
                                                </td>
                                                <td class="text-right">{{ $plant->total_farmers }}</td>
                                                <td class="text-right">{{ $plant->total_barangays }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="thead-light">
                                        <tr>
                                            <th><strong>TOTAL</strong></th>
                                            <th class="text-right"><strong>{{ number_format($totalPlantCount) }}</strong></th>
                                            <th class="text-right"><strong>100.0%</strong></th>
                                            <th class="text-right"><strong>{{ $combinedPlantsData->sum('total_farmers') }}</strong></th>
                                            <th class="text-right"><strong>{{ $combinedPlantsData->sum('total_barangays') }}</strong></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Top 5 Commodities</h6>
                            @foreach($combinedPlantsData->sortByDesc('total_plants')->take(5) as $index => $plant)
                                @php
                                    $percentage = $totalPlantCount > 0 ? ($plant->total_plants / $totalPlantCount) * 100 : 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="font-weight-bold">{{ $index + 1 }}. {{ $plant->plant_name }}</span>
                                        <span class="badge badge-success">{{ number_format($plant->total_plants) }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $percentage }}%" 
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage, 1) }}% of total</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
    const plantsData = @json($combinedPlantsData);

    const labels = plantsData.map(item => item.plant_name);
    const totalPlants = plantsData.map(item => item.total_plants);
    const totalFarmers = plantsData.map(item => item.total_farmers);
    const totalBarangays = plantsData.map(item => item.total_barangays);

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
                        size: 12
                    },
                    formatter: (value, ctx) => {
                        const plantName = ctx.chart.data.labels[ctx.dataIndex];
                        const percentage = ((value / totalPlants.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
                        return `${percentage}%`;
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#fff',
                    borderWidth: 1,
                    cornerRadius: 6,
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems[0].label;
                        },
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const plantName = labels[index];
                            const plantCount = totalPlants[index];
                            const farmers = totalFarmers[index];
                            const barangays = totalBarangays[index];
                            
                            const percentage = ((plantCount / totalPlants.reduce((a, b) => a + b, 0)) * 100).toFixed(1);

                            return [
                                `Plant Count: ${plantCount.toLocaleString()}`,
                                `Percentage: ${percentage}%`,
                                `Farmers: ${farmers}`,
                                `Barangays: ${barangays}`
                            ];
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
        plugins: [ChartDataLabels]
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
                    label: 'Total Plants',
                    data: [
                        @foreach($monthlyData as $data)
                            {{ $data->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Total Planted Area (HA)',
                    data: [
                        @foreach($monthlyData as $data)
                            {{ $data->total_planted_area }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Production Volume (MT)',
                    data: [
                        @foreach($monthlyData as $data)
                            {{ $data->final_production_volume }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#fff',
                    borderWidth: 1,
                    cornerRadius: 6,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label.includes('Area')) {
                                label += context.parsed.y.toFixed(2) + ' HA';
                            } else if (context.dataset.label.includes('Production')) {
                                label += context.parsed.y.toFixed(2) + ' MT';
                            } else {
                                label += context.parsed.y.toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month and Year',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Values',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
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

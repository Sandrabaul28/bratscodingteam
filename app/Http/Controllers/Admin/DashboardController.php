<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plant;
use App\Models\Farmer;
use App\Models\Affiliation;
use App\Models\InventoryValuedCrop;
use App\Models\UploadedInventory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MapExport;

class DashboardController extends Controller
{ 

    public function index()
{
    // Get the monthly data from both manual entries and uploaded data
    $monthlyData = DB::table('monthly_inventories')
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(total_planted_area) as total_planted_area'),
            DB::raw('SUM(final_production_volume) as final_production_volume')
        )
        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get();

    // Get uploaded inventory data grouped by month/year
    $uploadedMonthlyData = DB::table('uploaded_inventories')
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(planted_total) as total_planted_area'),
            DB::raw('SUM(production_volume_mt) as final_production_volume')
        )
        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get();

    // Combine both datasets
    $combinedMonthlyData = collect();
    
    // Get all unique year-month combinations
    $allMonths = collect();
    foreach ($monthlyData as $data) {
        $allMonths->push(['year' => $data->year, 'month' => $data->month]);
    }
    foreach ($uploadedMonthlyData as $data) {
        $allMonths->push(['year' => $data->year, 'month' => $data->month]);
    }
    $allMonths = $allMonths->unique(function ($item) {
        return $item['year'] . '-' . $item['month'];
    });

    // Combine data for each month
    foreach ($allMonths as $month) {
        $manualData = $monthlyData->where('year', $month['year'])->where('month', $month['month'])->first();
        $uploadedData = $uploadedMonthlyData->where('year', $month['year'])->where('month', $month['month'])->first();
        
        $combinedData = (object) [
            'year' => $month['year'],
            'month' => $month['month'],
            'total' => ($manualData->total ?? 0) + ($uploadedData->total ?? 0),
            'total_planted_area' => ($manualData->total_planted_area ?? 0) + ($uploadedData->total_planted_area ?? 0),
            'final_production_volume' => ($manualData->final_production_volume ?? 0) + ($uploadedData->final_production_volume ?? 0)
        ];
        
        $combinedMonthlyData->push($combinedData);
    }

    // Sort by year and month
    $monthlyData = $combinedMonthlyData->sortBy(function ($item) {
        return $item->year * 100 + $item->month;
    })->values();

    // Get the total data from both sources
    $totalData = DB::table('monthly_inventories')
        ->select(
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(total_planted_area) as total_planted_area'),
            DB::raw('SUM(final_production_volume) as final_production_volume')
        )
        ->get();

    $uploadedTotalData = DB::table('uploaded_inventories')
        ->select(
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(planted_total) as total_planted_area'),
            DB::raw('SUM(production_volume_mt) as final_production_volume')
        )
        ->get();

    // Combine total data
    $combinedTotalData = (object) [
        'total' => ($totalData->first()->total ?? 0) + ($uploadedTotalData->first()->total ?? 0),
        'total_planted_area' => ($totalData->first()->total_planted_area ?? 0) + ($uploadedTotalData->first()->total_planted_area ?? 0),
        'final_production_volume' => ($totalData->first()->final_production_volume ?? 0) + ($uploadedTotalData->first()->final_production_volume ?? 0)
    ];

    // Get plants data from both sources - using actual plant counts
    $plantsData = DB::table('plants')
        ->join('monthly_inventories', 'plants.id', '=', 'monthly_inventories.plant_id')
        ->join('farmers', 'farmers.id', '=', 'monthly_inventories.farmer_id')
        ->select('plants.name_of_plants as plant_name', 
                 DB::raw('SUM(monthly_inventories.total) as total_plants'), 
                 DB::raw('COUNT(DISTINCT farmers.id) as total_farmers'), 
                 DB::raw('COUNT(DISTINCT farmers.affiliation_id) as total_barangays'))
        ->groupBy('plants.name_of_plants')
        ->get();

    // Get uploaded plants data - using actual plant counts
    $uploadedPlantsData = DB::table('uploaded_inventories')
        ->select('commodity as plant_name',
                 DB::raw('SUM(total) as total_plants'),
                 DB::raw('COUNT(DISTINCT farmer) as total_farmers'),
                 DB::raw('COUNT(DISTINCT barangay) as total_barangays'))
        ->groupBy('commodity')
        ->get();

    // Combine plants data
    $combinedPlantsData = collect();
    $allPlants = collect();
    
    foreach ($plantsData as $plant) {
        $allPlants->push($plant->plant_name);
    }
    foreach ($uploadedPlantsData as $plant) {
        $allPlants->push($plant->plant_name);
    }
    $allPlants = $allPlants->unique();

    foreach ($allPlants as $plantName) {
        $manualPlant = $plantsData->where('plant_name', $plantName)->first();
        $uploadedPlant = $uploadedPlantsData->where('plant_name', $plantName)->first();
        
        $combinedPlant = (object) [
            'plant_name' => $plantName,
            'total_plants' => ($manualPlant->total_plants ?? 0) + ($uploadedPlant->total_plants ?? 0),
            'total_farmers' => ($manualPlant->total_farmers ?? 0) + ($uploadedPlant->total_farmers ?? 0),
            'total_barangays' => ($manualPlant->total_barangays ?? 0) + ($uploadedPlant->total_barangays ?? 0)
        ];
        
        $combinedPlantsData->push($combinedPlant);
    }

    // Get the total count of unique associations (excluding NULL values)
    $totalAffiliation = Affiliation::whereNotNull('name_of_association')
                                    ->distinct('name_of_association')
                                    ->count('name_of_association');

    // Get the total count of unique barangays (excluding NULL values)
    $totalBarangay = Affiliation::whereNotNull('name_of_barangay')
                                ->where('name_of_barangay', '!=', '')
                                ->distinct('name_of_barangay')
                                ->count('name_of_barangay');

    // Add uploaded barangays to total count
    $uploadedBarangays = DB::table('uploaded_inventories')
        ->distinct('barangay')
        ->count('barangay');
    
    $totalBarangay += $uploadedBarangays;

    // Get total number of users
    $totalUsers = User::count();

    // Get total plants
    $totalPlants = Plant::count();

    // Get additional insights
    $totalManualRecords = DB::table('monthly_inventories')->count();
    $totalUploadedRecords = DB::table('uploaded_inventories')->count();
    $totalRecords = $totalManualRecords + $totalUploadedRecords;

    return view('Admin.dashboard.index', compact(
        'totalUsers', 
        'totalPlants', 
        'plantsData', 
        'monthlyData', 
        'totalData', 
        'totalBarangay', 
        'totalAffiliation',
        'combinedTotalData',
        'combinedPlantsData',
        'totalRecords',
        'totalManualRecords',
        'totalUploadedRecords'
    ), [
        'title' => 'Admin | Dashboard'
    ]);
}


    public function exportPlantSummary(Request $request)
    {
        // Get month and year from the request
        $month = $request->input('month');
        $year = $request->input('year');

        
        // Manual entries: fetch plant data with optional month and year filtering
        $manualPlants = DB::table('plants')
            ->join('monthly_inventories', 'plants.id', '=', 'monthly_inventories.plant_id')
            ->join('farmers', 'farmers.id', '=', 'monthly_inventories.farmer_id')
            ->select(
                'plants.name_of_plants as plant_name',
                DB::raw('SUM(monthly_inventories.total) as total_plants'),
                DB::raw('COUNT(DISTINCT farmers.id) as total_farmers'),
                DB::raw('COUNT(DISTINCT farmers.affiliation_id) as total_barangays')
            )
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('monthly_inventories.created_at', $month);
            })
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('monthly_inventories.created_at', $year);
            })
            ->groupBy('plants.name_of_plants')
            ->get();

        // Uploaded entries: fetch plant data with optional month and year filtering
        $uploadedPlants = DB::table('uploaded_inventories')
            ->select(
                'commodity as plant_name',
                DB::raw('SUM(total) as total_plants'),
                DB::raw('COUNT(DISTINCT farmer) as total_farmers'),
                DB::raw('COUNT(DISTINCT barangay) as total_barangays')
            )
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('created_at', $month);
            })
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('created_at', $year);
            })
            ->groupBy('commodity')
            ->get();

        // Combine manual + uploaded into a single collection keyed by plant_name
        $allPlantNames = collect($manualPlants->pluck('plant_name'))
            ->merge($uploadedPlants->pluck('plant_name'))
            ->unique()
            ->values();

        $plantsData = $allPlantNames->map(function ($plantName) use ($manualPlants, $uploadedPlants) {
            $m = $manualPlants->firstWhere('plant_name', $plantName);
            $u = $uploadedPlants->firstWhere('plant_name', $plantName);
            return (object) [
                'plant_name' => $plantName,
                'total_plants' => ($m->total_plants ?? 0) + ($u->total_plants ?? 0),
                'total_farmers' => ($m->total_farmers ?? 0) + ($u->total_farmers ?? 0),
                'total_barangays' => ($m->total_barangays ?? 0) + ($u->total_barangays ?? 0),
            ];
        })->sortByDesc('total_plants')->values();

        // Generate Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Plant Name');
        $sheet->setCellValue('B1', 'Total Plants');
        $sheet->setCellValue('C1', 'Total Farmers');
        $sheet->setCellValue('D1', 'Total Barangays');

        $row = 2;
        $grandPlants = 0; $grandFarmers = 0; $grandBarangays = 0;
        foreach ($plantsData as $item) {
            $sheet->setCellValue('A' . $row, $item->plant_name);
            $sheet->setCellValue('B' . $row, $item->total_plants);
            $sheet->setCellValue('C' . $row, $item->total_farmers);
            $sheet->setCellValue('D' . $row, $item->total_barangays);
            $grandPlants += (int)$item->total_plants;
            $grandFarmers += (int)$item->total_farmers;
            $grandBarangays += (int)$item->total_barangays;
            $row++;
        }

        // Totals row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $grandPlants);
        $sheet->setCellValue('C' . $row, $grandFarmers);
        $sheet->setCellValue('D' . $row, $grandBarangays);

        // Basic formatting
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);

        // Generate a filename with month and year if provided
        $monthYearSuffix = '';
        if ($month && $year) {
            $monthName = date("F", mktime(0, 0, 0, $month, 1)); // Convert month number to name
            $monthYearSuffix = "_{$monthName}_{$year}";
        } elseif ($year) {
            $monthYearSuffix = "_{$year}";
        }

        $filename = "plant_summary{$monthYearSuffix}.xlsx";
        $tempFilePath = tempnam(sys_get_temp_dir(), $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        return response()->download($tempFilePath, $filename)->deleteFileAfterSend(true);
    }

    // JSON endpoint for plant distribution filtered by month/year (combined sources)
    public function plantDistribution(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');

        $manualPlants = DB::table('plants')
            ->join('monthly_inventories', 'plants.id', '=', 'monthly_inventories.plant_id')
            ->join('farmers', 'farmers.id', '=', 'monthly_inventories.farmer_id')
            ->select('plants.name_of_plants as plant_name',
                DB::raw('SUM(monthly_inventories.total) as total_plants'),
                DB::raw('COUNT(DISTINCT farmers.id) as total_farmers'),
                DB::raw('COUNT(DISTINCT farmers.affiliation_id) as total_barangays'))
            ->when($month, fn($q) => $q->whereMonth('monthly_inventories.created_at', $month))
            ->when($year, fn($q) => $q->whereYear('monthly_inventories.created_at', $year))
            ->groupBy('plants.name_of_plants')
            ->get();

        $uploadedPlants = DB::table('uploaded_inventories')
            ->select('commodity as plant_name',
                DB::raw('SUM(total) as total_plants'),
                DB::raw('COUNT(DISTINCT farmer) as total_farmers'),
                DB::raw('COUNT(DISTINCT barangay) as total_barangays'))
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->groupBy('commodity')
            ->get();

        $all = collect($manualPlants->pluck('plant_name'))
            ->merge($uploadedPlants->pluck('plant_name'))
            ->unique();

        $data = $all->map(function ($name) use ($manualPlants, $uploadedPlants) {
            $m = $manualPlants->firstWhere('plant_name', $name);
            $u = $uploadedPlants->firstWhere('plant_name', $name);
            return [
                'plant_name' => $name,
                'total_plants' => (int)(($m->total_plants ?? 0) + ($u->total_plants ?? 0)),
                'total_farmers' => (int)(($m->total_farmers ?? 0) + ($u->total_farmers ?? 0)),
                'total_barangays' => (int)(($m->total_barangays ?? 0) + ($u->total_barangays ?? 0)),
            ];
        })->sortByDesc('total_plants')->values();

        return response()->json(['data' => $data]);
    }

    
    public function map(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $locations = DB::table('inventory_valued_crops')
            ->join('farmers', 'inventory_valued_crops.farmer_id', '=', 'farmers.id')
            ->join('plants', 'inventory_valued_crops.plant_id', '=', 'plants.id')
            ->leftJoin('affiliations', 'farmers.affiliation_id', '=', 'affiliations.id')
            ->leftJoin('monthly_inventories', function($join) {
                $join->on('monthly_inventories.farmer_id', '=', 'farmers.id')
                     ->on('monthly_inventories.plant_id', '=', 'inventory_valued_crops.plant_id');
            })
            ->select(
                'inventory_valued_crops.*',
                'farmers.first_name',
                'farmers.middle_name', 
                'farmers.last_name',
                'plants.name_of_plants',
                'affiliations.name_of_barangay',
                'affiliations.name_of_association',
                'monthly_inventories.total',
                'monthly_inventories.total_planted_area',
                'monthly_inventories.created_at'
            )
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('monthly_inventories.created_at', [
                    Carbon::parse($startDate)->startOfDay(), 
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->whereNotNull('inventory_valued_crops.latitude')
            ->whereNotNull('inventory_valued_crops.longitude')
            ->where('inventory_valued_crops.latitude', '!=', 0)
            ->where('inventory_valued_crops.longitude', '!=', 0)
            ->orderBy('inventory_valued_crops.created_at', 'desc')
            ->get();

        // Format the data for better map display
        $locations = $locations->map(function ($location) {
            $location->formatted_date = Carbon::parse($location->created_at)->toDateString();
            $location->full_name = trim($location->first_name . ' ' . $location->middle_name . ' ' . $location->last_name);
            return $location;
        });

        return view('Admin.map.map', compact('locations'), [
            'title' => 'Admin | Extract'
        ]);
    }


    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new MapExport($startDate, $endDate), 'map_data.csv');
    }
    
    
}





<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plant;
use App\Models\Farmer;
use App\Models\Affiliation;
use App\Models\InventoryValuedCrop; 
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
    // Get the monthly data
    $monthlyData = DB::table('monthly_inventories')
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(total_planted_area) as total_planted_area'),
            DB::raw('SUM(final_production_volume) as final_production_volume')
        )
        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)')) // group by year and month
        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get();

    // Get the total data
    $totalData = DB::table('monthly_inventories')
        ->select(
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(total_planted_area) as total_planted_area'),
            DB::raw('SUM(final_production_volume) as final_production_volume')
        )
        ->get();

    // Get plants data
    $plantsData = DB::table('plants')
        ->join('inventory_valued_crops', 'plants.id', '=', 'inventory_valued_crops.plant_id')
        ->join('farmers', 'farmers.id', '=', 'inventory_valued_crops.farmer_id')
        ->select('plants.name_of_plants as plant_name', 
                 DB::raw('SUM(inventory_valued_crops.count) as total_plants'), 
                 DB::raw('COUNT(DISTINCT farmers.id) as total_farmers'), 
                 DB::raw('COUNT(DISTINCT farmers.affiliation_id) as total_barangays'))
        ->groupBy('plants.name_of_plants')
        ->get();

    // Get the total count of unique associations (excluding NULL values)
    $totalAffiliation = Affiliation::whereNotNull('name_of_association')
                                    ->distinct('name_of_association')
                                    ->count('name_of_association');

    // Get the total count of unique barangays (excluding NULL values)
    $totalBarangay = Affiliation::whereNotNull('name_of_barangay')
                                ->where('name_of_barangay', '!=', '')
                                ->distinct('name_of_barangay')
                                ->count('name_of_barangay');

    // Get total number of users
    $totalUsers = User::count(); // Ensure the User model is correctly referenced

    // Get total plants
    $totalPlants = Plant::count(); 

    return view('Admin.dashboard.index', compact('totalUsers', 'totalPlants', 'plantsData', 'monthlyData', 'totalData', 'totalBarangay', 'totalAffiliation'), [
        'title' => 'Admin | Dashboard'
    ]); // Passing the variable to the view
}


    public function exportPlantSummary(Request $request)
    {
        // Get month and year from the request
        $month = $request->input('month');
        $year = $request->input('year');

        
        // Query to fetch plant data with optional month and year filtering
        $plantsData = DB::table('plants')
            ->join('inventory_valued_crops', 'plants.id', '=', 'inventory_valued_crops.plant_id')
            ->join('farmers', 'farmers.id', '=', 'inventory_valued_crops.farmer_id')
            ->select(
                'plants.name_of_plants as plant_name',
                DB::raw('SUM(inventory_valued_crops.count) as total_plants'),
                DB::raw('COUNT(DISTINCT farmers.id) as total_farmers'),
                DB::raw('COUNT(DISTINCT farmers.affiliation_id) as total_barangays')
            )
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('inventory_valued_crops.created_at', $month);
            })
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('inventory_valued_crops.created_at', $year);
            })
            ->groupBy('plants.name_of_plants')
            ->get();

        // Generate Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Plant Name');
        $sheet->setCellValue('B1', 'Total Plants');
        $sheet->setCellValue('C1', 'Total Farmers');
        $sheet->setCellValue('D1', 'Total Barangays');

        $row = 2;
        foreach ($plantsData as $item) {
            $sheet->setCellValue('A' . $row, $item->plant_name);
            $sheet->setCellValue('B' . $row, $item->total_plants);
            $sheet->setCellValue('C' . $row, $item->total_farmers);
            $sheet->setCellValue('D' . $row, $item->total_barangays);
            $row++;
        }

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





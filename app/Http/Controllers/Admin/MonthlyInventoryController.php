<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use App\Models\Farmer;
use App\Models\MonthlyInventory; 
use App\Models\InventoryValuedCrop; 
use App\Models\MonthlyRecord; 
use App\Models\Plant;
use App\Models\UploadedInventory;
use Illuminate\Http\Request;
use App\Exports\MonthlyInventoryExport; // Import the export class
use App\Exports\MonthlyRecordsExport; // Import the export class
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MonthlyInventoryController extends Controller
{
    public function index()
    {
        // Increase memory limit for this page
        ini_set('memory_limit', '1024M');
        
        // Use pagination to limit records per page and reduce memory usage
        $inventories = MonthlyInventory::with(['farmer', 'plant', 'affiliation'])
            ->orderBy('created_at', 'desc')
            ->paginate(50); // Show 50 records per page

        // Fetch uploaded inventories with pagination
        $uploadedInventories = UploadedInventory::orderBy('created_at', 'desc')->paginate(50);
        
        // Empty collections since we use AJAX for edit forms now
        $farmers = collect();
        $plants = collect();
        $affiliations = collect();

        return view('Admin.inventory.index', compact('inventories', 'uploadedInventories', 'farmers', 'plants', 'affiliations'), [
            'title' => 'Monthly Inventory'
        ]);
    }
    public function store(Request $request)
{
    $validated = $request->validate([
        'farmer_id' => 'required|exists:farmers,id',
        'plant_id' => 'required|exists:plants,id',
        'affiliation_id' => 'required|exists:affiliations,id',
        'planting_density' => 'required|numeric|min:0',
        'production_volume' => 'required|numeric|min:0',
        'newly_planted' => 'nullable|numeric|min:0',
        'vegetative' => 'nullable|numeric|min:0',
        'reproductive' => 'nullable|numeric|min:0',
        'maturity_harvested' => 'nullable|numeric|min:0',
        'newly_planted_divided' => 'nullable|numeric|min:0',
        'vegetative_divided' => 'nullable|numeric|min:0',
        'reproductive_divided' => 'nullable|numeric|min:0',
        'maturity_harvested_divided' => 'nullable|numeric|min:0',
    ]);

    // Convert planting density and production volume to float to handle commas
    $plantingDensity = floatval(str_replace(',', '', $request->input('planting_density')));
    $productionVolume = floatval(str_replace(',', '', $request->input('production_volume')));

    // Calculate area harvested and production volume in metric tons
    $areaHarvested = $request->input('maturity_harvested') / $plantingDensity;
    $productionVolumeMT = ($areaHarvested * $productionVolume) / 1000;

    // Calculate total of planted areas
    $total = ($request->input('newly_planted') ?? 0) + 
             ($request->input('vegetative') ?? 0) + 
             ($request->input('reproductive') ?? 0) + 
             ($request->input('maturity_harvested') ?? 0);

    // Calculate total planted area from divided values
    $totalPlantedArea = (
        floatval($request->input('newly_planted_divided')) +
        floatval($request->input('vegetative_divided')) +
        floatval($request->input('reproductive_divided')) +
        floatval($request->input('maturity_harvested_divided'))
    );

    // Store monthly inventory record
    $inventory = MonthlyInventory::create([
        'farmer_id' => $request->input('farmer_id'),
        'plant_id' => $request->input('plant_id'),
        'affiliation_id' => $request->input('affiliation_id'),
        'planting_density' => round($plantingDensity),
        'production_volume' => round($productionVolume),
        'newly_planted' => $request->input('newly_planted'),
        'vegetative' => $request->input('vegetative'),
        'reproductive' => $request->input('reproductive'),
        'maturity_harvested' => $request->input('maturity_harvested'),
        'area_harvested' => round($areaHarvested, 4),
        'final_production_volume' => round($productionVolumeMT, 4),
        'total' => $total, // Ensure 'total' is defined here
        'newly_planted_divided' => $request->input('newly_planted_divided'),
        'vegetative_divided' => $request->input('vegetative_divided'),
        'reproductive_divided' => $request->input('reproductive_divided'),
        'maturity_harvested_divided' => $request->input('maturity_harvested_divided'),
        'total_planted_area' => $totalPlantedArea,
    ]);

    // Fetch previous month's record (if any)
    $previousRecord = MonthlyRecord::where('farmer_id', $validated['farmer_id'])
                                   ->where('plant_id', $validated['plant_id'])
                                   ->where('affiliation_id', $validated['affiliation_id'])
                                   ->latest()
                                   ->first();

    // Create or update MonthlyRecord based on previous data
    $monthlyRecord = new MonthlyRecord();
    $monthlyRecord->monthly_inventory_id = $inventory->id;
    $monthlyRecord->farmer_id = $validated['farmer_id'];
    $monthlyRecord->plant_id = $validated['plant_id'];
    $monthlyRecord->affiliation_id = $validated['affiliation_id'];
    $monthlyRecord->planting_density = $plantingDensity;
    $monthlyRecord->production_volume = $productionVolume;
    $monthlyRecord->newly_planted = $validated['newly_planted'];
    $monthlyRecord->vegetative = $validated['vegetative'];
    $monthlyRecord->reproductive = $validated['reproductive'];
    $monthlyRecord->maturity_harvested = $validated['maturity_harvested'];
    $monthlyRecord->newly_planted_divided = $validated['newly_planted_divided'];
    $monthlyRecord->vegetative_divided = $validated['vegetative_divided'];
    $monthlyRecord->reproductive_divided = $validated['reproductive_divided'];
    $monthlyRecord->maturity_harvested_divided = $validated['maturity_harvested_divided'];
    $monthlyRecord->total_planted_area = $totalPlantedArea;
    $monthlyRecord->total = $total; // Store the 'total' here as well
    $monthlyRecord->area_harvested = $areaHarvested;
    $monthlyRecord->final_production_volume = $productionVolumeMT;

    if ($previousRecord) {
        $monthlyRecord->newly_planted_divided = $previousRecord->newly_planted_divided;
        $monthlyRecord->vegetative_divided = $previousRecord->vegetative_divided;
        $monthlyRecord->reproductive_divided = $previousRecord->reproductive_divided;
        $monthlyRecord->maturity_harvested_divided = $previousRecord->maturity_harvested_divided;
    }

    $monthlyRecord->save();

    return redirect()->back()->with('success', 'Inventory record added successfully.');
}


    
    public function edit($id)
    {
        $inventory = MonthlyInventory::findOrFail($id);
        $farmers = Farmer::all(); // Fetch farmers
        $plants = Plant::all(); // Fetch plants
        $affiliations = Affiliation::all(); // Fetch affiliations

        return view('Admin.inventory.index', compact('inventory', 'farmers', 'plants', 'affiliations'));
    }

    public function editForm($id)
    {
        // Increase memory limit for this specific request
        ini_set('memory_limit', '512M');
        
        $inventory = MonthlyInventory::findOrFail($id);
        
        // Use select() to only get needed columns, reducing memory usage
        $farmers = Farmer::select('id', 'first_name', 'last_name')->get();
        $plants = Plant::select('id', 'name_of_plants')->get();
        $affiliations = Affiliation::select('id', 'name_of_association', 'name_of_barangay')->get();

        return view('Admin.inventory.edit-form', compact('inventory', 'farmers', 'plants', 'affiliations'));
    }



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'plant_id' => 'required|exists:plants,id',
            'affiliation_id' => 'required|exists:affiliations,id',
            'planting_density' => 'required|numeric',
            'production_volume' => 'required|numeric',
            'newly_planted' => 'nullable|numeric',
            'vegetative' => 'nullable|numeric',
            'reproductive' => 'nullable|numeric',
            'maturity_harvested' => 'nullable|numeric',
        ]);

        $inventory = MonthlyInventory::findOrFail($id);
        
        // Extract values from validated data
        $plantingDensity = $validated['planting_density'];
        $newlyPlanted = $validated['newly_planted'] ?? 0;
        $vegetative = $validated['vegetative'] ?? 0;
        $reproductive = $validated['reproductive'] ?? 0;
        $maturityHarvested = $validated['maturity_harvested'] ?? 0;
        
        // Calculate divided values
        $newlyPlantedDivided = $plantingDensity > 0 ? $newlyPlanted / $plantingDensity : 0;
        $vegetativeDivided = $plantingDensity > 0 ? $vegetative / $plantingDensity : 0;
        $reproductiveDivided = $plantingDensity > 0 ? $reproductive / $plantingDensity : 0;
        $maturityHarvestedDivided = $plantingDensity > 0 ? $maturityHarvested / $plantingDensity : 0;

        // Calculate totals
        $areaHarvested = $maturityHarvestedDivided;
        $total = $newlyPlanted + $vegetative + $reproductive + $maturityHarvested;
        $totalPlantedArea = $newlyPlantedDivided + $vegetativeDivided + $reproductiveDivided + $maturityHarvestedDivided;
        $finalProductionVolume = ($areaHarvested * $validated['production_volume']) / 1000;

        // Update the inventory record
        $inventory->update([
            'farmer_id' => $validated['farmer_id'],
            'plant_id' => $validated['plant_id'],
            'affiliation_id' => $validated['affiliation_id'],
            'planting_density' => $plantingDensity,
            'production_volume' => $validated['production_volume'],
            'newly_planted' => $newlyPlanted,
            'vegetative' => $vegetative,
            'reproductive' => $reproductive,
            'maturity_harvested' => $maturityHarvested,
            'newly_planted_divided' => $newlyPlantedDivided,
            'vegetative_divided' => $vegetativeDivided,
            'reproductive_divided' => $reproductiveDivided,
            'maturity_harvested_divided' => $maturityHarvestedDivided,
            'area_harvested' => $areaHarvested,
            'final_production_volume' => $finalProductionVolume,
            'total' => $total,
            'total_planted_area' => $totalPlantedArea,
        ]);

        // Optionally, log this update to a history table if needed
        $inventory->saveToHistory(); // Only if necessary, this will log the previous state

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory record updated successfully.');
    }



    public function exportMonthlyInventoryExcel($month, $year)
    {
        // Construct the start and end dates
        $startDate = "{$year}-{$month}-01"; // First day of the month
        $endDate = date('Y-m-d', strtotime("{$startDate} +1 month")); // First day of the next month

        // Fetch the inventory data filtered by the created_at timestamp
        $data = MonthlyInventory::where('created_at', '>=', $startDate)
                    ->where('created_at', '<', $endDate)
                    ->get();

        // Check if data is being retrieved
        \Log::info("Filtered data for month: $month, year: $year", $data->toArray());

        // Convert the numeric month to the full month name
        $monthName = date('F', mktime(0, 0, 0, $month, 10)); // Converts month number to month name (e.g., "January")

        // Logic for exporting to Excel
        return Excel::download(new MonthlyInventoryExport($data), 'monthly_inventory_' . $monthName . '_' . $year . '.xlsx');
    }


    
    public function destroy($id)
    {
        $inventory = MonthlyInventory::findOrFail($id);
        $inventory->delete();

        return redirect()->back()->with('success', 'Inventory record deleted successfully.');
    }

    // monthly records-----------------------------------
    public function showHistory(Request $request)
    {

        // Fetch records grouped by farmer and month
        $records = MonthlyInventory::with(['affiliation', 'plant', 'farmer'])
            ->orderBy('farmer_id')
            ->orderBy('created_at', 'desc') // Assuming `created_at` is used to denote the month
            ->get();

        $affiliations =Affiliation::all();

        
        $records = MonthlyRecord::with(['farmer', 'plant', 'affiliation'])->get();

        // Pass the records to the view
        return view('Admin.inventory.history', compact('records'), [
            'records' => $records,  // Use 'records' here
            'title' => 'View Monthly Records',
        ]);
    }

    public function delete($id)
    {
        $record = MonthlyRecord::find($id);

        if ($record) {
            $record->delete();
            return redirect()->route('admin.inventory.history')->with('success', 'Record deleted successfully.');
        }

        return redirect()->route('admin.inventory.history')->with('error', 'Record not found.');
    }

// VIEW EXCEL BEFORE DOWNLOADING
    // Preview ng Inventory sa isang buwan at taon
    public function previewMonthlyInventory($month, $year)
    {
        $inventories = MonthlyInventory::all();
        // Fetch latitude and longitude for each inventory manually
        foreach ($inventories as $inventory) {
            $inventoryValuedCrop = InventoryValuedCrop::where('farmer_id', $inventory->farmer_id)
                ->first(); // You can adjust this condition based on your schema

            $inventory->latitude = $inventoryValuedCrop ? $inventoryValuedCrop->latitude : null;
            $inventory->longitude = $inventoryValuedCrop ? $inventoryValuedCrop->longitude : null;
        }
        // Query the database using the created_at column instead of 'date'
        $inventories = MonthlyInventory::whereMonth('created_at', $month)
                                        ->whereYear('created_at', $year)
                                        ->get();

        return view('Admin.inventory.preview', compact('inventories'));
    }


// HISTORY OR PREVIOUS HISTORY
    public function previewHistory($month, $year)
    {
        $inventories = MonthlyRecord::all();
        // Fetch latitude and longitude manually
        foreach ($inventories as $inventory) {
            $inventoryValuedCrop = InventoryValuedCrop::where('farmer_id', $inventory->farmer_id)->first();
            $inventory->latitude = $inventoryValuedCrop ? $inventoryValuedCrop->latitude : null;
            $inventory->longitude = $inventoryValuedCrop ? $inventoryValuedCrop->longitude : null;
        }

        // Pagkuha sa data gikan sa monthly_records base sa month ug year
        $monthlyRecords = MonthlyRecord::with(['farmer.affiliation', 'plant'])
            ->whereHas('monthlyInventory', function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                      ->whereYear('created_at', $year);
            })->get();

        // I-render ang Blade view alang sa preview
        return view('Admin.inventory.preview_history', ['inventories' => $inventories]);
    }

    public function exportHistory($month, $year)
    {
        // Pagkuha sa data gikan sa monthly_records base sa month ug year
        $monthlyRecords = MonthlyRecord::with(['farmer.affiliation', 'plant'])
            ->whereHas('monthlyInventory', function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                      ->whereYear('created_at', $year);
            })->get();

        // Pag-download sa Excel file gamit ang MonthlyRecordsExport
        return Excel::download(new MonthlyRecordsExport($monthlyRecords), "MonthlyRecords_{$month}_{$year}.xlsx");
    }
    
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:51200' // 50MB max
        ]);

        try {
            // Increase memory and execution time for heavy Excel processing
            ini_set('memory_limit', '4096M');
            set_time_limit(900);
            
            // Reduce memory usage during bulk inserts
            DB::disableQueryLog();

            $file = $request->file('excel_file');
            
            // Read the Excel file with memory optimization
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return redirect()->back()->with('error', 'The Excel file is empty or invalid.');
            }

            $rows = $data[0];
            $headerRow = array_shift($rows); // Remove header row
            
            // Clear the original data to free memory
            unset($data);
            
            $importedCount = 0;
            $errors = [];
            $batchSize = 100; // Process in batches
            $batches = array_chunk($rows, $batchSize);

            foreach ($batches as $batchIndex => $batch) {
                foreach ($batch as $rowIndex => $row) {
                    $index = ($batchIndex * $batchSize) + $rowIndex;
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map Excel columns to database fields based on the sample structure
                    $barangay = $row[0] ?? ''; // Column A: Barangay
                    $commodity = $row[1] ?? ''; // Column B: Commodity
                    $farmer = $row[2] ?? ''; // Column C: Farmer
                    $plantingDensity = is_numeric($row[3]) ? (float)$row[3] : 0; // Column D: Planting Density (ha)
                    $productionVolHectare = is_numeric($row[4]) ? (float)$row[4] : 0; // Column E: Production Vol/ Hectare (kls)
                    $newlyPlanted = is_numeric($row[5]) ? (int)$row[5] : 0; // Column F: Newly Planted
                    $vegetative = is_numeric($row[6]) ? (int)$row[6] : 0; // Column G: Vegetative
                    $reproductive = is_numeric($row[7]) ? (int)$row[7] : 0; // Column H: Reproductive
                    $maturity = is_numeric($row[8]) ? (int)$row[8] : 0; // Column I: Maturity/
                    $total = is_numeric($row[9]) ? (int)$row[9] : 0; // Column J: TOTAL
                    
                    // Columns K-O: Per hectare values (if provided in Excel)
                    $newlyPlantedDivided = is_numeric($row[10]) ? (float)$row[10] : 0; // Column K: Newly Planted
                    $vegetativeDivided = is_numeric($row[11]) ? (float)$row[11] : 0; // Column L: Vegetative
                    $reproductiveDivided = is_numeric($row[12]) ? (float)$row[12] : 0; // Column M: Reproductive
                    $maturityDivided = is_numeric($row[13]) ? (float)$row[13] : 0; // Column N: Maturity/Harvested
                    $totalDivided = is_numeric($row[14]) ? (float)$row[14] : 0; // Column O: Total
                    
                    // Columns P-Q: Area and Production Volume (if provided in Excel)
                    $areaHarvested = is_numeric($row[15]) ? (float)$row[15] : 0; // Column P: Area
                    $productionVolumeMt = is_numeric($row[16]) ? (float)$row[16] : 0; // Column Q: Production Volume
                    
                    // If per hectare values are not provided or are 0, calculate them
                    if ($newlyPlantedDivided == 0 && $plantingDensity > 0) {
                        $newlyPlantedDivided = $newlyPlanted / $plantingDensity;
                    }
                    if ($vegetativeDivided == 0 && $plantingDensity > 0) {
                        $vegetativeDivided = $vegetative / $plantingDensity;
                    }
                    if ($reproductiveDivided == 0 && $plantingDensity > 0) {
                        $reproductiveDivided = $reproductive / $plantingDensity;
                    }
                    if ($maturityDivided == 0 && $plantingDensity > 0) {
                        $maturityDivided = $maturity / $plantingDensity;
                    }
                    
                    // Calculate total planted area if not provided
                    $plantedTotal = $totalDivided > 0 ? $totalDivided : ($newlyPlantedDivided + $vegetativeDivided + $reproductiveDivided + $maturityDivided);
                    
                    // Calculate area harvested and production volume if not provided
                    if ($areaHarvested == 0) {
                        $areaHarvested = $maturityDivided;
                    }
                    if ($productionVolumeMt == 0) {
                        $productionVolumeMt = ($areaHarvested * $productionVolHectare) / 1000;
                    }

                    UploadedInventory::create([
                        'barangay' => $barangay,
                        'commodity' => $commodity,
                        'farmer' => $farmer,
                        'planting_density' => $plantingDensity,
                        'production_vol_hectare' => $productionVolHectare,
                        'newly_planted' => $newlyPlanted,
                        'vegetative' => $vegetative,
                        'reproductive' => $reproductive,
                        'maturity' => $maturity,
                        'total' => $total,
                        'planted_area_newly' => $newlyPlantedDivided,
                        'planted_area_vegetative' => $vegetativeDivided,
                        'planted_area_reproductive' => $reproductiveDivided,
                        'planted_area_maturity' => $maturityDivided,
                        'planted_total' => $plantedTotal,
                        'area_harvested' => $areaHarvested,
                        'production_volume_mt' => $productionVolumeMt,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
                }
                
                // Clear memory after each batch
                unset($batch);
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }

            $message = "Successfully imported {$importedCount} records.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }


}

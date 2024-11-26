<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use App\Models\Farmer;
use App\Models\MonthlyInventory; 
use App\Models\InventoryValuedCrop; 
use App\Models\MonthlyRecord; 
use App\Models\Plant;
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

        // Fetch all inventories along with their relationships
        $inventories = MonthlyInventory::with(['farmer', 'plant', 'affiliation'])->get();

        
        // Fetch farmers, plants, and affiliations for the form
        $farmers = Farmer::all();
        $plants = Plant::all();
        $affiliations = Affiliation::all();

        return view('admin.inventory.index', compact('inventories', 'farmers', 'plants', 'affiliations'), [
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
        $monthlyRecord->previous_newly_planted = $previousRecord->newly_planted;
        $monthlyRecord->previous_vegetative = $previousRecord->vegetative;
        $monthlyRecord->previous_reproductive = $previousRecord->reproductive;
        $monthlyRecord->previous_maturity_harvested = $previousRecord->maturity_harvested;
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

        return view('admin.inventory.index', compact('inventory', 'farmers', 'plants', 'affiliations'));
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
        return view('admin.inventory.history', compact('records'), [
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

        return view('admin.inventory.preview', compact('inventories'));
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
        return view('admin.inventory.preview_history', ['inventories' => $inventories]);
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
    

}

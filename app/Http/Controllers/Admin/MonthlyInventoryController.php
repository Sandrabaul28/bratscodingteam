<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use App\Models\Farmer;
use App\Models\MonthlyInventory;
use App\Models\Plant;
use Illuminate\Http\Request;
use App\Exports\MonthlyInventoryExport; // Import the export class
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

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
    $request->validate([
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

    // Convert planting density and production volume to float to handle comma as thousands separator
    $plantingDensity = floatval(str_replace(',', '', $request->input('planting_density')));
    $productionVolume = floatval(str_replace(',', '', $request->input('production_volume')));

    // Calculate area harvested and production volume
    $areaHarvested = $request->input('maturity_harvested') / $plantingDensity;
    $productionVolumeMT = ($areaHarvested * $productionVolume) / 1000;

    // Calculate total planted area based on divided values
    $totalPlantedArea = (
        floatval($request->input('newly_planted_divided')) +
        floatval($request->input('vegetative_divided')) +
        floatval($request->input('reproductive_divided')) +
        floatval($request->input('maturity_harvested_divided'))
    );

    // Store a single record
    MonthlyInventory::create([
        'farmer_id' => $request->input('farmer_id'),
        'plant_id' => $request->input('plant_id'),
        'affiliation_id' => $request->input('affiliation_id'),
        'planting_density' => round($plantingDensity), // Round and save planting density
        'production_volume' => round($productionVolume), // Round and save production volume
        'newly_planted' => $request->input('newly_planted'),
        'vegetative' => $request->input('vegetative'),
        'reproductive' => $request->input('reproductive'),
        'maturity_harvested' => $request->input('maturity_harvested'),
        'area_harvested' => round($areaHarvested, 4), // Round area harvested
        'final_production_volume' => round($productionVolumeMT, 4), // Round production volume
        'total' => ($request->input('newly_planted') ?? 0) + 
                   ($request->input('vegetative') ?? 0) + 
                   ($request->input('reproductive') ?? 0) + 
                   ($request->input('maturity_harvested') ?? 0),
        // Add the new fields without rounding
        'newly_planted_divided' => $request->input('newly_planted_divided'),
        'vegetative_divided' => $request->input('vegetative_divided'),
        'reproductive_divided' => $request->input('reproductive_divided'),
        'maturity_harvested_divided' => $request->input('maturity_harvested_divided'),
        'total_planted_area' => $totalPlantedArea, // Save total planted area as decimal
    ]);

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
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'plant_id' => 'required|exists:plants,id',
            'affiliation_id' => 'required|exists:affiliations,id',
            'planting_density' => 'required|numeric|min:0',
            'production_volume' => 'required|numeric|min:0',
            'newly_planted' => 'nullable|numeric|min:0',
            'vegetative' => 'nullable|numeric|min:0',
            'reproductive' => 'nullable|numeric|min:0',
            'maturity_harvested' => 'nullable|numeric|min:0',
        ]);

        // Fetch the existing inventory record
        $inventory = MonthlyInventory::findOrFail($id);

        // Convert planting density and production volume to float
        $plantingDensity = floatval(str_replace(',', '', $request->input('planting_density')));
        $productionVolume = floatval(str_replace(',', '', $request->input('production_volume')));

        // Calculate area harvested and final production volume
        $areaHarvested = $request->input('maturity_harvested') / $plantingDensity;
        $productionVolumeMT = ($areaHarvested * $productionVolume) / 1000;

        // Update the record with new values
        $inventory->update([
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
            'total' => ($request->input('newly_planted') ?? 0) + ($request->input('vegetative') ?? 0) + ($request->input('reproductive') ?? 0) + ($request->input('maturity_harvested') ?? 0),
        ]);

        return redirect()->back()->with('success', 'Inventory record updated successfully.');
    }


    public function exportMonthlyInventoryExcel(Request $request)
    {
        $userId = auth()->id(); // o kung paano mo kinukuha ang user id
        $barangay = $request->input('barangay', null); // kung kailangan mo ng filter sa barangay
        $fromDate = $request->input('from_date', null); // optional filter
        $toDate = $request->input('to_date', null); // optional filter

        return Excel::download(new MonthlyInventoryExport($userId, $barangay, $fromDate, $toDate), 'monthly_inventory.xlsx');
    }
    
    public function destroy($id)
    {
        $inventory = MonthlyInventory::findOrFail($id);
        $inventory->delete();

        return redirect()->back()->with('success', 'Inventory record deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryValuedCrop; 
use App\Models\Farmer;
use App\Models\Plant; // Assuming you have a Plant model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountController extends Controller
{
    public function count()
    {

        $inventoryCrops = DB::table('inventory_valued_crops')
            ->join('farmers', 'inventory_valued_crops.farmer_id', '=', 'farmers.id')
            ->join('plants', 'inventory_valued_crops.plant_id', '=', 'plants.id')
            ->join('affiliations', 'farmers.affiliation_id', '=', 'affiliations.id')
            ->select(
                'farmers.first_name',
                'farmers.last_name',
                'affiliations.name_of_association',
                'affiliations.name_of_barangay',
                'inventory_valued_crops.id', 
                'inventory_valued_crops.farmer_id',
                'inventory_valued_crops.plant_id',
                'inventory_valued_crops.count', // Add this to get the count value
                'plants.name_of_plants' // Get plant name
            )
            ->get(); // No grouping here to get individual plant records

        $farmers = Farmer::all(); // Get all farmers for the form
        $plants = Plant::all(); // Get all plants for the form

        // Return the view with inventory data
        return view('admin.hvcdp.count', compact('inventoryCrops', 'farmers', 'plants'), [
            'title' => 'HVCDP - Counts'
        ]);
    }


    // Store a new inventory crop
    public function store(Request $request)
    {

        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'plant_id' => 'required|exists:plants,id',
            'count' => 'required|integer|min:1',
            'added_by' => $addedBy,
        ]);

        // Create a new inventory valued crop
        InventoryValuedCrop::create($request->all());

        // Redirect to the count view with success message
        return redirect()->route('admin.hvcdp.count')->with('success', 'Crop added successfully.');
    }

    public function edit($id)
{
    // Hanapin ang crop gamit ang ibinigay na id
    $crop = InventoryValuedCrop::findOrFail($id);

    // Kunin ang farmer id mula sa crop record
    $farmerId = $crop->farmer_id;

    // I-retrieve ang lahat ng inventory crops para sa farmer kasama ang kanilang IDs
    $inventoryCrops = InventoryValuedCrop::with('plant')->where('farmer_id', $farmerId)->get();

    // Kunin ang lahat ng plants para sa dropdown (o anumang field na kailangan)
    $plants = Plant::all(); // Assumes you want to allow changing plants

    // Ibalik ang view kasama ang crop, farmer id, inventory crops, at plants
    return view('admin.inventory.edit', compact('crop', 'inventoryCrops', 'farmerId', 'plants'));
}


    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'count' => 'required|integer|min:0',
    ]);

    // Find the inventory record by plant ID and update it
    $inventoryCrop = InventoryValuedCrop::findOrFail($id);
    $inventoryCrop->count = $validated['count'];
    $inventoryCrop->save();

    return redirect()->back()->with('success', 'Plant count updated successfully.');
}



    public function destroy($id)
    {
        // Find the inventory record by ID and delete it
        $inventoryCrops = InventoryValuedCrop::findOrFail($id);
        $inventoryCrops->delete();

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }



}

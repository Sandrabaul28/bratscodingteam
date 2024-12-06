<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryValuedCrop; 
use App\Models\InventoryDayApproval; 
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
        ->join('users', 'inventory_valued_crops.added_by', '=', 'users.id') // Join to users table for added_by
        ->join('roles', 'users.role_id', '=', 'roles.id') // Join to roles table for role name
        ->select(
            'farmers.first_name',
            'farmers.last_name',
            'affiliations.name_of_association',
            'affiliations.name_of_barangay',
            'inventory_valued_crops.id',
            'inventory_valued_crops.farmer_id',
            'inventory_valued_crops.plant_id',
            'inventory_valued_crops.count',
            'plants.name_of_plants',
            'inventory_valued_crops.latitude',
            'inventory_valued_crops.longitude',
            'inventory_valued_crops.created_at', // Include the created_at column
            'users.first_name as added_by_first_name', // Get the encoder's first name
            'users.last_name as added_by_last_name',   // Get the encoder's last name
            'roles.role_name'                         // Get the role name of the encoder
        )
        ->get();


        $farmers = Farmer::all(); // Get all farmers for the form
        $plants = Plant::all(); // Get all plants for the form

        // Return the view with inventory data
        return view('Admin.hvcdp.count', compact('inventoryCrops', 'farmers', 'plants'), [
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
        return redirect()->route('Admin.hvcdp.count')->with('success', 'Crop added successfully.');
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
    return view('Admin.inventory.edit', compact('crop', 'inventoryCrops', 'farmerId', 'plants'));
}



    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'count' => 'required|integer|min:0',
        'latitude' => 'nullable|numeric', // Make latitude optional
        'longitude' => 'nullable|numeric', // Make longitude optional
    ]);

    // Find the inventory record by ID and update it
    $inventoryCrop = InventoryValuedCrop::findOrFail($id);
    $inventoryCrop->count = $validated['count'];
    $inventoryCrop->latitude = $validated['latitude'] ?? null; // Update latitude, set to null if not provided
    $inventoryCrop->longitude = $validated['longitude'] ?? null; // Update longitude, set to null if not provided
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

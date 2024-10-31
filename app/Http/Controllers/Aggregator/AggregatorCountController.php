<?php

namespace App\Http\Controllers\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\InventoryValuedCrop; 
use App\Models\Farmer;
use App\Models\Plant; // Assuming you have a Plant model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AggregatorCountController extends Controller
{

    public function count()
    {
        // Get the currently logged-in user's ID
        $userId = auth()->user()->id;

        // Query for the inventory crops with the logged-in user filter
        $inventoryCrops = DB::table('inventory_valued_crops')
            ->join('farmers', 'inventory_valued_crops.farmer_id', '=', 'farmers.id')
            ->join('plants', 'inventory_valued_crops.plant_id', '=', 'plants.id')
            ->join('affiliations', 'farmers.affiliation_id', '=', 'affiliations.id')
            ->where('farmers.added_by', $userId) // Filter by the user who added the inventory
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
            ->get(); 

        // Get all farmers and plants for the form (this part is unchanged)
        $farmers = Farmer::all();
        $plants = Plant::all();

        // Return the view with the data, ensuring no other changes to the query behavior
        return view('aggregator.hvcdp.count', compact('inventoryCrops', 'farmers', 'plants'), [
            'title' => 'HVCDP - Counts'
        ]);
    }



    public function store(Request $request)
    {
        // Validate the form input
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'plant_id' => 'required|exists:plants,id',
            'count' => 'required|integer|min:1',
        ]);

        // Get the ID of the user who is adding the record
        $addedBy = auth()->user()->id; // Assuming you are using Laravel's built-in authentication

        // Store the new inventory record
        InventoryValuedCrop::create([
            'farmer_id' => $request->farmer_id,
            'plant_id' => $request->plant_id,
            'count' => $request->count,
            'added_by' => $addedBy, // Use the ID of the authenticated user
        ]);

        // Redirect back with success message
        return redirect()->route('aggregator.count.store')->with('success', 'Record added successfully!');
    }



    // Update an existing crop count
    public function update(Request $request, $id)
    {
        $request->validate([
            'count' => 'required|integer|min:0',
        ]);

        $crop = InventoryValuedCrop::findOrFail($id);
        $crop->count = $request->count;
        $crop->save();

        return redirect()->route('aggregator.count.store')->with('success', 'Crop count updated successfully!');
    }

    public function destroy($id)
    {
        // Find the inventory record by ID and delete it
        $inventoryCrops = InventoryValuedCrop::findOrFail($id);
        $inventoryCrops->delete();

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

}

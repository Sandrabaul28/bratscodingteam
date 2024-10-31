<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InventoryValuedCrop; 
use App\Models\Farmer;
use App\Models\Plant; // Assuming you have a Plant model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserEncodeController extends Controller
{
    public function count()
    {
        // Get the currently logged-in user's ID
        $userId = auth()->id();

        // Get only the inventory crops where 'added_by' matches the logged-in user's ID
        $inventoryCrops = InventoryValuedCrop::with('farmer', 'plant')
                            ->where('added_by', $userId)
                            ->get(); 

        // Retrieve all farmers and plants for the form
        $farmers = Farmer::all();
        $plants = Plant::all();

        // Return the view with the filtered data
        return view('user.encode.create_count', compact('inventoryCrops', 'farmers', 'plants'), [
            'title' => 'HVCDP - Counts'
        ]);
    }


    public function fetchFarmers(Request $request)
    {
        \Log::info('Fetching farmers with query: ' . $request->input('query')); // Debug log

        // Retrieve the query and affiliation ID from the request
        $query = $request->input('query');
        $affiliationId = $request->input('affiliation_id');

        // Fetch farmers filtered by affiliation_id and search query
        $farmers = Farmer::where('affiliation_id', $affiliationId)
                         ->where(function($q) use ($query) {
                             $q->where('first_name', 'like', "%{$query}%")
                               ->orWhere('last_name', 'like', "%{$query}%");
                         })
                         ->get(['id', 'first_name', 'last_name']);

        return response()->json($farmers);
    }



    public function store(Request $request)
{
    // Get the authenticated user
    $user = Auth::user();

    // Check if the user is logged in
    if (!$user) {
        return redirect()->back()->withErrors(['user' => 'User not authenticated.']);
    }

    // Validate the input, including latitude and longitude
    $request->validate([
        'farmer_id' => 'required|exists:farmers,id',
        'plant_id' => 'required|exists:plants,id',
        'count' => 'required|integer|min:1',
        'latitude' => 'required|numeric',   // Validation for latitude
        'longitude' => 'required|numeric',  // Validation for longitude
    ]);

    // Get the farmer based on the provided farmer_id
    $farmer = Farmer::find($request->input('farmer_id'));

    // Check if the farmer belongs to the same affiliation
    if ($farmer->affiliation_id !== $user->affiliation_id) {
        return redirect()->back()->withErrors(['farmer_id' => 'The selected farmer does not belong to your affiliation.']);
    }

    // Create a new inventory valued crop
    InventoryValuedCrop::create([
        'farmer_id' => $farmer->id, // Use the farmer ID from the request
        'plant_id' => $request->input('plant_id'),
        'count' => $request->input('count'),
        'latitude' => $request->input('latitude'), // Store latitude
        'longitude' => $request->input('longitude'), // Store longitude
        'added_by' => $user->id, // Store the ID of the logged-in user
    ]);

    // Redirect to the count view with a success message
    return redirect()->route('user.count.count')->with('success', 'Crop added successfully.');
}







    public function edit($id)
    {
        // Hanapin ang crop gamit ang ibinigay na id
        $crop = InventoryValuedCrop::findOrFail($id);

        // Kunin ang farmer id mula sa crop record
        $farmerId = $crop->farmer_id;

        // I-retrieve ang inventory crops para sa farmer kasama ang kanilang IDs
        $inventoryCrops = InventoryValuedCrop::with('plant')->where('farmer_id', $farmerId)->get();

        // Ibalik ang view kasama ang crop, farmer id, at inventory crops
        return view('user.inventory.edit', compact('crop', 'inventoryCrops', 'farmerId'));
    }

    public function update(Request $request, $id)
{
    // Validate the incoming data
    $request->validate([
        'name_of_plants.*' => 'required|string|max:255',
        'count.*' => 'required|integer|min:0',
        'latitude.*' => 'required|regex:/^-?\d+(\.\d+)?$/|between:-90,90',
        'longitude.*' => 'required|regex:/^-?\d+(\.\d+)?$/|between:-180,180',
        'crop_ids.*' => 'exists:inventory_valued_crops,id', // Ensure the crop IDs are valid
    ]);

    // Loop through the crop IDs to update each one
    foreach ($request->crop_ids as $cropId) {
        $crop = InventoryValuedCrop::find($cropId);
        
        if ($crop) {
            // Get the plant name for this crop and find or create the plant entry
            $plantName = $request->input("name_of_plants.$cropId");
            $plant = Plant::firstOrCreate(['name_of_plants' => $plantName]);

            // Update crop's details
            $crop->plant_id = $plant->id;
            $crop->count = $request->input("count.$cropId");
            $crop->latitude = $request->input("latitude.$cropId");
            $crop->longitude = $request->input("longitude.$cropId");

            // Save changes
            $crop->save();
        }
    }

    // Redirect back with a success message
    return redirect()->route('user.count.count')->with('success', 'Plant details updated successfully.');
}




    public function destroy($id)
        {
            // Find the inventory record by ID and delete it
            $inventoryCrops = InventoryValuedCrop::findOrFail($id);
            $inventoryCrops->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        }


    
}

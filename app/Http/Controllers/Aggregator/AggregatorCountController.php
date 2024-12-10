<?php

namespace App\Http\Controllers\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\InventoryValuedCrop; 
use App\Models\Farmer;
use App\Models\Plant; // Assuming you have a Plant model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use thiagoalessio\TesseractOCR\TesseractOCR;



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
            
            ->join('users', 'inventory_valued_crops.added_by', '=', 'users.id') // Join to users table for added_by
            ->join('roles', 'users.role_id', '=', 'roles.id') // Join to roles table for role name

            ->where('farmers.added_by', $userId) // Filter by the user who added the inventory
            ->select(
                'farmers.first_name',
                'farmers.last_name',
                'farmers.added_by', // Add this to include the "added_by" column
                'affiliations.name_of_association',
                'affiliations.name_of_barangay',
                'inventory_valued_crops.id', 
                'inventory_valued_crops.farmer_id',
                'inventory_valued_crops.plant_id',
                'inventory_valued_crops.count', 
                'plants.name_of_plants', 
                'inventory_valued_crops.latitude', 
                'inventory_valued_crops.longitude',
                'inventory_valued_crops.created_at',
                'roles.role_name', // Get the user's role name
                'users.first_name as added_by_first_name', // Get the encoder's first name
                'users.last_name as added_by_last_name'   // Get the encoder's last name

            )
            ->get();

        // Get only the farmers added by the logged-in user (aggregator)
        $farmers = Farmer::where('added_by', $userId)->get();
        $plants = Plant::all();

        // Return the view with the data, ensuring no other changes to the query behavior
        return view('Aggregator.hvcdp.count', compact('inventoryCrops', 'farmers', 'plants'), [
            'title' => 'HVCDP - Counts'
        ]);
    }



    public function store(Request $request)
{
    // Get the authenticated user
    $user = Auth::user();

    if (!$user) {
        return redirect()->back()->withErrors(['user' => 'User not authenticated.']);
    }

    // Validate the input
    $request->validate([
        'farmer_id' => 'required|exists:farmers,id',
        'plant_id' => 'required|exists:plants,id',
        'count' => 'required|integer|min:1',
        'image' => 'nullable|image|max:10240',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    $imagePath = null;

    // Handle the uploaded image
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $imageFullPath = storage_path('app/public/' . $imagePath);

        try {
            // Run OCR using Tesseract
            $ocrText = (new TesseractOCR($imageFullPath))
                ->executable('arisecodingteam/Tesseract-OCR/tesseract.exe')
                ->lang('eng')
                ->run();

            // Extract coordinates from OCR text if latitude/longitude not provided
            if (empty($latitude) && empty($longitude)) {
                $coordinates = $this->extractCoordinatesFromText($ocrText);
                if ($coordinates) {
                    $latitude = $coordinates['latitude'];
                    $longitude = $coordinates['longitude'];
                } else {
                    throw new \Exception('Coordinates not found in the image text.');
                }
            }
        } catch (\Exception $e) {
            return back()->withErrors(['ocr_error' => 'Failed to process OCR: ' . $e->getMessage()]);
        }
    }

    // Create the new inventory record
    InventoryValuedCrop::create([
        'farmer_id' => $request->input('farmer_id'),
        'plant_id' => $request->input('plant_id'),
        'count' => $request->input('count'),
        'latitude' => $latitude,
        'longitude' => $longitude,
        'added_by' => $user->id,
        'image_path' => $imagePath,
    ]);

    return redirect()->route('aggregator.count.store')->with('success', 'Crop added successfully.');
}

/**
 * Extract coordinates (latitude and longitude) from the OCR text.
 */
private function extractCoordinatesFromText($ocrText)
{
    preg_match('/latitude\s*[:=]?\s*([\-0-9\.]+)\s*longitude\s*[:=]?\s*([\-0-9\.]+)/i', $ocrText, $matches);
    if (count($matches) > 2) {
        return [
            'latitude' => $matches[1],
            'longitude' => $matches[2],
        ];
    }
    return null;
}




    // Update an existing crop count
public function update(Request $request, $id)
{
    // Validate the input data
    $request->validate([
        'count' => 'required|integer|min:0',
        'plant_name' => 'nullable|string', // Optional plant name validation
        'latitude' => 'nullable|numeric|between:-90,90', // Latitude validation
        'longitude' => 'nullable|numeric|between:-180,180', // Longitude validation
    ]);

    // Find the crop record by ID
    $crop = InventoryValuedCrop::findOrFail($id);

    // Update the crop attributes
    $crop->count = $request->count;
    
    // Check if plant name is provided and update accordingly
    if ($request->plant_name) {
        $plant = Plant::where('name_of_plants', $request->plant_name)->first();
        if ($plant) {
            $crop->plant_id = $plant->id; // Assuming there's a plant_id in the inventory table
        }
    }

    // Update latitude and longitude if provided
    $crop->latitude = $request->latitude ?? null; // Set to null if not provided
    $crop->longitude = $request->longitude ?? null; // Set to null if not provided

    // Save the changes
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

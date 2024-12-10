<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InventoryValuedCrop; 
use App\Models\Farmer; 
use App\Models\Plant; // Assuming you have a Plant model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use thiagoalessio\TesseractOCR\TesseractOCR;


class UserEncodeController extends Controller
{
    public function count()
    {
        // Retrieve all farmers and plants for the form
        $farmers = Farmer::all();
        $plants = Plant::all();
        
        // Get the currently logged-in user's ID
        $userId = auth()->id();

        // Get only the inventory crops where 'added_by' matches the logged-in user's ID
        $inventoryCrops = InventoryValuedCrop::with('farmer', 'plant')
                            ->where('added_by', $userId)
                            ->get(); 

        

        // Return the view with the filtered data
        return view('User.encode.create_count', compact('inventoryCrops', 'farmers', 'plants'), [
            'title' => 'HVCDP - Counts'
        ]);
    }



    public function store(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->back()->withErrors(['user' => 'User not authenticated.']);
    }

    // Validate input
    $request->validate([
        'plant_id' => 'required|exists:plants,id',
        'count' => 'required|integer|min:1',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'image' => 'nullable|image|max:10240', // Optional image
    ]);

    // Initialize latitude and longitude from form or OCR
    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    $imagePath = null;

    // Process image for OCR if provided
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $imageFullPath = storage_path('app/public/' . $imagePath);

        if (!$latitude || !$longitude) {
            try {
                $ocrText = (new TesseractOCR($imageFullPath))
                    ->executable('/home/bttovgt/public_html/subdomain/bci.slsubc.com/bratscodingteam/Tesseract-OCR/tesseract.exe')
                    ->lang('eng')
                    ->run();

                // Extract coordinates if not manually provided
                $coordinates = $this->extractCoordinatesFromText($ocrText);
                $latitude = $latitude ?? $coordinates['latitude'];
                $longitude = $longitude ?? $coordinates['longitude'];
            } catch (\Exception $e) {
                return back()->withErrors(['ocr_error' => 'OCR failed: ' . $e->getMessage()]);
            }
        }
    }

    // Validate coordinates
    if ($latitude && $longitude && (!is_numeric($latitude) || !is_numeric($longitude))) {
        return redirect()->back()->withErrors(['coordinates' => 'Invalid latitude or longitude values.']);
    }

    // Ensure farmer belongs to the user's affiliation
    $farmer = $user->farmer;
    if (!$farmer || $farmer->affiliation_id !== $user->affiliation_id) {
        return redirect()->back()->withErrors(['farmer_id' => 'The selected farmer does not belong to your affiliation.']);
    }

    // Save crop data
    InventoryValuedCrop::create([
        'farmer_id' => $farmer->id,
        'plant_id' => $request->input('plant_id'),
        'count' => $request->input('count'),
        'latitude' => $latitude,
        'longitude' => $longitude,
        'added_by' => $user->id,
        'image_path' => $imagePath,
    ]);

    return redirect()->route('user.count.store')->with('success', 'Crop added successfully.');
}


    /**
     * Extract coordinates (latitude and longitude) from the OCR text.
     *
     * @param string $ocrText
     * @return array|null
     */
    private function extractCoordinatesFromText($ocrText)
    {
        // Regular expression for matching coordinates like "latitude: 0.156684, longitude: 51.520321"
        preg_match('/latitude\s*[:=]?\s*([\-0-9\.]+)\s*longitude\s*[:=]?\s*([\-0-9\.]+)/i', $ocrText, $matches);

        if (count($matches) > 2) {
            return [
                'latitude' => $matches[1],
                'longitude' => $matches[2],
            ];
        }

        return null;  // Return null if no coordinates found
    }









    public function edit($id)
    {
        // Hanapin ang crop gamit ang ibinigay na id
        $crop = InventoryValuedCrop::findOrFail($id);

        // Kunin ang farmer mula sa crop record
        $farmer = Farmer::find($crop->farmer_id); // Get the farmer record

        // I-retrieve ang inventory crops para sa farmer kasama ang kanilang IDs
        $inventoryCrops = InventoryValuedCrop::with('plant')->where('farmer_id', $crop->farmer_id)->get();

        // Ibalik ang view kasama ang crop, farmer, at inventory crops
        return view('User.inventory.edit', compact('crop', 'inventoryCrops', 'farmer'));
    }



    public function update(Request $request, $id)
{
    // Validate incoming data, without using arrays since only one crop is being edited
    $request->validate([
        'name_of_plants' => 'required|string|max:255',
        'count' => 'required|integer|min:0',
        'latitude' => 'required|regex:/^-?\d+(\.\d+)?$/|between:-90,90',
        'longitude' => 'required|regex:/^-?\d+(\.\d+)?$/|between:-180,180',
        'crop_id' => 'required|exists:inventory_valued_crops,id', // Ensure crop ID exists in the table
    ]);

    // Retrieve the specific crop record using the provided crop ID
    $crop = InventoryValuedCrop::find($request->crop_id);

    if ($crop) {
        // Get the plant name, count, latitude, and longitude for this crop
        $plantName = $request->input("name_of_plants");
        $count = $request->input("count");
        $latitude = $request->input("latitude");
        $longitude = $request->input("longitude");

        // Find or create a plant entry with the given name
        $plant = Plant::firstOrCreate(['name_of_plants' => $plantName]);

        // Update the crop's details
        $crop->plant_id = $plant->id;
        $crop->count = $count;
        $crop->latitude = $latitude;
        $crop->longitude = $longitude;

        // Save the changes to the crop
        $crop->save();

        // Redirect back with a success message
        return redirect()->route('user.count.count')->with('success', 'Plant details updated successfully.');
    } else {
        // If the crop ID does not exist, return an error message
        return redirect()->back()->withErrors("Crop with ID {$request->crop_id} not found.");
    }
}






    public function destroy($id)
        {
            // Find the inventory record by ID and delete it
            $inventoryCrops = InventoryValuedCrop::findOrFail($id);
            $inventoryCrops->delete();

            return redirect()->back()->with('success', 'Record deleted successfully.');
        }


    
}

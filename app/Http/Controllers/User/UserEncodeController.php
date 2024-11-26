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
        return view('user.encode.create_count', compact('inventoryCrops', 'farmers', 'plants'), [
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
            'plant_id' => 'required|exists:plants,id',
            'count' => 'required|integer|min:1',
            'image' => 'nullable|image|max:10240',  // Make image optional
        ]);

        // Initialize variables for latitude and longitude
        $latitude = $longitude = null;
        $imagePath = null;

        // Handle the uploaded image
        if ($request->hasFile('image')) {
            // Store the image in the public storage
            $imagePath = $request->file('image')->store('images', 'public');
            $imageFullPath = storage_path('app/public/' . $imagePath);

            try {
                // Run OCR using Tesseract
                $ocrText = (new TesseractOCR($imageFullPath))
                    ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
                    ->lang('eng')
                    ->run();

                // Log the OCR output for debugging
                \Log::info('OCR Output: ' . $ocrText);

                if (empty($ocrText)) {
                    throw new \Exception('No text detected in the image.');
                }

                // Extract coordinates from OCR text
                $coordinates = $this->extractCoordinatesFromText($ocrText);

                if ($coordinates) {
                    $latitude = $coordinates['latitude'];
                    $longitude = $coordinates['longitude'];
                } else {
                    throw new \Exception('Coordinates not found in the image text.');
                }
            } catch (\Exception $e) {
                return back()->withErrors(['ocr_error' => 'Failed to process OCR: ' . $e->getMessage()]);
            }
        }

        // Get the farmer associated with the logged-in user
        $farmer = $user->farmer; // Assuming a relationship exists between User and Farmer

        if (!$farmer || $farmer->affiliation_id !== $user->affiliation_id) {
            return redirect()->back()->withErrors(['farmer_id' => 'The selected farmer does not belong to your affiliation.']);
        }

        // Create the new inventory record
        InventoryValuedCrop::create([
            'farmer_id' => $farmer->id,  // Use the farmer ID associated with the logged-in user
            'plant_id' => $request->input('plant_id'),
            'count' => $request->input('count'),
            'latitude' => $latitude,  // Store latitude
            'longitude' => $longitude,  // Store longitude
            'added_by' => $user->id,  // Save the ID of the logged-in user
            'image_path' => $imagePath,  // Save the image path or null if no image
        ]);

        return redirect()->route('user.count.count')->with('success', 'Crop added successfully.');
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
        return view('user.inventory.edit', compact('crop', 'inventoryCrops', 'farmer'));
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

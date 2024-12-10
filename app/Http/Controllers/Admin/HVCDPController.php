<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Affiliation;
use App\Models\Role;
use App\Models\User;
use App\Models\InventoryValuedCrop;
use App\Exports\HVCDPExport;
use Illuminate\Support\Facades\DB;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class HVCDPController extends Controller
{
    public function index(Request $request)
{
    // I-validate ang mga input na petsa
    $request->validate([
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
        'barangay' => 'nullable|string',
        'inputted_data' => 'nullable|array', // Para sa pag-filter ng may data o walang data
    ]);

    // I-query ang mga farmers
    $farmers = Farmer::with('inventoryValuedCrops.plant')->newQuery();

    // Kung may filter na provided para sa petsa
    if ($request->has('from_date') && $request->has('to_date')) {
        // I-format ang petsa para sa timestamp
        $fromDate = $request->from_date . ' 00:00:00'; // simula ng araw
        $toDate = $request->to_date . ' 23:59:59'; // katapusan ng araw

        $farmers->whereBetween('created_at', [$fromDate, $toDate]);
    }

    // Kung gusto mong i-filter ang farmers batay sa affiliation (barangay)
    if ($request->has('barangay')) {
        $farmers->whereHas('affiliation', function($query) use ($request) {
            $query->where('name_of_barangay', $request->barangay);
        });
    }

    // Filter para sa mga farmers na may data o walang data
    if ($request->has('inputted_data')) {
        if (in_array('yes', $request->input('inputted_data'))) {
            $farmers->whereHas('inventoryValuedCrops');
        }
        if (in_array('no', $request->input('inputted_data'))) {
            $farmers->doesntHave('inventoryValuedCrops');
        }
    }

    // Kunin ang mga farmers
    $farmers = $farmers->get();

    // Kunin ang lahat ng affiliations
    $affiliations = Affiliation::all();

    // Kunin ang mga unique plants
    $uniquePlants = $farmers->flatMap(function($farmer) {
        return $farmer->inventoryValuedCrops->pluck('plant.name_of_plants');
    })->unique();

    return view('Admin.hvcdp.index', compact('affiliations', 'farmers', 'uniquePlants'), [
        'title' => 'HVCDP - Records'
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
        'image' => 'nullable|image|max:10240',  // Optional image
        'latitude' => 'nullable|regex:/^\-?\d+(\.\d+)?$/',  // Validate latitude format
        'longitude' => 'nullable|regex:/^\-?\d+(\.\d+)?$/', // Validate longitude format
    ]);

    // Variables for latitude, longitude, and image path
    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    $imagePath = null;

    // Handle the uploaded image
    if ($request->hasFile('image')) {
        // Store the image
        $imagePath = $request->file('image')->store('images', 'public');
        $imageFullPath = storage_path('app/public/' . $imagePath);

        try {
            // Run OCR using Tesseract
            $ocrText = (new TesseractOCR($imageFullPath))
                ->executable('/home/bttovgt/public_html/subdomain/bci.slsubc.com/bratscodingteam/Tesseract-OCR/tesseract.exe')
                ->lang('eng')
                ->run();

            // Extract coordinates from OCR text
            $coordinates = $this->extractCoordinatesFromText($ocrText);

            if ($coordinates) {
                $latitude = $coordinates['latitude'];
                $longitude = $coordinates['longitude'];
            }
        } catch (\Exception $e) {
            return back()->withErrors(['ocr_error' => 'Failed to process OCR: ' . $e->getMessage()]);
        }
    }

    // If no latitude or longitude and no image uploaded, set default values or allow empty values
    if (is_null($latitude) || is_null($longitude)) {
        $latitude = $latitude ?: null; // Allow null if not provided
        $longitude = $longitude ?: null; // Allow null if not provided
    }

    // Create the new inventory record, allowing latitude and longitude to be null
    InventoryValuedCrop::create([
        'farmer_id' => $request->input('farmer_id'),
        'plant_id' => $request->input('plant_id'),
        'count' => $request->input('count'),
        'latitude' => $latitude,  // Can be null
        'longitude' => $longitude,  // Can be null
        'added_by' => $user->id,
        'image_path' => $imagePath,  // Image is optional
    ]);

    return redirect()->route('admin.hvcdp.index')->with('success', 'Crop added successfully.');
}









    public function update(Request $request, $id)
    {
        // Hanapin ang farmer gamit ang id
        $farmer = Farmer::find($id);

        if (!$farmer) {
            return redirect()->route('admin.hvcdp.index')->with('error', 'Farmer not found.');
        }

        $farmer->first_name = $request->first_name;
        $farmer->last_name = $request->last_name;
        $farmer->save();

        $user = User::find($farmer->user_id); // Siguraduhing may user_id sa farmers table
        if ($user) {
            $user->first_name = $request->first_name; // Update first name
            $user->last_name = $request->last_name;   // Update last name
            $user->save(); // I-save ang mga pagbabago
        }
        if (isset($request->plants) && is_array($request->plants)) {
            // Update the crops associated with the farmer
            foreach ($request->plants as $plant_id => $count) {
                DB::table('inventory_valued_crops')
                    ->where('farmer_id', $id)
                    ->where('plant_id', $plant_id)
                    ->update(['count' => $count]);
            }
        }

        return redirect()->route('admin.hvcdp.index')->with('success', 'Farmer and crops updated successfully');
    }

    public function destroy($id)
    {
        // Hanapin ang farmer gamit ang id
        $farmer = Farmer::findOrFail($id); // Gumamit ng 'findOrFail' para masigurong may makikitang farmer

        if ($farmer) {
            // Kunin ang kaugnay na user_id mula sa farmer record
            $userId = $farmer->user_id;

            // I-delete muna ang farmer record
            $farmer->delete();

            // Hanapin ang user gamit ang user_id na nakuha mula sa farmer record
            if ($userId) {
                $user = User::findOrFail($userId); // Tiyakin na may makikita kang user
                $user->delete(); // I-delete ang user
            }

            // Mag-redirect pabalik na may success message
            return redirect()->route('admin.hvcdp.index')->with('success', 'Farmer and associated user deleted successfully.');
        }

        // Mag-redirect kung walang farmer na nakita
        return redirect()->route('admin.hvcdp.index')->with('error', 'Farmer not found.');
    }




    // Updated print method to include filters
    public function print(Request $request)
{
    // Validate input dates and filters
    $request->validate([
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
        'barangay' => 'nullable|string',
        'inputted_data' => 'nullable|array',
        'inputted_data.*' => 'in:yes,no', // Ensuring only 'yes' or 'no' values
    ]);

    // Start query for farmers with inventory data and affiliations
    $farmers = Farmer::with(['inventoryValuedCrops.plant', 'affiliation'])->newQuery();

    // Apply date filters if provided
    if ($request->filled(['from_date', 'to_date'])) {
        $fromDate = $request->from_date . ' 00:00:00'; // Start of the day
        $toDate = $request->to_date . ' 23:59:59'; // End of the day
        $farmers->whereBetween('created_at', [$fromDate, $toDate]);
    }

    // Apply barangay filter if provided
    if ($request->filled('barangay')) {
        $farmers->whereHas('affiliation', function($query) use ($request) {
            $query->where('name_of_barangay', $request->barangay);
        });
    }

    // Apply inputted data filter if provided
    $inputtedData = $request->input('inputted_data', []);
    if (in_array('yes', $inputtedData) && !in_array('no', $inputtedData)) {
        // Filter farmers with inventory data
        $farmers->whereHas('inventoryValuedCrops');
    } elseif (in_array('no', $inputtedData) && !in_array('yes', $inputtedData)) {
        // Filter farmers without inventory data
        $farmers->whereDoesntHave('inventoryValuedCrops');
    }

    // Retrieve filtered farmers
    $farmers = $farmers->get();

    // Get unique plants from the filtered farmers' inventory
    $uniquePlants = $farmers->flatMap(function($farmer) {
        return $farmer->inventoryValuedCrops->pluck('plant.name_of_plants');
    })->unique()->values();

    return view('Admin.hvcdp.print', compact('farmers', 'uniquePlants'), [
        'title' => 'HVCDP Print'
    ]);
}


    // Method to export data filtered by Barangay
    public function exportBarangay(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'barangay' => 'nullable|string',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $barangay = $request->input('barangay');

        // Query to filter farmers based on barangay and date range
        $farmers = Farmer::with(['inventoryValuedCrops.plant', 'affiliation'])
            ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                $fromDate = $request->from_date . ' 00:00:00'; // Start of the day
                $toDate = $request->to_date . ' 23:59:59'; // End of the day
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->when($barangay, function ($query) use ($barangay) {
                $query->whereHas('affiliation', function ($q) use ($barangay) {
                    $q->where('name_of_barangay', $barangay);
                });
            })
            ->get();

        // Pass the filtered farmers to the export class
        return Excel::download(new HVCDPExport($barangay, $request->from_date, $request->to_date, $farmers), 'hvcdp_' . ($barangay ?? 'all') . '.xlsx');
    }


}

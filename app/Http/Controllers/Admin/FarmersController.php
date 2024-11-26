<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\User;
use App\Models\Farmer;
use App\Models\Affiliation; // Import the Affiliation model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\FarmersImport;
use Maatwebsite\Excel\Facades\Excel;

class FarmersController extends Controller 
{

    public function index(Request $request)
{
    
    // Fetch all affiliations for the dropdowns
    $affiliations = Affiliation::all();

    // Fetch unique barangays and associations for filter options
    $uniqueBarangays = Affiliation::distinct()->pluck('name_of_barangay');
    // Fetch unique associations from the Affiliation table and exclude null or empty values
    $uniqueAssociations = Affiliation::distinct()
        ->whereNotNull('name_of_association')
        ->where('name_of_association', '!=', '')
        ->pluck('name_of_association');


    // Pass filtered farmers, affiliations, and unique filters to the view
    return view('admin.farmers.create', compact('farmers', 'affiliations', 'uniqueBarangays', 'uniqueAssociations'), [
        'title' => 'Farmers List'
    ]);
}


 
    public function create()
{
    // Fetch all affiliations 
    $affiliations = Affiliation::all();

    // Fetch unique barangays and associations from the Affiliation table
    $uniqueBarangays = Affiliation::distinct()->pluck('name_of_barangay');  // Array of strings
    $uniqueAssociations = Affiliation::distinct()->pluck('name_of_association');  // Array of strings

    $farmers = Farmer::get();


    
    // Pass affiliations, uniqueBarangays, and uniqueAssociations to the create view
    return view('admin.farmers.create', compact('affiliations', 'uniqueBarangays', 'uniqueAssociations', 'farmers'), [
        'title' => 'Add Farmer'
    ]);
}



    public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'name_of_barangay' => 'nullable|string|max:255',
        'name_of_association' => 'nullable|string|max:255',
        'birthdate' => 'nullable|date',
        'email' => 'nullable|email',
        'password' => 'nullable|min:6|confirmed',
    ]);

    // Check if barangay is provided
    if ($request->name_of_barangay) {
        // If association is provided, create or retrieve affiliation with both barangay and association
        if ($request->name_of_association) {
            $affiliation = Affiliation::firstOrCreate([
                'name_of_barangay' => $request->name_of_barangay,
                'name_of_association' => $request->name_of_association,
            ]);
        } else {
            // If no association is provided, check if there is already an affiliation with just the barangay
            $affiliation = Affiliation::firstOrCreate([
                'name_of_barangay' => $request->name_of_barangay,
            ]);

            // Ensure there is no affiliation with the same barangay that has an association
            // If an affiliation already exists with the same barangay but with an association, we won't create it again
            if ($affiliation->name_of_association) {
                // In case of association, we just use the existing record
                return redirect()->back()->withErrors(['name_of_barangay' => 'Barangay with association already exists.']);
            }
        }
    } else {
        // If no barangay is provided, return error or handle as needed
        return redirect()->back()->withErrors(['name_of_barangay' => 'Barangay is required']);
    }

    // Generate the control number
    $controlNumber = $this->generateControlNumber();

    // Create farmer and link to the affiliation
    $farmer = Farmer::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'extension' => $request->extension,
        'barangay' => $request->name_of_barangay, // Store barangay in the farmers table
        'affiliation_id' => $affiliation->id, // Link to the affiliation
        'control_number' => $controlNumber,
        'birthdate' => $request->birthdate,
        'added_by' => auth()->user()->id,
    ]);

    // If email and password are provided, create user
    if ($request->email && $request->password) {
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2, // Adjust role as needed
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'affiliation_id' => $affiliation->id,
        ]);

        // Link user to farmer
        $farmer->user_id = $user->id;
        $farmer->save();
    }

    // Redirect back with success message
    return redirect()->back()->with('success', 'Farmer added successfully!');
}






private function generateControlNumber()
{
    // Get the last farmer record based on control_number (if any)
    $lastFarmer = Farmer::orderBy('id', 'desc')->first();
    
    // Default to '000001' if there are no records
    $serialNumber = $lastFarmer ? (substr($lastFarmer->control_number, -6) + 1) : 1;
    $serialNumber = str_pad($serialNumber, 6, '0', STR_PAD_LEFT); // Ensure it's 6 digits
    
    // The fixed parts of the control number
    $yearCode = '08';     // Example: Fixed year part
    $barangayCode = '64'; // Example: Fixed barangay code
    $associationCode = '02'; // Example: Fixed association code
    $districtCode = '037';  // Example: Fixed district code
    
    // Construct and return the full control number
    return "$yearCode-$barangayCode-$associationCode-$districtCode-$serialNumber";
}






    public function show(Farmer $farmer)
    {
        return response()->json($farmer->load(['affiliation', 'addedBy']));
    }


    public function update(Request $request, $id)
{
    $farmer = Farmer::with('user')->findOrFail($id);

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'control_number' => 'required|string|max:255|unique:farmers,control_number,' . $id,
        'birthdate' => 'nullable|date',
        'affiliation_id' => 'nullable|exists:affiliations,id',
        'email' => 'nullable|email|unique:users,email,' . ($farmer->user->id ?? 'null'),
        'password' => 'nullable|min:6|confirmed',
    ]);

    // Update Farmer Details
    $farmer->update($request->only(['first_name', 'last_name', 'middle_name', 'extension', 'control_number', 'birthdate', 'affiliation_id']));

    // Handle Account Creation/Update
    if ($request->filled('add_account')) {
        if ($farmer->user) {
            // Update Existing User Account
            $user = $farmer->user;
            $user->update([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'extension' => $request->extension,
                'affiliation_id' => $request->affiliation_id,
            ]);

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
                $user->save();
            }
        } else {
            // Create New User Account
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => 2, // Farmer Role
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'extension' => $request->extension,
                'affiliation_id' => $request->affiliation_id,
            ]);

            // Associate User with Farmer
            $farmer->user()->associate($user);
            $farmer->save();
        }
    } else {
        // Remove Account if Account Fields Are Empty
        if ($farmer->user) {
            $farmer->user()->delete();
        }
    }

    return redirect()->back()->with('success', 'Farmer and account updated successfully.');
}




    public function destroy($id)
    {
        $farmer = Farmer::findOrFail($id);

        // Check if the farmer has an associated user account
        if ($farmer->user) {
            // Delete the associated user account
            $farmer->user->delete();
        }

        // Delete the farmer record
        $farmer->delete();

        return redirect()->back()->with('success', 'Farmer deleted successfully!');
    }


    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Import the Excel file
        Excel::import(new FarmersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Farmers imported successfully!');
    }

    
}

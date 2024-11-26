<?php

namespace App\Http\Controllers\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Affiliation; // Import the Affiliation model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AggregatorFarmersController extends Controller
{

    public function index(Request $request)
{
    // Get the currently logged-in user's ID
    $userId = auth()->id();


    // Fetch unique barangays and associations
    $uniqueBarangays = Affiliation::distinct()
        ->whereNotNull('name_of_barangay')
        ->where('name_of_barangay', '!=', '')
        ->pluck('name_of_barangay');

    $uniqueAssociations = Affiliation::distinct()
        ->whereNotNull('name_of_association')
        ->where('name_of_association', '!=', '')
        ->pluck('name_of_association');

    // Fetch all affiliations
    $affiliations = Affiliation::all();

    return view('aggregator.farmers.create', compact('farmers', 'affiliations', 'uniqueBarangays', 'uniqueAssociations'), [
        'title' => 'Farmers List',
    ]);
}



    public function create()
    {
        // Fetch all affiliations
        $affiliations = Affiliation::all();
        
        // Fetch all farmers for the create view
        $farmers = Farmer::with('affiliation')->get();

        // Fetch unique barangays and associations from the Affiliation table
        $uniqueBarangays = Affiliation::distinct()->pluck('name_of_barangay');  // Array of strings
        $uniqueAssociations = Affiliation::distinct()->pluck('name_of_association');  // Array of strings

        // Pass affiliations and farmers to the create view
        return view('aggregator.farmers.create', compact('affiliations', 'farmers', 'uniqueAssociations', 'uniqueBarangays'), [
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
        'name_of_barangay' => 'nullable|string|max:255', // Validate barangay
        'name_of_association' => 'nullable|string|max:255', // Validate association
        'birthdate' => 'nullable|date',
        // Make email and password optional
        'email' => 'nullable|email|unique:users,email',  
        'password' => 'nullable|min:6|confirmed', // Password is required only if provided
    ]);

    // Check if barangay and association exist in the Affiliation table
    if ($request->name_of_association) {
        // Check for the combination of barangay and association
        $affiliation = Affiliation::firstOrCreate([
            'name_of_barangay' => $request->name_of_barangay,
            'name_of_association' => $request->name_of_association,
        ]);
    } else {
        // Only check for the barangay if there's no association
        $affiliation = Affiliation::firstOrCreate([
            'name_of_barangay' => $request->name_of_barangay,
        ]);
    }

    // Generate the control number
    $controlNumber = $this->generateControlNumber();

    // Initialize the user variable
    $user = null;

    // Create the user account only if email and password are provided
    if ($request->email && $request->password) {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'extension' => $request->extension,
            'affiliation_id' => $request->affiliation_id,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash the password
            'role_id' => 2, // Set the appropriate role (adjust as needed)
        ]);
    }

    // Create farmer and link to the affiliation
    $farmer = Farmer::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'extension' => $request->extension,
        'affiliation_id' => $affiliation->id, // Link to the affiliation
        'control_number' => $controlNumber,
        'birthdate' => $request->birthdate,
        'added_by' => auth()->user()->id, // Link to the current logged-in user (admin/aggregator)
        'user_id' => $user ? $user->id : null, // Link the farmer to the user if created
    ]);

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
        return response()->json($farmer->load('affiliation'));
    }

    public function update(Request $request, $id)
{
    // Retrieve the farmer instance and its user
    $farmer = Farmer::with('user')->findOrFail($id);

    // Validate the request
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'affiliation_id' => 'nullable|exists:affiliations,id',
        'birthdate' => 'nullable|date',
        'email' => 'nullable|email|unique:users,email,' . ($farmer->user->id ?? 'null'), // Validate email if provided
        'password' => 'nullable|min:6|confirmed', // Optional password update
    ]);

    // Update the farmer's details including birthdate and affiliation
    $farmer->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'extension' => $request->extension,
        'affiliation_id' => $request->affiliation_id,
        'birthdate' => $request->birthdate, // Update birthdate
    ]);

    // Check if the farmer has an associated user account
    if ($farmer->user) {
        // Update the associated user's details if they exist
        $user = $farmer->user;

        // Only update email if provided
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Save user changes
        $user->save();

        return redirect()->back()->with('success', 'Farmer and user updated successfully.');
    } else {
        // If no user account exists, create a new user
        if ($request->filled('email') && $request->filled('password')) {
            $user = new User();
            $user->first_name = $request->first_name; // Optional
            $user->last_name = $request->last_name; // Optional
            $user->middle_name = $request->middle_name; // Optional
            $user->extension = $request->extension; // Optional
            $user->email = $request->email;
            $user->password = bcrypt($request->password); // Ensure the password is hashed
            $user->role_id = 2; // Set the appropriate role for farmers
            $user->save(); // Save the new user

            // Associate the user with the farmer
            $farmer->user()->associate($user);
            $farmer->save();
        }

        return redirect()->back()->with('success', 'Farmer details updated, and new user account created successfully.');
    }
}






    public function destroy($id)
    {
        // Find the farmer with the given ID
        $farmer = Farmer::findOrFail($id);

        // Retrieve the associated user
        $user = $farmer->user;

        // Delete the farmer
        $farmer->delete();

        // Check if the user exists and delete it
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', 'Farmer and associated user deleted successfully.');
    }

}

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
    return view('Admin.farmers.create', compact('farmers', 'affiliations', 'uniqueBarangays', 'uniqueAssociations'), [
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
    return view('Admin.farmers.create', compact('affiliations', 'uniqueBarangays', 'uniqueAssociations', 'farmers'), [
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
        'control_number' => [
            'required',
            'string',
            'regex:/^08-64-02-\d{3}-\d{6}$/', // Accepts format: 08-64-02-XXX-YYYYYY
            'unique:farmers,control_number',  // Ensures uniqueness
        ],
    ]);

    // Check if barangay is provided
    if ($request->name_of_barangay) {
        if ($request->name_of_association) {
            $affiliation = Affiliation::firstOrCreate([
                'name_of_barangay' => $request->name_of_barangay,
                'name_of_association' => $request->name_of_association,
            ]);
        } else {
            $affiliation = Affiliation::firstOrCreate([
                'name_of_barangay' => $request->name_of_barangay,
            ]);

            if ($affiliation->name_of_association) {
                return redirect()->back()->withErrors(['name_of_barangay' => 'Barangay with association already exists.']);
            }
        }
    } else {
        return redirect()->back()->withErrors(['name_of_barangay' => 'Barangay is required']);
    }

    // Create farmer and link to the affiliation
    $farmer = Farmer::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'extension' => $request->extension,
        'barangay' => $request->name_of_barangay,
        'affiliation_id' => $affiliation->id,
        'control_number' => $request->control_number, // Use user-provided control number
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

        $farmer->user_id = $user->id;
        $farmer->save();
    }

    return redirect()->back()->with('success', 'Farmer added successfully!');
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

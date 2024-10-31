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
    // FarmerController.php
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default to 10 if not specified
        $search = $request->get('search');
        $affiliationId = $request->get('affiliation_id');
        
        $farmers = Farmer::when($search, function($query) use ($search) {
                return $query->where('first_name', 'like', '%' . $search . '%')
                             ->orWhere('last_name', 'like', '%' . $search . '%');
            })
            ->when($affiliationId, function($query) use ($affiliationId) {
                return $query->where('affiliation_id', $affiliationId);
            })
            ->paginate($perPage);
            
        $affiliations = Affiliation::all();

        return view('admin.farmers.create', compact('farmers', 'affiliations'), [
            'title' => 'Farmers Lists'
        ]);
    }



    public function create()
    {
        // Fetch all affiliations 
        $affiliations = Affiliation::all();
        
        // Fetch all farmers for the create view
        $farmers = Farmer::with('affiliation')->get();

        // Pass affiliations and farmers to the create view
        return view('admin.farmers.create', compact('affiliations', 'farmers'), [
            'title' => 'Add Farmer'
        ]);
    }


    public function store(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension' => 'nullable|string|max:255',
        'affiliation_id' => 'nullable|exists:affiliations,id',
        'control_number' => 'required|string|size:19|unique:farmers,control_number|max:20', // Unique control number validation
        'birthdate' => 'nullable|date',
        'email' => 'nullable|email',
        'password' => 'nullable|min:6|confirmed',
    ]);

    // Create farmer
    $farmer = Farmer::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'extension' => $request->extension,
        'affiliation_id' => $request->affiliation_id,
        'control_number' => $request->control_number, // Save control number
        'birthdate' => $request->birthdate, // Save birthdate
        'added_by' => auth()->user()->id,
    ]);

    // If email and password are provided, create user
    if ($request->email && $request->password) {
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2, // Adjust role as needed
            'first_name' => $request->first_name, // Optional, if needed
            'last_name' => $request->last_name,   // Optional, if needed
            'affiliation_id' => $request->affiliation_id, // Optional, if needed
        ]);

        // Link user to farmer
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
        // Retrieve the farmer instance
        $farmer = Farmer::with('user')->findOrFail($id); // Load user relationship

        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'extension' => 'nullable|string|max:255',
            'affiliation_id' => 'nullable|exists:affiliations,id',
            'email' => 'nullable|email|unique:users,email,' . ($farmer->user->id ?? 'null'), // Validate email if provided
            'password' => 'nullable|min:6|confirmed', // Optional password update
        ]);

        // Update the farmer's details
        $farmer->first_name = $request->first_name;
        $farmer->last_name = $request->last_name;
        $farmer->middle_name = $request->middle_name;
        $farmer->extension = $request->extension;
        $farmer->affiliation_id = $request->affiliation_id;
        $farmer->save();

         // Check if the farmer has an associated user account
        if ($farmer->user) {
            // Update the farmer's details
            $farmer->first_name = $request->first_name;
            $farmer->last_name = $request->last_name;
            $farmer->middle_name = $request->middle_name;
            $farmer->extension = $request->extension;
            $farmer->affiliation_id = $request->affiliation_id;
            $farmer->save();

            // Update the associated user's details
            $user = $farmer->user;

            // Only update the email if provided
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
            if ($request->filled('email')) {
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
        $farmer = Farmer::findOrFail($id);

        // Check if the farmer has an associated user account
        if ($farmer->user) {
            // Delete the associated user account
            $farmer->user->delete();
        }

        // Delete the farmer record
        $farmer->delete();

        return redirect()->back()->with('success', 'Farmer added successfully!');
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

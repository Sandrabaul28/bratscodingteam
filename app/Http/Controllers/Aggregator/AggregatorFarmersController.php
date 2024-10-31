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
    public function index()
    {
        // Get the currently logged-in user's ID
        $userId = auth()->user()->id;
        // Fetch all farmers
        $farmers = Farmer::with('affiliation')->where('added_by', $userId)->get();

        // Fetch all affiliations 
        $affiliations = Affiliation::all();

        // Pass both farmers and affiliations to the view
        return view('aggregator.farmers.index', compact('farmers', 'affiliations'));
    }


    public function create()
    {
        // Fetch all affiliations
        $affiliations = Affiliation::all();
        
        // Fetch all farmers for the create view
        $farmers = Farmer::with('affiliation')->get();

        // Pass affiliations and farmers to the create view
        return view('aggregator.farmers.create', compact('affiliations', 'farmers'), [
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
            'added_by' => auth()->user()->id,
        ]);

        // If email and password are provided, create user
        if ($request->email && $request->password) {
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => 2, // Adjust as needed
                'first_name' => $request->first_name, // Optional, if needed
                'last_name' => $request->last_name,   // Optional, if needed
                'affiliation_id' => $request->affiliation_id,   // Optional, if needed
            ]);

            // Link user to farmer
            $farmer->user_id = $user->id; // Link user ID to farmer
            $farmer->save();
        }

        return redirect()->back()->with('success', 'Farmer added successfully!');
    }

    public function show(Farmer $farmer)
    {
        return response()->json($farmer->load('affiliation'));
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

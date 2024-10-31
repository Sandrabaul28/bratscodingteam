<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Farmer;
use App\Models\Affiliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // Constructor to apply auth middleware to all methods
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function createUser()
    {
        $users = User::with('role')->get();

        $roles = Role::whereIn('id', [1, 3])->get();
        $affiliations = Affiliation::all();
        return view('admin.roles.createUser', compact('roles', 'affiliations', 'users'), [
            'title' => 'Create User',
            'users' => User::all()
        ]);
    }


    public function storeUser(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'extension' => 'nullable|string|max:10',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'affiliation_id' => 'required|exists:affiliations,id',
        ]);
        

        // Create a new user and assign the validated data
        $user = new User();
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->middle_name   = $validated['middle_name'];
        $user->extension = $validated['extension'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']); // Encrypt the password
        $user->role_id = $validated['role_id']; // Assign role ID
        $user->affiliation_id = $validated['affiliation_id']; // Assign affiliation ID
        $user->save(); // Save the user to the database

        // Redirect back with success message
        return redirect()->route('admin.roles.createUser')->with('success', 'User created successfully.');
    }
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $affiliations = Affiliation::all();
        return view('admin.roles.edit-user', compact('user', 'roles', 'affiliations'));
    }

    public function updateUser(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',  // Add middle name validation
            'extension' => 'nullable|string|max:10',  // Add extension validation (optional)
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'affiliation_id' => 'nullable|exists:affiliations,id', // Validate affiliation if provided
            'password' => 'nullable|min:8|confirmed' // Optionally validate password
        ]);

        // Update the user in the users table
        $user = User::findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        // Update password if provided
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save(); // Save to the users table

        // Update the farmer data in the farmers table
        $farmer = Farmer::where('user_id', $id)->first(); // Assuming there's a relationship between user and farmer

        if ($farmer) {
            $farmer->first_name = $request->first_name;
            $farmer->last_name = $request->last_name;
            $farmer->middle_name = $request->middle_name ?? $farmer->middle_name; // Update only if provided
            $farmer->extension = $request->extension ?? $farmer->extension; // Update only if provided
            $farmer->affiliation_id = $request->affiliation_id ?? $farmer->affiliation_id; // Update only if provided

            $farmer->save(); // Save to the farmers table
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'User and farmer updated successfully!');
    }

    public function deleteUser($id)
    {
        // Delete the farmer data from the farmers table
        $farmer = Farmer::where('user_id', $id)->first();
        if ($farmer) {
            $farmer->delete();
        }

        // Delete the user from the users table
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.roles.createUser')->with('success', 'User and farmer deleted successfully.');
    }


}

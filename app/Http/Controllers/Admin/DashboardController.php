<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plant;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{ 

    public function index()
    {
        // Retrieve the total number of users
        $totalUsers = User::count(); // Make sure the User model is correctly referenced
        $totalPlants = Plant::count(); 
        return view('admin.dashboard.index', compact('totalUsers', 'totalPlants'), [
            'title' => 'Admin | Dashboard'
        ]); // Passing the variable to the view
    }
}

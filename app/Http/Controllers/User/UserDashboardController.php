<?php

namespace App\Http\Controllers\User;

use App\Models\Plant;
use App\Models\Affiliation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {

        $totalPlants = Plant::count(); 
        $totalBarangay = Affiliation::count();

        return view('User.dashboard.index', compact('totalPlants', 'totalBarangay'), [
            'title' => 'User | Dashboard'
        ]);
    }
}

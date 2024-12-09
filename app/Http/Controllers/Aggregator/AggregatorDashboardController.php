<?php

namespace App\Http\Controllers\Aggregator;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plant;
use App\Models\Farmer;
use App\Models\Role;
use App\Models\Affiliation;
use App\Models\InventoryValuedCrop;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AggregatorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    } 
    
    public function index()
    {

        $plantsData = DB::table('plants')
            ->join('inventory_valued_crops', 'plants.id', '=', 'inventory_valued_crops.plant_id')
            ->join('farmers', 'farmers.id', '=', 'inventory_valued_crops.farmer_id')
            ->select('plants.name_of_plants as plant_name', 
                     DB::raw('SUM(inventory_valued_crops.count) as total_plants'), 
                     DB::raw('COUNT(DISTINCT farmers.id) as total_farmers'), 
                     DB::raw('COUNT(DISTINCT farmers.affiliation_id) as total_barangays'))
            ->groupBy('plants.name_of_plants')
            ->get();

        // Get the total count of unique associations (excluding NULL values)
        $totalAffiliation = Affiliation::whereNotNull('name_of_association')
                                ->distinct('name_of_association')
                                ->count('name_of_association');

        // Get the role_id for 'aggregator' from the roles table (assuming the role name is 'aggregator')
        $aggregatorRoleId = Role::where('role_name', 'aggregator')->first()->id;

        // Retrieve the total number of users added by the aggregator
        $totalUsers = User::where('role_id', $aggregatorRoleId)->count();

        $totalPlants = Plant::count(); 
         // Get the total count of unique barangays (excluding NULL values)
        $totalBarangay = Affiliation::whereNotNull('name_of_barangay')
                                    ->where('name_of_barangay', '!=', '')
                                    ->distinct('name_of_barangay')
                                    ->count('name_of_barangay');

        return view('Aggregator.dashboard.index', compact('totalUsers', 'totalPlants', 'plantsData', 'totalBarangay', 'totalAffiliation'), [
            'title' => 'Aggregator | Dashboard'
        ]); // Passing the variable to the view
    }
}

<?php

namespace App\Http\Controllers\Aggregator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AggregatorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    } 
    
    public function index()
    {
        // Return the view for the aggregator dashboard
        return view('aggregator.dashboard.index', [
            'title' => 'Aggregator | Dashboard'
        ]);
    }
}

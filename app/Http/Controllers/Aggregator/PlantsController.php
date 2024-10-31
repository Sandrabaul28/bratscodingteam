<?php

namespace App\Http\Controllers\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\PlantVariety;
use Illuminate\Http\Request;

class PlantsController extends Controller
{
    public function index()
    {
        $plants = Plant::all();
        $plants = Plant::with('varieties')->get(); // Adjust the pagination as needed
        return view('aggregator.plants.index', compact('plants'), [
            'title' => 'CROPS | Plants'
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name_of_plants' => 'required|string|max:255',
            'variety_name' => 'required|string|max:255', // Changed validation for variety
        ]);

        // Create the plant
        $plant = Plant::create([
            'name_of_plants' => $request->name_of_plants,
        ]);

        // Save the single variety
        PlantVariety::create([
            'plant_id' => $plant->id,
            'variety_name' => $request->variety_name, // Save the variety name
        ]);

        return redirect()->back()->with('success', 'Plant added successfully!');
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);
        $plant->update([
            'name_of_plants' => $request->name_of_plants,
        ]);

        // Update or create the variety associated with the plant
        $plant->varieties()->updateOrCreate(
            ['plant_id' => $plant->id], // condition
            ['variety_name' => $request->variety_name] // update data
        );

        return redirect()->route('aggregator.plants.index')->with('success', 'Plant updated successfully.');
    }

    
    public function destroy($id)
    {
        $plant = Plant::findOrFail($id);
        $plant->varieties()->delete(); // Assuming you have a relationship to delete varieties
        $plant->delete();

        return redirect()->route('aggregator.plants.index')->with('success', 'Plant and its varieties deleted successfully.');
    }


}

<?php

namespace App\Http\Controllers\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\User;
use App\Models\Affiliation;
use App\Models\InventoryValuedCrop;
use App\Exports\HVCDPExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RecordController extends Controller
{
    public function index(Request $request)
{
    // Validate the input dates
    $request->validate([
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
        'barangay' => 'nullable|string',
        'inputted_data' => 'nullable|array', // For filtering by data presence
    ]);

    // Get the current user's role ID
    $currentUserRoleId = auth()->user()->role_id;

    // Query the farmers, including their inventory
    $farmers = Farmer::with('inventoryValuedCrops.plant')->newQuery();

    // Filter by date if provided
    if ($request->has('from_date') && $request->has('to_date')) {
        $fromDate = $request->from_date . ' 00:00:00'; // start of the day
        $toDate = $request->to_date . ' 23:59:59'; // end of the day

        $farmers->whereBetween('created_at', [$fromDate, $toDate]);
    }

    // Filter by barangay if provided
    if ($request->has('barangay')) {
        $farmers->whereHas('affiliation', function($query) use ($request) {
            $query->where('name_of_barangay', $request->barangay);
        });
    }

    // Filter for farmers with or without data
    if ($request->has('inputted_data')) {
        if (in_array('yes', $request->input('inputted_data'))) {
            $farmers->whereHas('inventoryValuedCrops');
        }
        if (in_array('no', $request->input('inputted_data'))) {
            $farmers->doesntHave('inventoryValuedCrops');
        }
    }

    // Filter to show only farmers added by the current user
    $farmers->where('added_by', $currentUserRoleId);

    // Get the filtered farmers
    $farmers = $farmers->get();

    // Get all affiliations
    $affiliations = Affiliation::all();

    // Get unique plants
    $uniquePlants = $farmers->flatMap(function($farmer) {
        return $farmer->inventoryValuedCrops->pluck('plant.name_of_plants');
    })->unique();

    return view('aggregator.hvcdp.index', compact('affiliations', 'farmers', 'uniquePlants'), [
        'title' => 'HVCDP - Records'
    ]);
}




    public function create()
    {
        return view('hvcdp.create');
    }

    public function show($id)
    {
        $farmer = Farmer::findOrFail($id);
        return view('aggregator.hvcdp.show', compact('farmer'));
    }

    public function edit($id)
    {
        $farmer = Farmer::with('inventoryValuedCrops')->findOrFail($id);
        return view('aggregator.hvcdp.edit', compact('farmer'));
    }

    public function update(Request $request, $id)
    {
        // Hanapin ang farmer gamit ang id
        $farmer = Farmer::find($id);

        // Siguraduhing ang farmer ay natagpuan
        if (!$farmer) {
            return redirect()->route('aggregator.hvcdp.index')->with('error', 'Farmer not found.');
        }

        // Update farmer details
        $farmer->first_name = $request->first_name;
        $farmer->last_name = $request->last_name;
        $farmer->save();

        // Hanapin ang User gamit ang user_id ng farmer
        $user = User::find($farmer->user_id); // Siguraduhing may user_id sa farmers table
        if ($user) {
            $user->first_name = $request->first_name; // Update first name
            $user->last_name = $request->last_name;   // Update last name
            $user->save(); // I-save ang mga pagbabago
        }

        // Siguraduhing may laman ang $request->plants bago mag-loop
        if (isset($request->plants) && is_array($request->plants)) {
            // Update the crops associated with the farmer
            foreach ($request->plants as $plant_id => $count) {
                DB::table('inventory_valued_crops')
                    ->where('farmer_id', $id)
                    ->where('plant_id', $plant_id)
                    ->update(['count' => $count]);
            }
        }

        return redirect()->route('aggregator.hvcdp.index')->with('success', 'Farmer and crops updated successfully');
    }

    public function destroy($id)
    {
        // Hanapin ang farmer gamit ang id
        $farmer = Farmer::findOrFail($id); // Gumamit ng 'findOrFail' para masigurong may makikitang farmer

        if ($farmer) {
            // Kunin ang kaugnay na user_id mula sa farmer record
            $userId = $farmer->user_id;

            // I-delete muna ang farmer record
            $farmer->delete();

            // Hanapin ang user gamit ang user_id na nakuha mula sa farmer record
            if ($userId) {
                $user = User::findOrFail($userId); // Tiyakin na may makikita kang user
                $user->delete(); // I-delete ang user
            }

            // Mag-redirect pabalik na may success message
            return redirect()->route('aggregator.hvcdp.index')->with('success', 'Farmer and associated user deleted successfully.');
        }

        // Mag-redirect kung walang farmer na nakita
        return redirect()->route('aggregator.hvcdp.index')->with('error', 'Farmer not found.');
    }

    // Updated print method to include filters
    public function print(Request $request)
    {
        // I-validate ang mga input na petsa
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'barangay' => 'nullable|string',
        ]);

        // Kunin ang ID ng kasalukuyang naka-login na user
        $userId = auth()->user()->id;

        // I-query ang mga farmers na naidagdag ng user
        $farmers = Farmer::with(['inventoryValuedCrops.plant', 'affiliation'])
            ->where('added_by', $userId) // Filter sa added_by ng user
            ->newQuery();

        // Kung may filter na provided
        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->from_date . ' 00:00:00'; // simula ng araw
            $toDate = $request->to_date . ' 23:59:59'; // katapusan ng araw

            $farmers->whereBetween('created_at', [$fromDate, $toDate]);
        }

        // Kung gusto mong i-filter ang farmers batay sa affiliation (barangay)
        if ($request->has('barangay')) {
            $farmers->whereHas('affiliation', function($query) use ($request) {
                $query->where('name_of_barangay', $request->barangay);
            });
        }

        // Kunin ang mga farmers
        $farmers = $farmers->get();

        // Kunin ang mga unique plants mula sa mga filtered farmers
        $uniquePlants = $farmers->flatMap(function($farmer) {
            return $farmer->inventoryValuedCrops->pluck('plant.name_of_plants');
        })->unique()->values();

        return view('aggregator.hvcdp.print', compact('farmers', 'uniquePlants'), [
            'title' => 'HVCDP Print'
        ]);
    }


    public function exportBarangay(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'barangay' => 'nullable|string',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $barangay = $request->input('barangay');
        $userId = auth()->user()->id; // Get the currently logged-in user's ID

        // Query to filter farmers based on barangay and date range
        $farmers = Farmer::with(['inventoryValuedCrops.plant', 'affiliation'])
            ->where('added_by', $userId) // Filter by the current user's ID
            ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                $fromDate = $request->from_date . ' 00:00:00'; // Start of the day
                $toDate = $request->to_date . ' 23:59:59'; // End of the day
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->when($barangay, function ($query) use ($barangay) {
                $query->whereHas('affiliation', function ($q) use ($barangay) {
                    $q->where('name_of_barangay', $barangay);
                });
            })
            ->get();

        // Pass the filtered farmers to the export class
        return Excel::download(new HVCDPExport($barangay, $request->from_date, $request->to_date, $farmers), 'hvcdp_' . ($barangay ?? 'all') . '.xlsx');
    }



}

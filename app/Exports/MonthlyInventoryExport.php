<?php

namespace App\Exports;

use App\Models\MonthlyInventory;
use App\Models\InventoryValuedCrop; // Import the InventoryValuedCrop model
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; 
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyInventoryExport implements FromView, WithHeadings, WithStyles
{

    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function view(): View
    {
       // Use the data passed into the constructor
        $monthlyInventories = $this->data;

        // Map through the inventories to create an array of data
        $data = $monthlyInventories->map(function ($inventory) {
            // Get the latitude and longitude from the inventory_valued_crops table
            $valuedCrop = InventoryValuedCrop::where('farmer_id', $inventory->farmer_id)->first();

            // Concatenate the farmer's name
            $farmerFullName = trim($inventory->farmer->last_name . ', ' . $inventory->farmer->first_name . ' ' . $inventory->farmer->middle_name . ' ' . $inventory->farmer->extension);

            return [
                'barangay' => $inventory->farmer->affiliation->name_of_barangay ?? '', // Barangay
                'commodity' => $inventory->plant->name_of_plants ?? '',                // Commodity (Plant name)
                'farmer' => $farmerFullName,                                          // Full Farmer Name
                'planting_density' => $inventory->planting_density,                   // Planting Density (ha)
                'production_volume' => $inventory->production_volume,                 // Production Volume / Hectare (MT)
                'newly_planted' => $inventory->newly_planted,                        // # Hill/Puno - Newly Planted
                'vegetative' => $inventory->vegetative,                              // # Hill/Puno - Vegetative
                'reproductive' => $inventory->reproductive,                          // # Hill/Puno - Reproductive
                'maturity_harvested' => $inventory->maturity_harvested,              // # Hill/Puno - Maturity/Harvested
                'total' => $inventory->newly_planted + $inventory->vegetative + $inventory->reproductive + $inventory->maturity_harvested, // Total # Hills

                'newly_planted_divided' => $inventory->newly_planted_divided,        // Planted Area (Ha) - Newly Planted
                'vegetative_divided' => $inventory->vegetative_divided,              // Planted Area (Ha) - Vegetative
                'reproductive_divided' => $inventory->reproductive_divided,          // Planted Area (Ha) - Reproductive
                'maturity_harvested_divided' => $inventory->maturity_harvested_divided, // Planted Area (Ha) - Maturity/Harvested
                'total_planted_area' => $inventory->total_planted_area,

                'area_harvested' => $inventory->area_harvested,                     // Area Harvested (Has)

                'final_production_volume' => $inventory->final_production_volume,   // Final Production Volume (MT)

                // Add latitude and longitude
                'latitude' => $valuedCrop->latitude ?? '',                          // Latitude
                'longitude' => $valuedCrop->longitude ?? '',                        // Longitude
                'control_number' => $inventory->farmer->control_number ?? '',        // Control Number
                'birthdate' => $inventory->farmer->birthdate ?? '', 
                'association' => $inventory->farmer->affiliation->name_of_association ?? '', // Barangay

            ];
        });

        // Return the view with the data variable
        return view('admin.inventory.monthly_inventory', ['data' => $data]);
    }

    public function headings(): array
    {
        return [
            ['BARANGAY', 'COMMODITY', "FARMER'S", 'Planting Density (ha)', 'Prodn. Vol/Hectare (MT)', '# Hill/Puno', 'Newly Planted', 'Vegetative', 'Reproductive', 'Maturity/Harvested', 'TOTAL', 'Area Harvested (HAS)', 'Production Volume (MT)', 'Latitude', 'Longitude'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge headers
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:E1');
        $sheet->mergeCells('F1:H1');
        $sheet->mergeCells('I1:K1');
        $sheet->mergeCells('L1:M1');
        $sheet->mergeCells('N1:N2'); // Latitude
        $sheet->mergeCells('O1:O2'); // Longitude

        // Apply styles
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4CAF50']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Set auto size for columns
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }
}

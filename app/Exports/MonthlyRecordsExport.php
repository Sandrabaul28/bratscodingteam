<?php

namespace App\Exports;

use App\Models\MonthlyRecords; // Gamitin ang MonthlyRecords model
use App\Models\InventoryValuedCrop; // Import InventoryValuedCrop model
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; 
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyRecordsExport implements FromView, WithHeadings, WithStyles
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
        // Gamitin ang data na ipinasa sa constructor
        $monthlyRecords = $this->data;

        // I-map ang mga record sa data
        $data = $monthlyRecords->map(function ($record) {
            // Kunin ang latitude at longitude mula sa InventoryValuedCrop
            $valuedCrop = InventoryValuedCrop::where('farmer_id', $record->farmer_id)->first();

            // I-concatenate ang pangalan ng farmer
            $farmerFullName = trim($record->farmer->last_name . ', ' . $record->farmer->first_name . ' ' . $record->farmer->middle_name . ' ' . $record->farmer->extension);

            return [
                'barangay' => $record->farmer->affiliation->name_of_barangay ?? '', // Barangay
                'commodity' => $record->plant->name_of_plants ?? '',                // Commodity (Plant name)
                'farmer' => $farmerFullName,                                          // Full Farmer Name
                'planting_density' => $record->planting_density,                     // Planting Density (ha)
                'production_volume' => $record->production_volume,                   // Production Volume / Hectare (MT)
                'newly_planted' => $record->newly_planted,                           // # Hill/Puno - Newly Planted
                'vegetative' => $record->vegetative,                                 // # Hill/Puno - Vegetative
                'reproductive' => $record->reproductive,                             // # Hill/Puno - Reproductive
                'maturity_harvested' => $record->maturity_harvested,                 // # Hill/Puno - Maturity/Harvested
                'total' => $record->newly_planted + $record->vegetative + $record->reproductive + $record->maturity_harvested, // Total # Hills

                'newly_planted_divided' => $record->newly_planted_divided,           // Planted Area (Ha) - Newly Planted
                'vegetative_divided' => $record->vegetative_divided,                 // Planted Area (Ha) - Vegetative
                'reproductive_divided' => $record->reproductive_divided,             // Planted Area (Ha) - Reproductive
                'maturity_harvested_divided' => $record->maturity_harvested_divided, // Planted Area (Ha) - Maturity/Harvested
                'total_planted_area' => $record->total_planted_area,

                'area_harvested' => $record->area_harvested,                        // Area Harvested (Has)

                'final_production_volume' => $record->final_production_volume,      // Final Production Volume (MT)

                // Add latitude and longitude
                'latitude' => $valuedCrop->latitude ?? '',                           // Latitude
                'longitude' => $valuedCrop->longitude ?? '',                         // Longitude
                'control_number' => $record->farmer->control_number ?? '',           // Control Number
                'birthdate' => $record->farmer->birthdate ?? '', 
                'association' => $record->farmer->affiliation->name_of_association ?? '', // Barangay
            ];
        });

        // Ibalik ang view na may data na variable
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
        // I-merge ang mga headers
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:E1');
        $sheet->mergeCells('F1:H1');
        $sheet->mergeCells('I1:K1');
        $sheet->mergeCells('L1:M1');
        $sheet->mergeCells('N1:N2'); // Latitude
        $sheet->mergeCells('O1:O2'); // Longitude

        // I-apply ang styles sa header
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4CAF50']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // I-set ang auto size para sa mga column
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }
}

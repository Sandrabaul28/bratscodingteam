<?php

namespace App\Exports;

use App\Models\InventoryValueCrops;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Carbon\Carbon;

class MapExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    protected $startDate;
    protected $endDate;

    // Constructor to accept the start and end date
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return InventoryValueCrops::join('farmers', 'inventory_valued_crops.farmer_id', '=', 'farmers.id')
            ->join('plants', 'inventory_valued_crops.plant_id', '=', 'plants.id')
            ->leftJoin('affiliations', 'farmers.affiliation_id', '=', 'affiliations.id')
            ->leftJoin('monthly_inventories', function($join) {
                $join->on('monthly_inventories.farmer_id', '=', 'farmers.id')
                     ->on('monthly_inventories.plant_id', '=', 'inventory_valued_crops.plant_id');
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                return $query->whereBetween('monthly_inventories.created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            })
            ->select(
                'farmers.first_name',
                'farmers.middle_name',
                'farmers.last_name',
                'plants.name_of_plants',
                'affiliations.name_of_barangay',
                'inventory_valued_crops.latitude',
                'inventory_valued_crops.longitude',
                'inventory_valued_crops.total',
                'inventory_valued_crops.total_planted_area',
                'monthly_inventories.created_at' // Add the created_at field
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Middle Name',
            'Last Name',
            'Plant',
            'Barangay',
            'Latitude',
            'Longitude',
            'Tree/Hills',
            'Area (ha)',
            'Encoded Date' // Add the header for created_at
        ];
    }
}

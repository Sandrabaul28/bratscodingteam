<?php


namespace App\Exports;

use App\Models\Farmer;
use App\Models\MonthlyInventory;
use App\Models\Plant;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyInventoryExport implements FromView, WithHeadings, WithStyles
{
    protected $userId;
    protected $barangay;
    protected $fromDate;
    protected $toDate;

    public function __construct($userId, $barangay, $fromDate, $toDate)
    {
        $this->userId = $userId;
        $this->barangay = $barangay;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        $farmersQuery = Farmer::with('affiliation')->where('added_by', $this->userId);

        if ($this->barangay) {
            $farmersQuery->whereHas('affiliation', function ($query) {
                $query->where('name_of_barangay', $this->barangay);
            });
        }

        if ($this->fromDate && $this->toDate) {
            $fromDateTime = $this->fromDate . ' 00:00:00';
            $toDateTime = $this->toDate . ' 23:59:59';
            $farmersQuery->whereBetween('created_at', [$fromDateTime, $toDateTime]);
        }

        $farmers = $farmersQuery->get();

        $data = $farmers->map(function ($farmer) {
            $data = [
                $farmer->affiliation->name_of_barangay ?? '',
                $farmer->last_name,
                $farmer->first_name,
                $farmer->middle_name,
                $farmer->extension,
            ];

            $monthlyInventories = MonthlyInventory::where('farmer_id', $farmer->id)->get();
            $uniquePlants = Plant::all();
            $plantCounts = [];

            foreach ($uniquePlants as $plant) {
                $inventory = $monthlyInventories->where('plant_id', $plant->id)->first();

                $plantCounts[] = [
                    'newly_planted' => $inventory ? $inventory->newly_planted : 0,
                    'vegetative' => $inventory ? $inventory->vegetative : 0,
                    'reproductive' => $inventory ? $inventory->reproductive : 0,
                    'maturity_harvested' => $inventory ? $inventory->maturity_harvested : 0,
                    'total' => $inventory ? $inventory->newly_planted + $inventory->vegetative + $inventory->reproductive + $inventory->maturity_harvested : 0,
                ];
            }

            foreach ($plantCounts as $count) {
                $data = array_merge($data, array_values($count));
            }

            return $data;
        });

        return view('exports.monthly_inventory', ['data' => $data]);
    }

    public function headings(): array
    {
        $uniquePlants = Plant::all();
        $plantHeadings = $uniquePlants->pluck('name_of_plants')->toArray();

        return [
            ['BARANGAY', 'COMMODITY', "FARMER'S", 'Planting Density (ha)', 'Prodn. Vol/Hectare (MT)', '# Hill/Puno', '', '', '', '', 'PLANTED AREA (Ha)', '', '', '', '', 'AREA HARVESTED (HAS)', 'PRODUCTION VOLUME (MT)'],
            ['', '', '', 'Planting Density (ha)', 'Prodn. Vol/Hectare (MT)', 'Newly Planted', 'Vegetative', 'Reproductive', 'Maturity/Harvested', 'TOTAL', 'Newly Planted', 'Vegetative', 'Reproductive', 'Maturity/Harvested', 'TOTAL', '', ''],
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:A3'); // Barangay
        $sheet->mergeCells('B1:B3'); // Commodity
        $sheet->mergeCells('C1:C3'); // Farmers
        $sheet->mergeCells('D1:J1'); // # Hill/Puno header
        $sheet->mergeCells('D2:D3'); // Planting Density (ha)
        $sheet->mergeCells('E2:E3'); // Prodn. Vol/Hectare (MT)
        $sheet->mergeCells('F2:F3'); // Newly Planted
        $sheet->mergeCells('G2:G3'); // Vegetative
        $sheet->mergeCells('H2:H3'); // Reproductive
        $sheet->mergeCells('I2:I3'); // Maturity/Harvested
        $sheet->mergeCells('J2:J3'); // TOTAL
        $sheet->mergeCells('K1:O1'); // PLANTED AREA (Ha)
        $sheet->mergeCells('K2:K3'); // Newly Planted
        $sheet->mergeCells('L2:L3'); // Vegetative
        $sheet->mergeCells('M2:M3'); // Reproductive
        $sheet->mergeCells('N2:N3'); // Maturity/Harvested
        $sheet->mergeCells('O2:O3'); // TOTAL
        $sheet->mergeCells('P1:P3'); // Area Harvested (HAS)
        $sheet->mergeCells('Q1:Q3'); // Production Volume (MT)

        $sheet->getStyle('A1:Q3')->getAlignment()->applyFromArray([
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ]);

        $sheet->getStyle('A1:Q3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White text color
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => 'FF4CAF50'], // Green background for headers
            ],
            'borders' => [ 
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getStyle('A4:Q' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }
}

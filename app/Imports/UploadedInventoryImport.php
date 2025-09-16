<?php

namespace App\Imports;

use App\Models\UploadedInventory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UploadedInventoryImport implements ToModel, WithChunkReading, WithBatchInserts, SkipsEmptyRows
{
    /**
     * Map each row to a model insert. Uses numeric indexes (no heading row).
     *
     * @param array<int, mixed> $row
     */
    public function model(array $row)
    {
        // Skip header-like rows (first cell is a string header)
        if (isset($row[0]) && is_string($row[0])) {
            $firstCell = trim(strtolower($row[0]));
            if ($firstCell === 'barangay' || $firstCell === 'brgy') {
                return null;
            }
        }

        // Skip empty rows
        $nonEmpty = array_filter($row, function ($value) {
            return $value !== null && $value !== '';
        });
        if (empty($nonEmpty)) {
            return null;
        }

        $barangay = isset($row[0]) && $row[0] !== '' ? (string) $row[0] : '';
        $commodity = isset($row[1]) && $row[1] !== '' ? (string) $row[1] : '';
        $farmer = isset($row[2]) && $row[2] !== '' ? (string) $row[2] : '';

        $plantingDensity = isset($row[3]) && is_numeric($row[3]) ? (float) $row[3] : 0.0; // D
        $productionVolHectare = isset($row[4]) && is_numeric($row[4]) ? (float) $row[4] : 0.0; // E
        $newlyPlanted = isset($row[5]) && is_numeric($row[5]) ? (int) $row[5] : 0; // F
        $vegetative = isset($row[6]) && is_numeric($row[6]) ? (int) $row[6] : 0; // G
        $reproductive = isset($row[7]) && is_numeric($row[7]) ? (int) $row[7] : 0; // H
        $maturity = isset($row[8]) && is_numeric($row[8]) ? (int) $row[8] : 0; // I
        $total = isset($row[9]) && is_numeric($row[9]) ? (int) $row[9] : 0; // J

        $newlyPlantedDivided = isset($row[10]) && is_numeric($row[10]) ? (float) $row[10] : 0.0; // K
        $vegetativeDivided = isset($row[11]) && is_numeric($row[11]) ? (float) $row[11] : 0.0; // L
        $reproductiveDivided = isset($row[12]) && is_numeric($row[12]) ? (float) $row[12] : 0.0; // M
        $maturityDivided = isset($row[13]) && is_numeric($row[13]) ? (float) $row[13] : 0.0; // N
        $totalDivided = isset($row[14]) && is_numeric($row[14]) ? (float) $row[14] : 0.0; // O

        $areaHarvested = isset($row[15]) && is_numeric($row[15]) ? (float) $row[15] : 0.0; // P
        $productionVolumeMt = isset($row[16]) && is_numeric($row[16]) ? (float) $row[16] : 0.0; // Q

        // Compute missing per-hectare values
        if ($newlyPlantedDivided == 0.0 && $plantingDensity > 0) {
            $newlyPlantedDivided = $newlyPlanted / $plantingDensity;
        }
        if ($vegetativeDivided == 0.0 && $plantingDensity > 0) {
            $vegetativeDivided = $vegetative / $plantingDensity;
        }
        if ($reproductiveDivided == 0.0 && $plantingDensity > 0) {
            $reproductiveDivided = $reproductive / $plantingDensity;
        }
        if ($maturityDivided == 0.0 && $plantingDensity > 0) {
            $maturityDivided = $maturity / $plantingDensity;
        }

        // Total planted area
        $plantedTotal = $totalDivided > 0.0 ? $totalDivided : ($newlyPlantedDivided + $vegetativeDivided + $reproductiveDivided + $maturityDivided);

        // Derived values if not provided
        if ($areaHarvested == 0.0) {
            $areaHarvested = $maturityDivided;
        }
        if ($productionVolumeMt == 0.0) {
            $productionVolumeMt = ($areaHarvested * $productionVolHectare) / 1000.0;
        }

        return new UploadedInventory([
            'barangay' => $barangay,
            'commodity' => $commodity,
            'farmer' => $farmer,
            'planting_density' => $plantingDensity,
            'production_vol_hectare' => $productionVolHectare,
            'newly_planted' => $newlyPlanted,
            'vegetative' => $vegetative,
            'reproductive' => $reproductive,
            'maturity' => $maturity,
            'total' => $total,
            'planted_area_newly' => $newlyPlantedDivided,
            'planted_area_vegetative' => $vegetativeDivided,
            'planted_area_reproductive' => $reproductiveDivided,
            'planted_area_maturity' => $maturityDivided,
            'planted_total' => $plantedTotal,
            'area_harvested' => $areaHarvested,
            'production_volume_mt' => $productionVolumeMt,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}



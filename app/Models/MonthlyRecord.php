<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_inventory_id',
        'farmer_id',
        'plant_id',
        'affiliation_id',
        'planting_density',
        'production_volume',
        'newly_planted',
        'vegetative',
        'reproductive',
        'maturity_harvested',
        'newly_planted_divided',
        'vegetative_divided',
        'reproductive_divided',
        'maturity_harvested_divided',
        'total_planted_area',
        'total',
        'area_harvested',
        'final_production_volume',
    ];

    public function monthlyInventory()
    {
        return $this->belongsTo(MonthlyInventory::class);
    }
    // MonthlyRecord Model

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class);
    }
    // Scope to filter records based on month and year
    public function scopeFilterByMonthAndYear($query, $month, $year)
    {
        return $query->whereHas('monthlyInventory', function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        });
    }
    public function getLatitude()
    {
        return $this->inventoryValuedCrop ? $this->inventoryValuedCrop->latitude : null;
    }

    public function getLongitude()
    {
        return $this->inventoryValuedCrop ? $this->inventoryValuedCrop->longitude : null;
    }
    
}

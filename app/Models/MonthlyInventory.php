<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class MonthlyInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'plant_id',
        'affiliation_id',
        'planting_density',
        'production_volume',
        'newly_planted',
        'vegetative',
        'reproductive',
        'maturity_harvested',
        // divided
        'newly_planted_divided',   // Newly Planted divided by Planting Density
        'vegetative_divided',      // Vegetative divided by Planting Density
        'reproductive_divided',    // Reproductive divided by Planting Density
        'maturity_harvested_divided', // Maturity/Harvested divided by Planting Density
        'total_planted_area',
        'total',
        'area_harvested',
        'final_production_volume',
    ];


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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedInventory extends Model
{
    use HasFactory;

    protected $table = 'uploaded_inventories';

    protected $fillable = [
        'barangay',
        'commodity',
        'farmer',
        'planting_density',
        'production_vol_hectare',
        'newly_planted',
        'vegetative',
        'reproductive',
        'maturity',
        'total',
        'planted_area_newly',
        'planted_area_vegetative',
        'planted_area_reproductive',
        'planted_area_maturity',
        'planted_total',
        'area_harvested',
        'production_volume_mt',
    ];

    protected $casts = [
        'planting_density' => 'decimal:4',
        'production_vol_hectare' => 'decimal:4',
        'newly_planted' => 'integer',
        'vegetative' => 'integer',
        'reproductive' => 'integer',
        'maturity' => 'integer',
        'total' => 'integer',
        'planted_area_newly' => 'decimal:4',
        'planted_area_vegetative' => 'decimal:4',
        'planted_area_reproductive' => 'decimal:4',
        'planted_area_maturity' => 'decimal:4',
        'planted_total' => 'decimal:4',
        'area_harvested' => 'decimal:4',
        'production_volume_mt' => 'decimal:4',
    ];
}

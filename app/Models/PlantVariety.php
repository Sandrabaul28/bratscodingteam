<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantVariety extends Model
{
    use HasFactory;

    protected $fillable = ['plant_id', 'variety_name'];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}

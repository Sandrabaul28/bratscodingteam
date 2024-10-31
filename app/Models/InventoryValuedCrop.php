<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryValuedCrop extends Model
{
    use HasFactory;

    protected $table = 'inventory_valued_crops'; // adjust this as necessary

    protected $fillable = [
        'farmer_id',
        'plant_id',
        'count',
        'latitude',
        'longitude',
        'added_by',
    ];

    // Define relationships if needed
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'aggregator_user_id'); // Adjust the foreign key as necessary
    }
    public function addedby()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

}

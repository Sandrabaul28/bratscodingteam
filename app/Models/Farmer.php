<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;

    // Specify the table associated with the model (if not the plural form of the model name)
    protected $table = 'farmers'; 

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'extension',
        'affiliation_id',
        'control_number',
        'birthdate',
        'user_id',
        'added_by',
    ];

    protected $hidden = [
        'password', // This will hide the password when retrieving farmer data
    ];

    // Define a relationship with the Affiliation model
    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class, 'affiliation_id'); // Adjust the affiliation model path if needed
    }

    public function inventoryValuedCrops()
    {
        return $this->hasMany(InventoryValuedCrop::class);
    }

    public static function getUniquePlants()
    {
        return InventoryValuedCrop::with('plant')->pluck('plant.plant_id')->unique();
    }

    public function varieties()
    {
        return $this->hasMany(PlantVariety::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'role_id', 'id',  'added_by');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Use 'user_id' as the foreign key
    }
    public function inventories()
    {
        return $this->hasMany(MonthlyInventory::class);
    }
    public function monthlyRecords() 
    {
    return $this->hasMany(MonthlyRecord::class);
    }


}

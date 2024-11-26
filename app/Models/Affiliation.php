<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    use HasFactory;

    protected $fillable = ['name_of_association', 'name_of_barangay'];

    public function users()
    {
        return $this->hasMany(User::class, 'affiliation_id');
    }
    public function farmers()
    {
        return $this->hasMany(Farmer::class);
    }
    public function inventories()
    {
        return $this->hasMany(MonthlyInventory::class);
    }

    public function print()
    {
        $farmers = Farmer::with('affiliation', 'inventoryValuedCrops.plant')->get();
        $uniquePlants = Plant::pluck('name_of_plants'); // Assuming you have a Plant model

        return view('hvcdp.print', compact('farmers', 'uniquePlants'));
    }
    public function monthlyRecords() 
    {
    return $this->hasMany(MonthlyRecord::class);
    }

}

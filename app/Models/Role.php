<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'role_name' 
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
    public function farmers()
    {
        return $this->hasMany(User::class, 'farmer_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

}

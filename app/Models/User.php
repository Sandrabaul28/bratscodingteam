<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'extension',
        'email',
        'password',
        'role_id',
        'affiliation_id', // assuming affiliation is needed too
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Sa User model
    public function farmer()
    {
        return $this->hasOne(Farmer::class, 'user_id', 'id');
    }
        /**
     * Get the affiliation associated with the user.
     */
    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class, 'affiliation_id');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // Make sure this line is correct
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['role_name' => 'Admin']);
        Role::firstOrCreate(['role_name' => 'User']);
        Role::firstOrCreate(['role_name' => 'Aggregator']);
    }
}

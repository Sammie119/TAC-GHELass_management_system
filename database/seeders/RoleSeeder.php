<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'usher']);
        Role::firstOrCreate(['name' => 'member']);
        Role::firstOrCreate(['name' => 'finance']);
        Role::firstOrCreate(['name' => 'membership']);
        Role::firstOrCreate(['name' => 'pastor']);
        Role::firstOrCreate(['name' => 'finance_chairman']);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        //        Role::create(['name' => 'admin']);
        //        Role::create(['name' => 'usher']);
        //        Role::create(['name' => 'member']);

        // Create a default admin user
        $user = User::create([
            'name' => 'Church Admin',
            'email' => 'admin@church.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('admin');

        $this->call(DropdownOptionSeeder::class);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'name'     => 'Church Admin',
            'email'    => 'admin@church.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('admin');
    }
}

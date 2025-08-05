<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $user = User::create([
        //     'name' => 'Super Admin',
        //     'email' => 'superadmin@cctv.com',
        //     'password' => bcrypt('SecurePassword123!'), 
        //     'email_verified_at' => now(),
        // ]);
        $this->call([
            RolePermissionSeeder::class,
        ]);
    }
}

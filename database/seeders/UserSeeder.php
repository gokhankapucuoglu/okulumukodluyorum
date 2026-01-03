<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'super_admin@okulumukodluyorum.com'],
            [
                'name' => 'Super',
                'surname' => 'Admin',
                'password' => Hash::make('GokhaN2635!'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        //$superAdmin->assignRole('super_admin');
    }
}

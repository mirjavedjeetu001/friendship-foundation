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
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@friendshipfoundation.com',
            'password' => Hash::make('password'),
            'phone' => '01700000000',
            'is_active' => true,
            'joined_date' => now(),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // Create Accountant
        $accountant = User::create([
            'name' => 'Accountant',
            'email' => 'accountant@friendshipfoundation.com',
            'password' => Hash::make('password'),
            'phone' => '01700000001',
            'is_active' => true,
            'joined_date' => now(),
            'email_verified_at' => now(),
        ]);
        $accountant->assignRole('accountant');

        // Create Sample Members
        $members = [
            ['name' => 'Rahul Ahmed', 'email' => 'rahul@example.com', 'phone' => '01700000002'],
            ['name' => 'Karim Hassan', 'email' => 'karim@example.com', 'phone' => '01700000003'],
            ['name' => 'Sakib Khan', 'email' => 'sakib@example.com', 'phone' => '01700000004'],
        ];

        foreach ($members as $memberData) {
            $member = User::create([
                'name' => $memberData['name'],
                'email' => $memberData['email'],
                'password' => Hash::make('password'),
                'phone' => $memberData['phone'],
                'is_active' => true,
                'joined_date' => now(),
                'email_verified_at' => now(),
            ]);
            $member->assignRole('member');
        }
    }
}

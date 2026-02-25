<?php

namespace Database\Seeders;

use App\Models\MonthlySetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MonthlySetting::create([
            'monthly_contribution_amount' => 500.00,
            'due_day' => 10,
            'bank_balance' => 0.00,
            'bank_name' => 'Example Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Friendship Foundation',
            'is_active' => true,
        ]);
    }
}

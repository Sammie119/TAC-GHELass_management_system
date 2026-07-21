<?php

namespace Database\Seeders;

use App\Models\DropdownOption;
use Illuminate\Database\Seeder;

class DropdownOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read the config files directly rather than via config() — by the time this
        // seeder runs, AppServiceProvider may have already overridden config('finance.*')
        // from the (still empty, on first run) dropdown_options table.
        $finance = require config_path('finance.php');
        $departments = require config_path('departments.php');

        $rates = ['GHS' => 1, 'USD' => 15.5, 'GBP' => 19.5, 'EUR' => 17.0];

        $this->seedGroup('income_category', $finance['income_categories']);
        $this->seedGroup('expense_category', $finance['expense_categories']);
        $this->seedGroup('payment_method', $finance['payment_methods']);

        $order = 0;
        foreach ($finance['currencies'] as $code => $info) {
            DropdownOption::updateOrCreate(
                ['group' => 'currency', 'key' => $code],
                [
                    'label' => $info['name'],
                    'meta' => [
                        'symbol' => $info['symbol'],
                        'rate' => $rates[$code] ?? 1,
                    ],
                    'sort_order' => $order,
                    'is_active' => true,
                ]
            );
            $order++;
        }

        $order = 0;
        foreach ($departments as $department) {
            DropdownOption::updateOrCreate(
                ['group' => 'department', 'key' => $department],
                [
                    'label' => $department,
                    'sort_order' => $order,
                    'is_active' => true,
                ]
            );
            $order++;
        }
    }

    private function seedGroup(string $group, array $items): void
    {
        $order = 0;
        foreach ($items as $key => $label) {
            DropdownOption::updateOrCreate(
                ['group' => $group, 'key' => $key],
                [
                    'label' => $label,
                    'sort_order' => $order,
                    'is_active' => true,
                ]
            );
            $order++;
        }
    }
}

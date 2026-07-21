<?php

namespace App\Providers;

use App\Models\ChurchSetting;
use App\Models\DropdownOption;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        $this->overrideDropdownConfig();
        $this->overrideChurchConfig();
    }

    private function overrideDropdownConfig(): void
    {
        try {
            if (! Schema::hasTable('dropdown_options')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        config([
            'finance.income_categories' => DropdownOption::group('income_category')->pluck('label', 'key')->toArray(),
            'finance.expense_categories' => DropdownOption::group('expense_category')->pluck('label', 'key')->toArray(),
            'finance.payment_methods' => DropdownOption::group('payment_method')->pluck('label', 'key')->toArray(),
            'finance.currencies' => DropdownOption::group('currency')->get()
                ->mapWithKeys(fn ($option) => [$option->key => [
                    'name' => $option->label,
                    'symbol' => $option->meta['symbol'] ?? '',
                    'rate' => $option->meta['rate'] ?? 1,
                ]])->toArray(),
            'departments' => DropdownOption::group('department')->pluck('label')->toArray(),
        ]);
    }

    private function overrideChurchConfig(): void
    {
        try {
            if (! Schema::hasTable('church_settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $setting = ChurchSetting::first();

        if (! $setting) {
            return;
        }

        if ($setting->name) {
            config(['app.name' => $setting->name]);
        }

        config([
            'church.address' => $setting->address,
            'church.logo_path' => $setting->logo_path,
            'church.logo_url' => $setting->logo_path ? Storage::disk('public')->url($setting->logo_path) : null,
        ]);
    }
}

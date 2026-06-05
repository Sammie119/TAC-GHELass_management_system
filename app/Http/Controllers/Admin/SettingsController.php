<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        $departments        = config('departments');
        $incomeCategories   = config('finance.income_categories');
        $expenseCategories  = config('finance.expense_categories');

        return view('admin.settings.index', compact(
            'departments', 'incomeCategories', 'expenseCategories'
        ));
    }

    public function updateDepartments(Request $request)
    {
        $request->validate([
            'departments'   => 'required|array|min:1',
            'departments.*' => 'required|string|max:100',
        ]);

        $items = array_values(array_filter(array_map('trim', $request->departments)));
        $this->writeConfig('departments', $items, 'array');

        return back()->with('success', 'Departments updated successfully.');
    }

    public function updateIncomeCategories(Request $request)
    {
        $request->validate([
            'keys'   => 'required|array|min:1',
            'keys.*' => 'required|string|max:50',
            'labels' => 'required|array|min:1',
            'labels.*' => 'required|string|max:100',
        ]);

        $categories = [];
        foreach ($request->keys as $i => $key) {
            $key = strtolower(preg_replace('/[^a-z0-9_]/', '_', trim($key)));
            $categories[$key] = trim($request->labels[$i]);
        }

        $this->updateFinanceConfig('income_categories', $categories);
        return back()->with('success', 'Income categories updated.');
    }

    public function updateExpenseCategories(Request $request)
    {
        $request->validate([
            'keys'    => 'required|array|min:1',
            'keys.*'  => 'required|string|max:50',
            'labels'  => 'required|array|min:1',
            'labels.*'=> 'required|string|max:100',
        ]);

        $categories = [];
        foreach ($request->keys as $i => $key) {
            $key = strtolower(preg_replace('/[^a-z0-9_]/', '_', trim($key)));
            $categories[$key] = trim($request->labels[$i]);
        }

        $this->updateFinanceConfig('expense_categories', $categories);
        return back()->with('success', 'Expense categories updated.');
    }

    private function writeConfig(string $filename, array $data, string $type = 'array'): void
    {
        $export = var_export($data, true);
        $content = "<?php\n\nreturn {$export};\n";
        File::put(config_path("{$filename}.php"), $content);
        \Artisan::call('config:clear');
    }

    private function updateFinanceConfig(string $key, array $data): void
    {
        $config = config('finance');
        $config[$key] = $data;

        $export  = var_export($config, true);
        $content = "<?php\n\nreturn {$export};\n";
        File::put(config_path('finance.php'), $content);
        \Artisan::call('config:clear');
    }
}

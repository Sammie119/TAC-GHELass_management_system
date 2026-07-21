<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BudgetTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\ExpenseRecord;
use App\Models\PettyCashTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);

        [$budgetLines, $budgetGrid, $actualGrid, $unassignedActual] = $this->buildGrid($year);

        $summary = $budgetLines->map(function ($line) use ($budgetGrid, $actualGrid) {
            $budgeted = ($budgetGrid[$line->id] ?? collect())->sum();
            $actual = array_sum($actualGrid[$line->id]);

            return [
                'label' => $line->name,
                'budgeted' => $budgeted,
                'actual' => $actual,
                'variance' => $budgeted - $actual,
            ];
        });

        $monthlyTotals = collect(range(1, 12))->map(function ($month) use ($budgetGrid, $actualGrid, $unassignedActual) {
            return [
                'month' => Carbon::create()->month($month)->format('M'),
                'budgeted' => $budgetGrid->sum(fn ($months) => $months[$month] ?? 0),
                'actual' => collect($actualGrid)->sum(fn ($months) => $months[$month]) + $unassignedActual[$month],
            ];
        });

        $unassignedTotal = array_sum($unassignedActual);
        $allBudgetLines = BudgetLine::orderBy('name')->get();

        return view('admin.finance.budgets', compact(
            'year', 'budgetLines', 'allBudgetLines', 'budgetGrid', 'actualGrid', 'summary', 'monthlyTotals', 'unassignedTotal'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'entries.*.budget_line_id' => 'required|exists:budget_lines,id',
            'entries.*.month' => 'required|integer|min:1|max:12',
            'entries.*.amount' => 'nullable|numeric|min:0',
        ]);

        $year = (int) $request->year;

        $filled = collect($request->input('entries', []))
            ->filter(fn ($e) => isset($e['amount']) && (float) $e['amount'] > 0)
            ->values();

        foreach ($filled as $entry) {
            Budget::updateOrCreate(
                [
                    'financial_year' => $year,
                    'budget_line_id' => (int) $entry['budget_line_id'],
                    'month' => (int) $entry['month'],
                ],
                [
                    'amount' => (float) $entry['amount'],
                    'created_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Budgets saved successfully.');
    }

    // ── Budget Lines management ──────────────────────────────
    public function storeBudgetLine(Request $request)
    {
        $validated = $this->validateBudgetLine($request);

        BudgetLine::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Budget line added.');
    }

    public function updateBudgetLine(Request $request, BudgetLine $budgetLine)
    {
        $validated = $this->validateBudgetLine($request);

        $budgetLine->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Budget line updated.');
    }

    public function destroyBudgetLine(BudgetLine $budgetLine)
    {
        $budgetLine->delete();

        return back()->with('success', 'Budget line deleted.');
    }

    private function validateBudgetLine(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);
    }

    // ── Excel template download ──────────────────────────────
    public function downloadTemplate(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);

        [$budgetLines, $budgetGrid] = $this->buildGrid($year);

        $headers = ['ID', 'Budget Line', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $rows = $budgetLines->map(function ($line) use ($budgetGrid) {
            $row = [$line->id, $line->name];
            for ($month = 1; $month <= 12; $month++) {
                $row[] = $budgetGrid[$line->id][$month] ?? '';
            }

            return $row;
        })->toArray();

        return Excel::download(
            new BudgetTemplateExport($headers, $rows),
            "budget-template-{$year}.xlsx"
        );
    }

    // ── Process Excel upload ─────────────────────────────────
    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $year = (int) $request->year;
        $validIds = BudgetLine::pluck('id')->all();

        $rows = Excel::toArray([], $request->file('excel_file'));
        $data = $rows[0] ?? [];
        $count = 0;
        $errors = [];

        foreach (array_slice($data, 1) as $i => $row) {
            $id = (int) ($row[0] ?? 0);

            if (empty($row[0]) && empty($row[1])) {
                continue;
            }

            if (! in_array($id, $validIds, true)) {
                $errors[] = 'Row '.($i + 2).": Unknown budget line ID '{$id}' — skipped";

                continue;
            }

            for ($month = 1; $month <= 12; $month++) {
                $cell = $row[$month + 1] ?? null;

                if ($cell === null || $cell === '' || (float) $cell <= 0) {
                    continue;
                }

                Budget::updateOrCreate(
                    ['financial_year' => $year, 'budget_line_id' => $id, 'month' => $month],
                    ['amount' => (float) $cell, 'created_by' => auth()->id()]
                );
                $count++;
            }
        }

        $msg = "{$count} budget line(s) updated for {$year}.";
        if ($errors) {
            $msg .= ' Errors: '.implode(', ', $errors);
        }

        return back()->with('success', $msg);
    }

    /**
     * @return array{0: Collection, 1: Collection, 2: array, 3: array}
     */
    private function buildGrid(int $year): array
    {
        $budgetLines = BudgetLine::where('is_active', true)->orderBy('name')->get();

        $budgetGrid = Budget::where('financial_year', $year)->get()
            ->groupBy('budget_line_id')
            ->map(fn ($rows) => $rows->keyBy('month')->map(fn ($row) => (float) $row->amount));

        $expenseActuals = ExpenseRecord::where('status', 'approved')
            ->whereYear('expense_date', $year)
            ->selectRaw('budget_line_id, MONTH(expense_date) as month, SUM(amount_ghs) as total')
            ->groupBy('budget_line_id', 'month')
            ->get();

        $pettyCashActuals = PettyCashTransaction::where('type', 'disbursement')
            ->whereYear('transaction_date', $year)
            ->selectRaw('budget_line_id, MONTH(transaction_date) as month, SUM(amount_ghs) as total')
            ->groupBy('budget_line_id', 'month')
            ->get();

        $actualGrid = $budgetLines->mapWithKeys(function ($line) {
            return [$line->id => array_fill(1, 12, 0.0)];
        })->toArray();

        $unassignedActual = array_fill(1, 12, 0.0);

        foreach ($expenseActuals->concat($pettyCashActuals) as $row) {
            $month = (int) $row->month;

            if ($row->budget_line_id === null) {
                $unassignedActual[$month] += (float) $row->total;

                continue;
            }

            if (isset($actualGrid[$row->budget_line_id][$month])) {
                $actualGrid[$row->budget_line_id][$month] += (float) $row->total;
            }
        }

        return [$budgetLines, $budgetGrid, $actualGrid, $unassignedActual];
    }
}

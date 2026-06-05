<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ExpenseRecord;
use App\Models\IncomeRecord;
use App\Models\Member;
use App\Models\OnlinePayment;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        // Summary
        $totalIncome = IncomeRecord::where('status', 'confirmed')
            ->whereBetween('payment_date', [$from, $to])
            ->sum('amount_ghs');

        $totalExpenses = ExpenseRecord::where('status', 'approved')
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount_ghs');

        $netBalance = $totalIncome - $totalExpenses;

        // Income by category
        $incomeByCategory = IncomeRecord::where('status', 'confirmed')
            ->whereBetween('payment_date', [$from, $to])
            ->selectRaw('category, SUM(amount_ghs) as total')
            ->groupBy('category')
            ->get()
            ->map(fn($r) => [
                'category' => ucfirst($r->category),
                'total'    => (float) $r->total,
            ]);

        // Expense by category
        $expenseByCategory = ExpenseRecord::where('status', 'approved')
            ->whereBetween('expense_date', [$from, $to])
            ->selectRaw('category, SUM(amount_ghs) as total')
            ->groupBy('category')->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month'    => $date->format('M Y'),
                'income'   => IncomeRecord::where('status', 'confirmed')
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date',  $date->year)
                    ->sum('amount_ghs'),
                'expenses' => ExpenseRecord::where('status', 'approved')
                    ->whereMonth('expense_date', $date->month)
                    ->whereYear('expense_date',  $date->year)
                    ->sum('amount_ghs'),
            ];
        });

        // Pending online payments
        $pendingPayments = OnlinePayment::where('status', 'pending')->count();

        // Recent transactions
        $recentIncome = IncomeRecord::with(['member', 'recordedBy'])
            ->latest()->take(5)->get();

        $recentExpenses = ExpenseRecord::with('recordedBy')
            ->latest()->take(5)->get();

        return view('admin.finance.index', compact(
            'totalIncome', 'totalExpenses', 'netBalance',
            'incomeByCategory', 'expenseByCategory',
            'monthlyTrend', 'pendingPayments',
            'recentIncome', 'recentExpenses',
            'from', 'to'
        ));
    }

    // ── Income list ─────────────────────────────────────────
    public function income(Request $request)
    {
        $query = IncomeRecord::with(['member', 'recordedBy'])->orderByDesc('id');

        if ($request->filled('from')) {
            $query->whereDate('payment_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        $records = $query->latest('payment_date')->paginate(20)->withQueryString();
        $total   = $query->sum('amount_ghs');
        $members = Member::where('status', 'active')->orderBy('first_name')->get();
        $events  = Event::orderBy('event_date', 'desc')->take(20)->get();

        return view('admin.finance.income', compact('records', 'total', 'members', 'events'));
    }

    // ── Store income ────────────────────────────────────────
    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'member_id'      => 'nullable|exists:members,id',
            'category'       => 'required|string',
            'amount'         => 'required|numeric|min:0.01',
            'currency'       => 'required|string',
            'exchange_rate'  => 'required|numeric|min:0.0001',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|string',
            'reference'      => 'nullable|string|max:100',
            'event_id'       => 'nullable|exists:events,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        $validated['amount_ghs']  = $validated['amount'] * $validated['exchange_rate'];
        $validated['recorded_by'] = auth()->id();
        $validated['status']      = 'confirmed';

        // Match on: member + category + date + currency
        // Update amount and other fields if already exists
        $record = IncomeRecord::updateOrCreate(
            [
                'member_id'    => $validated['member_id'] ?? null,
                'category'     => $validated['category'],
                'payment_date' => $validated['payment_date'],
                'currency'     => $validated['currency'],
            ],
            [
                'amount'         => $validated['amount'],
                'exchange_rate'  => $validated['exchange_rate'],
                'amount_ghs'     => $validated['amount_ghs'],
                'payment_method' => $validated['payment_method'],
                'reference'      => $validated['reference'] ?? null,
                'event_id'       => $validated['event_id'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'recorded_by'    => $validated['recorded_by'],
                'status'         => $validated['status'],
            ]
        );

        $message = $record->wasRecentlyCreated
            ? 'Income record added successfully.'
            : 'Existing income record updated successfully.';

        return back()->with('success', $message);
    }

    // ── Expense list ────────────────────────────────────────
    public function expenses(Request $request)
    {
        $query = ExpenseRecord::with('recordedBy')->orderByDesc('id');

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $records = $query->latest('expense_date')->paginate(20)->withQueryString();
        $total   = $query->sum('amount_ghs');

        return view('admin.finance.expenses', compact('records', 'total'));
    }

    // ── Store expense ────────────────────────────────────────
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'category'       => 'required|string',
            'description'    => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'currency'       => 'required|string',
            'exchange_rate'  => 'required|numeric|min:0.0001',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|string',
            'payee'          => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')
                ->store('finance/attachments', 'public');
        }

        $validated['amount_ghs']  = $validated['amount'] * $validated['exchange_rate'];
        $validated['recorded_by'] = auth()->id();
        $validated['status']      = 'approved';

        // Match on: category + description + date + currency
        $record = ExpenseRecord::updateOrCreate(
            [
                'category'     => $validated['category'],
                'description'  => $validated['description'],
                'expense_date' => $validated['expense_date'],
                'currency'     => $validated['currency'],
            ],
            [
                'amount'         => $validated['amount'],
                'exchange_rate'  => $validated['exchange_rate'],
                'amount_ghs'     => $validated['amount_ghs'],
                'payment_method' => $validated['payment_method'],
                'payee'          => $validated['payee'] ?? null,
                'receipt_number' => $validated['receipt_number'] ?? null,
                'attachment'     => $validated['attachment'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'recorded_by'    => $validated['recorded_by'],
                'status'         => $validated['status'],
            ]
        );

        $message = $record->wasRecentlyCreated
            ? 'Expense record added successfully.'
            : 'Existing expense record updated with new values.';

        return back()->with('success', $message);
    }

    // ── Soft delete income ───────────────────────────────────
    public function destroyIncome(IncomeRecord $income)
    {
        $income->delete();
        return back()->with('success', 'Income record archived successfully.');
    }

// ── Soft delete expense ──────────────────────────────────
    public function destroyExpense(ExpenseRecord $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense record archived successfully.');
    }

// ── Archived income list ─────────────────────────────────
    public function archivedIncome(Request $request)
    {
        $query = IncomeRecord::onlyTrashed()->with(['member', 'recordedBy']);

        if ($request->filled('from')) {
            $query->whereDate('payment_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $records = $query->latest('deleted_at')->paginate(20)->withQueryString();

        return view('admin.finance.archived-income', compact('records'));
    }

// ── Archived expense list ────────────────────────────────
    public function archivedExpenses(Request $request)
    {
        $query = ExpenseRecord::onlyTrashed()->with('recordedBy');

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $records = $query->latest('deleted_at')->paginate(20)->withQueryString();

        return view('admin.finance.archived-expenses', compact('records'));
    }

// ── Restore income ───────────────────────────────────────
    public function restoreIncome($id)
    {
        $record = IncomeRecord::onlyTrashed()->findOrFail($id);
        $record->restore();
        return back()->with('success', 'Income record restored successfully.');
    }

// ── Restore expense ──────────────────────────────────────
    public function restoreExpense($id)
    {
        $record = ExpenseRecord::onlyTrashed()->findOrFail($id);
        $record->restore();
        return back()->with('success', 'Expense record restored successfully.');
    }

// ── Permanently delete income ────────────────────────────
    public function forceDeleteIncome($id)
    {
        $record = IncomeRecord::onlyTrashed()->findOrFail($id);
        $record->forceDelete();
        return back()->with('success', 'Income record permanently deleted.');
    }

// ── Permanently delete expense ───────────────────────────
    public function forceDeleteExpense($id)
    {
        $record = ExpenseRecord::onlyTrashed()->findOrFail($id);
        $record->forceDelete();
        return back()->with('success', 'Expense record permanently deleted.');
    }

    // ── Online payments ──────────────────────────────────────
    public function onlinePayments(Request $request)
    {
        $payments = OnlinePayment::with(['member', 'confirmedBy'])
            ->latest()->paginate(20);

        return view('admin.finance.online-payments', compact('payments'));
    }

    // ── Confirm online payment ───────────────────────────────
    public function confirmPayment(Request $request, OnlinePayment $payment)
    {
        $response = PaymentService::verifyTransaction($request->vm_reference);

        if ($response['status'] && $response['data']['status'] === 'success') {

            $payment->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);

            // Create income record
            IncomeRecord::create([
                'member_id' => $payment->member_id,
                'category' => $payment->category,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'amount_ghs' => $payment->amount,
                'exchange_rate' => 1,
                'payment_date' => today(),
                'payment_method' => 'online',
                'reference' => $request->vm_reference,
                'notes' => 'Online payment confirmed',
                'status' => 'confirmed',
                'recorded_by' => auth()->id(),
            ]);

            return back()->with('success', 'Payment confirmed and income record created.');
        } else {
            $response['error'] = $response['error'] ?? 'Transaction reference not found.';
            return back()->with('error', $response['error']);
        }
    }

    // ── Member tithe history ─────────────────────────────────
    public function memberTithes(Request $request)
    {
        $members = Member::where('status', 'active')
            ->withSum(['incomeRecords as total_tithe' => function ($q) {
                $q->where('category', 'tithe')->where('status', 'confirmed');
            }], 'amount_ghs')
            ->orderByDesc('total_tithe')
            ->paginate(20);

        return view('admin.finance.member-tithes', compact('members'));
    }

    // ── Reports ──────────────────────────────────────────────
    public function report(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $income = IncomeRecord::with(['member', 'recordedBy'])
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$from, $to])
            ->latest('payment_date')->get();

        $expenses = ExpenseRecord::with('recordedBy')
            ->where('status', 'approved')
            ->whereBetween('expense_date', [$from, $to])
            ->latest('expense_date')->get();

        $totalIncome   = $income->sum('amount_ghs');
        $totalExpenses = $expenses->sum('amount_ghs');
        $netBalance    = $totalIncome - $totalExpenses;

        // ── Always show ALL configured categories, even zero ──
        $allIncomeCategories = config('finance.income_categories');
        $incomeByCategory = collect($allIncomeCategories)
            ->map(function ($label, $key) use ($income) {
                $group = $income->where('category', $key);
                return [
                    'label' => $label,
                    'total' => $group->sum('amount_ghs'),
                    'count' => $group->count(),
                ];
            });

        $allExpenseCategories = config('finance.expense_categories');
        $expenseByCategory = collect($allExpenseCategories)
            ->map(function ($label, $key) use ($expenses) {
                $group = $expenses->where('category', $key);
                return [
                    'label' => $label,
                    'total' => $group->sum('amount_ghs'),
                    'count' => $group->count(),
                ];
            });

        // ── Per member tithe summary ───────────────────────────
        $memberTithes = $income->where('category', 'tithe')
            ->groupBy('member_id')
            ->map(function ($group) {
                $member = $group->first()->member;
                return [
                    'name'  => $member?->full_name ?? 'Anonymous',
                    'id'    => $member?->member_id_card ?? '—',
                    'total' => $group->sum('amount_ghs'),
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        return view('admin.finance.report', compact(
            'income', 'expenses', 'totalIncome', 'totalExpenses',
            'netBalance', 'incomeByCategory', 'expenseByCategory',
            'memberTithes', 'from', 'to'
        ));
    }

    // ── Export report PDF ────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $income = IncomeRecord::with('member')
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$from, $to])
            ->latest('payment_date')->get();

        $expenses = ExpenseRecord::with('recordedBy')
            ->where('status', 'approved')
            ->whereBetween('expense_date', [$from, $to])
            ->latest('expense_date')->get();

        $totalIncome   = $income->sum('amount_ghs');
        $totalExpenses = $expenses->sum('amount_ghs');
        $netBalance    = $totalIncome - $totalExpenses;

        // Build category breakdowns — all categories, even zeros
        $incomeByCategory = collect(config('finance.income_categories'))
            ->map(function ($label, $key) use ($income) {
                $group = $income->where('category', $key);
                return [
                    'label' => $label,
                    'total' => $group->sum('amount_ghs'),
                    'count' => $group->count(),
                ];
            });

        $expenseByCategory = collect(config('finance.expense_categories'))
            ->map(function ($label, $key) use ($expenses) {
                $group = $expenses->where('category', $key);
                return [
                    'label' => $label,
                    'total' => $group->sum('amount_ghs'),
                    'count' => $group->count(),
                ];
            });

        // Member tithes breakdown
        $memberTithes = $income->where('category', 'tithe')
            ->groupBy('member_id')
            ->map(function ($group) {
                $member = $group->first()->member;
                return [
                    'name'  => $member?->full_name ?? 'Anonymous',
                    'id'    => $member?->member_id_card ?? '—',
                    'total' => $group->sum('amount_ghs'),
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $pdf = Pdf::loadView('admin.finance.pdf.report', compact(
            'income', 'expenses',
            'totalIncome', 'totalExpenses', 'netBalance',
            'incomeByCategory', 'expenseByCategory',
            'memberTithes', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("finance-report-{$from}-to-{$to}.pdf");
    }

    // ── Export Excel ─────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        return Excel::download(
            new \App\Exports\FinanceExport($from, $to),
            "finance-{$from}-to-{$to}.xlsx"
        );
    }

    // ── Bulk income entry page ───────────────────────────────
    public function bulkIncome()
    {
        $members = Member::where('status', 'active')
            ->orderBy('first_name')->get();
        $events  = Event::orderBy('event_date', 'desc')->take(20)->get();

        return view('admin.finance.bulk-income', compact('members', 'events'));
    }

// ── Save bulk income entries ─────────────────────────────
    public function storeBulkIncome(Request $request)
    {
        $request->validate([
            'entries'                  => 'required|array|min:1',
            'entries.*.member_id'      => 'nullable|exists:members,id',
            'entries.*.amount'         => 'required|numeric|min:0.01',
            'entries.*.category'       => 'required|string',
            'entries.*.currency'       => 'required|string',
            'entries.*.exchange_rate'  => 'required|numeric|min:0.0001',
            'entries.*.payment_method' => 'required|string',
            'entries.*.payment_date'   => 'required|date',
        ]);

        $count = 0;
        foreach ($request->entries as $entry) {
            if (empty($entry['amount']) || $entry['amount'] <= 0) continue;

            IncomeRecord::firstorCreate([
                'member_id'      => $entry['member_id'] ?? null,
                'category'       => $entry['category'],
                'amount'         => $entry['amount'],
                'currency'       => $entry['currency'],
                'amount_ghs'     => $entry['amount'] * $entry['exchange_rate'],
                'exchange_rate'  => $entry['exchange_rate'],
                'payment_date'   => $entry['payment_date'],
                'payment_method' => $entry['payment_method'],
                'reference'      => $entry['reference'] ?? null,
                'notes'          => $entry['notes'] ?? null,
                'status'         => 'confirmed',
                'recorded_by'    => auth()->id(),
            ]);
            $count++;
        }

        return back()->with('success', "{$count} income records saved successfully.");
    }

// ── Excel template download ──────────────────────────────
    public function downloadTemplate()
    {
        $headers = ['Member ID Card', 'Category', 'Amount', 'Currency', 'Payment Method', 'Payment Date', 'Reference', 'Notes'];
        $example = [
            ['CHR-00001', 'tithe', '100.00', 'GHS', 'cash', date('Y-m-d'), 'REF001', 'January tithe'],
            ['CHR-00002', 'offering', '50.00', 'GHS', 'momo', date('Y-m-d'), '', ''],
            ['CHR-00003', 'tithe', '200.00', 'GHS', 'cash', date('Y-m-d'), '', ''],
        ];

        return Excel::download(
            new \App\Exports\IncomeTemplateExport($headers, $example),
            'income-bulk-template.xlsx'
        );
    }

// ── Process Excel upload ─────────────────────────────────
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $rows    = Excel::toArray([], $request->file('excel_file'));
        $data    = $rows[0] ?? [];
        $count   = 0;
        $errors  = [];

        // Skip header row
        foreach (array_slice($data, 1) as $i => $row) {
            if (empty($row[0]) && empty($row[2])) continue;

            $member = Member::where('member_id_card', trim($row[0] ?? ''))->first();
            $amount = (float) ($row[2] ?? 0);

            if ($amount <= 0) {
                $errors[] = "Row " . ($i + 2) . ": Invalid amount";
                continue;
            }

            $currency     = strtoupper(trim($row[3] ?? 'GHS'));
            $exchangeRate = match($currency) {
                'USD' => 15.5, 'GBP' => 19.5, 'EUR' => 17.0, default => 1,
            };

            IncomeRecord::create([
                'member_id'      => $member?->id,
                'category'       => strtolower(trim($row[1] ?? 'tithe')),
                'amount'         => $amount,
                'currency'       => $currency,
                'amount_ghs'     => $amount * $exchangeRate,
                'exchange_rate'  => $exchangeRate,
                'payment_date'   => !empty($row[5]) ? date('Y-m-d', strtotime($row[5])) : today()->toDateString(),
                'payment_method' => strtolower(str_replace(' ', '_', trim($row[4] ?? 'cash'))),
                'reference'      => $row[6] ?? null,
                'notes'          => $row[7] ?? null,
                'status'         => 'confirmed',
                'recorded_by'    => auth()->id(),
            ]);
            $count++;
        }

        $msg = "{$count} records imported successfully.";
        if ($errors) $msg .= ' Errors: ' . implode(', ', $errors);

        return back()->with('success', $msg);
    }
}

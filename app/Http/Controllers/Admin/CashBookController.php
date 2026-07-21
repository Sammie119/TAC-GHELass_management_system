<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CashBookOpeningBalance;
use App\Models\ExpenseRecord;
use App\Models\IncomeRecord;
use Illuminate\Http\Request;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('name')->get();

        $account = $this->resolveAccount($request->account, $bankAccounts);

        $tabs = collect(['cash' => 'Cash'])
            ->merge($bankAccounts->mapWithKeys(fn ($a) => [(string) $a->id => $a->bank_name.' — '.$a->name]))
            ->merge(['other' => 'Other / Unlinked']);

        $income = IncomeRecord::with('member')
            ->where('status', 'confirmed')
            ->whereYear('payment_date', $year)
            ->tap(fn ($q) => $this->applyAccountFilter($q, $account))
            ->get()
            ->groupBy(fn ($r) => $r->payment_date->format('Y-m-d').'|'.$r->category)
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'date' => $first->payment_date,
                    'particulars' => config('finance.income_categories')[$first->category] ?? ucfirst(str_replace('_', ' ', $first->category)),
                    'reference' => $group->count() > 1 ? $group->count().' entries' : $first->reference,
                    'type' => 'receipt',
                    'amount_ghs' => (float) $group->sum('amount_ghs'),
                ];
            })->values();

        $expenses = ExpenseRecord::where('status', 'approved')
            ->whereYear('expense_date', $year)
            ->tap(fn ($q) => $this->applyAccountFilter($q, $account))
            ->get()
            ->groupBy(fn ($r) => $r->expense_date->format('Y-m-d').'|'.$r->category)
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'date' => $first->expense_date,
                    'particulars' => config('finance.expense_categories')[$first->category] ?? ucfirst(str_replace('_', ' ', $first->category)),
                    'reference' => $group->count() > 1 ? $group->count().' entries' : $first->receipt_number,
                    'type' => 'payment',
                    'amount_ghs' => (float) $group->sum('amount_ghs'),
                ];
            })->values();

        $transactions = $income->concat($expenses)->sortBy('date')->values();

        $openingBalanceKey = $this->openingBalanceKey($account);
        $openingBalance = (float) (CashBookOpeningBalance::where('financial_year', $year)
            ->where($openingBalanceKey)
            ->value('amount') ?? 0);

        $runningBalance = $openingBalance;
        $totalReceipts = 0;
        $totalPayments = 0;

        $ledger = $transactions->map(function ($t) use (&$runningBalance, &$totalReceipts, &$totalPayments) {
            if ($t['type'] === 'receipt') {
                $runningBalance += $t['amount_ghs'];
                $totalReceipts += $t['amount_ghs'];
            } else {
                $runningBalance -= $t['amount_ghs'];
                $totalPayments += $t['amount_ghs'];
            }
            $t['balance'] = $runningBalance;

            return $t;
        });

        $closingBalance = $runningBalance;

        return view('admin.finance.cash-book', compact(
            'year', 'account', 'tabs', 'ledger',
            'openingBalance', 'totalReceipts', 'totalPayments', 'closingBalance'
        ));
    }

    public function updateOpeningBalance(Request $request)
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('name')->get();

        $validated = $request->validate([
            'financial_year' => 'required|integer|min:2000|max:2100',
            'account' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $account = $this->resolveAccount($validated['account'], $bankAccounts);
        $key = $this->openingBalanceKey($account);

        CashBookOpeningBalance::updateOrCreate(
            array_merge(['financial_year' => $validated['financial_year']], $key),
            [
                'amount' => $validated['amount'],
                'created_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Opening balance updated.');
    }

    /**
     * Normalize the requested account into 'cash', 'other', or a bank account id string.
     */
    private function resolveAccount(?string $account, $bankAccounts): string
    {
        if ($account === 'cash' || $account === 'other') {
            return $account;
        }

        if ($account && $bankAccounts->contains('id', (int) $account)) {
            return (string) (int) $account;
        }

        return 'cash';
    }

    private function applyAccountFilter($query, string $account): void
    {
        if ($account === 'cash') {
            $query->where('payment_method', 'cash');
        } elseif ($account === 'other') {
            $query->where('payment_method', '!=', 'cash')->whereNull('bank_account_id');
        } else {
            $query->where('bank_account_id', (int) $account);
        }
    }

    private function openingBalanceKey(string $account): array
    {
        if ($account === 'cash') {
            return ['payment_method' => 'cash', 'bank_account_id' => null];
        }

        if ($account === 'other') {
            return ['payment_method' => 'other', 'bank_account_id' => null];
        }

        return ['payment_method' => null, 'bank_account_id' => (int) $account];
    }
}

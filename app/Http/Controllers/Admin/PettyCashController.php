<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BudgetLine;
use App\Models\ExpenseRecord;
use App\Models\PettyCashTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class PettyCashController extends Controller
{
    private function balance(): float
    {
        $replenished = PettyCashTransaction::where('type', 'replenishment')->sum('amount_ghs');
        $disbursed = PettyCashTransaction::where('type', 'disbursement')->sum('amount_ghs');

        return (float) $replenished - (float) $disbursed;
    }

    public function index(Request $request)
    {
        $query = PettyCashTransaction::with(['recordedBy', 'custodian'])->orderByDesc('id');

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('transaction_date')->paginate(20)->withQueryString();

        $balance = $this->balance();
        $totalReplenished = PettyCashTransaction::where('type', 'replenishment')->sum('amount_ghs');
        $totalDisbursed = PettyCashTransaction::where('type', 'disbursement')->sum('amount_ghs');
        $monthDisbursed = PettyCashTransaction::where('type', 'disbursement')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount_ghs');

        $users = User::orderBy('name')->get();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('name')->get();
        $budgetLines = BudgetLine::where('is_active', true)->orderBy('name')->get();

        return view('admin.finance.petty-cash', compact(
            'transactions', 'balance', 'totalReplenished', 'totalDisbursed', 'monthDisbursed', 'users', 'bankAccounts', 'budgetLines'
        ));
    }

    public function replenish(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'transaction_date' => 'required|date',
            'payment_method' => 'required|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'custodian_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $amountGhs = $validated['amount'] * $validated['exchange_rate'];

        $expense = ExpenseRecord::create([
            'category' => 'petty_cash_float',
            'description' => 'Petty cash float replenishment',
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'amount_ghs' => $amountGhs,
            'exchange_rate' => $validated['exchange_rate'],
            'expense_date' => $validated['transaction_date'],
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'approved',
            'recorded_by' => auth()->id(),
        ]);

        PettyCashTransaction::create([
            'type' => 'replenishment',
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'amount_ghs' => $amountGhs,
            'exchange_rate' => $validated['exchange_rate'],
            'description' => 'Petty cash float replenishment',
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'custodian_id' => $validated['custodian_id'] ?? null,
            'transaction_date' => $validated['transaction_date'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
            'expense_record_id' => $expense->id,
        ]);

        return back()->with('success', 'Float replenished successfully.');
    }

    public function disburse(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'budget_line_id' => 'nullable|exists:budget_lines,id',
            'payee' => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['amount'] > $this->balance()) {
            return back()->with('error', 'Disbursement exceeds the current petty cash balance.');
        }

        PettyCashTransaction::create([
            'type' => 'disbursement',
            'amount' => $validated['amount'],
            'currency' => 'GHS',
            'amount_ghs' => $validated['amount'],
            'exchange_rate' => 1,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'budget_line_id' => $validated['budget_line_id'] ?? null,
            'payee' => $validated['payee'] ?? null,
            'receipt_number' => $validated['receipt_number'] ?? null,
            'transaction_date' => $validated['transaction_date'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Disbursement recorded successfully.');
    }

    public function destroy(PettyCashTransaction $transaction)
    {
        $transaction->delete();

        return back()->with('success', 'Transaction voided.');
    }

    public function archived(Request $request)
    {
        $query = PettyCashTransaction::onlyTrashed()->with(['recordedBy', 'custodian']);

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }

        $transactions = $query->latest('deleted_at')->paginate(20)->withQueryString();

        return view('admin.finance.archived-petty-cash', compact('transactions'));
    }

    public function restore($id)
    {
        $transaction = PettyCashTransaction::onlyTrashed()->findOrFail($id);
        $transaction->restore();

        return back()->with('success', 'Transaction restored.');
    }
}

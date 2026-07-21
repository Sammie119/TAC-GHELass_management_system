<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = BankAccount::query()->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('bank_name', 'like', "%{$search}%")
                ->orWhere('account_number', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $accounts = $query->paginate(20)->withQueryString();

        return view('admin.finance.bank-accounts', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateAccount($request);

        BankAccount::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Bank account added.');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $this->validateAccount($request);

        $bankAccount->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Bank account updated.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return back()->with('success', 'Bank account deleted.');
    }

    private function validateAccount(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'bank_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:100',
            'account_type' => 'required|in:savings,current',
            'currency' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);
    }
}

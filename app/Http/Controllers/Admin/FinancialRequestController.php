<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BudgetLine;
use App\Models\ExpenseRecord;
use App\Models\FinancialRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinancialRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialRequest::with(['requestedBy', 'pastorApprovedBy', 'superAdminApprovedBy'])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $query->whereDate('request_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('request_date', '<=', $request->to);
        }

        $requests = $query->latest('request_date')->paginate(20)->withQueryString();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('name')->get();
        $budgetLines = BudgetLine::where('is_active', true)->orderBy('name')->get();

        return view('admin.finance.financial-requests.index', compact('requests', 'bankAccounts', 'budgetLines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'payee' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'payment_method' => 'required|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'budget_line_id' => 'nullable|exists:budget_lines,id',
            'request_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        FinancialRequest::create([
            'requested_by' => auth()->id(),
            'category' => $validated['category'],
            'description' => $validated['description'],
            'payee' => $validated['payee'] ?? null,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'amount_ghs' => $validated['amount'] * $validated['exchange_rate'],
            'exchange_rate' => $validated['exchange_rate'],
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'budget_line_id' => $validated['budget_line_id'] ?? null,
            'request_date' => $validated['request_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Financial request submitted for approval.');
    }

    public function show(FinancialRequest $financialRequest)
    {
        $financialRequest->load([
            'requestedBy', 'pastorApprovedBy', 'superAdminApprovedBy', 'rejectedBy', 'pvGeneratedBy', 'expenseRecord',
        ]);

        return view('admin.finance.financial-requests.show', compact('financialRequest'));
    }

    public function approvePastor(FinancialRequest $financialRequest)
    {
        abort_unless(auth()->user()->hasRole('pastor'), 403);

        if ($financialRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been finalized.');
        }

        $financialRequest->update([
            'pastor_approved_at' => now(),
            'pastor_approved_by' => auth()->id(),
        ]);

        $this->finalizeIfFullyApproved($financialRequest);

        return back()->with('success', 'Approved as Pastor.');
    }

    public function approveSuperAdmin(FinancialRequest $financialRequest)
    {
        abort_unless(auth()->user()->isFinanceChairman(), 403);

        if ($financialRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been finalized.');
        }

        $financialRequest->update([
            'super_admin_approved_at' => now(),
            'super_admin_approved_by' => auth()->id(),
        ]);

        $this->finalizeIfFullyApproved($financialRequest);

        return back()->with('success', 'Approved as Finance Chairman.');
    }

    public function reject(Request $request, FinancialRequest $financialRequest)
    {
        abort_unless(auth()->user()->hasRole('pastor') || auth()->user()->isFinanceChairman(), 403);

        if ($financialRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been finalized.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $financialRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Request rejected.');
    }

    private function finalizeIfFullyApproved(FinancialRequest $financialRequest): void
    {
        if (! $financialRequest->pastor_approved_at || ! $financialRequest->super_admin_approved_at) {
            return;
        }

        $expense = ExpenseRecord::create([
            'category' => $financialRequest->category,
            'description' => $financialRequest->description,
            'amount' => $financialRequest->amount,
            'currency' => $financialRequest->currency,
            'amount_ghs' => $financialRequest->amount_ghs,
            'exchange_rate' => $financialRequest->exchange_rate,
            'expense_date' => $financialRequest->request_date,
            'payment_method' => $financialRequest->payment_method,
            'bank_account_id' => $financialRequest->bank_account_id,
            'budget_line_id' => $financialRequest->budget_line_id,
            'payee' => $financialRequest->payee,
            'notes' => "Financial request #{$financialRequest->id}: {$financialRequest->description}",
            'status' => 'approved',
            'recorded_by' => auth()->id(),
        ]);

        $financialRequest->update([
            'status' => 'approved',
            'expense_record_id' => $expense->id,
        ]);
    }

    public function generatePv(FinancialRequest $financialRequest)
    {
        if ($financialRequest->status !== 'approved') {
            return back()->with('error', 'The request must be fully approved before a PV can be generated.');
        }

        if (! $financialRequest->pv_number) {
            $financialRequest->update([
                'pv_number' => 'PV-'.str_pad($financialRequest->id, 5, '0', STR_PAD_LEFT),
                'pv_generated_at' => now(),
                'pv_generated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.financial-requests.pv.download', $financialRequest);
    }

    public function downloadPv(FinancialRequest $financialRequest)
    {
        if (! $financialRequest->pv_number) {
            return back()->with('error', 'Generate the PV first.');
        }

        $financialRequest->load(['requestedBy', 'pastorApprovedBy', 'superAdminApprovedBy']);

        $pdf = Pdf::loadView('admin.finance.pdf.payment-voucher', [
            'financialRequest' => $financialRequest,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("{$financialRequest->pv_number}.pdf");
    }
}

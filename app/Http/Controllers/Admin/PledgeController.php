<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomeRecord;
use App\Models\Member;
use App\Models\Pledge;
use App\Models\PledgePayment;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    public function index(Request $request)
    {
        $query = Pledge::with(['member', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('member', fn($q) =>
            $q->where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name',  'like', "%{$request->search}%")
            );
        }

        $pledges = $query->latest('pledge_date')->paginate(20)->withQueryString();

        // Auto-update overdue status
        Pledge::where('status', 'active')
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->update(['status' => 'overdue']);

        $stats = [
            'total'        => Pledge::count(),
            'active'       => Pledge::where('status', 'active')->count(),
            'completed'    => Pledge::where('status', 'completed')->count(),
            'overdue'      => Pledge::where('status', 'overdue')->count(),
            'total_pledged'=> Pledge::sum('pledged_amount'),
            'total_paid'   => Pledge::sum('paid_amount'),
        ];

        $members = Member::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.pledges.index', compact('pledges', 'stats', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'category'       => 'required|string',
            'description'    => 'nullable|string|max:255',
            'pledged_amount' => 'required|numeric|min:1',
            'currency'       => 'required|string',
            'pledge_date'    => 'required|date',
            'due_date'       => 'nullable|date|after:pledge_date',
            'notes'          => 'nullable|string',
        ]);

        $validated['status']      = 'active';
        $validated['paid_amount'] = 0;
        $validated['recorded_by'] = auth()->id();

        Pledge::create($validated);

        return back()->with('success', 'Pledge recorded successfully.');
    }

    public function show(Pledge $pledge)
    {
        $pledge->load(['member', 'payments.recordedBy', 'recordedBy']);
        return view('admin.pledges.show', compact('pledge'));
    }

    public function addPayment(Request $request, Pledge $pledge)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . $pledge->remaining,
            'payment_date'   => 'required|date',
            'payment_method' => 'required|string',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        // Record payment
        PledgePayment::create([
            'pledge_id'      => $pledge->id,
            'amount'         => $request->amount,
            'payment_date'   => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference'      => $request->reference,
            'notes'          => $request->notes,
            'recorded_by'    => auth()->id(),
        ]);

        // Update pledge paid amount
        $newPaid = $pledge->paid_amount + $request->amount;
        $status  = $newPaid >= $pledge->pledged_amount ? 'completed' : $pledge->status;

        $pledge->update([
            'paid_amount' => $newPaid,
            'status'      => $status,
        ]);

        // Also create an income record
        IncomeRecord::create([
            'member_id'      => $pledge->member_id,
            'category'       => 'pledge',
            'amount'         => $request->amount,
            'currency'       => $pledge->currency,
            'amount_ghs'     => $request->amount,
            'exchange_rate'  => 1,
            'payment_date'   => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference'      => $request->reference,
            'notes'          => "Pledge payment — {$pledge->description}",
            'status'         => 'confirmed',
            'recorded_by'    => auth()->id(),
        ]);

        $msg = $status === 'completed'
            ? '🎉 Pledge fully paid! Income record created.'
            : 'Payment recorded. Remaining: ' . $pledge->currency . ' ' . number_format($pledge->remaining - $request->amount, 2);

        return back()->with('success', $msg);
    }

    public function cancel(Pledge $pledge)
    {
        $pledge->update(['status' => 'cancelled']);
        return back()->with('success', 'Pledge cancelled.');
    }

    public function destroy(Pledge $pledge)
    {
        $pledge->delete();
        return redirect()->route('admin.pledges.index')
            ->with('success', 'Pledge deleted.');
    }
}

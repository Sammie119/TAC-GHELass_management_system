@extends('layouts.admin')
@section('page-title', 'Finance Dashboard')
@section('content')

    {{-- Date filter --}}
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;">
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">From</label>
            <input type="date" name="from" value="{{ $from }}"
                   style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">To</label>
            <input type="date" name="to" value="{{ $to }}"
                   style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
        </div>
        <button type="submit"
                style="background:#2563eb;color:white;padding:9px 18px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
            Apply
        </button>
        <a href="{{ route('admin.finance.report') }}?from={{ $from }}&to={{ $to }}"
           style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:9px 18px;border-radius:8px;font-size:14px;text-decoration:none;">
            Full Report →
        </a>
    </form>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:1.5rem;">

        <div style="background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:14px;padding:20px;color:white;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <p style="font-size:13px;opacity:0.9;font-weight:500;">Total Income</p>
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
            <p style="font-size:28px;font-weight:800;margin-bottom:4px;">GH₵ {{ number_format($totalIncome, 2) }}</p>
            <p style="font-size:12px;opacity:0.75;">{{ \Carbon\Carbon::parse($from)->format('d M') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>

        <div style="background:linear-gradient(135deg,#dc2626,#ef4444);border-radius:14px;padding:20px;color:white;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <p style="font-size:13px;opacity:0.9;font-weight:500;">Total Expenses</p>
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
            <p style="font-size:28px;font-weight:800;margin-bottom:4px;">GH₵ {{ number_format($totalExpenses, 2) }}</p>
            <p style="font-size:12px;opacity:0.75;">{{ \Carbon\Carbon::parse($from)->format('d M') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>

        <div style="background:linear-gradient(135deg,{{ $netBalance >= 0 ? '#1d4ed8,#3b82f6' : '#7c3aed,#8b5cf6' }});border-radius:14px;padding:20px;color:white;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <p style="font-size:13px;opacity:0.9;font-weight:500;">Net Balance</p>
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p style="font-size:28px;font-weight:800;margin-bottom:4px;">GH₵ {{ number_format(abs($netBalance), 2) }}</p>
            <p style="font-size:12px;opacity:0.75;">
                {{ $netBalance >= 0 ? '▲ Surplus' : '▼ Deficit' }}
            </p>
        </div>

    </div>

    {{-- Quick action cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:1.5rem;">

        <a href="{{ route('admin.finance.income') }}"
           style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px 12px;text-decoration:none;text-align:center;display:block;transition:box-shadow 0.15s;"
           onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
           onmouseleave="this.style.boxShadow='none'">
            <div style="font-size:26px;margin-bottom:8px;">💰</div>
            <p style="font-size:13px;font-weight:600;color:#111827;margin-bottom:2px;">Income</p>
            <p style="font-size:11px;color:#9ca3af;">Record & view</p>
        </a>

        <a href="{{ route('admin.finance.expenses') }}"
           style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px 12px;text-decoration:none;text-align:center;display:block;transition:box-shadow 0.15s;"
           onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
           onmouseleave="this.style.boxShadow='none'">
            <div style="font-size:26px;margin-bottom:8px;">📤</div>
            <p style="font-size:13px;font-weight:600;color:#111827;margin-bottom:2px;">Expenses</p>
            <p style="font-size:11px;color:#9ca3af;">Record & view</p>
        </a>

        <a href="{{ route('admin.finance.member-tithes') }}"
           style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px 12px;text-decoration:none;text-align:center;display:block;transition:box-shadow 0.15s;"
           onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
           onmouseleave="this.style.boxShadow='none'">
            <div style="font-size:26px;margin-bottom:8px;">🙏</div>
            <p style="font-size:13px;font-weight:600;color:#111827;margin-bottom:2px;">Member Tithes</p>
            <p style="font-size:11px;color:#9ca3af;">Per member history</p>
        </a>

        <a href="{{ route('admin.finance.online-payments') }}"
           style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px 12px;text-decoration:none;text-align:center;display:block;position:relative;transition:box-shadow 0.15s;"
           onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
           onmouseleave="this.style.boxShadow='none'">
            <div style="font-size:26px;margin-bottom:8px;">📱</div>
            <p style="font-size:13px;font-weight:600;color:#111827;margin-bottom:2px;">Online Payments</p>
            <p style="font-size:11px;color:#9ca3af;">{{ $pendingPayments }} pending</p>
            @if($pendingPayments > 0)
                <span style="position:absolute;top:8px;right:8px;background:#dc2626;color:white;border-radius:50%;width:20px;height:20px;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;">
            {{ $pendingPayments }}
        </span>
            @endif
        </a>

    </div>

    {{-- Charts + breakdown --}}
    <div style="display:grid;gap:20px;margin-bottom:1.5rem;">
        <style>@media(min-width:1024px){.fin-charts{grid-template-columns:2fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="fin-charts">

            {{-- Monthly trend --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Income vs Expenses (Last 6 months)</h3>
                <div style="position:relative;height:220px;">
                    <canvas id="finTrendChart"></canvas>
                </div>
            </div>

            {{-- Income by category --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Income breakdown</h3>
                <div style="position:relative;height:220px;">
                    <canvas id="incomeBreakdownChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Recent transactions --}}
    <div style="display:grid;gap:20px;">
        <style>@media(min-width:1024px){.recent-grid{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="recent-grid">

            {{-- Recent income --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Recent Income</h3>
                    <a href="{{ route('admin.finance.income') }}" style="font-size:12px;color:#2563eb;text-decoration:none;">View all →</a>
                </div>
                @forelse($recentIncome as $record)
                    <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f9fafb;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;font-size:14px;">
                                💰
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">
                                    {{ $record->member?->full_name ?? 'Anonymous' }}
                                </p>
                                <p style="font-size:11px;color:#9ca3af;">
                                    {{ ucfirst($record->category) }} · {{ $record->payment_date->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <p style="font-size:13px;font-weight:700;color:#16a34a;">
                                {{ $record->currency }} {{ number_format($record->amount, 2) }}
                            </p>
                            @if($record->currency !== 'GHS')
                                <p style="font-size:11px;color:#9ca3af;">GH₵ {{ number_format($record->amount_ghs, 2) }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding:32px;text-align:center;color:#9ca3af;font-size:14px;">No income records yet.</div>
                @endforelse
            </div>

            {{-- Recent expenses --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Recent Expenses</h3>
                    <a href="{{ route('admin.finance.expenses') }}" style="font-size:12px;color:#2563eb;text-decoration:none;">View all →</a>
                </div>
                @forelse($recentExpenses as $record)
                    <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f9fafb;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:#fee2e2;display:flex;align-items:center;justify-content:center;font-size:14px;">
                                📤
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">{{ $record->description }}</p>
                                <p style="font-size:11px;color:#9ca3af;">
                                    {{ ucfirst($record->category) }} · {{ $record->expense_date->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <p style="font-size:13px;font-weight:700;color:#dc2626;">
                            {{ $record->currency }} {{ number_format($record->amount, 2) }}
                        </p>
                    </div>
                @empty
                    <div style="padding:32px;text-align:center;color:#9ca3af;font-size:14px;">No expense records yet.</div>
                @endforelse
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const months   = @json($monthlyTrend->pluck('month'));
        const incomes  = @json($monthlyTrend->pluck('income'));
        const expenses = @json($monthlyTrend->pluck('expenses'));

        const catLabels = @json($incomeByCategory->pluck('category'));
        const catTotals = @json($incomeByCategory->pluck('total'));

        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, Segoe UI, sans-serif';
        Chart.defaults.font.size   = 12;
        Chart.defaults.color       = '#6b7280';

        new Chart(document.getElementById('finTrendChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income',
                        data: incomes,
                        backgroundColor: 'rgba(22,163,74,0.8)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        backgroundColor: 'rgba(220,38,38,0.8)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } },
                    tooltip: { backgroundColor: '#1f2937', padding: 10, cornerRadius: 8 }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('incomeBreakdownChart'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catTotals,
                    backgroundColor: ['#2563eb','#16a34a','#d97706','#7c3aed','#0891b2','#6b7280'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, pointStyleWidth: 8 } },
                    tooltip: { backgroundColor: '#1f2937', padding: 10, cornerRadius: 8 }
                }
            }
        });
    </script>

@endsection

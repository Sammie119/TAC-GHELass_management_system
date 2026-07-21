@extends('layouts.admin')
@section('page-title', 'Budgets')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Budgets</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Set budget line amounts and track spend against them</p>
        </div>

        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <button onclick="openAddLineForm()"
                    style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                + Manage budget lines
            </button>
            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <label style="font-size:13px;color:#374151;">Financial year</label>
                <select name="year" onchange="this.form.submit()"
                        style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
                    @foreach(range(now()->year + 1, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Manage Budget Lines panel --}}
    <div id="line-form-panel" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:16px;">Manage budget lines</h3>

        <form id="line-form" method="POST" action="{{ route('admin.budget-lines.store') }}" style="margin-bottom:20px;">
            @csrf
            <input type="hidden" name="_method" id="line-form-method" value="">

            <div style="display:grid;grid-template-columns:2fr 3fr auto auto;gap:12px;align-items:end;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px;">Name *</label>
                    <input type="text" name="name" id="line-field-name" placeholder="e.g. Salaries" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px;">Description</label>
                    <input type="text" name="description" id="line-field-description" placeholder="Optional"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                </div>
                <div style="display:flex;align-items:center;gap:6px;padding-bottom:9px;">
                    <input type="checkbox" name="is_active" id="line-field-active" value="1" checked style="width:15px;height:15px;">
                    <label for="line-field-active" style="font-size:12px;color:#374151;">Active</label>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" id="line-form-submit"
                            style="background:#2563eb;color:white;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;white-space:nowrap;">
                        Add line
                    </button>
                    <button type="button" onclick="resetLineForm()"
                            style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        </form>

        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
            <tr style="background:#f9fafb;">
                <th style="padding:8px 10px;text-align:left;font-size:11px;color:#6b7280;text-transform:uppercase;">Name</th>
                <th style="padding:8px 10px;text-align:left;font-size:11px;color:#6b7280;text-transform:uppercase;">Description</th>
                <th style="padding:8px 10px;text-align:left;font-size:11px;color:#6b7280;text-transform:uppercase;">Status</th>
                <th style="padding:8px 10px;text-align:left;font-size:11px;color:#6b7280;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($allBudgetLines as $line)
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:8px 10px;font-weight:500;color:#111827;">{{ $line->name }}</td>
                    <td style="padding:8px 10px;color:#6b7280;">{{ $line->description }}</td>
                    <td style="padding:8px 10px;">
                        @if($line->is_active)
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#dcfce7;color:#15803d;">Active</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#f3f4f6;color:#6b7280;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding:8px 10px;">
                        <div style="display:flex;gap:10px;align-items:center;">
                            <button type="button"
                                    onclick="openEditLineForm({{ $line->id }}, '{{ addslashes($line->name) }}', '{{ addslashes($line->description ?? '') }}', {{ $line->is_active ? 'true' : 'false' }})"
                                    style="color:#2563eb;font-size:12px;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.budget-lines.destroy', $line) }}"
                                  onsubmit="return confirm('Delete this budget line?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:20px;text-align:center;color:#9ca3af;font-size:13px;">
                        No budget lines yet — add one above (e.g. Salaries, Utilities).
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Budget vs actual summary --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:14px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.budget-stats{grid-template-columns:repeat(3,1fr) !important;}}</style>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;" class="budget-stats">
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#2563eb;">GH₵ {{ number_format($summary->sum('budgeted'), 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total budgeted ({{ $year }})</p>
            </div>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#dc2626;">GH₵ {{ number_format($summary->sum('actual') + $unassignedTotal, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total actual spend</p>
            </div>
            <div style="background:{{ $summary->sum('variance') >= 0 ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $summary->sum('variance') >= 0 ? '#bbf7d0' : '#fecaca' }};border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:{{ $summary->sum('variance') >= 0 ? '#16a34a' : '#dc2626' }};">GH₵ {{ number_format($summary->sum('variance'), 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">{{ $summary->sum('variance') >= 0 ? 'Under budget' : 'Over budget' }}</p>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Budget vs Actual by month</h3>
        <div style="position:relative;height:260px;">
            <canvas id="budgetChart"></canvas>
        </div>
    </div>

    {{-- Excel template download / upload --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:6px;">Excel template</h3>
        <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">
            Download the template below, fill in monthly amounts in Excel, then upload it here.
            The first column (ID) identifies each budget line — don't edit it. New budget lines must be added above first, then re-download the template.
        </p>

        <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <a href="{{ route('admin.budgets.template', ['year' => $year]) }}"
               style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:10px 16px;border-radius:8px;font-size:13px;text-decoration:none;white-space:nowrap;">
                📥 Download template ({{ $year }})
            </a>

            <form method="POST" action="{{ route('admin.budgets.upload') }}" enctype="multipart/form-data"
                  style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <div style="flex:1;min-width:220px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px;">
                        Select filled template (.xlsx, .xls, .csv)
                    </label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;box-sizing:border-box;">
                </div>
                <button type="submit"
                        style="background:#16a34a;color:white;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:500;border:none;cursor:pointer;white-space:nowrap;">
                    Upload & Import
                </button>
            </form>
        </div>
    </div>

    {{-- Editable budget grid --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;overflow-x:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <h3 style="font-size:14px;font-weight:600;color:#111827;">Set monthly budgets for {{ $year }}</h3>
            <button type="button" id="budget-grid-edit-btn" onclick="enableBudgetGridEdit()"
                    style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:7px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                ✏️ Edit
            </button>
        </div>

        <form method="POST" action="{{ route('admin.budgets.store') }}">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">

            <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:900px;">
                <thead>
                <tr style="background:#f9fafb;">
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:#6b7280;text-transform:uppercase;position:sticky;left:0;background:#f9fafb;">Budget line</th>
                    @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
                        <th style="padding:8px 6px;text-align:center;font-size:11px;color:#6b7280;text-transform:uppercase;">{{ $m }}</th>
                    @endforeach
                    <th style="padding:8px 10px;text-align:right;font-size:11px;color:#6b7280;text-transform:uppercase;">Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($budgetLines as $line)
                    <tr style="border-top:1px solid #f3f4f6;">
                        <td style="padding:8px 10px;font-weight:500;color:#111827;white-space:nowrap;position:sticky;left:0;background:white;">{{ $line->name }}</td>
                        @for($month = 1; $month <= 12; $month++)
                            <td style="padding:4px;">
                                <input type="hidden" name="entries[{{ $line->id }}_{{ $month }}][budget_line_id]" value="{{ $line->id }}">
                                <input type="hidden" name="entries[{{ $line->id }}_{{ $month }}][month]" value="{{ $month }}">
                                <input type="number" step="0.01" min="0" readonly
                                       name="entries[{{ $line->id }}_{{ $month }}][amount]"
                                       value="{{ $budgetGrid[$line->id][$month] ?? '' }}"
                                       class="budget-cell" data-line="{{ $line->id }}"
                                       oninput="recalcBudgetTotals()"
                                       style="width:76px;border:1px solid #e5e7eb;border-radius:6px;padding:5px 6px;font-size:12px;outline:none;text-align:right;background:#f9fafb;color:#6b7280;cursor:default;">
                            </td>
                        @endfor
                        <td style="padding:8px 10px;text-align:right;font-weight:700;color:#2563eb;" id="row-total-{{ $line->id }}">
                            {{ number_format(($budgetGrid[$line->id] ?? collect())->sum(), 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">
                            No active budget lines yet — add one via "Manage budget lines" above.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @if($budgetLines->isNotEmpty())
                <div id="budget-grid-actions" style="display:none;margin-top:16px;gap:10px;">
                    <button type="submit"
                            style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        Save budgets
                    </button>
                    <button type="button" onclick="cancelBudgetGridEdit()"
                            style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                        Cancel
                    </button>
                </div>
            @endif
        </form>
    </div>

    {{-- Budget vs actual table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Budget line</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Budgeted</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actual</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Variance</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">% used</th>
            </tr>
            </thead>
            <tbody>
            @foreach($summary as $row)
                @php $pct = $row['budgeted'] > 0 ? round(($row['actual'] / $row['budgeted']) * 100) : ($row['actual'] > 0 ? 100 : 0); @endphp
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:12px 16px;font-weight:500;color:#111827;">{{ $row['label'] }}</td>
                    <td style="padding:12px 16px;color:#374151;">GH₵ {{ number_format($row['budgeted'], 2) }}</td>
                    <td style="padding:12px 16px;color:#374151;">GH₵ {{ number_format($row['actual'], 2) }}</td>
                    <td style="padding:12px 16px;font-weight:600;color:{{ $row['variance'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        GH₵ {{ number_format($row['variance'], 2) }}
                    </td>
                    <td style="padding:12px 16px;min-width:110px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ min(100, $pct) }}%;background:{{ $pct > 100 ? '#dc2626' : ($pct >= 80 ? '#d97706' : '#16a34a') }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:#374151;min-width:32px;">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if($unassignedTotal > 0)
                <tr style="border-top:1px solid #f3f4f6;background:#fffbeb;">
                    <td style="padding:12px 16px;font-weight:500;color:#92400e;">Unassigned (no budget line tagged)</td>
                    <td style="padding:12px 16px;color:#9ca3af;">—</td>
                    <td style="padding:12px 16px;color:#92400e;font-weight:600;">GH₵ {{ number_format($unassignedTotal, 2) }}</td>
                    <td style="padding:12px 16px;color:#9ca3af;">—</td>
                    <td style="padding:12px 16px;color:#9ca3af;">—</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, Segoe UI, sans-serif';
        Chart.defaults.font.size   = 12;
        Chart.defaults.color       = '#6b7280';

        const budgetMonths   = @json($monthlyTotals->pluck('month'));
        const budgetBudgeted = @json($monthlyTotals->pluck('budgeted'));
        const budgetActual   = @json($monthlyTotals->pluck('actual'));

        new Chart(document.getElementById('budgetChart'), {
            type: 'bar',
            data: {
                labels: budgetMonths,
                datasets: [
                    {
                        label: 'Budgeted',
                        data: budgetBudgeted,
                        backgroundColor: 'rgba(37,99,235,0.8)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Actual',
                        data: budgetActual,
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

        function recalcBudgetTotals() {
            const totals = {};
            document.querySelectorAll('.budget-cell').forEach(input => {
                const line = input.dataset.line;
                const val = parseFloat(input.value) || 0;
                totals[line] = (totals[line] || 0) + val;
            });
            Object.keys(totals).forEach(line => {
                const cell = document.getElementById('row-total-' + line);
                if (cell) cell.textContent = totals[line].toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            });
        }

        function enableBudgetGridEdit() {
            document.querySelectorAll('.budget-cell').forEach(input => {
                input.readOnly = false;
                input.style.background = 'white';
                input.style.color = '#111827';
                input.style.cursor = 'text';
            });
            document.getElementById('budget-grid-edit-btn').style.display = 'none';
            document.getElementById('budget-grid-actions').style.display = 'flex';
        }

        function cancelBudgetGridEdit() {
            location.reload();
        }

        const budgetLineStoreUrl = '{{ route('admin.budget-lines.store') }}';

        function updateBudgetLineUrlFor(id) {
            return budgetLineStoreUrl.replace(/\/budget-lines$/, '/budget-lines/' + id);
        }

        function resetLineForm() {
            document.getElementById('line-form').reset();
            document.getElementById('line-form-method').value = '';
            document.getElementById('line-form').action = budgetLineStoreUrl;
            document.getElementById('line-field-active').checked = true;
            document.getElementById('line-form-submit').innerText = 'Add line';
        }

        function openAddLineForm() {
            const panel = document.getElementById('line-form-panel');
            const isOpen = panel.style.display === 'block';
            if (isOpen) {
                panel.style.display = 'none';
                return;
            }
            resetLineForm();
            panel.style.display = 'block';
            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function openEditLineForm(id, name, description, isActive) {
            const panel = document.getElementById('line-form-panel');
            document.getElementById('line-form-method').value = 'PUT';
            document.getElementById('line-form').action = updateBudgetLineUrlFor(id);
            document.getElementById('line-field-name').value = name;
            document.getElementById('line-field-description').value = description;
            document.getElementById('line-field-active').checked = isActive;
            document.getElementById('line-form-submit').innerText = 'Save changes';
            panel.style.display = 'block';
            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        @if($errors->any())
            document.getElementById('line-form-panel').style.display = 'block';
        @endif
    </script>

@endsection

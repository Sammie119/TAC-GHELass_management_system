@extends('layouts.admin')
@section('page-title', 'Dropdown Options')
@section('content')

    @php
        $groupColors = [
            'income_category' => ['bg' => '#dcfce7', 'text' => '#15803d'],
            'expense_category' => ['bg' => '#fee2e2', 'text' => '#dc2626'],
            'payment_method' => ['bg' => '#eff6ff', 'text' => '#2563eb'],
            'currency' => ['bg' => '#f0fdf4', 'text' => '#16a34a'],
            'department' => ['bg' => '#dbeafe', 'text' => '#2563eb'],
        ];
    @endphp

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Dropdown Options</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Manage every dropdown list used across the app in one place</p>
        </div>
        <div style="display:flex;gap:10px;">
            <button onclick="openAddForm()"
                    style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                + Add new dropdown
            </button>
            <a href="{{ route('admin.settings.index') }}"
               style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
                ← Back to settings
            </a>
        </div>
    </div>

{{--    @if(session('success'))--}}
{{--        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">--}}
{{--            {{ session('success') }}--}}
{{--        </div>--}}
{{--    @endif--}}
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Add / edit form panel --}}
    <div id="option-form-panel" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 id="option-form-title" style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Add new dropdown option</h3>

        <form id="option-form" method="POST" action="{{ route('admin.settings.dropdowns.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="">

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Group *</label>
                    <select name="group" id="field-group" required onchange="onGroupChange()"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach($groups as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="key-field-wrapper">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Key (no spaces) *</label>
                    <input type="text" name="key" id="field-key" placeholder="key_name" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;font-family:monospace;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Label *</label>
                    <input type="text" name="label" id="field-label" placeholder="Display label" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Symbol</label>
                    <input type="text" name="symbol" id="field-symbol" placeholder="e.g. GH₵ (currency only)"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Rate to GHS</label>
                    <input type="number" step="0.0001" min="0.0001" name="rate" id="field-rate" placeholder="1 (currency only)"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Sort order</label>
                    <input type="number" min="0" name="sort_order" id="field-sort-order" placeholder="auto"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div style="display:flex;align-items:center;gap:8px;margin-top:24px;">
                    <input type="checkbox" name="is_active" id="field-active" value="1" checked style="width:16px;height:16px;">
                    <label for="field-active" style="font-size:13px;color:#374151;">Active</label>
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save
                </button>
                <button type="button" onclick="closeOptionForm()"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Search / filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search key or label..."
               style="flex:1;min-width:220px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="group"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All groups</option>
            @foreach($groups as $key => $label)
                <option value="{{ $key }}" {{ request('group') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Search
        </button>
        @if(request('search') || request('group'))
            <a href="{{ route('admin.settings.dropdowns.index') }}"
               style="display:flex;align-items:center;color:#6b7280;font-size:13px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Group</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Key</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Label</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Symbol</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Rate</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Sort</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($options as $option)
                @php $colors = $groupColors[$option->group] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280']; @endphp
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:{{ $colors['bg'] }};color:{{ $colors['text'] }};">
                            {{ $groups[$option->group] ?? $option->group }}
                        </span>
                    </td>
                    <td style="padding:12px 16px;font-family:monospace;color:#374151;">{{ $option->key }}</td>
                    <td style="padding:12px 16px;font-weight:500;color:#111827;">{{ $option->label }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $option->meta['symbol'] ?? '—' }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $option->meta['rate'] ?? '—' }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $option->sort_order }}</td>
                    <td style="padding:12px 16px;">
                        @if($option->is_active)
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#dcfce7;color:#15803d;">Active</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#f3f4f6;color:#6b7280;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:10px;align-items:center;">
                            <button type="button"
                                    onclick="openEditForm({{ $option->id }}, '{{ $option->group }}', '{{ addslashes($option->key) }}', '{{ addslashes($option->label) }}', '{{ addslashes($option->meta['symbol'] ?? '') }}', '{{ $option->meta['rate'] ?? '' }}', {{ $option->sort_order }}, {{ $option->is_active ? 'true' : 'false' }})"
                                    style="color:#2563eb;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.settings.dropdowns.destroy', $option) }}"
                                  onsubmit="return confirm('Delete this dropdown option?')">
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
                    <td colspan="8" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        No dropdown options found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $options->links() }}</div>

    <script>
        const storeUrl = '{{ route('admin.settings.dropdowns.store') }}';

        function updateUrlFor(id) {
            return storeUrl.replace(/\/dropdowns$/, '/dropdowns/' + id);
        }

        function onGroupChange() {
            const group = document.getElementById('field-group').value;
            const keyWrapper = document.getElementById('key-field-wrapper');
            const keyField = document.getElementById('field-key');
            if (group === 'department') {
                keyWrapper.style.display = 'none';
                keyField.required = false;
            } else {
                keyWrapper.style.display = 'block';
                keyField.required = true;
            }
        }

        function resetForm() {
            document.getElementById('option-form').reset();
            document.getElementById('form-method').value = '';
            document.getElementById('option-form').action = storeUrl;
            document.getElementById('field-active').checked = true;
            onGroupChange();
        }

        function openAddForm() {
            resetForm();
            document.getElementById('option-form-title').innerText = 'Add new dropdown option';
            document.getElementById('option-form-panel').style.display = 'block';
            document.getElementById('option-form-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function openEditForm(id, group, key, label, symbol, rate, sortOrder, isActive) {
            resetForm();
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('option-form').action = updateUrlFor(id);
            document.getElementById('field-group').value = group;
            document.getElementById('field-key').value = key;
            document.getElementById('field-label').value = label;
            document.getElementById('field-symbol').value = symbol;
            document.getElementById('field-rate').value = rate;
            document.getElementById('field-sort-order').value = sortOrder;
            document.getElementById('field-active').checked = isActive;
            onGroupChange();
            document.getElementById('option-form-title').innerText = 'Edit dropdown option';
            document.getElementById('option-form-panel').style.display = 'block';
            document.getElementById('option-form-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function closeOptionForm() {
            document.getElementById('option-form-panel').style.display = 'none';
        }

        document.getElementById('option-form').addEventListener('submit', function () {
            if (document.getElementById('field-group').value === 'department') {
                document.getElementById('field-key').value = document.getElementById('field-label').value;
            }
        });

        onGroupChange();

        @if($errors->any())
            document.getElementById('option-form-panel').style.display = 'block';
        @endif
    </script>

@endsection

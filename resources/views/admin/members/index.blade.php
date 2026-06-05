@extends('layouts.admin')
@section('page-title', 'Members')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Members</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $members->total() }} members total</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">

            {{-- Bulk print --}}
            <button onclick="printSelected()"
                    style="background:#7c3aed;color:white;padding:8px 14px;border-radius:8px;font-size:14px;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Selected
            </button>

            {{-- Import button --}}
            <button onclick="document.getElementById('import-modal').style.display='flex'"
                    style="background:#16a34a;color:white;padding:8px 14px;border-radius:8px;font-size:14px;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>

            {{-- Add member --}}
            <a href="{{ route('admin.members.create') }}"
               style="background:#2563eb;color:white;padding:8px 14px;border-radius:8px;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                + Add Member
            </a>

        </div>
    </div>

    {{-- Import success/errors --}}
    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1rem;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1rem;font-size:13px;">
            <p style="font-weight:600;margin-bottom:6px;">Import warnings:</p>
            <ul style="list-style:disc;padding-left:20px;space-y:4px;">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bulk print hidden form --}}
    <form id="bulk-print-form" method="POST"
          action="{{ route('admin.members.print-cards-bulk') }}"
          target="_blank">
        @csrf
        <div id="bulk-member-ids"></div>
    </form>

    {{-- ── Import Modal ────────────────────────────────────── --}}
    <div id="import-modal"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:100;align-items:flex-start;justify-content:center;padding:1rem;overflow-y:auto;">
        <div style="background:white;border-radius:20px;padding:28px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,0.2);margin:auto;position:relative;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div>
                    <h3 style="font-size:17px;font-weight:700;color:#111827;">Import Members from Excel</h3>
                    <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Upload an Excel file to bulk-add members</p>
                </div>
                <button onclick="document.getElementById('import-modal').style.display='none'"
                        style="color:#9ca3af;background:none;border:none;cursor:pointer;font-size:20px;line-height:1;">✕</button>
            </div>

            {{-- Step 1 --}}
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <span style="width:24px;height:24px;background:#2563eb;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</span>
                    <p style="font-size:14px;font-weight:600;color:#1d4ed8;">Download the template</p>
                </div>
{{--                <p style="font-size:13px;color:#374151;margin-bottom:10px;padding-left:34px;">--}}
{{--                    Download the Excel template, fill in your member details, then upload it below.--}}
{{--                </p>--}}
                <a href="{{ route('admin.members.template') }}"
                   style="margin-left:34px;display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template (.xlsx)
                </a>
            </div>

            {{-- Template columns info --}}
            <div style="background:#f9fafb;border-radius:10px;padding:12px;margin-bottom:16px;font-size:12px;">
                <p style="font-weight:600;color:#374151;margin-bottom:8px;">Template columns:</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                    <span style="color:#dc2626;font-weight:500;">• first_name *</span>
                    <span style="color:#dc2626;font-weight:500;">• last_name *</span>
                    <span style="color:#6b7280;">• phone</span>
                    <span style="color:#6b7280;">• email</span>
                    <span style="color:#6b7280;">• gender (male/female)</span>
                    <span style="color:#6b7280;">• date_of_birth (YYYY-MM-DD)</span>
                    <span style="color:#6b7280;">• address</span>
                    <span style="color:#6b7280;">• department</span>
                    <span style="color:#6b7280;">• tacms_number</span>
                    <span style="color:#6b7280;">• status (active/inactive)</span>
                </div>
                <p style="color:#dc2626;margin-top:6px;font-size:11px;">* Required fields</p>
            </div>

            {{-- Step 2 --}}
            <div style="margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <span style="width:24px;height:24px;background:#16a34a;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</span>
                    <p style="font-size:14px;font-weight:600;color:#15803d;">Upload your filled file</p>
                </div>

                <form method="POST" action="{{ route('admin.members.import') }}"
                      enctype="multipart/form-data" style="padding-left:34px;">
                    @csrf

                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                        Select Excel file (.xlsx, .xls, .csv)
                    </label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                           id="import-file"
                           style="width:100%;border:1.5px dashed #d1d5db;border-radius:10px;padding:12px;font-size:13px;color:#374151;cursor:pointer;box-sizing:border-box;background:#fafafa;"
                           onchange="updateFileName(this)">
                    <p id="file-name-display" style="font-size:12px;color:#9ca3af;margin-top:4px;">No file selected</p>

                    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:10px;margin-top:10px;font-size:12px;color:#92400e;">
                        ⚠️ Duplicate phone numbers, emails, or TACMS numbers will be skipped automatically.
                        Existing members will NOT be overwritten.
                    </div>

                    <div style="display:flex;gap:10px;margin-top:14px;">
                        <button type="submit"
                                style="flex:1;background:#16a34a;color:white;padding:11px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Import Members
                        </button>
                        <button type="button"
                                onclick="document.getElementById('import-modal').style.display='none'"
                                style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:11px 16px;border-radius:10px;font-size:14px;cursor:pointer;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Hidden bulk print form --}}
    <form id="bulk-print-form" method="POST"
          action="{{ route('admin.members.print-cards-bulk') }}"
          target="_blank">
        @csrf
        <div id="bulk-member-ids"></div>
    </form>

    {{-- Search & Filter --}}
    <form method="GET" class="flex gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, phone, or ID..."
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All statuses</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <select name="department"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
            <option value="">All departments</option>
            @foreach(config('departments') as $dept)
                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                    {{ $dept }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            Search
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.members.index') }}"
               class="border border-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th style="padding:12px 20px;text-align:left;width:40px;">
                    <input type="checkbox" id="select-all" onclick="toggleAll(this)"
                           style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer;">
                </th>
                <th class="px-5 py-3 text-left">Member</th>
                <th class="px-5 py-3 text-left">ID Card</th>
                <th class="px-5 py-3 text-left">Department</th>
                <th class="px-5 py-3 text-left">Phone</th>
                <th class="px-5 py-3 text-left">Gender</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($members as $member)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td style="padding:14px 20px;">
                        <input type="checkbox" class="member-checkbox" value="{{ $member->id }}"
                               style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer;">
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}"
                                     class="w-8 h-8 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center
                                        text-blue-600 font-semibold text-xs shrink-0">
                                    {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                                <p class="text-gray-400 text-xs">{{ $member->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-600 text-xs">{{ $member->member_id_card }}</td>
                    <td class="px-5 py-3 text-gray-600 text-sm">{{ $member->department ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $member->phone ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ ucfirst($member->gender ?? '—') }}</td>
                    <td class="px-5 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $member->status === 'active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($member->status) }}
                    </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.members.show', $member) }}"
                               class="text-blue-600 hover:underline text-xs">View</a>
                            <a href="{{ route('admin.members.edit', $member) }}"
                               class="text-gray-500 hover:underline text-xs">Edit</a>
                            <a href="{{ route('admin.members.qr', $member) }}"
                               class="text-purple-600 hover:underline text-xs">QR</a>
                            <a href="{{ route('admin.members.print-card', $member) }}"
                               target="_blank"
                               style="color:#7c3aed;font-size:13px;text-decoration:none;">Card</a>
                            <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                                  onsubmit="return confirm('Delete {{ $member->full_name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                        No members found.
                        <a href="{{ route('admin.members.create') }}" class="text-blue-600 hover:underline ml-1">
                            Add the first member →
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-5">
        {{ $members->links() }}
    </div>

<script>
    function toggleAll(source) {
        document.querySelectorAll('.member-checkbox')
            .forEach(cb => cb.checked = source.checked);
    }

    function printSelected() {
        const checked = document.querySelectorAll('.member-checkbox:checked');
        if (checked.length === 0) {
            alert('Please select at least one member to print.');
            return;
        }

        const container = document.getElementById('bulk-member-ids');
        container.innerHTML = '';

        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'member_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        document.getElementById('bulk-print-form').submit();
    }

    // Close modal on overlay click
    document.getElementById('import-modal').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });

    // Show selected file name
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        display.textContent = input.files[0]
            ? `Selected: ${input.files[0].name} (${(input.files[0].size / 1024).toFixed(1)} KB)`
            : 'No file selected';
        display.style.color = '#16a34a';
    }
</script>

@endsection

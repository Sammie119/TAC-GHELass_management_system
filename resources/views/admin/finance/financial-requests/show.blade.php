@extends('layouts.admin')
@section('page-title', 'Financial Request Detail')
@section('content')

    @php
        $canApprovePastor = auth()->user()->hasRole('pastor') && ! $financialRequest->pastor_approved_at && $financialRequest->status === 'pending';
        $canApproveSuperAdmin = auth()->user()->isFinanceChairman() && ! $financialRequest->super_admin_approved_at && $financialRequest->status === 'pending';
        $canReject = (auth()->user()->hasRole('pastor') || auth()->user()->isFinanceChairman()) && $financialRequest->status === 'pending';
    @endphp

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;">
        <a href="{{ route('admin.financial-requests.index') }}" style="color:#9ca3af;font-size:13px;text-decoration:none;">← Back</a>
        <h2 style="font-size:18px;font-weight:600;color:#111827;">{{ $financialRequest->description }}</h2>
    </div>

{{--    @if(session('success'))--}}
{{--        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">--}}
{{--            {{ session('success') }}--}}
{{--        </div>--}}
{{--    @endif--}}
    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display:grid;gap:20px;">
        <style>@media(min-width:1024px){.fr-detail{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="fr-detail">

            {{-- Left: request info --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                    <div style="background:linear-gradient(135deg,#2563eb,#4f46e5);padding:20px;color:white;">
                        <p style="font-size:12px;opacity:0.8;margin-bottom:4px;">{{ ucfirst(str_replace('_', ' ', $financialRequest->category)) }}</p>
                        <h3 style="font-size:18px;font-weight:700;margin-bottom:4px;">{{ $financialRequest->description }}</h3>
                        <p style="font-size:26px;font-weight:800;">
                            {{ $financialRequest->currency }} {{ number_format($financialRequest->amount, 2) }}
                        </p>
                    </div>

                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Requested by</span>
                            <span style="font-weight:500;">{{ $financialRequest->requestedBy?->name ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Payee</span>
                            <span style="font-weight:500;">{{ $financialRequest->payee ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Request date</span>
                            <span style="font-weight:500;">{{ $financialRequest->request_date->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Intended payment method</span>
                            <span style="font-weight:500;">{{ ucwords(str_replace('_', ' ', $financialRequest->payment_method)) }}</span>
                        </div>
                        @if($financialRequest->notes)
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:#9ca3af;">Notes</span>
                                <span style="font-weight:500;text-align:right;">{{ $financialRequest->notes }}</span>
                            </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Status</span>
                            <span style="padding:3px 12px;border-radius:20px;font-size:11px;font-weight:600;
                                {{ $financialRequest->status === 'approved' ? 'background:#dcfce7;color:#15803d;' : '' }}
                                {{ $financialRequest->status === 'rejected' ? 'background:#fee2e2;color:#dc2626;' : '' }}
                                {{ $financialRequest->status === 'pending'  ? 'background:#fef3c7;color:#d97706;' : '' }}">
                                {{ ucfirst($financialRequest->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Reject form --}}
                @if($canReject)
                    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                        <button type="button" onclick="document.getElementById('reject-form').style.display='block'; this.style.display='none';"
                                style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;">
                            Reject request
                        </button>
                        <form id="reject-form" method="POST" action="{{ route('admin.financial-requests.reject', $financialRequest) }}" style="display:none;margin-top:12px;">
                            @csrf
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Reason *</label>
                            <textarea name="reason" rows="2" required
                                      style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;resize:none;box-sizing:border-box;margin-bottom:10px;"></textarea>
                            <button type="submit"
                                    style="background:#dc2626;color:white;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                                Confirm rejection
                            </button>
                        </form>
                    </div>
                @endif

            </div>

            {{-- Right: approval trail + PV --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                    <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                        <h3 style="font-size:14px;font-weight:600;color:#111827;">Approval trail</h3>
                    </div>

                    <div style="padding:16px;display:flex;flex-direction:column;gap:14px;">

                        {{-- Pastor --}}
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">Pastor</p>
                                @if($financialRequest->pastor_approved_at)
                                    <p style="font-size:11px;color:#16a34a;">
                                        Approved by {{ $financialRequest->pastorApprovedBy?->name }} on {{ $financialRequest->pastor_approved_at->format('d M Y, h:i A') }}
                                    </p>
                                @else
                                    <p style="font-size:11px;color:#9ca3af;">Awaiting approval</p>
                                @endif
                            </div>
                            @if($canApprovePastor)
                                <form method="POST" action="{{ route('admin.financial-requests.approve-pastor', $financialRequest) }}">
                                    @csrf
                                    <button type="submit"
                                            style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                                        Approve as Pastor
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Super Admin --}}
                        <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f3f4f6;padding-top:14px;">
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">Finance Chairman</p>
                                @if($financialRequest->super_admin_approved_at)
                                    <p style="font-size:11px;color:#16a34a;">
                                        Approved by {{ $financialRequest->superAdminApprovedBy?->name }} on {{ $financialRequest->super_admin_approved_at->format('d M Y, h:i A') }}
                                    </p>
                                @else
                                    <p style="font-size:11px;color:#9ca3af;">Awaiting approval</p>
                                @endif
                            </div>
                            @if($canApproveSuperAdmin)
                                <form method="POST" action="{{ route('admin.financial-requests.approve-super-admin', $financialRequest) }}">
                                    @csrf
                                    <button type="submit"
                                            style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                                        Approve as Finance Chairman
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if($financialRequest->status === 'rejected')
                            <div style="border-top:1px solid #f3f4f6;padding-top:14px;">
                                <p style="font-size:13px;font-weight:500;color:#dc2626;">Rejected</p>
                                <p style="font-size:11px;color:#9ca3af;">
                                    By {{ $financialRequest->rejectedBy?->name }} on {{ $financialRequest->rejected_at->format('d M Y, h:i A') }}
                                </p>
                                <p style="font-size:12px;color:#374151;margin-top:6px;">"{{ $financialRequest->rejection_reason }}"</p>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Payment / PV --}}
                @if($financialRequest->status === 'approved')
                    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:12px;">Payment Voucher</h3>

                        @if($financialRequest->expenseRecord)
                            <p style="font-size:12px;color:#9ca3af;margin-bottom:12px;">
                                Recorded in Finance as an expense on {{ $financialRequest->expenseRecord->expense_date->format('d M Y') }}.
                            </p>
                        @endif

                        @if($financialRequest->pv_number)
                            <p style="font-size:13px;color:#374151;margin-bottom:12px;">
                                PV Number: <span style="font-family:monospace;font-weight:600;">{{ $financialRequest->pv_number }}</span>
                                — generated {{ $financialRequest->pv_generated_at->format('d M Y, h:i A') }}
                            </p>
                            <a href="{{ route('admin.financial-requests.pv.download', $financialRequest) }}"
                               style="display:inline-block;background:#2563eb;color:white;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                                Download PV
                            </a>
                        @else
                            <form method="POST" action="{{ route('admin.financial-requests.generate-pv', $financialRequest) }}">
                                @csrf
                                <button type="submit"
                                        style="background:#2563eb;color:white;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                                    Generate PV
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

            </div>

        </div>
    </div>

@endsection

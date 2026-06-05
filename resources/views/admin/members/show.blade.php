@extends('layouts.admin')
@section('page-title', 'Member Profile')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $member->full_name }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $member->member_id_card }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.members.print-card', $member) }}"
               target="_blank"
               style="border:1px solid #7c3aed;color:#7c3aed;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print ID Card
            </a>
            <a href="{{ route('admin.members.qr', $member) }}"
               class="border border-purple-300 text-purple-600 px-4 py-2 rounded-lg text-sm hover:bg-purple-50 transition-colors">
                Download QR
            </a>
            <a href="{{ route('admin.members.edit', $member) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                Edit member
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Profile card --}}
        <div class="lg:col-span-1 space-y-4">

            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                @if($member->photo)
                    <img src="{{ Storage::url($member->photo) }}"
                         class="w-20 h-20 rounded-full object-cover mx-auto mb-3">
                @else
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center
                            text-blue-600 font-bold text-2xl mx-auto mb-3">
                        {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                    </div>
                @endif

                <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $member->member_id_card }}</p>

                <span class="mt-2 inline-block px-3 py-1 rounded-full text-xs font-medium
                {{ $member->status === 'active'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-gray-100 text-gray-500' }}">
                {{ ucfirst($member->status) }}
            </span>
            </div>

            {{-- Details card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Phone</span>
                        <span class="text-gray-700 font-medium">{{ $member->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Email</span>
                        <span class="text-gray-700 font-medium truncate max-w-[160px]">{{ $member->email ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Gender</span>
                        <span class="text-gray-700 font-medium">{{ ucfirst($member->gender ?? '—') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Date of birth</span>
                        <span class="text-gray-700 font-medium">
                        {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y') : '—' }}
                    </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Address</span>
                        <span class="text-gray-700 font-medium text-right max-w-[160px]">{{ $member->address ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Department</span>
                        <span class="text-gray-700 font-medium">{{ $member->department ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">TACMS Number</span>
                        <span class="text-gray-700 font-medium font-mono text-sm">
                            {{ $member->tacms_number ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Joined</span>
                        <span class="text-gray-700 font-medium">{{ $member->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Stats card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Attendance</h3>
                <div class="text-center">
                    <p class="text-4xl font-bold text-blue-600">{{ $totalAttendance }}</p>
                    <p class="text-sm text-gray-400 mt-1">total services attended</p>
                </div>
            </div>

        </div>

        {{-- Right: Attendance history --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 text-sm">Attendance history</h3>
                    <span class="text-xs text-gray-400">Showing last 10 records</span>
                </div>

                @forelse($recentAttendance as $record)
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold
                        {{ $record->event->type === 'sunday'  ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $record->event->type === 'midweek' ? 'bg-purple-100 text-purple-600' : '' }}
                        {{ $record->event->type === 'special' ? 'bg-amber-100 text-amber-600' : '' }}">
                                {{ strtoupper(substr($record->event->type, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $record->event->title }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $record->event->event_date->format('D, d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                        {{ ucwords(str_replace('_', ' ', $record->checkin_method)) }}
                    </span>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $record->checked_in_at->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-gray-400 text-sm">No attendance records yet for this member.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection

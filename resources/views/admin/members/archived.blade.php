@extends('layouts.admin')
@section('page-title', 'Archived Members')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Archived Members</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $members->total() }} archived members</p>
        </div>
        <a href="{{ route('admin.members.index') }}"
           class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
            ← Back to active members
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search archived members..."
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
                class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            Search
        </button>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Member</th>
                <th class="px-5 py-3 text-left">ID Card</th>
                <th class="px-5 py-3 text-left">Phone</th>
                <th class="px-5 py-3 text-left">Archived on</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center
                                    text-gray-400 font-semibold text-xs shrink-0">
                                {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-500">{{ $member->full_name }}</p>
                                <p class="text-gray-400 text-xs">{{ $member->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-400 text-xs">{{ $member->member_id_card }}</td>
                    <td class="px-5 py-3 text-gray-400">{{ $member->phone ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs">
                        {{ $member->deleted_at->format('d M Y, h:i A') }}
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">

                            {{-- Restore --}}
                            <form method="POST" action="{{ route('admin.members.restore', $member->id) }}">
                                @csrf
                                <button type="submit"
                                        class="text-green-600 hover:underline text-xs">
                                    Restore
                                </button>
                            </form>

                            {{-- Permanently delete --}}
                            <form method="POST"
                                  action="{{ route('admin.members.force-delete', $member->id) }}"
                                  onsubmit="return confirm('Permanently delete {{ $member->full_name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-red-400 hover:underline text-xs">
                                    Delete permanently
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">
                        No archived members found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $members->links() }}
    </div>

@endsection

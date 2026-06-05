@extends('layouts.admin')
@section('page-title', 'New Event')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Create New Event</h2>
            <p class="text-sm text-gray-400 mt-0.5">Schedule a service or special event</p>
        </div>
        <a href="{{ route('admin.events.index') }}"
           class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
            ← Back to events
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
            <p class="font-medium mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.events.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main fields --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Event details</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Event title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               placeholder="e.g. Sunday Morning Service"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none
                                  @error('title') border-red-400 @enderror"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  placeholder="Optional notes about this event..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End time</label>
                            <input type="time" name="end_time" value="{{ old('end_time') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar options --}}
            <div class="lg:col-span-1 space-y-5">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Event settings</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="event_date" value="{{ old('event_date') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select type</option>
                            <option value="sunday"  {{ old('type') === 'sunday'  ? 'selected' : '' }}>Sunday service</option>
                            <option value="midweek" {{ old('type') === 'midweek' ? 'selected' : '' }}>Midweek service</option>
                            <option value="special" {{ old('type') === 'special' ? 'selected' : '' }}>Special event</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="upcoming" {{ old('status', 'upcoming') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="active"   {{ old('status') === 'active'   ? 'selected' : '' }}>Active (open for check-in)</option>
                            <option value="closed"   {{ old('status') === 'closed'   ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm
                               hover:bg-blue-700 transition-colors font-medium">
                        Create event
                    </button>

                    <a href="{{ route('admin.events.index') }}"
                       class="block text-center mt-2 text-sm text-gray-400 hover:text-gray-600">
                        Cancel
                    </a>
                </div>
            </div>

        </div>
    </form>

@endsection

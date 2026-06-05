@extends('layouts.admin')
@section('page-title', 'Edit Event')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Edit Event</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $event->title }}</p>
        </div>
        <a href="{{ route('admin.events.show', $event) }}"
           class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
            ← Back to event
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

    <form method="POST" action="{{ route('admin.events.update', $event) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Event details</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Event title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none
                                  @error('title') border-red-400 @enderror"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('description', $event->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="start_time"
                                   value="{{ old('start_time', \Carbon\Carbon::parse($event->start_time)->format('H:i')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End time</label>
                            <input type="time" name="end_time"
                                   value="{{ old('end_time', $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-5">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Event settings</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="event_date"
                               value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="sunday"  {{ old('type', $event->type) === 'sunday'  ? 'selected' : '' }}>Sunday service</option>
                            <option value="midweek" {{ old('type', $event->type) === 'midweek' ? 'selected' : '' }}>Midweek service</option>
                            <option value="special" {{ old('type', $event->type) === 'special' ? 'selected' : '' }}>Special event</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="upcoming" {{ old('status', $event->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="active"   {{ old('status', $event->status) === 'active'   ? 'selected' : '' }}>Active (open for check-in)</option>
                            <option value="closed"   {{ old('status', $event->status) === 'closed'   ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm
                               hover:bg-blue-700 transition-colors font-medium">
                        Save changes
                    </button>
                </div>

                {{-- Danger zone --}}
                <div class="bg-white rounded-xl border border-red-200 p-5">
                    <h3 class="text-sm font-semibold text-red-600 mb-3">Danger zone</h3>
                    <p class="text-xs text-gray-400 mb-3">
                        Deleting this event will also remove all attendance records associated with it.
                    </p>
                    <button type="button"
                            onclick="document.getElementById('delete-form').submit()"
                            class="w-full border border-red-300 text-red-500 py-2 rounded-lg text-sm
                               hover:bg-red-50 transition-colors">
                        Delete event
                    </button>
                </div>
            </div>

        </div>
    </form>

    {{-- Delete form outside the update form --}}
    <form id="delete-form"
          method="POST"
          action="{{ route('admin.events.destroy', $event) }}"
          onsubmit="return confirm('Delete {{ $event->title }}? All attendance records will also be removed.')">
        @csrf @method('DELETE')
    </form>

@endsection

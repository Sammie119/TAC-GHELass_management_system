@extends('layouts.admin')
@section('page-title', 'Edit Cell Group')
@section('content')

    {{-- Same form as create but with old values pre-filled and PUT method --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;">
        <a href="{{ route('admin.cells.show', $cell) }}" style="color:#9ca3af;text-decoration:none;font-size:13px;">← Back</a>
        <h2 style="font-size:18px;font-weight:600;color:#111827;">Edit: {{ $cell->name }}</h2>
    </div>

    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;max-width:700px;">
        <form method="POST" action="{{ route('admin.cells.update', $cell) }}">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Group name *</label>
                    <input type="text" name="name" value="{{ old('name', $cell->name) }}" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Area</label>
                    <input type="text" name="area" value="{{ old('area', $cell->area) }}"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Status</label>
                    <select name="status"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;">
                        <option value="active"   {{ $cell->status === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $cell->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Leader</label>
                    <select name="leader_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;">
                        <option value="">— None —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ $cell->leader_id == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Assistant leader</label>
                    <select name="assistant_leader_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;">
                        <option value="">— None —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ $cell->assistant_leader_id == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Meeting day</label>
                    <select name="meeting_day"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;">
                        <option value="">— Select day —</option>
                        @foreach(['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" {{ $cell->meeting_day === $day ? 'selected' : '' }}>
                                {{ ucfirst($day) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Meeting time</label>
                    <input type="time" name="meeting_time" value="{{ old('meeting_time', $cell->meeting_time) }}"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Meeting venue</label>
                    <input type="text" name="meeting_venue" value="{{ old('meeting_venue', $cell->meeting_venue) }}"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Description</label>
                    <textarea name="description" rows="2"
                              style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;">{{ old('description', $cell->description) }}</textarea>
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save changes
                </button>
                <a href="{{ route('admin.cells.show', $cell) }}"
                   style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:11px 20px;border-radius:8px;font-size:14px;text-decoration:none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@endsection

@extends('layouts.admin')
@section('page-title', 'Edit Visitor')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Edit Visitor</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $visitor->full_name }}</p>
        </div>
        <a href="{{ route('admin.visitors.show', $visitor) }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            ← Back
        </a>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            <ul style="list-style:disc;padding-left:20px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.visitors.update', $visitor) }}">
        @csrf @method('PUT')

        <div style="display:grid;gap:24px;">
            <style>@media(min-width:1024px){.edit-grid{grid-template-columns:2fr 1fr !important;}}</style>
            <div style="display:grid;gap:24px;" class="edit-grid">

                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                    <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:20px;">Visitor details</h3>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">First name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $visitor->first_name) }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"
                                   required>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Last name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $visitor->last_name) }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"
                                   required>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $visitor->phone) }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Email</label>
                            <input type="email" name="email" value="{{ old('email', $visitor->email) }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Notes</label>
                        <textarea name="notes" rows="3"
                                  style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"
                                  onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">{{ old('notes', $visitor->notes) }}</textarea>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                        <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:16px;">Event</h3>
                        <select name="event_id"
                                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;margin-bottom:16px;">
                            @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ old('event_id', $visitor->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} — {{ $event->event_date->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                                style="width:100%;background:#2563eb;color:white;padding:11px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Save changes
                        </button>
                    </div>

                    <div style="background:white;border-radius:12px;border:1px solid #fecaca;padding:20px;">
                        <p style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:8px;">Danger zone</p>
                        <button type="button" onclick="document.getElementById('delete-form').submit()"
                                style="width:100%;border:1px solid #fecaca;color:#ef4444;padding:9px;border-radius:8px;font-size:13px;background:none;cursor:pointer;">
                            Delete record
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <form id="delete-form" method="POST" action="{{ route('admin.visitors.destroy', $visitor) }}"
          onsubmit="return confirm('Delete this visitor record permanently?')">
        @csrf @method('DELETE')
    </form>

@endsection

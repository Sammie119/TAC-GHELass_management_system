<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Imports\MembersImport;
use App\Exports\MembersTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    // Active members list
    public function index(Request $request)
    {
        $query = Member::query()->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('member_id_card', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $members = $query->latest()->paginate(20)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function create(Request $request)
    {
        $prefill = $request->only(['first_name', 'last_name', 'phone', 'email']);
        return view('admin.members.create', compact('prefill'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'phone'         => 'nullable|string|unique:members,phone',
            'email'         => 'nullable|email|unique:members,email',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:male,female,other',
            'address'       => 'nullable|string|max:255',
            'photo'         => 'nullable|image|max:2048',
            'department'   => 'nullable|string|max:100',
            'tacms_number' => 'nullable|string|max:50|unique:members,tacms_number',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }

        $member = Member::create($validated);

        $qrContent = QrCode::format('svg')->size(200)->generate($member->qr_code);
        Storage::disk('public')->put('qrcodes/' . $member->qr_code . '.svg', $qrContent);

        // Send welcome notification
        try {
            app(NotificationService::class)->sendWelcome($member);
        } catch (\Exception $e) {
            \Log::error('Welcome notification failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member created successfully.');
    }

    public function show(Member $member)
    {
        $totalAttendance  = $member->attendance()->count();
        $recentAttendance = $member->attendance()->with('event')
            ->latest('checked_in_at')->take(10)->get();

        return view('admin.members.show', compact(
            'member', 'totalAttendance', 'recentAttendance'
        ));
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'phone'         => 'nullable|string|unique:members,phone,' . $member->id,
            'email'         => 'nullable|email|unique:members,email,' . $member->id,
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:male,female,other',
            'address'       => 'nullable|string|max:255',
            'status'        => 'required|in:active,inactive',
            'photo'         => 'nullable|image|max:2048',
            'department'   => 'nullable|string|max:100',
            'tacms_number' => 'nullable|string|max:50|unique:members,tacms_number,' . $member->id,
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }

        $member->update($validated);

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member updated successfully.');
    }

    // Soft delete
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', "{$member->full_name} has been archived.");
    }

    // Archived members list
    public function archived(Request $request)
    {
        $query = Member::onlyTrashed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('member_id_card', 'like', "%{$search}%");
            });
        }

        $members = $query->latest('deleted_at')->paginate(20)->withQueryString();

        return view('admin.members.archived', compact('members'));
    }

    // Restore soft-deleted member
    public function restore($id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);
        $member->restore();

        return redirect()->route('admin.members.archived')
            ->with('success', "{$member->full_name} has been restored.");
    }

    // Permanently delete
    public function forceDelete($id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);

        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }

        Storage::disk('public')->delete('qrcodes/' . $member->qr_code . '.svg');

        $member->forceDelete();

        return redirect()->route('admin.members.archived')
            ->with('success', 'Member permanently deleted.');
    }

    public function downloadQr(Member $member)
    {
        $qrPath = 'qrcodes/' . $member->qr_code . '.svg';

        if (!Storage::disk('public')->exists($qrPath)) {
            $qrContent = QrCode::format('svg')->size(200)->generate($member->qr_code);
            Storage::disk('public')->put($qrPath, $qrContent);
        }

        return response(Storage::disk('public')->get($qrPath))
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $member->member_id_card . '-qr.svg"');
    }

    // Single ID card PDF
    public function printCard(Member $member)
    {
        // Generate QR if not exists
        $qrPath = 'qrcodes/' . $member->qr_code . '.svg';
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($qrPath)) {
            $qrContent = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(200)->generate($member->qr_code);
            \Illuminate\Support\Facades\Storage::disk('public')->put($qrPath, $qrContent);
        }

        $qrBase64 = base64_encode(
            \Illuminate\Support\Facades\Storage::disk('public')->get($qrPath)
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.members.card',
            compact('member', 'qrBase64')
        )->setPaper([0, 0, 241.89, 153.07]); // CR80 card size in points (85.6mm x 54mm)

        return $pdf->download($member->member_id_card . '-id-card.pdf');
    }

    // Bulk print — multiple cards per page
    public function printCards(Request $request)
    {
        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'exists:members,id',
        ]);

        $members = Member::whereIn('id', $request->member_ids)->get();

        $members = $members->map(function ($member) {
            $qrPath = 'qrcodes/' . $member->qr_code . '.svg';
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($qrPath)) {
                $qrContent = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(200)->generate($member->qr_code);
                \Illuminate\Support\Facades\Storage::disk('public')->put($qrPath, $qrContent);
            }
            $member->qr_base64 = base64_encode(
                \Illuminate\Support\Facades\Storage::disk('public')->get($qrPath)
            );
            return $member;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.members.cards-bulk',
            compact('members')
        )->setPaper('a4', 'portrait');

        return $pdf->download('id-cards-' . now()->format('Y-m-d') . '.pdf');
    }

// Download import template
    public function downloadTemplate()
    {
        return Excel::download(
            new MembersTemplateExport(),
            'members-import-template.xlsx'
        );
    }

// Process Excel upload
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new MembersImport();
        Excel::import($import, $request->file('excel_file'));

        $message = "{$import->imported} members imported successfully.";
        if ($import->skipped > 0) {
            $message .= " {$import->skipped} rows skipped.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $import->errors);
    }
}

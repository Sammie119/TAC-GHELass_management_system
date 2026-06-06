<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CellGroup;
use App\Models\Member;
use Illuminate\Http\Request;

class CellGroupController extends Controller
{
    public function index()
    {
        $groups = CellGroup::withCount('members')
            ->with(['leader', 'assistantLeader'])
            ->latest()->paginate(20);

        $stats = [
            'total'    => CellGroup::count(),
            'active'   => CellGroup::where('status', 'active')->count(),
            'members'  => \DB::table('cell_group_members')->count(),
        ];

        return view('admin.cells.index', compact('groups', 'stats'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('first_name')->get();
        return view('admin.cells.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:100',
            'area'                 => 'nullable|string|max:100',
            'description'          => 'nullable|string',
            'leader_id'            => 'nullable|exists:members,id',
            'assistant_leader_id'  => 'nullable|exists:members,id',
            'meeting_day'          => 'nullable|string',
            'meeting_time'         => 'nullable',
            'meeting_venue'        => 'nullable|string|max:150',
            'status'               => 'required|in:active,inactive',
        ]);

        $group = CellGroup::create($validated);

        // Auto-add leader as member
        if ($validated['leader_id']) {
            $group->members()->syncWithoutDetaching([
                $validated['leader_id'] => [
                    'joined_date' => today(),
                    'is_leader'   => true,
                ],
            ]);
        }

        return redirect()->route('admin.cells.show', $group)
            ->with('success', 'Cell group created successfully.');
    }

    public function show(CellGroup $cell)
    {
        $cell->load(['leader', 'assistantLeader', 'members']);
        $allMembers = Member::where('status', 'active')
            ->orderBy('first_name')->get();

        return view('admin.cells.show', compact('cell', 'allMembers'));
    }

    public function edit(CellGroup $cell)
    {
        $members = Member::where('status', 'active')->orderBy('first_name')->get();
        return view('admin.cells.edit', compact('cell', 'members'));
    }

    public function update(Request $request, CellGroup $cell)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'area'                => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'leader_id'           => 'nullable|exists:members,id',
            'assistant_leader_id' => 'nullable|exists:members,id',
            'meeting_day'         => 'nullable|string',
            'meeting_time'        => 'nullable',
            'meeting_venue'       => 'nullable|string|max:150',
            'status'              => 'required|in:active,inactive',
        ]);

        $cell->update($validated);

        return redirect()->route('admin.cells.show', $cell)
            ->with('success', 'Cell group updated.');
    }

    public function addMember(Request $request, CellGroup $cell)
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'joined_date' => 'nullable|date',
        ]);

        $cell->members()->syncWithoutDetaching([
            $request->member_id => [
                'joined_date' => $request->joined_date ?? today(),
                'is_leader'   => false,
            ],
        ]);

        return back()->with('success', 'Member added to cell group.');
    }

    public function removeMember(CellGroup $cell, Member $member)
    {
        $cell->members()->detach($member->id);
        return back()->with('success', 'Member removed from cell group.');
    }

    public function destroy(CellGroup $cell)
    {
        $cell->delete();
        return redirect()->route('admin.cells.index')
            ->with('success', 'Cell group deleted.');
    }
}

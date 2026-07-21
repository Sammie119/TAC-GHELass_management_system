<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DropdownOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DropdownOptionController extends Controller
{
    public const GROUPS = [
        'income_category' => 'Income Category',
        'expense_category' => 'Expense Category',
        'payment_method' => 'Payment Method',
        'currency' => 'Currency',
        'department' => 'Department',
    ];

    public function index(Request $request)
    {
        $query = DropdownOption::query()->orderBy('group')->orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('key', 'like', "%{$search}%")
                ->orWhere('label', 'like', "%{$search}%"));
        }

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $options = $query->paginate(20)->withQueryString();
        $groups = self::GROUPS;

        return view('admin.settings.dropdowns', compact('options', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateOption($request);
        $data = $this->prepareData($validated);
        $this->assertUnique($data['group'], $data['key']);

        DropdownOption::create($data);

        return back()->with('success', 'Dropdown option added.');
    }

    public function update(Request $request, DropdownOption $dropdownOption)
    {
        $validated = $this->validateOption($request);
        $data = $this->prepareData($validated);
        $this->assertUnique($data['group'], $data['key'], $dropdownOption->id);

        $dropdownOption->update($data);

        return back()->with('success', 'Dropdown option updated.');
    }

    public function destroy(DropdownOption $dropdownOption)
    {
        $dropdownOption->delete();

        return back()->with('success', 'Dropdown option deleted.');
    }

    private function validateOption(Request $request): array
    {
        return $request->validate([
            'group' => ['required', Rule::in(array_keys(self::GROUPS))],
            'key' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'rate' => 'nullable|numeric|min:0.0001',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function prepareData(array $validated): array
    {
        $group = $validated['group'];

        $key = match ($group) {
            'department' => trim($validated['label']),
            'currency' => strtoupper(trim($validated['key'])),
            default => strtolower(preg_replace('/[^a-z0-9_]/', '_', trim($validated['key']))),
        };

        $sortOrder = $validated['sort_order'] ?? ((int) DropdownOption::where('group', $group)->max('sort_order') + 1);

        return [
            'group' => $group,
            'key' => $key,
            'label' => trim($validated['label']),
            'meta' => $group === 'currency' ? [
                'symbol' => $validated['symbol'] ?? '',
                'rate' => (float) ($validated['rate'] ?? 1),
            ] : null,
            'sort_order' => $sortOrder,
            'is_active' => $validated['is_active'] ?? true,
        ];
    }

    private function assertUnique(string $group, string $key, ?int $ignoreId = null): void
    {
        $exists = DropdownOption::where('group', $group)
            ->where('key', $key)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'key' => "A \"{$key}\" option already exists in this group.",
            ]);
        }
    }
}

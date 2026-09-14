<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SundayTithesImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows
{
    public array $errors  = [];
    public array $entries = [];
    public int   $imported = 0;
    public int   $skipped  = 0;

    public function collection(Collection $rows)
    {
        $validCategories = array_keys(config('finance.income_categories'));

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            $identifier  = trim((string) ($row['member_id_card_or_tacms_number'] ?? ''));
            $amountRaw   = $row['amount'] ?? null;
            $categoryRaw = trim((string) ($row['category'] ?? ''));
            $notes       = trim((string) ($row['notes'] ?? '')) ?: null;

            if ($identifier === '' && $categoryRaw === '' && ($amountRaw === null || $amountRaw === '')) {
                continue;
            }

            if (!is_numeric($amountRaw) || (float) $amountRaw <= 0) {
                $this->errors[] = "Row {$rowNum}: Amount must be a positive number — skipped.";
                $this->skipped++;
                continue;
            }

            $memberId = null;
            if ($identifier !== '') {
                $member = Member::where('member_id_card', $identifier)->first()
                    ?: Member::where('tacms_number', $identifier)->first();

                if ($member) {
                    $memberId = $member->id;
                } else {
                    $this->errors[] = "Row {$rowNum}: Member ID card or TACMS number '{$identifier}' not found — recorded as anonymous.";
                }
            }

            $category = $categoryRaw !== '' ? strtolower(str_replace(' ', '_', $categoryRaw)) : 'tithe';
            if (!in_array($category, $validCategories, true)) {
                $this->errors[] = "Row {$rowNum}: Unknown category '{$categoryRaw}' — defaulted to 'tithe'.";
                $category = 'tithe';
            }

            $this->entries[] = [
                'member_id' => $memberId,
                'amount'    => (float) $amountRaw,
                'category'  => $category,
                'notes'     => $notes,
            ];
            $this->imported++;
        }
    }
}

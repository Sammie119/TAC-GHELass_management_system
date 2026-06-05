<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MembersImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows
{
    public array $errors   = [];
    public int   $imported = 0;
    public int   $skipped  = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            // Skip completely empty rows
            if (empty($row['first_name']) && empty($row['last_name'])) {
                continue;
            }

            // Validate required fields
            if (empty($row['first_name'])) {
                $this->errors[] = "Row {$rowNum}: First name is required.";
                $this->skipped++;
                continue;
            }

            if (empty($row['last_name'])) {
                $this->errors[] = "Row {$rowNum}: Last name is required.";
                $this->skipped++;
                continue;
            }

            // Check duplicate phone
            if (!empty($row['phone']) && Member::where('phone', trim($row['phone']))->exists()) {
                $this->errors[] = "Row {$rowNum}: Phone {$row['phone']} already exists — skipped.";
                $this->skipped++;
                continue;
            }

            // Check duplicate email
            if (!empty($row['email']) && Member::where('email', trim($row['email']))->exists()) {
                $this->errors[] = "Row {$rowNum}: Email {$row['email']} already exists — skipped.";
                $this->skipped++;
                continue;
            }

            // Check duplicate TACMS
            if (!empty($row['tacms_number']) && Member::where('tacms_number', trim($row['tacms_number']))->exists()) {
                $this->errors[] = "Row {$rowNum}: TACMS {$row['tacms_number']} already exists — skipped.";
                $this->skipped++;
                continue;
            }

            try {
                $member = Member::create([
                    'first_name'    => trim($row['first_name']),
                    'last_name'     => trim($row['last_name']),
                    'phone'         => !empty($row['phone'])      ? trim($row['phone'])       : null,
                    'email'         => !empty($row['email'])      ? strtolower(trim($row['email'])) : null,
                    'gender'        => !empty($row['gender'])     ? strtolower(trim($row['gender'])) : null,
                    'date_of_birth' => !empty($row['date_of_birth']) ? $this->parseDate($row['date_of_birth']) : null,
                    'address'       => !empty($row['address'])    ? trim($row['address'])     : null,
                    'department'    => !empty($row['department'])  ? trim($row['department'])  : null,
                    'tacms_number'  => !empty($row['tacms_number']) ? trim($row['tacms_number']) : null,
                    'status'        => !empty($row['status'])     ? strtolower(trim($row['status'])) : 'active',
                ]);

                $qrContent = QrCode::format('svg')->size(200)->generate($member->qr_code);
                Storage::disk('public')->put('qrcodes/' . $member->qr_code . '.svg', $qrContent);

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNum}: Error — " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        try {
            // Handle Excel numeric dates
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersTemplateExport implements
    FromArray, WithHeadings, WithStyles,
    ShouldAutoSize, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['John',  'Mensah',   '0244000001', 'john@example.com',  'Male',   '1990-01-15', '12 Church St, Accra', 'Choir / Music Ministry', 'TAC00ABC010101', 'active'],
            ['Abena', 'Asante',   '0244000002', 'abena@example.com', 'Female', '1985-06-20', '5 Peace Ave, Kumasi', 'Women Ministry',         'TAC00ABC010102', 'active'],
            ['Kofi',  'Boateng',  '0244000003', '',                  'Male',   '',           '',                    'Youth Ministry',          '',          'active'],
        ];
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'phone',
            'email',
            'gender',
            'date_of_birth',
            'address',
            'department',
            'tacms_number',
            'status',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 15, 'C' => 16,
            'D' => 25, 'E' => 10, 'F' => 15,
            'G' => 25, 'H' => 25, 'I' => 15, 'J' => 10,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Style header row
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 12,
            ],
            'fill' => [
                'fillType'   => 'solid',
                'startColor' => ['argb' => 'FF2563EB'],
            ],
        ]);

        // Add notes row
        $sheet->setCellValue('A5', '← Required');
        $sheet->setCellValue('B5', '← Required');
        $sheet->setCellValue('C5', 'Optional');
        $sheet->setCellValue('D5', 'Optional');
        $sheet->setCellValue('E5', 'male/female');
        $sheet->setCellValue('F5', 'YYYY-MM-DD');
        $sheet->setCellValue('G5', 'Optional');
        $sheet->setCellValue('H5', 'Must match system');
        $sheet->setCellValue('I5', 'Optional');
        $sheet->setCellValue('J5', 'active/inactive');

        $sheet->getStyle('A5:J5')->applyFromArray([
            'font'  => ['italic' => true, 'color' => ['argb' => 'FF9CA3AF']],
            'fill'  => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFF9FAFB']],
        ]);

        // Highlight required columns
        $sheet->getStyle('A1:B1')->applyFromArray([
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1D4ED8']],
        ]);

        return [];
    }

    public function title(): string { return 'Members Import'; }
}

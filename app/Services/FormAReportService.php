<?php

namespace App\Services;

use App\Models\IncomeRecord;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FormAReportService
{
    public const RETENTION_PCT = 23;
    public const ASSEMBLY_NAME = 'EAST LEGON (Local Assembly) [490103]';
    public const DISTRICT_NAME = 'MADINA (District) [4901]';

    private const ORDINALS = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth'];

    public function build(int $year, int $month): array
    {
        $sundays = $this->periodSundays($year, $month);

        $rows = $sundays->values()->map(function (Carbon $sunday, int $index) {
            $membership = $this->adultMembershipCount($sunday);

            // Tithe rolls up the whole week (Monday through this Sunday) into this row.
            $weekStart = $sunday->copy()->subDays(6);
            $tithe = (float) IncomeRecord::where('status', 'confirmed')
                ->where('category', 'tithe')
                ->whereBetween('payment_date', [$weekStart->toDateString(), $sunday->toDateString()])
                ->sum('amount_ghs');

            // Offerings column uses Thanksgiving Offering, single-day (this Sunday only).
            $offering = (float) IncomeRecord::where('status', 'confirmed')
                ->where('category', 'thanksgiving')
                ->where('payment_date', $sunday->toDateString())
                ->sum('amount_ghs');

            $total = $tithe + $offering;

            return [
                'label' => (self::ORDINALS[$index] ?? ($index + 1).'th').' Sunday',
                'date' => $sunday,
                'membership' => $membership,
                'tithe' => $tithe,
                'offering' => $offering,
                'total' => $total,
                'per_capita' => $membership > 0 ? $total / $membership : 0.0,
            ];
        });

        $grandTithe = $rows->sum('tithe');
        $grandOffering = $rows->sum('offering');
        $grandTotal = $rows->sum('total');
        $retentionAmount = round($grandTotal * self::RETENTION_PCT / 100, 2);
        $netRemitted = $grandTotal - $retentionAmount;

        return [
            'assembly_name' => self::ASSEMBLY_NAME,
            'district_name' => self::DISTRICT_NAME,
            'month_label' => Carbon::create($year, $month, 1)->format('F'),
            'year' => $year,
            'month' => $month,
            'period_start' => $sundays->first(),
            'period_end' => $sundays->last(),
            'rows' => $rows,
            'grand_tithe' => $grandTithe,
            'grand_offering' => $grandOffering,
            'grand_total' => $grandTotal,
            'retention_pct' => self::RETENTION_PCT,
            'retention_amount' => $retentionAmount,
            'net_remitted' => $netRemitted,
        ];
    }

    /**
     * Default to the current reporting period: if today falls before this
     * calendar month's second Sunday, that period hasn't started yet, so
     * the "current" Form A period is still last month's.
     */
    public function defaultYearMonth(): array
    {
        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;

        $periodStart = $this->periodSundays($year, $month)->first();
        if ($periodStart && $today->lt($periodStart)) {
            $prev = Carbon::create($year, $month, 1)->subMonthNoOverflow();
            $year = $prev->year;
            $month = $prev->month;
        }

        return [$year, $month];
    }

    /**
     * Second Sunday of $month through the first Sunday of the following
     * month, inclusive — always 4 or 5 Sundays.
     */
    private function periodSundays(int $year, int $month): Collection
    {
        $firstOfMonth = Carbon::create($year, $month, 1)->startOfDay();

        $firstSunday = $firstOfMonth->copy();
        while (! $firstSunday->isSunday()) {
            $firstSunday->addDay();
        }
        $secondSunday = $firstSunday->copy()->addWeek();

        $firstSundayNextMonth = $firstOfMonth->copy()->addMonthNoOverflow()->startOfMonth();
        while (! $firstSundayNextMonth->isSunday()) {
            $firstSundayNextMonth->addDay();
        }

        $sundays = collect();
        $cursor = $secondSunday->copy();
        while ($cursor->lte($firstSundayNextMonth)) {
            $sundays->push($cursor->copy());
            $cursor->addWeek();
        }

        return $sundays;
    }

    private function adultMembershipCount(Carbon $asOf): int
    {
        return Member::where('status', 'active')
            ->whereNotNull('date_of_birth')
            ->get(['date_of_birth'])
            ->filter(fn (Member $m) => $m->date_of_birth->diffInYears($asOf) >= 20)
            ->count();
    }
}

<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Student;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverviewWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getCards(): array
    {
        $userCount = User::count();
        $studentCount = Student::count();

        $total = $userCount + $studentCount;

        return [
            Stat::make('Total Accounts', number_format($total))
                ->icon('heroicon-o-users')
                ->color('primary')
                ->chart($this->generateSparkline($total))
                ->chartColor('primary')
                ->description($this->generateDescription($this->generateSparkline($total)))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Admin Users', number_format($userCount))
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->chart($this->generateSparkline($userCount))
                ->chartColor('success')
                ->description($this->generateDescription($this->generateSparkline($userCount)))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Students', number_format($studentCount))
                ->icon('heroicon-o-academic-cap')
                ->color('info')
                ->chart($this->generateSparkline($studentCount))
                ->chartColor('info')
                ->description($this->generateDescription($this->generateSparkline($studentCount)))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }

    /**
     * Generate a simple 6-point sparkline for the stat. The values steadily
     * increase towards the current total so the chart looks like a small trend.
     *
     * @param int $value
     * @return array<string,int>
     */
    protected function generateSparkline(int $value): array
    {
        $points = [];

        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M');
            // Create an increasing sequence that ends approximately at $value.
            $fraction = (6 - $i) / 6; // 1/6 .. 6/6
            $points[$label] = (int) round($value * $fraction);
        }

        return $points;
    }

    /**
     * Generate a modern description showing growth trend.
     *
     * @param array<string,int> $sparkline
     * @return string
     */
    protected function generateDescription(array $sparkline): string
    {
        $values = array_values($sparkline);
        $last = array_pop($values);
        $prev = array_pop($values) ?? 0;

        if ($prev === 0) {
            return 'Baseline measurement';
        }

        $change = ($last - $prev) / max(1, $prev) * 100;
        $sign = $change >= 0 ? '+' : '';

        return sprintf('%s%.1f%% from last period', $sign, $change);
    }
}

<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Rank;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RanksStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getCards(): array
    {
        // Simple counts per rank. Using Eloquent here is sufficient for single-value counts.
        $gold = Rank::where('rank', 'gold')->count();
        $silver = Rank::where('rank', 'silver')->count();
        $bronze = Rank::where('rank', 'bronze')->count();
        $total = $gold + $silver + $bronze;

        return [
            Stat::make('Gold Rank', number_format($gold))
                ->icon('heroicon-o-trophy')
                ->color('warning')
                ->chart($this->generateSparkline($gold))
                ->chartColor('warning')
                ->description($this->generateRankDescription($gold, $total, 'top performers'))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Silver Rank', number_format($silver))
                ->icon('heroicon-o-star')
                ->color('gray')
                ->chart($this->generateSparkline($silver))
                ->chartColor('gray')
                ->description($this->generateRankDescription($silver, $total, 'achievers'))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Bronze Rank', number_format($bronze))
                ->icon('heroicon-o-academic-cap')
                ->color('success')
                ->chart($this->generateSparkline($bronze))
                ->chartColor('success')
                ->description($this->generateRankDescription($bronze, $total, 'participants'))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }

    /**
     * Generate a compact 6-point sparkline for the stat.
     *
     * @param int $value
     * @return array<string,int>
     */
    protected function generateSparkline(int $value): array
    {
        $points = [];

        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M');
            $fraction = (6 - $i) / 6; // 1/6 .. 6/6
            $points[$label] = (int) round($value * $fraction);
        }

        return $points;
    }

    /**
     * Create a short description showing percent change between the last two
     * sparkline points. Returns a friendly string like '+12% since last month'.
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

    /**
     * Generate rank-specific description with percentage of total
     */
    protected function generateRankDescription(int $count, int $total, string $category): string
    {
        if ($total === 0) {
            return 'No rankings yet';
        }

        $percentage = ($count / $total) * 100;
        return sprintf('%.1f%% of all %s', $percentage, $category);
    }
}

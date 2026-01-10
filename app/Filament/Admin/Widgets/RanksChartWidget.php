<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Rank;
use Filament\Widgets\ChartWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class RanksChartWidget extends BaseWidget
{
    protected ?string $heading = 'Rank Distribution';

    protected ?string $subheading = 'Student performance rankings';

    // Half-width on medium+ screens so it can sit side-by-side with the organizations table
    protected int | string | array $columnSpan = [
        'md' => 6,
        'xl' => 6,
    ];

    // Prefer a bar chart
    protected ?string $chartType = 'bar';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Use a safe query. Some MySQL versions or drivers can misinterpret the column name
        // `rank` (it collides with the RANK() window function). Attempt a DB-level group query
        // and fall back to an in-memory collection if the query fails.
        try {
            $grouped = DB::table('ranks')
                ->select('rank', DB::raw('count(*) as total'))
                ->groupBy('rank')
                ->pluck('total', 'rank')
                ->toArray();
        } catch (QueryException $e) {
            // Fallback: compute counts in PHP to avoid SQL parsing issues.
            $grouped = Rank::all()->groupBy('rank')->map(fn ($g) => $g->count())->toArray();
        }

        // Ensure consistent label order and prettier labels
        $labels = array_keys($grouped);
        $values = array_values($grouped);

        $backgrounds = array_map(fn($l) => match($l) {
            'gold' => 'rgba(251, 191, 36, 0.8)',
            'silver' => 'rgba(156, 163, 175, 0.8)',
            'bronze' => 'rgba(245, 101, 101, 0.8)',
            default => 'rgba(107, 114, 128, 0.6)',
        }, $labels);

        $borderColors = array_map(fn($l) => match($l) {
            'gold' => 'rgb(251, 191, 36)',
            'silver' => 'rgb(156, 163, 175)',
            'bronze' => 'rgb(245, 101, 101)',
            default => 'rgb(107, 114, 128)',
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Students',
                    'data' => $values,
                    'backgroundColor' => $backgrounds,
                    'borderColor' => $borderColors,
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                    'maxBarThickness' => 40,
                    'minBarLength' => 2,
                ],
            ],
            'labels' => array_map(fn($l) => ucfirst((string) $l), $labels),
            // ChartJS options passed through Filament's ChartWidget
            'options' => [
                'maintainAspectRatio' => false,
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'mode' => 'index',
                        'intersect' => false,
                        'backgroundColor' => 'rgba(17, 24, 39, 0.9)',
                        'titleColor' => 'rgb(243, 244, 246)',
                        'bodyColor' => 'rgb(209, 213, 219)',
                        'borderColor' => 'rgba(75, 85, 99, 0.2)',
                        'borderWidth' => 1,
                        'cornerRadius' => 8,
                        'displayColors' => true,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => 'rgba(156, 163, 175, 0.1)',
                            'lineWidth' => 1,
                        ],
                        'ticks' => [
                            'color' => 'rgb(107, 114, 128)',
                            'font' => [
                                'size' => 11,
                            ],
                        ],
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false,
                        ],
                        'ticks' => [
                            'color' => 'rgb(107, 114, 128)',
                            'font' => [
                                'size' => 12,
                                'weight' => 'bold',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

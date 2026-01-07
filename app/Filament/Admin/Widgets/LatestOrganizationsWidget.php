<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Collection;

class LatestOrganizationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Organizations';

    // Half-width on medium+ screens so it can sit side-by-side with the chart widget
    protected int | string | array $columnSpan = [
        'md' => 6,
        'xl' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(Organization::query()->withCount('evaluations')->orderBy('evaluations_count', 'desc')->limit(4))
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->rounded()
                    // smaller logo for compact rows
                    ->height(32)
                    ->width(32)
                    ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800 px-1 py-1']),

                TextColumn::make('name')
                    ->label('Name')
                    ->weight(FontWeight::SemiBold)
                    // do not truncate name; allow wrapping and show full name on hover
                    ->wrap()
                    ->extraAttributes(['class' => 'text-sm px-2 py-1'])
                    ->tooltip(fn ($record) => $record->name),

                BadgeColumn::make('evaluations_count')
                    ->label('Evaluations')
                    ->sortable()
                    ->colors([
                        'danger' => fn($state): bool => (int) $state === 0,
                        'warning' => fn($state): bool => (int) $state > 0 && (int) $state <= 3,
                        'success' => fn($state): bool => (int) $state > 3,
                    ])
                    ->formatStateUsing(fn($state) => (int) $state)
                    ->extraAttributes(['class' => 'text-sm px-2 py-1 font-medium']),
            ])
            ->defaultSort('evaluations_count', 'desc')
            ->bulkActions([])
            ->filters([])
            ->headerActions([
                Action::make('view_all')
                    ->label('View All Organizations')
                    ->icon('heroicon-o-eye')
                    ->url(\App\Filament\Admin\Resources\Organizations\OrganizationResource::getUrl('index')),
            ])
            ->paginated(false);
    }
}

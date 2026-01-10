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

    protected ?string $subheading = 'Organizations with most evaluations';

    // Half-width on medium+ screens so it can sit side-by-side with the chart widget
    protected int | string | array $columnSpan = [
        'md' => 6,
        'xl' => 6,
    ];

    protected ?string $maxHeight = '300px';

    public function table(Table $table): Table
    {
        return $table
            ->query(Organization::query()->withCount('evaluations')->orderBy('evaluations_count', 'desc')->limit(4))
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->extraAttributes([
                        'class' => 'ring-2 ring-white dark:ring-gray-800 shadow-sm'
                    ]),

                TextColumn::make('name')
                    ->label('Organization')
                    ->weight(FontWeight::Medium)
                    ->size('sm')
                    ->color('gray')
                    ->wrap()
                    ->tooltip(fn ($record) => $record->name),
            ])
            ->defaultSort('evaluations_count', 'desc')
            ->bulkActions([])
            ->filters([])
            ->headerActions([
                Action::make('view_all')
                    ->label('View All')
                    ->icon('heroicon-o-arrow-right')
                    ->color('gray')
                    ->outlined()
                    ->size('sm')
                    ->url(\App\Filament\Admin\Resources\Organizations\OrganizationResource::getUrl('index')),
            ])
            ->paginated(false);
    }
}

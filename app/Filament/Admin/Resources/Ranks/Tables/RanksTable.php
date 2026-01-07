<?php

namespace App\Filament\Admin\Resources\Ranks\Tables;

use App\Models\Organization;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Ranks table configuration for Filament admin panel.
 * Displays student rankings with evaluation scores and filters.
 */
class RanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make('Student', [
                    ImageColumn::make('student.image')
                        ->circular()
                        ->size(50)
                        ->grow(false)
                        ->label(' ')
                        ->alignCenter()
                        ->url(fn ($record) => ($record->student && $record->student->image) ? Storage::url($record->student->image) : null)
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode(optional($record->student)->name ?? 'Student') . '&color=7F9CF5&background=EBF4FF')
                        ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800']),
                    TextColumn::make('student.name')
                        ->weight('medium')
                        ->searchable(),
                    TextColumn::make('position')
                        ->getStateUsing(function ($record) {
                            $pivot = $record->student->evaluations()
                                ->where('evaluation_id', $record->evaluation_id)
                                ->first()?->pivot;
                            return $pivot?->position ?? 'Member';
                        })
                        ->color('gray')
                        ->size('sm'),
                ]),
                ColumnGroup::make('Organization', [
                    TextColumn::make('organization.name')
                        ->searchable(),
                    TextColumn::make('evaluation.year')
                        ->label('Evaluation Year')
                        ->searchable(),
                ]),
                ColumnGroup::make('Results', [
                    TextColumn::make('final_score')
                        ->numeric(decimalPlaces: 2)
                        ->placeholder('Pending')
                        ->label('Final Score')
                        ->alignCenter(),
                    BadgeColumn::make('rank')
                        ->colors([
                            'warning' => 'gold',
                            'gray' => 'silver',
                            'orange' => 'bronze',
                            'danger' => 'none',
                        ])
                        ->formatStateUsing(fn (?string $state): string => match($state) {
                            'gold' => 'Gold',
                            'silver' => 'Silver',
                            'bronze' => 'Bronze',
                            'none' => 'None',
                            default => 'Pending'
                        })
                        ->label('Rank')
                        ->alignCenter(),

                ]),
                ColumnGroup::make('Status', [

                    BadgeColumn::make('status')
                        ->colors([
                            'success' => 'finalized',
                            'warning' => 'pending',
                        ])
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->label(' ')
                        ->alignCenter(),
                ]),
            ])
            ->filters([
                SelectFilter::make('rank')
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                        'bronze' => 'Bronze',
                        'none' => 'None',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'finalized' => 'Finalized',
                        'pending' => 'Pending',
                    ]),

                SelectFilter::make('organization_id')
                    ->relationship('organization', 'name')
                    ->label('Organization'),

                SelectFilter::make('year')
                    ->options(function () {
                        return \App\Models\Evaluation::distinct('year')
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->whereHas('evaluation', function ($q) use ($data) {
                                $q->where('year', $data['value']);
                            });
                        }
                    }),
            ])
            ->emptyStateHeading('No rankings yet')
            ->emptyStateDescription('Rankings will appear here once evaluations are completed.');
    }
}

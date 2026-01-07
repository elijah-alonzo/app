<?php

namespace App\Filament\Admin\Resources\Evaluations\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    // Organization image
                    ImageColumn::make('organization.logo')
                        ->circular()
                        ->size(80)
                        ->defaultImageUrl(function ($record) {
                            $orgName = $record->organization->name ?? 'Organization';
                            return 'https://ui-avatars.com/api/?name=' . urlencode($orgName) . '&color=7F9CF5&background=EBF4FF';
                        })
                        ->grow(false),

                    Stack::make([
                        // Organization name above evaluation name and adviser
                        TextColumn::make('organization.name')
                            ->weight(FontWeight::Bold)
                            ->size('md')
                            ->label('Organization')
                            ->color('emerald'),

                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable()
                            ->wrap(),

                        TextColumn::make('user.name')
                            ->label('Adviser')
                            ->color('gray')
                            ->prefix('Adviser: ')
                            ->size('sm'),

                        Split::make([
                            TextColumn::make('year')
                                ->badge()
                                ->color('primary')
                                ->grow(false),

                            TextColumn::make('students_count')
                                ->counts('students')
                                ->badge()
                                ->color('success')
                                ->suffix(' students')
                                ->grow(false),
                        ])->from('sm'),
                    ])->space(1),
                ])
                ->from('md'),
            ])
            ->filters([
                // Filter by owning organization or its department
                SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name'),

                SelectFilter::make('year')
                    ->label('Academic Year')
                    ->options(function () {
                        return \App\Models\Evaluation::distinct()->pluck('year', 'year')->toArray();
                    }),
            ])
            ->recordActions([
                // Actions hidden as requested
            ])
            ->bulkActions([
                // Removed bulk actions to eliminate checkboxes
            ])
            ->searchable()
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 1,
                'lg' => 2,
                'xl' => 3,
            ]);
    }
}

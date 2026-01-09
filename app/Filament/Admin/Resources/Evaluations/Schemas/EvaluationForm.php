<?php

namespace App\Filament\Admin\Resources\Evaluations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evaluation Details')
                    ->description('Configure the evaluation period and organization settings')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('organization_id')
                                    ->label('Organization')
                                    ->prefixIcon('heroicon-m-building-office')
                                    ->options(function () {
                                        $user = auth()->user();
                                        if ($user && $user->organization_id) {
                                            return \App\Models\Organization::where('id', $user->organization_id)
                                                ->pluck('name', 'id');
                                        }
                                        return \App\Models\Organization::pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Select an option')
                                    ->helperText('Choose the organization for this evaluation'),

                                TextInput::make('year')
                                    ->label('Academic Year')
                                    ->prefixIcon('heroicon-m-calendar-days')
                                    ->required()
                                    ->placeholder('e.g., 2024-2025')
                                    ->helperText('Enter academic year range (e.g., 2024-2025)')
                                    ->default(date('Y') . '-' . (date('Y') + 1))
                                    ->unique(
                                        table: 'evaluations',
                                        column: 'year',
                                        ignoreRecord: true,
                                        modifyRuleUsing: function ($rule, $get) {
                                            return $rule->where('organization_id', $get('organization_id'));
                                        }
                                    )
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),
                            ]),

                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ]),
            ]);
    }
}

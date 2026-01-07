<?php

namespace App\Filament\Admin\Resources\Evaluations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evaluation Details')
                    ->schema([
                        Select::make('organization_id')
                            ->label('Organization')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user && $user->organization_id) {
                                    return \App\Models\Organization::where('id', $user->organization_id)
                                        ->pluck('name', 'id');
                                }
                                return \App\Models\Organization::pluck('name', 'id');
                            })
                            ->required(),
                        TextInput::make('year')
                            ->label('Academic Year')
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
                            ),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}

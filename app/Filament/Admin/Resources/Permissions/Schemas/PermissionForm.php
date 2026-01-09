<?php

namespace App\Filament\Admin\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permission Information')
                    ->description('Define permission details and settings')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Permission Name')
                                    ->prefixIcon('heroicon-m-key')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(125)
                                    ->placeholder('Enter permission name')
                                    ->helperText('Unique name for this permission (e.g., "manage users")')
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),

                                TextInput::make('guard_name')
                                    ->default('web')
                                    ->label('Guard Name')
                                    ->prefixIcon('heroicon-m-shield-check')
                                    ->required()
                                    ->maxLength(125)
                                    ->placeholder('web')
                                    ->helperText('Authentication guard (usually "web")')
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),
                            ]),
                    ]),
            ]);
    }
}

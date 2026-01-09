<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Information')
                    ->description('Define role details and basic settings')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Role Name')
                                    ->prefixIcon('heroicon-m-shield-check')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(125)
                                    ->disabled(fn ($record) => $record && $record->name === 'Admin')
                                    ->placeholder('Enter role name')
                                    ->helperText(fn ($record) =>
                                        $record && $record->name === 'Admin'
                                            ? 'Admin role name cannot be changed to prevent system lockout'
                                            : 'Unique name for this role'
                                    )
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),

                                TextInput::make('guard_name')
                                    ->default('web')
                                    ->label('Guard Name')
                                    ->prefixIcon('heroicon-m-key')
                                    ->required()
                                    ->maxLength(125)
                                    ->placeholder('web')
                                    ->helperText('Authentication guard (usually "web")')
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),
                            ]),
                    ]),

                Section::make('Permissions')
                    ->description('Assign permissions to this role')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->helperText('Select permissions for this role')
                            ->hiddenLabel(),
                    ]),
            ]);
    }
}

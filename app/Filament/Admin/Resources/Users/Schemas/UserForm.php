<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Account Information Section
                Section::make('Account Information')
                    ->description('Manage user account details and organization settings')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        // Full Name - Full Width
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-user')
                            ->extraAttributes([
                                'style' => 'font-size: 18px;'
                            ])
                            ->columnSpanFull(),

                        // School ID Number - Full Width
                        TextInput::make('school_number')
                            ->label('School ID Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-identification')
                            ->extraAttributes([
                                'style' => 'font-size: 16px;'
                            ])
                            ->columnSpanFull(),

                        // Email Address - Full Width
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191)
                            ->prefixIcon('heroicon-m-envelope')
                            ->extraAttributes([
                                'style' => 'font-size: 16px;'
                            ])
                            ->columnSpanFull(),

                        // Roles - Full Width
                        Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->prefixIcon('heroicon-m-user-group')
                            ->placeholder('Select roles for this user')
                            ->columnSpanFull(),

                        // Organization - Full Width
                        Select::make('organization_id')
                            ->label('Organization')
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-m-building-office')
                            ->placeholder('Select organization')
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes([
                        'class' => 'mb-6'
                    ]),

                // Security & Privacy Section
                Section::make('Security & Privacy')
                    ->description('Set up password for the user account')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->prefixIcon('heroicon-m-key')
                                    ->placeholder('Minimum 8 characters')
                                    ->revealable(),

                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->prefixIcon('heroicon-m-shield-check')
                                    ->placeholder('Repeat password')
                                    ->revealable(),
                            ]),
                    ]),
            ]);
    }
}

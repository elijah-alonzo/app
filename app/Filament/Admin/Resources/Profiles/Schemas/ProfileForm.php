<?php

namespace App\Filament\Admin\Resources\Profiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Account Information Section
                Section::make('Account Information')
                    ->description('Manage your account details and profile information')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Full Name
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-user')
                                    ->extraAttributes([
                                        'style' => 'font-size: 18px;'
                                    ]),

                                // School ID Number
                                TextInput::make('school_number')
                                    ->label('School ID Number')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-identification')
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),

                                // Email Address
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px;'
                                    ]),

                                // Roles (Read Only)
                                Select::make('roles')
                                    ->label('Roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->disabled()
                                    ->prefixIcon('heroicon-m-user-group')
                                    ->placeholder('Select roles for this user'),

                                // Organization (Read Only)
                                Select::make('organization_id')
                                    ->label('Organization')
                                    ->relationship('organization', 'name')
                                    ->disabled()
                                    ->prefixIcon('heroicon-m-building-office'),
                            ]),
                    ])
                    ->extraAttributes([
                        'class' => 'mb-6'
                    ]),

                // Security & Privacy Section
                Section::make('Security & Privacy')
                    ->description('Update your password to keep your account secure')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('current_password')
                                    ->label('Current Password')
                                    ->password()
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-m-lock-closed')
                                    ->placeholder('Enter your current password')
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                if ($value && !Hash::check($value, auth()->user()->password)) {
                                                    $fail('Current password is incorrect.');
                                                }
                                            };
                                        },
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('password')
                                            ->label('New Password')
                                            ->password()
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                            ->minLength(8)
                                            ->same('password_confirmation')
                                            ->prefixIcon('heroicon-m-key')
                                            ->placeholder('Minimum 8 characters')
                                            ->revealable(),

                                        TextInput::make('password_confirmation')
                                            ->label('Confirm New Password')
                                            ->password()
                                            ->dehydrated(false)
                                            ->prefixIcon('heroicon-m-shield-check')
                                            ->placeholder('Repeat new password')
                                            ->revealable(),
                                    ]),
                            ])
                    ]),
            ]);
    }
}

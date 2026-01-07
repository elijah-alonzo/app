<?php

namespace App\Filament\Student\Resources\Profiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
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
                // Modern Profile Header Section
                Section::make('Account Information')
                    ->description('Manage your account details and profile information')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Profile Picture - Left Column
                                FileUpload::make('image')
                                    ->label('Profile Picture')
                                    ->image()
                                    ->directory('student-avatars')
                                    ->disk('public')
                                    ->maxSize(2048)
                                    ->imagePreviewHeight('200')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('300')
                                    ->imageResizeTargetHeight('300')
                                    ->loadingIndicatorPosition('center')
                                    ->panelLayout('integrated')
                                    ->removeUploadedFileButtonPosition('top-right')
                                    ->uploadButtonPosition('center')
                                    ->uploadProgressIndicatorPosition('center')
                                    ->columnSpan(1),

                                // Basic Info - Right Column
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Full Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-user')
                                            ->extraAttributes([
                                                'style' => 'font-size: 18px;'
                                            ]),

                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-envelope')
                                            ->extraAttributes([
                                                'style' => 'font-size: 16px;'
                                            ]),
                                    ])
                                    ->columnSpan(1),
                            ]),

                        // About Me - Full Width Below
                        Grid::make(1)
                            ->schema([
                                Textarea::make('description')
                                    ->label('About Me')
                                    ->rows(8)
                                    ->maxLength(1000)
                                    ->placeholder('Tell us about yourself, your interests, and your goals...')
                                    ->extraAttributes([
                                        'style' => 'resize: vertical;'
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes([
                        'class' => 'pb-6'
                    ]),

                // Security Section - Modern Card Style
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
                                                if ($value && !Hash::check($value, auth('student')->user()->password)) {
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
                    ])
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit')
                    ->extraAttributes([
                        'class' => 'mt-6'
                    ]),
            ]);
    }
}

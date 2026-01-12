<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Hash;

/**
 * Student form schema for Filament admin panel.
 * Handles student creation and editing with proper validation.
 */
class StudentForm
{
    /**
     * Configure the student form schema with validation and security.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Account Information Section
                Section::make('Account Information')
                    ->description('Manage student profile details and personal information')
                    ->columnSpanFull()
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

                                        TextInput::make('school_number')
                                            ->label('School Number')
                                            ->required()
                                            ->maxLength(50)
                                            ->prefixIcon('heroicon-m-identification')
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('Enter student ID number')
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
                    ]),

                // Security & Privacy Section
                Section::make('Security & Privacy')
                    ->description('Set up password for the student account')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('password')
                                            ->label('Password')
                                            ->password()
                                            ->required(fn (string $operation): bool => $operation === 'create')
                                            ->minLength(8)
                                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                            ->dehydrated(fn (?string $state): bool => filled($state))
                                            ->prefixIcon('heroicon-m-key')
                                            ->placeholder('Minimum 8 characters')
                                            ->helperText(fn (string $operation): string => $operation === 'create' ? 'Minimum 8 characters' : 'Leave blank to keep current password')
                                            ->revealable(),

                                        TextInput::make('password_confirmation')
                                            ->label('Confirm Password')
                                            ->password()
                                            ->required(fn (string $operation): bool => $operation === 'create')
                                            ->same('password')
                                            ->dehydrated(false)
                                            ->prefixIcon('heroicon-m-shield-check')
                                            ->placeholder('Repeat password')
                                            ->revealable(),
                                    ]),
                            ])
                    ]),
            ]);
    }
}

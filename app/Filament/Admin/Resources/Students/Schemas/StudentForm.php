<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
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
                Section::make('Student Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                // Profile picture upload (visible on create and edit)
                FileUpload::make('image')
                    ->label('Profile Picture')
                    ->image()
                    ->directory('students')
                    ->imagePreviewHeight(48)
                    ->maxSize(2048)
                    ->columnSpan(1),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Full Name')
                    ->columnSpan(2),

                TextInput::make('school_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->label('School Number'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Email Address'),

                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->label('Password')
                    ->helperText(fn (string $operation): string => $operation === 'create' ? 'Minimum 8 characters' : 'Leave blank to keep current password'),

                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->same('password')
                    ->dehydrated(false)
                    ->label('Confirm Password')
                    ->visible(fn (string $operation): bool => $operation === 'create' || filled(request()->input('data.password'))),
                    ]),
        ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Account Information Section - Modern Layout
            Section::make('Account Information')
                ->description('Your account details and organization settings')
                ->columnSpanFull()
                ->schema([
                    // Full Name - Full Width
                    TextEntry::make('name')
                        ->label('Name')
                        ->size('lg')
                        ->weight('bold')
                        ->color('primary')
                        ->icon('heroicon-m-user')
                        ->columnSpanFull(),

                    // School ID Number - Full Width
                    TextEntry::make('school_number')
                        ->label('School ID Number')
                        ->badge()
                        ->color('gray')
                        ->icon('heroicon-m-identification')
                        ->columnSpanFull(),

                    // Email Address - Full Width
                    TextEntry::make('email')
                        ->label('Email address')
                        ->copyable()
                        ->icon('heroicon-m-envelope')
                        ->color('info')
                        ->columnSpanFull(),

                    // Roles - Full Width
                    TextEntry::make('roles.name')
                        ->label('Roles')
                        ->badge()
                        ->separator(', ')
                        ->color('success')
                        ->icon('heroicon-m-user-group')
                        ->default('No roles assigned')
                        ->columnSpanFull(),

                    // Organization - Full Width
                    TextEntry::make('organization.name')
                        ->label('Organization')
                        ->badge()
                        ->color('primary')
                        ->icon('heroicon-m-building-office')
                        ->default('Not Assigned')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

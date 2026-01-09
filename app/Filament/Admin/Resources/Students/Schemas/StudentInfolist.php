<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

/**
 * InfoList schema for displaying student portfolio in the admin panel.
 * Based on the student panel's ProfileInfolist but adapted for admin use.
 */
class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Main Header Section - Name and Profile Picture
            Grid::make(3)
                ->schema([
                    // Left side - Name and basic info
                    Grid::make(1)
                        ->schema([
                            TextEntry::make('name')
                                ->label('')
                                ->hiddenLabel()
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->extraAttributes([
                                    'style' => 'font-size: 48px; line-height: 1.2;',
                                    'class' => 'text-gray-900 dark:text-gray-100'
                                ]),

                            TextEntry::make('description')
                                ->label('')
                                ->hiddenLabel()
                                ->extraAttributes([
                                    'style' => 'font-size: 16px; line-height: 1.6; margin-top: 24px;',
                                    'class' => 'text-gray-600 dark:text-gray-300'
                                ])
                                ->placeholder('Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here. Student\'s bio goes here.')
                        ])
                        ->columnSpan(2),

                    // Right side - Profile Picture
                    ImageEntry::make('image')
                        ->label('')
                        ->hiddenLabel()
                        ->height(200)
                        ->width(200)
                        ->circular()
                        ->alignCenter()
                        ->extraImgAttributes([
                            'style' => 'object-fit: cover; border: 4px solid #e5e7eb;'
                        ])
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'Student') . '&size=200&background=f3f4f6&color=374151')
                        ->columnSpan(1),
                ])
                ->extraAttributes([
                    'style' => 'padding: 40px 0; border-bottom: none;'
                ])
                ->columnSpanFull(),

            // Student Details Section
            Section::make('Student Information')
                ->description('Key student details and account information')
                ->icon('heroicon-o-identification')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('school_number')
                                ->label('Student ID')
                                ->weight(FontWeight::SemiBold)
                                ->size(TextSize::Medium)
                                ->icon('heroicon-o-identification')
                                ->iconColor('primary')
                                ->badge()
                                ->color('primary')
                                ->extraAttributes([
                                    'class' => 'font-mono tracking-wider'
                                ]),

                            TextEntry::make('email')
                                ->label('Email Address')
                                ->weight(FontWeight::Medium)
                                ->copyable()
                                ->icon('heroicon-o-envelope')
                                ->iconColor('gray')
                                ->color('gray')
                                ->extraAttributes([
                                    'class' => 'break-all'
                                ]),

                            TextEntry::make('created_at')
                                ->label('Member Since')
                                ->dateTime('M j, Y')
                                ->icon('heroicon-o-calendar-days')
                                ->iconColor('success')
                                ->color('success')
                                ->weight(FontWeight::Medium),
                        ])
                ])
                ->columnSpanFull(),

            // Participated Organizations Section
            \Filament\Infolists\Components\TextEntry::make('name')
                ->label('Participated Organizations')
                ->hiddenLabel()
                ->formatStateUsing(fn () => 'Participated Organizations')
                ->extraAttributes([
                    'style' => 'font-size: 24px; font-weight: bold; margin: 48px 0 32px 0;',
                    'class' => 'text-gray-900 dark:text-gray-100'
                ])
                ->columnSpanFull(),

            \Filament\Infolists\Components\RepeatableEntry::make('evaluations')
                ->label('')
                ->hiddenLabel()
                ->schema([
                    Section::make('')
                        ->schema([
                            ImageEntry::make('organization.logo')
                                ->label('')
                                ->hiddenLabel()
                                ->height(80)
                                ->width(80)
                                ->alignCenter()
                                ->extraImgAttributes([
                                    'style' => 'margin-bottom: 16px;'
                                ])
                                ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->organization->name ?? 'Org') . '&size=80&background=22c55e&color=ffffff&bold=true'),

                            TextEntry::make('organization.name')
                                ->label('')
                                ->hiddenLabel()
                                ->weight(FontWeight::Bold)
                                ->alignCenter()
                                ->extraAttributes(['style' => 'font-size: 16px; margin-bottom: 4px;']),

                            TextEntry::make('year')
                                ->label('')
                                ->hiddenLabel()
                                ->alignCenter()
                                ->color('gray')
                                ->extraAttributes(['style' => 'font-size: 14px; margin-bottom: 4px;']),

                            TextEntry::make('pivot.position')
                                ->label('')
                                ->hiddenLabel()
                                ->alignCenter()
                                ->color('gray')
                                ->extraAttributes(['style' => 'font-size: 14px;']),
                        ])
                        ->extraAttributes([
                            'style' => 'text-align: center;'
                        ])
                ])
                ->contained(false)
                ->grid([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                    'xl' => 4,
                ])
                ->extraAttributes([
                    'style' => 'gap: 24px;'
                ])
                ->columnSpanFull(),
        ]);
    }
}

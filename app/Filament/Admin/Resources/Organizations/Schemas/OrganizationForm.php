<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Organization Information')
                    ->description('Manage organization details and branding')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Logo - Left Column
                                FileUpload::make('logo')
                                    ->label('Organization Logo')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('organization-logos')
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
                                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'])
                                    ->helperText('Upload a logo for the organization (PNG, JPG, JPEG, SVG)')
                                    ->columnSpan(1),

                                // Organization Info - Right Column
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Organization Name')
                                            ->prefixIcon('heroicon-m-building-office-2')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Enter organization name')
                                            ->helperText('Full name of the organization')
                                            ->extraAttributes([
                                                'style' => 'font-size: 18px;'
                                            ]),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\Evaluations\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class EvaluationDetailsInfolist
{
    public static function configure($schema): mixed
    {
        return $schema->components([
            \Filament\Schemas\Components\Grid::make(2)
                ->schema([
                    \Filament\Schemas\Components\Section::make('Evaluation Details')
                        ->schema([
                            \Filament\Infolists\Components\ImageEntry::make('logo')
                                ->label('Logo')
                                ->height(64)
                                ->width(64)
                                ->alignCenter(),
                            \Filament\Infolists\Components\TextEntry::make('name')->label('Evaluation Name'),
                            \Filament\Infolists\Components\TextEntry::make('organization.name')->label('Organization'),
                            \Filament\Infolists\Components\TextEntry::make('year')->label('Academic Year'),
                            \Filament\Infolists\Components\TextEntry::make('description')->label('Description'),
                        ]),
                    \Filament\Schemas\Components\Section::make('Peer Evaluator Details')
                        ->schema([
                            // Add peer evaluator TextEntry/ImageEntry fields here
                        ]),
                ]),
        ]);
    }
}

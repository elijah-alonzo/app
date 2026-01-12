<?php

namespace App\Filament\Admin\Resources\Ranks;

use App\Filament\Admin\Resources\Ranks\Pages\ListRanks;
use App\Filament\Admin\Resources\Ranks\Pages\ViewRank;
use App\Filament\Admin\Resources\Ranks\Tables\RanksTable;
use App\Models\Rank;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RankResource extends Resource
{
    protected static ?string $model = Rank::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    protected static UnitEnum|string|null $navigationGroup = 'Council Management';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Rankings';

    protected static ?string $modelLabel = 'Rank';

    protected static ?string $pluralModelLabel = 'Rankings';

    protected static ?string $recordTitleAttribute = 'student.name';

    // Make resource read-only
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return RanksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRanks::route('/'),
        ];
    }
}

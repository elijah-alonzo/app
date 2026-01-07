<?php

namespace App\Filament\Admin\Resources\Profiles;

use App\Filament\Admin\Resources\Profiles\Pages\EditProfile;
use App\Filament\Admin\Resources\Profiles\Pages\IndexProfile;
use App\Filament\Admin\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Admin\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Admin\Resources\Profiles\Schemas\ProfileInfolist;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?int $navigationSort = 70;

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->id() === $record->id;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => IndexProfile::route('/'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }
}

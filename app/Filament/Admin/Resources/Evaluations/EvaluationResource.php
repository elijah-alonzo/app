<?php
namespace App\Filament\Admin\Resources\Evaluations;
use Filament\Infolists;
use Filament\Schemas\Schema;

use App\Filament\Admin\Resources\Evaluations\Pages\CreateEvaluation;
use App\Filament\Admin\Resources\Evaluations\Pages\EditEvaluation;
use App\Filament\Admin\Resources\Evaluations\Pages\ListEvaluation;
use App\Filament\Admin\Resources\Evaluations\Pages\ViewEvaluation;
use App\Filament\Admin\Resources\Evaluations\Pages;
use App\Filament\Admin\Resources\Evaluations\RelationManagers;
use App\Filament\Admin\Resources\Evaluations\Schemas\EvaluationForm;
use App\Filament\Admin\Resources\Evaluations\Tables\EvaluationsTable;
use App\Models\Evaluation;
use App\Models\EvaluationPeerEvaluator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;

class EvaluationResource extends Resource
{
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Evaluation Details')
                ->description('View evaluation period and organization information')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('organization.name')
                            ->label('Organization')
                            ->icon('heroicon-m-building-office')
                            ->weight('semibold'),

                        TextEntry::make('year')
                            ->label('Academic Year')
                            ->icon('heroicon-m-calendar-days')
                            ->weight('semibold'),
                    ]),

                    TextEntry::make('user.name')
                        ->label('Created by')
                        ->icon('heroicon-m-user')
                        ->color('gray'),
                ]),
        ]);
    }
    public static function form(Schema $schema): Schema
    {
        return EvaluationForm::configure($schema);
    }
    protected static ?string $model = Evaluation::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Evaluations';

    protected static ?string $modelLabel = 'Evaluation';

    protected static ?string $pluralModelLabel = 'Evaluations';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        // Allow access for admin role or users with manage-evaluations permission
        return auth()->user()?->hasRole('Admin') ||
               auth()->user()?->can('manage evaluations') ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit($record): bool
    {
        return static::canAccess();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false; // Only admin can delete
    }

    // Removed form() method to ensure InfoList is used for the view page

    public static function table(Table $table): Table
    {
        return EvaluationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'organization']);

        // Filter evaluations by user's organization
        $user = auth()->user();
        if ($user && $user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluation::route('/'),
            'create' => CreateEvaluation::route('/create'),
            'view' => ViewEvaluation::route('/{record}'),
            'edit' => EditEvaluation::route('/{record}/edit'),
            'evaluate-student' => Pages\EvaluateStudent::route('/{evaluation}/evaluate/{student}/{type}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['students', 'organization']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        // The evaluations table in some environments may not include 'name' or 'description'
        // columns. Restrict global search to related organization name to avoid SQL errors
        // when those columns are missing. If you add those columns later, restore them here.
        return ['organization.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Organization' => $record->organization?->name,
            'Year' => $record->year,
            'Students' => $record->students->count(),
        ];
    }
}

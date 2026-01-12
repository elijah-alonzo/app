<?php

namespace App\Filament\Admin\Resources\Students;

use App\Filament\Admin\Resources\Students\Pages\CreateStudent;
use App\Filament\Admin\Resources\Students\Pages\EditStudent;
use App\Filament\Admin\Resources\Students\Pages\ListStudents;
use App\Filament\Admin\Resources\Students\Pages\ViewStudent;
use App\Filament\Admin\Resources\Students\Schemas\StudentForm;
use App\Filament\Admin\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Admin\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/**
 * Filament resource for managing students in the admin panel.
 */
class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static UnitEnum|string|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    public static function canAccess(): bool
    {
        // Allow access for admin role or users with manage-students permission
        return auth()->user()?->hasRole('Admin') ||
               auth()->user()?->can('manage students') ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canView($record): bool
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

    /**
     * Configure the form schema for creating/editing students.
     */
    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    /**
     * Configure the infolist schema for viewing student portfolios.
     */
    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    /**
     * Configure the table for listing students.
     */
    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    /**
     * Get the pages available for this resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}

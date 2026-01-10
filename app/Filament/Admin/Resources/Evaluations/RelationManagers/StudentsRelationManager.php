<?php

namespace App\Filament\Admin\Resources\Evaluations\RelationManagers;

use App\Models\Student;
use App\Models\EvaluationPeerEvaluator;
use App\Models\EvaluationScore;
use App\Models\Evaluation;
use App\Filament\Admin\Resources\Evaluations\EvaluationResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

// Students Relation Manager: Manages student memberships and evaluations within organizations.
class StudentsRelationManager extends RelationManager
{

        protected static string $relationship = 'students';
        protected static ?string $recordTitleAttribute = 'name';

        /**
         * Get the evaluation score for a student and type (self, peer, adviser)
         */
        protected function getEvaluationScore(int $studentId, string $evaluatorType): string
        {
                $score = EvaluationScore::where('evaluation_id', $this->ownerRecord->id)
                    ->where('student_id', $studentId)
                    ->where('evaluator_type', $evaluatorType)
                    ->first();

                if ($score && $score->evaluator_score !== null) {
                    return number_format($score->evaluator_score, 2);
                }
            return '-';
        }



    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getTableColumns())
            ->headerActions($this->getHeaderActions())
            ->actions($this->getTableActions())
            ->filters([])
            ->bulkActions([])
            ->striped();
    }

    // Table columns configuration
    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\ColumnGroup::make('Student', [
                \Filament\Tables\Columns\ImageColumn::make('image')
                    ->label('Profile')
                    ->circular()
                    ->size(40)
                    ->url(fn ($record) => $record->image ? Storage::url($record->image) : null)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned'),
            ]),
            \Filament\Tables\Columns\ColumnGroup::make('Evaluation', [
                \Filament\Tables\Columns\TextColumn::make('self_score')
                    ->label('Self')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self')),
                \Filament\Tables\Columns\TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer')),
                \Filament\Tables\Columns\TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser')),
            ]),
        ];
    }

    // Header actions configuration
    protected function getHeaderActions(): array
    {
        return [
            AttachAction::make()
                ->label('Add Student')
                ->form($this->getAttachForm())
                ->preloadRecordSelect(),

        ];
    }

    // Row actions configuration
    protected function getTableActions(): array
    {
        $actions = [
            $this->getEditAction(),
            $this->getDetachAction(),
        ];
            // In view evaluation page, show Evaluate button
            if ($this->pageClass === \App\Filament\Admin\Resources\Evaluations\Pages\ViewEvaluation::class) {
                array_unshift($actions, $this->getDirectEvaluationAction());
            }
        return $actions;
    }



    // Direct evaluation action (defaults to adviser)
    protected function getDirectEvaluationAction(): Action
    {
        return Action::make('evaluate')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('primary')
            ->button()
            ->size('sm')
            ->url(fn ($record) => $this->getEvaluationUrl($record->id, 'adviser'))
            ->tooltip('Complete adviser evaluation for this student');
    }

    // Peer assignment action for assigning peer evaluatees (edit org page only)
    protected function createPeerAssignmentAction(): Action
    {
        return Action::make('assign_peer_evaluatees')
            ->label('Assign Peer Evaluatees')
            ->color('success')
            ->icon('heroicon-o-user-group')
            ->form([
                \Filament\Forms\Components\CheckboxList::make('peer_evaluatees')
                    ->label('Select Peer Evaluatees')
                    ->options(function ($record) {
                        // Get all students in the organization except the current student (evaluator)
                        // Only include students who are not already assigned as evaluatees to another evaluator
                        $allStudentIds = $this->ownerRecord->students()
                            ->where('students.id', '!=', $record->id)
                            ->pluck('students.id')
                            ->toArray();
                            $alreadyAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                                ->whereIn('evaluatee_student_id', $allStudentIds)
                                ->where('evaluator_student_id', '!=', $record->id)
                                ->pluck('evaluatee_student_id')
                                ->toArray();
                        // Get students not already assigned as evaluatees to another evaluator
                        $eligibleIds = array_diff($allStudentIds, $alreadyAssignedIds);
                        // Always include students already assigned to this evaluator (for editing)
                        $currentAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                            ->where('evaluator_student_id', $record->id)
                            ->pluck('evaluatee_student_id')
                            ->toArray();
                        $finalIds = array_unique(array_merge($eligibleIds, $currentAssignedIds));
                        return $this->ownerRecord->students()
                            ->whereIn('students.id', $finalIds)
                            ->pluck('name', 'students.id')
                            ->toArray();
                    })
                    ->default(function ($record) {
                            return EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                                ->where('evaluator_student_id', $record->id)
                                ->pluck('evaluatee_student_id')
                                ->toArray();
                    })
                    ->columns(2)
                    ->required(),
            ])
            ->action(function ($record, $data) {
                $this->assignPeerEvaluatees($record->id, $data['peer_evaluatees']);
            })
            ->modalHeading(fn ($record) => 'Assign Peer Evaluatees for ' . $record->name)
            ->modalWidth('lg');
    }

    // Combined Edit action for student position and peer evaluator assignment
    protected function getEditAction(): EditAction
    {
        return EditAction::make()
            ->form([
                TextInput::make('position')
                    ->label('Position')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-m-identification'),

                CheckboxList::make('peer_evaluatees')
                    ->label('Select Peer Evaluatees')
                    ->options(function ($record) {
                        // Get all students in the organization except the current student (evaluator)
                        $allStudentIds = $this->ownerRecord->students()
                            ->where('students.id', '!=', $record->id)
                            ->pluck('students.id')
                            ->toArray();

                        $alreadyAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                            ->whereIn('evaluatee_student_id', $allStudentIds)
                            ->where('evaluator_student_id', '!=', $record->id)
                            ->pluck('evaluatee_student_id')
                            ->toArray();

                        // Get students not already assigned as evaluatees to another evaluator
                        $eligibleIds = array_diff($allStudentIds, $alreadyAssignedIds);

                        // Always include students already assigned to this evaluator (for editing)
                        $currentAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                            ->where('evaluator_student_id', $record->id)
                            ->pluck('evaluatee_student_id')
                            ->toArray();

                        $finalIds = array_unique(array_merge($eligibleIds, $currentAssignedIds));

                        return $this->ownerRecord->students()
                            ->whereIn('students.id', $finalIds)
                            ->pluck('name', 'students.id')
                            ->toArray();
                    })
                    ->default(function ($record) {
                        return EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                            ->where('evaluator_student_id', $record->id)
                            ->pluck('evaluatee_student_id')
                            ->toArray();
                    })
                    ->columns(2)
            ])
            ->action(function ($record, $data) {
                // Update student position
                $record->pivot->update(['position' => $data['position']]);

                // Update peer evaluator assignments
                if (isset($data['peer_evaluatees'])) {
                    $this->assignPeerEvaluatees($record->id, $data['peer_evaluatees']);
                }
            })
            ->modalHeading(fn ($record) => 'Edit ' . $record->name)
            ->modalDescription('Update student details and peer evaluation assignments')
            ->modalWidth('lg');
    }

    // Detach action to remove student
    protected function getDetachAction(): DetachAction
    {
        return DetachAction::make()
            ->label('Remove')
            ->after(function ($record) {
                // Remove all peer evaluator assignments for this student in this evaluation
                EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                    ->where(function ($query) use ($record) {
                        $query->where('evaluatee_student_id', $record->id)
                              ->orWhere('evaluator_student_id', $record->id);
                    })
                    ->delete();

                // Remove all evaluations for this student in this organization
                Evaluation::where('organization_id', $this->ownerRecord->id)
                    ->where('student_id', $record->id)
                    ->delete();

                // Remove rank record for this student in this organization
                \App\Models\Rank::where('organization_id', $this->ownerRecord->id)
                    ->where('student_id', $record->id)
                    ->delete();
            });
    }



    // Restore working Filament form configuration for attaching students
    protected function getAttachForm(): array
    {
        return [
            \Filament\Forms\Components\Select::make('recordId')
                ->label('Student')
                ->options(Student::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            \Filament\Forms\Components\TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255),
        ];
    }



    // Assign evaluatees to a peer evaluator
    protected function assignPeerEvaluatees(int $evaluatorStudentId, array $evaluateeIds): void
    {
        try {
            EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                ->where('evaluator_student_id', $evaluatorStudentId)
                ->delete();
            foreach ($evaluateeIds as $evaluateeId) {
                EvaluationPeerEvaluator::create([
                    'evaluation_id' => $this->ownerRecord->id,
                    'evaluatee_student_id' => $evaluateeId,
                    'evaluator_student_id' => $evaluatorStudentId,
                    'assigned_by_user_id' => auth()->id(),
                    'assigned_at' => now(),
                ]);
            }
            $evaluatorName = Student::find($evaluatorStudentId)->name;
            $evaluateeNames = Student::whereIn('id', $evaluateeIds)->pluck('name')->join(', ');
            Notification::make()
                ->title('Peer Evaluatees Assigned Successfully')
                ->body("Assigned {$evaluatorName} to evaluate: {$evaluateeNames}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Assigning Peer Evaluatees')
                ->body('There was an error assigning the peer evaluatees. Please try again.')
                ->danger()
                ->send();
        }
    }

    // Restore getEvaluationUrl for Evaluate action
    protected function getEvaluationUrl(int $studentId, string $evaluatorType): string
    {
        return \App\Filament\Admin\Resources\Evaluations\EvaluationResource::getUrl('evaluate-student', [
            'evaluation' => $this->ownerRecord->id,
            'student' => $studentId,
            'type' => $evaluatorType,
        ]);
    }
}

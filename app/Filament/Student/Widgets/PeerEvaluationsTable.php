<?php

namespace App\Filament\Student\Widgets;

use App\Models\EvaluationPeerEvaluator;
use App\Models\Evaluation;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class PeerEvaluationsTable extends BaseWidget
{
    protected static ?string $heading = 'Peer Evaluations';
    
    protected static ?string $description = 'Students assigned to you for peer evaluation';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getPeerEvaluationQuery())
            ->columns([
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->weight(FontWeight::SemiBold),
                    
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('evaluateeStudent.name')
                    ->label('Student to Evaluate')
                    ->weight(FontWeight::Medium),
                    
                TextColumn::make('evaluateeStudent.school_number')
                    ->label('School Number'),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $this->getPeerEvaluationStatus($record))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Completed' => 'success',
                        'Pending' => 'warning',
                    })
            ])
            ->actions([
                Action::make('evaluate')
                    ->label(fn ($record) => $this->getPeerEvaluationStatus($record) === 'Completed' ? 'View' : 'Start')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => $this->getPeerEvaluationUrl($record))
            ])
            ->paginated(false);
    }

    protected function getPeerEvaluationQuery(): Builder
    {
        $studentId = auth('student')->id();
        
        return EvaluationPeerEvaluator::query()
            ->where('evaluator_student_id', $studentId)
            ->with([
                'evaluation.organization',
                'evaluateeStudent'
            ]);
    }

    protected function getPeerEvaluationStatus($assignment): string
    {
        $studentId = auth('student')->id();
        
        $evaluation = \App\Models\EvaluationScore::where([
            'evaluation_id' => $assignment->evaluation_id,
            'student_id' => $assignment->evaluatee_student_id,
            'evaluator_type' => 'peer',
            'evaluator_id' => $studentId
        ])->first();

        return $evaluation ? 'Completed' : 'Pending';
    }

    protected function getPeerEvaluationUrl($assignment): string
    {
        return route('filament.admin.resources.evaluations.evaluate-student', [
            'record' => $assignment->evaluation_id,
            'student' => $assignment->evaluatee_student_id,
            'type' => 'peer'
        ]);
    }
}
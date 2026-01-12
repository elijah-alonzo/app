<?php

namespace App\Filament\Admin\Resources\Evaluations\Pages;

use App\Filament\Admin\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationScore;
use App\Filament\Admin\Resources\Evaluations\Schemas\EvaluationDetailsInfolist;
use App\Filament\Admin\Resources\Evaluations\Schemas\PeerDetailsInfolist;
use App\Filament\Admin\Resources\Evaluations\RelationManagers\StudentsRelationManager;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Edit Evaluation'),
        ];
    }


    public function getEvaluationStatus(int $studentId, string $evaluatorType): ?string
    {
        $score = EvaluationScore::where('evaluation_id', $this->record->id)
            ->where('student_id', $studentId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        return $score ? 'Done' : null;
    }

    // Render the InfoList and the relation managers (students table) together
    protected function getViewContent(): array
    {
        return array_merge(
            [$this->getInfolist()],
            $this->getRelationManagerComponents()
        );
    }

    protected function getInfolist()
    {
        return $this->getResource()::infolist(app(\Filament\Schemas\Schema::class))->render($this->record);
    }
}

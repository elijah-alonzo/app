<?php

namespace App\Filament\Admin\Resources\Evaluations\Pages;

use App\Filament\Admin\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationScore;
use App\Models\Evaluation;
use App\Models\Student;
use App\Traits\HandlesEvaluationForms;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Evaluate Student Page
 *
 * Provides dynamic evaluation forms based on evaluator type (adviser, peer, self).
 * Organizes questions by domain and strand for better user experience.
 */
class EvaluateStudent extends Page implements HasForms
{
    use InteractsWithForms, HandlesEvaluationForms;

    protected static string $resource = EvaluationResource::class;
    protected string $view = 'filament.admin.resources.evaluations.pages.EvaluationSheet';

    // The evaluation event (year-scoped)
    public Evaluation $evaluation;
    // Target student being evaluated
    public Student $student;
    // Evaluator type: 'adviser' | 'peer' | 'self'
    public string $type;
    // Per-student evaluation score record (answers + evaluator_score)
    public ?EvaluationScore $evaluationRecord = null;
    public array $data = [];
    // Lock the form if an evaluation score already exists
    public bool $isLocked = false;

    public function mount(Evaluation $evaluation, Student $student, string $type): void
    {
        $this->evaluation = $evaluation;
        $this->student = $student;
        $this->type = $type;
        $this->loadExistingEvaluation();
        $this->isLocked = $this->evaluationRecord !== null; // Lock if already submitted
    }

    /**
     * Load existing evaluation score if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        $query = EvaluationScore::where([
            'evaluation_id' => $this->evaluation->id,
            'student_id' => $this->student->id,
            'evaluator_type' => $this->type,
        ]);

        // For peer evaluations, also match the evaluator_id
        if ($this->type === 'peer') {
            $query->where('evaluator_id', auth('student')->id());
        } else {
            $query->whereNull('evaluator_id');
        }

        $this->evaluationRecord = $query->first();

        if ($this->evaluationRecord) {
            $this->data = $this->evaluationRecord->answers ?? [];
        }
    }

    public function getTitle(): string|Htmlable
    {
        $evaluatorTitle = ucfirst($this->type);
        return "{$evaluatorTitle} Evaluation for {$this->student->name}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        $orgName = $this->evaluation->organization->name ?? 'Organization';
        $yearRange = $this->evaluation->year;
        return "Organization: {$orgName}{$this->evaluation->name} ({$yearRange})";
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    /**
     * Save evaluation data to database
     */
    public function save(): void
    {
        $questions = EvaluationScore::getQuestionsForEvaluator($this->type);
        if (!$this->validateAnswers($questions)) {
            return;
        }

        $this->evaluationRecord
            ? $this->updateExistingEvaluation($this->data)
            : $this->createNewEvaluation($this->data);

        $this->sendSuccessNotification();
        $this->redirectToEvaluation();
    }

    /**
     * Create new evaluation score record
     */
    protected function createNewEvaluation(array $data): void
    {
        $this->evaluationRecord = EvaluationScore::create([
            'evaluation_id' => $this->evaluation->id,
            'student_id' => $this->student->id,
            'evaluator_type' => $this->type,
            'evaluator_id' => $this->type === 'peer' ? auth('student')->id() : null,
            'answers' => $data,
        ]);
    }

    /**
     * Redirect back to evaluation view
     */
    protected function redirectToEvaluation(): void
    {
        $this->redirect(route('filament.admin.resources.evaluations.view', [
            'record' => $this->evaluation->id
        ]));
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        if (! $this->isLocked) {
            $actions[] = Action::make('save')
                ->label('Submit Evaluation')
                ->action('save')
                ->requiresConfirmation()
                ->modalHeading('Submit Evaluation?')
                ->modalDescription('Are you sure you want to submit this evaluation? You will not be able to edit it afterwards.')
                ->keyBindings(['mod+s'])
                ->color('success')
                ->icon('heroicon-o-check');
        }
        $actions[] = Action::make('back')
            ->label('Back to Evaluation')
            ->url(route('filament.admin.resources.evaluations.view', ['record' => $this->evaluation->id]))
            ->color('gray')
            ->icon('heroicon-o-arrow-left');
        return $actions;
    }
}

<?php

namespace App\Filament\Student\Resources\Evaluations\Pages;

use App\Filament\Student\Resources\Evaluations\EvaluationResource as OrganizationResource;
use App\Models\Student;
use App\Models\Evaluation;
use App\Models\EvaluationPeerEvaluator;
use Filament\Resources\Pages\Page;

// Student evaluation tasks page (custom Blade view)
class ListEvaluations extends Page
{
    protected static string $resource = OrganizationResource::class;

    protected ?string $heading = 'Evaluations';

    protected ?string $subheading = 'Complete your self-evaluations and assigned peer evaluations';

    protected function getHeaderActions(): array
    {
        return [];
    }


    public function getView(): string
    {
        return 'filament.student.resources.evaluations.pages.EvaluationList';
    }

    protected function getViewData(): array
    {
        return [
            'tasks' => $this->getEvaluationTasks(),
        ];
    }

    protected function getEvaluationTasks()
    {
        $studentId = auth('student')->id();
        if (!$studentId) {
            return collect([]);
        }

        $tasks = collect();
        $student = Student::with(['evaluations.organization'])->find($studentId);

        if (!$student) {
            return collect([]);
        }


        // No avatar handling here — images removed from the evaluations table to avoid broken avatars.

        // Self-evaluation tasks
        foreach ($student->evaluations as $evaluation) {
            $selfEvaluation = \App\Models\EvaluationScore::where([
                'evaluation_id' => $evaluation->id,
                'student_id' => $studentId,
                'evaluator_type' => 'self'
            ])->whereNull('evaluator_id')->first();

            $tasks->push([
                'id' => 'self_' . $evaluation->id,
                'task_type' => 'Self-Evaluation',
                'evaluation_id' => $evaluation->id,
                'organization_id' => $evaluation->organization->id,
                'organization_name' => $evaluation->organization->name,
                'target_name' => 'Yourself',
                'status' => $selfEvaluation ? 'Completed' : 'Pending',
            ]);
        }

        // Peer evaluation tasks
        $peerAssignments = EvaluationPeerEvaluator::where('evaluator_student_id', $studentId)
            ->with(['evaluation.organization', 'evaluateeStudent'])
            ->get();

        foreach ($peerAssignments as $assignment) {
            $peerEvaluation = \App\Models\EvaluationScore::where([
                'evaluation_id' => $assignment->evaluation_id,
                'student_id' => $assignment->evaluatee_student_id,
                'evaluator_type' => 'peer',
                'evaluator_id' => $studentId
            ])->first();

            $tasks->push([
                'id' => 'peer_' . $assignment->evaluation_id . '_' . $assignment->evaluatee_student_id,
                'task_type' => 'Peer Evaluation',
                'evaluation_id' => $assignment->evaluation_id,
                'organization_id' => $assignment->evaluation->organization->id,
                'organization_name' => $assignment->evaluation->organization->name,
                'target_id' => $assignment->evaluatee_student_id,
                'target_name' => $assignment->evaluateeStudent->name,
                'status' => $peerEvaluation ? 'Completed' : 'Pending',
            ]);
        }

        return $tasks;
    }
}

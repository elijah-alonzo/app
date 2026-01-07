<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use App\Models\Rank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Organizations
        $orgNames = [
            'Paulinian Student Government',
            'Paulinian Student Government - SITE',
            'Paulinian Student Government - SBAHM',
            'Paulinian Student Government - SNAHS',
            'Paulinian Student Government - SASTE',
        ];

        $organizations = [];
        foreach ($orgNames as $name) {
            $organizations[] = Organization::create(['name' => $name]);
        }

        // Students
        $studentNames = [
            'Krystel Lingat',
            'Manuel Dizon',
            'Elijah Alonzo',
            'Jhon Danver Abogado',
            'Cristina Tuazon',
            'Micaela Duran',
            'Nicole Evangelista',
            'Jan Alcaide',
            'Arkin Dela Cruz',
            'Nayah Ostonal',
            'Julliene Almojera',
        ];

        // 80-word bio (approx) reused for all students
        $bio = "Hello, my name is a student leader who is passionate about service and collaboration. I strive to contribute positively to my organization, lead with integrity, and support my peers in achieving shared goals. Through active participation in activities and a commitment to learning, I aim to grow as a responsible and compassionate Paulinian leader. I look forward to serving and learning from others while fostering a respectful and productive environment.";

        $students = [];
        foreach ($studentNames as $fullName) {
            $initials = $this->initialsFromName($fullName);
            $local = strtolower($initials);
            $email = $this->uniqueEmail($local);

            $students[] = Student::create([
                'name' => $fullName,
                'email' => $email,
                'password' => bcrypt('password'),
                'description' => $bio,
                'school_number' => (string) rand(1000, 9999) . '-' . rand(100000, 999999),
            ]);
        }

        // Users assigned to organizations (one per organization)
        $userNames = [
            'Jacinto Furigay',
            'Karl Lavadia',
            'Queen Cabiao',
            'Krisha Berbano',
            'Biegh Alonzo',
        ];

        $users = [];
        foreach ($userNames as $index => $name) {
            $initials = $this->initialsFromName($name);
            $local = strtolower($initials);
            $email = $this->uniqueEmail($local);

            $users[] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'school_number' => (string) rand(1000, 9999) . '-' . rand(100000, 999999),
                'organization_id' => $organizations[$index]->id,
            ]);
        }

        // Positions list
        $positions = [
            'President',
            'Vice President',
            'Secretary',
            'Assistant Secretary',
            'Public Relation Officer',
            'Assistant Public Relation Officer',
            'Auditor',
            'Senate President',
            'House Speaker',
            'Chief Justice',
            'Sports Coordinator',
        ];

        // Create evaluations for the main organization (first one)
        $years = ['2022-2023', '2023-2024', '2024-2025', '2025-2026'];

        foreach ($years as $i => $year) {
            $evaluation = Evaluation::create([
                'organization_id' => $organizations[0]->id,
                'user_id' => $users[0]->id,
                'year' => $year,
            ]);

            // Shuffle students for distinct positions
            $shuffled = $students;
            shuffle($shuffled);

            foreach ($shuffled as $index => $student) {
                $position = $positions[$index] ?? 'Member';
                // attach to pivot table with position
                $evaluation->students()->attach($student->id, ['position' => $position]);

                // Create self evaluation (student evaluates self)
                $selfQuestions = EvaluationScore::getSelfQuestionsForStudents();
                $selfAnswers = $this->randomAnswersForKeys(array_keys($selfQuestions));
                EvaluationScore::create([
                    'evaluation_id' => $evaluation->id,
                    'student_id' => $student->id,
                    'evaluator_type' => 'self',
                    'evaluator_id' => $student->id,
                    'answers' => $selfAnswers,
                ]);

                // Create a peer evaluation from a random other student
                $other = $this->randomOtherStudent($students, $student->id);
                $peerQuestions = EvaluationScore::getPeerQuestionsForStudents();
                $peerAnswers = $this->randomAnswersForKeys(array_keys($peerQuestions));
                EvaluationScore::create([
                    'evaluation_id' => $evaluation->id,
                    'student_id' => $student->id,
                    'evaluator_type' => 'peer',
                    'evaluator_id' => $other->id,
                    'answers' => $peerAnswers,
                ]);

                // Adviser evaluation
                $adviserQuestions = EvaluationScore::getQuestionsForEvaluator('adviser');
                $adviserAnswers = $this->randomAnswersForKeys(array_keys($adviserQuestions));
                EvaluationScore::create([
                    'evaluation_id' => $evaluation->id,
                    'student_id' => $student->id,
                    'evaluator_type' => 'adviser',
                    'evaluator_id' => null,
                    'answers' => $adviserAnswers,
                ]);

                // Override Rank with a random tier so seed data shows mixed results
                $tiers = ['gold', 'silver', 'bronze', 'none'];
                $tier = $tiers[array_rand($tiers)];

                // Generate a plausible final_score that maps to the chosen tier
                switch ($tier) {
                    case 'gold':
                        $finalScore = round(mt_rand(241, 500) / 100, 3); // 2.41 - 5.00
                        break;
                    case 'silver':
                        $finalScore = round(mt_rand(181, 240) / 100, 3); // 1.81 - 2.40
                        break;
                    case 'bronze':
                        $finalScore = round(mt_rand(121, 180) / 100, 3); // 1.21 - 1.80
                        break;
                    default:
                        $finalScore = round(mt_rand(0, 120) / 100, 3); // 0.00 - 1.20
                }

                Rank::updateOrCreate([
                    'evaluation_id' => $evaluation->id,
                    'student_id' => $student->id,
                    'organization_id' => $evaluation->organization_id,
                ], [
                    'final_score' => $finalScore,
                    'rank' => $tier,
                    'status' => 'finalized',
                ]);
            }
        }
    }

    private function initialsFromName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) >= 2) {
            // Use the first two name-parts' initials (first and second)
            return substr($parts[0], 0, 1) . substr($parts[1], 0, 1);
        }
        return substr($parts[0], 0, 1);
    }

    private function randomAnswersForKeys(array $keys): array
    {
        $answers = [];
        foreach ($keys as $key) {
            // Random integer 1-5
            $answers[$key] = rand(1, 5);
        }
        return $answers;
    }

    private function randomOtherStudent(array $students, int $excludeId)
    {
        $pool = array_filter($students, fn($s) => $s->id !== $excludeId);
        $pool = array_values($pool);
        return $pool[array_rand($pool)];
    }

    /**
     * Generate a unique email local part by checking existing students and users.
     * If the base local part exists, append a numeric suffix (e.g., md1, md2).
     */
    private function uniqueEmail(string $local): string
    {
        $domain = '@spup.edu.ph';
        $candidate = $local . $domain;
        $i = 1;
        while (Student::where('email', $candidate)->exists() || User::where('email', $candidate)->exists()) {
            $candidate = $local . $i . $domain;
            $i++;
        }
        return $candidate;
    }
}

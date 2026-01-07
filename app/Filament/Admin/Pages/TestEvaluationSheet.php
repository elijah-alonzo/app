<?php
namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RadioButton;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Fieldset;

class TestEvaluationSheet
{
    /**
     * Generate Filament form schema for evaluation questions.
     * @param array $questions Array of questions with domain, strand, key, and text
     * @param bool $isLocked Whether to disable the fields
     * @return array
     */
    public static function getSchema(array $questions, bool $isLocked = false): array
    {
        $grouped = self::groupQuestions($questions);
        $schema = [];

        foreach ($grouped as $domainName => $strands) {
            $domainSection = Section::make($domainName)
                ->schema([]);
            foreach ($strands as $strandName => $strandQuestions) {
                $domainSection->schema[] = Group::make()
                    ->schema([
                        Fieldset::make($strandName)
                            ->schema(
                                array_map(function ($questionText, $questionKey) use ($isLocked) {
                                    return RadioButton::make("data.$questionKey")
                                        ->label($questionText)
                                        ->options([
                                            3 => 'Exceeds Expectations',
                                            2 => 'Meets Expectations',
                                            1 => 'Satisfactory',
                                            0 => 'Not Evident',
                                        ])
                                        ->required()
                                        ->disabled($isLocked);
                                }, $strandQuestions, array_keys($strandQuestions))
                            )
                    ]);
            }
            $schema[] = $domainSection;
        }
        return $schema;
    }

    // Example grouping function (replace with your actual logic)
    public static function groupQuestions(array $questions): array
    {
        // $questions should be an array with domain, strand, and question info
        // Example: [ ['domain' => 'Domain 1', 'strand' => 'Strand A', 'key' => 'q1', 'text' => 'Question 1'], ... ]
        $grouped = [];
        foreach ($questions as $q) {
            $grouped[$q['domain']][$q['strand']][$q['key']] = $q['text'];
        }
        return $grouped;
    }
}

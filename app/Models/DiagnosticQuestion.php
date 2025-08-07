<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticQuestion extends Model {
    protected $table = 'diagnostic_questions';

    protected $casts = [
        'active' => 'boolean',
        'choices' => 'array',
        'surveyjs' => 'array'
    ];

    /**
     * Export data to SurveyJS JSON format and save to a file.
     */
    public static function exportSurveyJsJsonToFile(): void {
        $output = self::generateSurveyJs();
        // Save the output to a JSON file
        $filePath = database_path('data/forms/surveyJSExport.json');
        file_put_contents($filePath, json_encode($output, JSON_PRETTY_PRINT));
    }

    public static function generateSurveyJs(): string {
        // Fetch questions grouped by category, ordered by ID
        $questions = self::where('active', true)->orderBy('id')->get()->groupBy('category');
        if ($questions->isEmpty()) {
            return json_encode([]);
        }
        $pages = [];

        foreach ($questions as $category => $items) {
            $elements = [];

            foreach ($items as $item) {
                $surveyJsData = is_string($item->surveyjs) ? json_decode($item->surveyjs, true) : $item->surveyjs;

                if (!$surveyJsData || !is_array($surveyJsData)) {
                    continue;
                }

                // Base element structure with surveyjs data, excluding nulls
                $element = array_filter([
                    'type' => $surveyJsData['type'] ?? 'text',
                    'name' => $item->name,
                    'visibleIf' => $item->visible_if,
                    'title' => $item->question,
                    'description' => $item->description,
                    'choices' => is_string($item->choices) ? json_decode($item->choices, true) : $item->choices,
                ], fn($value) => !is_null($value));

                // Add additional fields for specific types
                if (in_array($surveyJsData['type'] ?? '', ['range', 'number', 'rating'])) {
                    $additionalFields = array_intersect_key($surveyJsData, array_flip(['inputType', 'min', 'max', 'step', 'rateValues']));

                    // Order specific fields: inputType first, min before max, then step
                    $orderedFields = [];
                    foreach (['inputType', 'min', 'max', 'step'] as $key) {
                        if (isset($additionalFields[$key])) {
                            $orderedFields[$key] = $additionalFields[$key];
                        }
                    }

                    $element = array_replace($orderedFields, $element);
                }

                if ($surveyJsData['type'] === 'checkbox') {
                    $checkboxFields = array_intersect_key($surveyJsData, array_flip(['maxSelectedChoices', 'minSelectedChoices']));
                    $element = array_merge($element, array_filter($checkboxFields, fn($value) => !is_null($value)));
                }

                // Merge additional SurveyJS data while preserving base structure
                $elements[] = array_merge($element, array_filter($surveyJsData, fn($value) => !is_null($value)));
            }

            // Sort elements by ID
            usort($elements, function ($a, $b) use ($items) {
                $idA = $items->where('name', $a['name'])->first()->id ?? 0;
                $idB = $items->where('name', $b['name'])->first()->id ?? 0;
                return $idA <=> $idB;
            });

            $pageTitle = match ($category) {
                'sales-marketing' => 'Sales and Marketing',
                default => str_replace('-', ' ', $category),
            };

            $pages[] = [
                'name' => $category,
                'title' => ucfirst($pageTitle),
                'elements' => $elements,
            ];
        }

        // Add global SurveyJS settings
        $output = [
            'pages' => $pages,
            'showQuestionNumbers' => 'off',
            'showProgressBar' => 'top',
            'progressBarShowPageTitles' => false,
            'progressBarShowPageNumbers' => true,
            'showTOC' => true,
            'goNextPageAutomatic' => true,
            'allowCompleteSurveyAutomatic' => false,
            'completedHTML' => '<h3>Your diagnostic is complete!</h3>',
        ];

        return json_encode($output, JSON_PRETTY_PRINT);
    }

    public static function exportAll(): void {
        $questions = [];
        foreach (DiagnosticQuestion::all() as $question) {
            // Build an array with non-null values only
            $questions[] = array_filter([
                'id' => $question->id,
                'category' => $question->category,
                'name' => $question->name,
                'type' => $question->type,
                'active' => $question->active,
                'visible_if' => $question->visible_if,
                'question' => $question->question,
                'description' => $question->description,
                'choices' => $question->choices,
                'surveyjs' => $question->surveyjs,
                'notes' => $question->notes,
                'advisor_feedback' => $question->advisor_feedback,
                'html_element' => $question->html_element,
            ], function ($value) {
                return !is_null($value);
            });
        }
        // Save questions to a JSON file on the server
        $filePath = database_path('data/forms/questionsExport.json');
        file_put_contents($filePath, json_encode($questions, JSON_PRETTY_PRINT));
    }

    public function toJsonArray(): array {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'name' => $this->name,
            'type' => $this->type,
            'active' => $this->active,
            'visibleIf' => empty($this->visible_if) ? null : $this->visible_if,
            'question' => $this->question,
            'description' => $this->description,
            'choices' => empty($this->choices) ? null : $this->choices,
            'surveyJs' => empty($this->surveyjs) ? null : $this->surveyjs,
            'notes' => empty($this->notes) ? null : $this->notes,
            'advisor_feedback' => empty($this->advisor_feedback) ? null : $this->advisor_feedback,
            'htmlElement' => empty($this->html_element) ? null : $this->html_element
        ];
    }

    protected function getDependentName(?string $visibleIf): ?string {
        if (!$visibleIf) return null;
        preg_match('/\{([^}]+)\}/', $visibleIf, $matches);
        return $matches[1] ?? null;
    }
}

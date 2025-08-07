<?php

namespace Database\Seeders;

use App\Models\DiagnosticQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\DiagnosticSurveyMerger;

class DiagnosticQuestionsSeeder extends Seeder {
    public function run(): void {
        // DiagnosticSurveyMerger was used to get the merged data but is now deprecated.
        // $merger = new DiagnosticSurveyMerger();
        // $mergedData = $merger->merge();

        $jsonData = json_decode(File::get(database_path('data/forms/diagnostic-master.json')), true);

        // Clear the diagnostic_questions table
        // php artisan db:seed --class=DiagnosticQuestionsSeeder
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('diagnostic_questions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert each question into the diagnostic_questions table
        foreach ($jsonData as $question) {
            // Remove unwanted keys from the surveyjs field
            $filteredSurveyJS = isset($question['surveyjs'])
                ? collect($question['surveyjs'])->except(['name', 'title', 'description', 'choices'])->toArray()
                : null;

            DB::table('diagnostic_questions')->insert([
                'type' => $question['type'] ?? null,
                'name' => $question['name'] ?? null,
                'active' => $question['active'] ?? true,
                'category' => $question['category'] ?? null,
                'question' => $question['question'] ?? null,
                'description' => $question['description'] ?? null,
                'choices' => isset($question['choices']) ? json_encode($question['choices']) : null,
                'html_element' => $question['html_element'] ?? null,
                'notes' => $question['notes'] ?? null,
                'advisor_feedback' => $question['advisor_feedback'] ?? null,
                'visible_if' => $question['visible_if'] ?? null,
                'surveyjs' => $filteredSurveyJS ? json_encode($filteredSurveyJS) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Uncomment if you want to trigger these exports after seeding
        DiagnosticQuestion::exportAll();
//        DiagnosticQuestion::exportSurveyJsJsonToFile();
    }
}

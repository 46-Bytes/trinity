<?php

namespace Database\Seeders;

use App\Models\DiagnosticQuestion;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FormSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // php artisan db:seed --class=FormSeeder
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('forms')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//        $formSurveyJson = DiagnosticQuestion::generateSurveyJs();
        $forms = [
            [
                'title' => 'Automated Business Accelerator',
                'description' => 'Automated Business Accelerator',
                'ai_prompt' => "Please provide detailed and professional advice for a business owner based on the following input. The goal is to offer actionable insights that the business owner can use to improve their company's performance, address challenges, and capitalize on opportunities. Focus on areas where they can streamline processes, optimize operations, improve financial health, and develop long-term strategies for growth and sustainability. Consider offering step-by-step advice for overcoming key challenges and strategic recommendations tailored to their business situation.",
//                'form_json' => $formSurveyJson,
                'form_json' => File::get(database_path('data/forms/diagnostic-surveyjs.json')),
                'theme_json' => File::get(database_path('data/forms/diagnostic-surveyjs-theme.json'))
            ]
        ];
        foreach ($forms as $form) {
            $ai_prompt = $form['ai_prompt'];
            $title = $form['title'];
            $description = $form['description'];
            $form_json = $form['form_json'];
            $theme_json = $form['theme_json'];

            DB::table('forms')->insert([
                'ai_prompt' => $ai_prompt,
                'title' => $title,
                'slug' => 'diagnostic',
                'description' => $description,
                'form_json' => $form_json,
                'theme_json' => $theme_json,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}

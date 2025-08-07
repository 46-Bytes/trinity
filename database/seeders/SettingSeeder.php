<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SettingSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Truncate the table first
        DB::table('settings')->truncate();

        $settings = [
            [
                'title' => 'Default System Prompt',
                'description' => 'The default prompt used to set the behavior and context for the GPT model.',
                'setting_name' => 'system_prompt',
                'setting_value' => $this->getOrCreatePrompt('/system_prompt.md')
            ],
            [
                'title' => 'Task Prompt',
                'description' => 'The initial prompt for generating task requests and guiding responses related to tasks.',
                'setting_name' => 'initial_task_prompt',
                'setting_value' => $this->getOrCreatePrompt('/initial_task_prompt.md')
            ],
            [
                'title' => 'Diagnostic Summary Prompt',
                'description' => 'The prompt used when generating summary for users after a diagnostic session.',
                'setting_name' => 'diagnostic_summary',
                'setting_value' => $this->getOrCreatePrompt('/diagnostic_summary.md')
            ],
            [
                'title' => 'Diagnostic Advice Prompt',
                'description' => 'The prompt used when generating advice for users after a diagnostic session.',
                'setting_name' => 'advice_prompt_diagnostic',
                'setting_value' => $this->getOrCreatePrompt('/advice_prompt_diagnostic.md')
            ],
            [
                'title' => 'Diagnostic Scoring Prompt',
                'description' => 'The prompt used when scoring users responses.',
                'setting_name' => 'scoring_prompt',
                'setting_value' => $this->getOrCreatePrompt('/scoring_prompt.md')
            ],
            [
                'title' => 'JSON Extract',
                'description' => 'The prompt used for extracting JSON-formatted data from diagnostic responses.',
                'setting_name' => 'diagnostic_json_extract',
                'setting_value' => $this->getOrCreatePrompt('/diagnostic_json_extract.md')
            ], [
                'title' => 'General Category Prompt',
                'description' => 'Used for broad, uncategorized questions and general assistance across topics.',
                'setting_name' => 'category_prompt_general',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_general.md')
            ],
            [
                'title' => 'Legal‑Licensing Category Prompt',
                'description' => 'Used for questions about legal considerations, licensing, and compliance matters.',
                'setting_name' => 'category_prompt_legal-licensing',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_legal-licensing.md')
            ],
            [
                'title' => 'Financial Category Prompt',
                'description' => 'Used for general financial questions and high‑level financial planning discussions.',
                'setting_name' => 'category_prompt_financial',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_financial.md')
            ],
            [
                'title' => 'Operations Category Prompt',
                'description' => 'Used for organizational design, system architecture, and project structure topics.',
                'setting_name' => 'category_prompt_operations',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_operations.md')
            ],
            [
                'title' => 'Human‑Resources Category Prompt',
                'description' => 'Used for HR processes, policies, and people management discussions.',
                'setting_name' => 'category_prompt_human-resources',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_human-resources.md')
            ],
            [
                'title' => 'Customers Category Prompt',
                'description' => 'Used for customer‑centric discussions, support, and engagement strategies.',
                'setting_name' => 'category_prompt_customers',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_customers.md')
            ],
            [
                'title' => 'Competitive‑Forces Category Prompt',
                'description' => 'Used for competitive analysis, market forces, and strategic positioning discussions.',
                'setting_name' => 'category_prompt_competitive-forces',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_competitive-forces.md')
            ],
            [
                'title' => 'Diagnostics Category Prompt',
                'description' => 'Used for technical troubleshooting, debugging, and diagnostic procedures.',
                'setting_name' => 'category_prompt_diagnostics',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_diagnostics.md')
            ],
            [
                'title' => 'Due Diligence Category Prompt',
                'description' => 'Used for due diligence processes, risk assessments, and investigative reviews.',
                'setting_name' => 'category_prompt_due-diligence',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_due-diligence.md')
            ],
            [
                'title' => 'Tax Category Prompt',
                'description' => 'Covers tax strategies, filings, and compliance requirements.',
                'setting_name' => 'category_prompt_tax',
                'setting_value' => $this->getOrCreatePrompt('/category_prompt_tax.md')
            ]
        ];

        foreach ($settings as $setting) {
            Setting::create([
                'title' => $setting['title'],
                'description' => $setting['description'],
                'setting_name' => $setting['setting_name'],
                'setting_value' => $setting['setting_value'],
                'status' => $setting['status'] ?? 'active'
            ]);
        }
    }

    function getOrCreatePrompt(string $filename, ?string $defaultContent = null): string {
        $fullPath = rtrim(database_path('data/prompts'), '/') . '/' . $filename;

        // if the file doesn't exist, create it with your default
        if (!File::exists($fullPath)) {
            File::put($fullPath, $defaultContent);
        }

        // now safely get it
        return File::get($fullPath);
    }
}

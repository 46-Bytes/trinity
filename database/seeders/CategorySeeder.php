<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder {
    public function run(): void {
        $jsonPath = database_path('data/categories.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("categories.json not found in /database/data/");
            return;
        }

        $categories = json_decode(File::get($jsonPath), true);
        DB::table('categories')->insert($categories);
    }
}

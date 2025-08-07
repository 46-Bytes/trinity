<?php

namespace Database\Seeders;

use App\Helpers\GPT;
use App\Models\Media;
use App\Models\Setting;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GPTSeeder extends Seeder {
    private GPT $gpt;

    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run(): void {
        $this->gpt = new GPT();
        $this->deleteAllFiles();
    }

    /**
     * @throws ConnectionException
     */
    private function deleteAllFiles(): void {
        $listFiles = $this->gpt->listFiles();
        if (count($listFiles['data']) > 0) {
            $this->command->info('GPT Files found. ' . count($listFiles['data']));
            $deleted_files = $this->gpt->deleteAllFiles()['deleted_files'];
            $this->command->info('GPT Files deleted. ' . implode(', ', $deleted_files));
        }

        // Delete all files in storage/app/users
        Storage::deleteDirectory('users');
        Storage::makeDirectory('users'); // Recreate the directory

        $this->command->info('Users directory reset.');
    }

    /**
     * @throws Exception
     */
    private function syncFiles(): void {
        $syncedFiles = $this->gpt->syncFiles();

        $this->command->info('Sync complete! ' . count($syncedFiles) . ' files uploaded.');
    }
}

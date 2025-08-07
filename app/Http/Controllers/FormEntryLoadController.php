<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class FormEntryLoadController extends Controller {
    /**
     * Load an “in-progress” form entry for the given user.
     */
    public function inProgress(User $user): RedirectResponse {
        // Run your artisan command with the user’s ID and form_id = 1
        $exitCode = Artisan::call('form-entry:load', [
            'status' => 'in-progress',
            'user_id' => $user->id,
            'form_id' => 1,
        ]);

        $output = Artisan::output();

        return redirect()
            ->back()
            ->with([
                'status' => $exitCode === 0 ? 'success' : 'error',
                'consoleOut' => trim($output),
            ]);
    }
}

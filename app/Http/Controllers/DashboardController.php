<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Media;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;

class DashboardController extends Controller {
    private ?User $user;

    public function __construct() {
        $this->user = auth()->user();
    }

    public function index() {
//        $user = auth()->user();
//        $subscriptionStatus = $user->subscriptions()->where('stripe_status', 'active')->first();

        // Disable billing for trinity
//        if (!$user->isAdmin() && !$subscriptionStatus) {
//            return redirect()->route('account.billing');
//        }

        // Fetch the most recent diagnostic for the user
//        if (session()->has('diagnostic') && session('diagnostic') instanceof Diagnostic) {
//            $diagnostic = session('diagnostic');
//        } else {
        if ($this->user->hasIncompleteDiagnostic()) {
            $diagnostic = $this->user->getActiveDiagnostic();
        } else {
            $diagnostic = Diagnostic::getLatestDiagnostic($this->user->id);
        }

        session(['diagnostic' => $diagnostic]);

//        }

        // Set diagnostic action based on the status of the diagnostic
        if (!$diagnostic) {
            // No diagnostic found, so offer to start the primary one
            $action = 'start_primary';
        } elseif ($diagnostic->status === Diagnostic::STATUS_IN_PROGRESS or $diagnostic->status === Diagnostic::STATUS_NEEDS_ACTION) {
            // Continue if the diagnostic is still in progress
            $action = 'continue';
        } elseif ($diagnostic->status === Diagnostic::STATUS_COMPLETED && now()->diffInDays($diagnostic->end_date) >= 27) {
            // If it's been 27 days since the last diagnostic, start a new monthly one
            $action = 'start_monthly';
        } else {
            // If a diagnostic was recently completed but not old enough for a new one
            $action = 'completed';
        }

        // Fetch tasks, notes, and files for the current user
        $tasks = Task::where('user_id', auth()->id())->paginate(10);
        $notes = Note::where('user_id', auth()->id())->paginate(10);
        $files = Media::where('user_id', auth()->id())->paginate(10);

        return view('dashboard', compact('diagnostic', 'action', 'tasks', 'notes', 'files'));
    }

}

<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\SubscriptionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SubscriptionCreatedListener {
    /**
     * Create the event listener.
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void {
        $user = User::where('stripe_id', $event->customer)->first();
        if ($user) {
            $user->notify(new SubscriptionCreated($event->subscription));
        }
    }
}

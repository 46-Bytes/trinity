<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use App\Notifications\SubscriptionCreated;
use App\Notifications\SubscriptionCancelled;
use App\Models\User;

class StripeEventListener {
    public function handle(WebhookReceived $event) {
        if ($event->payload['type'] === 'customer.subscription.created') {
            $user = User::where('stripe_id', $event->payload['data']['object']['customer'])->first();
            if ($user) {
                $user->notify(new SubscriptionCreated($event->payload['data']['object']));
            }
        }

        if ($event->payload['type'] === 'customer.subscription.deleted') {
            $user = User::where('stripe_id', $event->payload['data']['object']['customer'])->first();
            if ($user) {
                $user->notify(new SubscriptionCancelled());
            }
        }
    }
}

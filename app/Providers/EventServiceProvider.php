<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

class EventServiceProvider extends ServiceProvider {
    protected $listen = [
        WebhookReceived::class => [
            'App\Listeners\StripeEventListener',
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ]
    ];

    public function boot() {
        //
    }
}

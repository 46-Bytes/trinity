<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionCancelled extends Notification {
    use Queueable;

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('TrinityAi Subscription Cancelled')
            ->view('emails.subscription-cancelled', [
                'user' => $notifiable,
                'date' => now()
            ]);
    }
}

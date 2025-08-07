<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Price;

class SubscriptionCreated extends Notification {
    use Queueable;

    protected $subscription;

    public function __construct($subscription) {
        $this->subscription = $subscription;
    }

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Get price ID directly from the subscription
            $priceId = $this->subscription->stripe_price;

            // Retrieve price from Stripe
            $price = Price::retrieve($priceId);
            $baseAmount = $price->unit_amount / 100; // Convert from cents to dollars

            // Get discount if any
            $stripeSubscription = \Stripe\Subscription::retrieve($this->subscription->stripe_id);
            if (!empty($stripeSubscription->discount)) {
                $coupon = $stripeSubscription->discount->coupon;
                if ($coupon->percent_off) {
                    $baseAmount = $baseAmount * (1 - ($coupon->percent_off / 100));
                } elseif ($coupon->amount_off) {
                    $baseAmount = max(0, $baseAmount - ($coupon->amount_off / 100));
                }
            }

            $gst = $baseAmount / 11; // GST is 1/11th of total in Australia
            $totalAmount = $baseAmount;
            $baseAmountExGst = $baseAmount - $gst;

            LogThis('info', 'Sending Subscription created email', ['user' => $notifiable->email]);
            return (new MailMessage)
                ->subject('TrinityAi Subscription Created')
                ->bcc(['peter@malekso.com.au'])
                ->view('emails.subscription-created', [
                    'user' => $notifiable,
                    'baseAmount' => $baseAmountExGst,
                    'gst' => $gst,
                    'totalAmount' => $totalAmount,
                    'date' => now(),
                    'invoiceNumber' => 'INV-' . now()->format('Ymd') . '-' . $notifiable->id,
                    'planName' => 'TrinityAi Subscription',
                    'stripeId' => $priceId,
                    'coupon' => $stripeSubscription->discount->coupon ?? null
                ]);
        } catch (\Exception $e) {
            Log::error('Error creating subscription email: ' . $e->getMessage(), [
                'exception' => $e,
                'subscription' => $this->subscription
            ]);

            return (new MailMessage)
                ->subject('Welcome to TrinityAi')  // Fallback subject
                ->line('Thank you for subscribing to TrinityAi!');
        }
    }
}

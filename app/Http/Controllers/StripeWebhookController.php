<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    
    public function handleWebhook(Request $request)
    {
        // Handle webhook events
        $payload = $request->all();

        if ($payload['type'] === 'invoice.payment_succeeded') {
            // Payment succeeded logic
        } elseif ($payload['type'] === 'invoice.payment_failed') {
            // Payment failed logic
        }

        return response('Webhook received', 200);
    }
}

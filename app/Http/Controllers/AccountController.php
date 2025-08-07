<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;
use Stripe\Product as StripeProduct;
use Stripe\Stripe;
use Stripe\Price;
use App\Models\Product as YobaProduct;


class AccountController extends Controller {
    private ?User $user;

    public function __construct() {
        $this->user = Auth::user();
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * @throws ApiErrorException
     */
    public function show(Request $request) {
        $subscription = $this->user->subscription('default');
        $stripeSubscription = $subscription ? $subscription->asStripeSubscription() : null;
        $planName = $stripeSubscription
            ? $stripeSubscription->items->data[0]->plan->nickname ?? 'Unknown Plan'
            : 'No Active Plan';

        $productName = null;
        $availableProducts = [];

        if ($stripeSubscription) {
            $stripeProductId = $stripeSubscription->items->data[0]->plan->product;
            $currentProduct = YobaProduct::findByStripeProductId($stripeProductId);
            $productName = $currentProduct?->name ?? 'Unknown Product';
        }

        foreach (YobaProduct::getStripeProductIds() as $stripeProductId) {
            $product = YobaProduct::findByStripeProductId($stripeProductId);

            if ($product && $product->price_id) {
                $price = Price::retrieve($product->price_id);
                $currency = strtoupper($price->currency);
                $productPrice = $currency . ' $' . ($price->unit_amount / 100);
                $availableProducts[$stripeProductId] = $product->name . ' - ' . $productPrice;
            }
        }

        return view('account.show', [
            'user' => $this->user,
            'org' => $this->user->org()->first(),
            'subscription' => $subscription,
            'productName' => $productName,
            'stripeSubscription' => $stripeSubscription,
            'invoices' => $this->user->invoices(),
            'availableProducts' => $availableProducts
        ]);
    }
}

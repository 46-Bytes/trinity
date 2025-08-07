<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Stripe;
use Stripe\Price;
use Stripe\Product;
use Stripe\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;
use App\Models\Product as YobaProduct;


class SubscriptionController extends Controller {

    public function __construct() {
        Stripe::setApiKey(config('services.stripe.secret')); // Use the secret key
//        Log::info('Stripe API Key', ['key' => config('services.stripe.secret')]);
    }

    /**
     * Show available subscription plans.
     */
    public function index(Request $request) {
        $subscriptions = $request->user()->subscriptions;

        return view('subscriptions.index', compact('subscriptions'));
    }

//    public function plans() {
//        Stripe::setApiKey(env('STRIPE_SECRET'));
//
//        try {
//            // Retrieve only active products
//            $products = Product::all(['limit' => 100, 'active' => true]);
//            $plans = [];
//
//            foreach ($products->data as $product) {
//                $prices = Price::all(['product' => $product->id]);
//                foreach ($prices->data as $price) {
//                    $plans[] = [
//                        'name' => $product->name,
//                        'description' => $product->description,
//                        'stripe_id' => $price->id, // Stripe Price ID
//                        'amount' => $price->unit_amount / 100,
//                        'currency' => strtoupper($price->currency),
//                    ];
//                }
//            }
//
//        } catch (Exception $e) {
//            return back()->withErrors(['error' => 'Unable to fetch plans from Stripe: ' . $e->getMessage()]);
//        }
//        return view('plans.index', compact('plans'));
//    }

    /**
     * Create a new subscription for the user.
     */
    public function subscribe(Request $request) {
        $request->validate([
            'plan' => 'required|string', // Stripe Price ID
            'payment_method' => 'required|string', // Stripe Payment Method ID
        ]);

        $user = $request->user();
        $plan = $request->input('plan');
        $paymentMethod = $request->input('payment_method');

        try {
            $user->newSubscription('default', $plan)
                ->create($paymentMethod);

            return redirect()->route('subscription.show')
                ->with('success', 'Subscription created successfully!');
        } catch (IncompletePayment $e) {
            // Handle scenarios like 3D secure payment requirements
            return redirect($e->payment->next_action->redirect_to_url->url);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the user's current subscription.
     */
    public function show(Request $request) {
        $subscription = $request->user()->subscription('default');

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel the user's subscription.
     */
    public function cancel(Request $request) {
        $subscription = $request->user()->subscription('default');

        if ($subscription) {
            $subscription->cancel();

            return redirect()->route('subscription.show')
                ->with('success', 'Subscription cancelled successfully!');
        }

        return back()->withErrors(['error' => 'No active subscription found.']);
    }

    /**
     * Pause the user's subscription.
     */
    public function pause(Request $request) {
        $subscription = $request->user()->subscription('default');
        if ($subscription && !$subscription->onGracePeriod()) {
            $subscription->pause(); // Cashier pause
            session()->flash('success', 'Subscription paused successfully.');

            return redirect()->route('account.billing');
        }
        return back()->withErrors(['error' => 'No active subscription found.']);
    }

    /**
     * Resume the user's subscription.
     */
    public function resume(Request $request) {
        $subscription = $request->user()->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume(); // Cashier resume
            session()->flash('success', 'Subscription resumed successfully.');
            return redirect()->route('account.billing');
        }

        return back()->withErrors(['error' => 'No subscription to resume.']);
    }

    /**
     * Swap the user's subscription plan.
     */
    public function swap(Request $request) {
        $request->validate([
            'new_plan' => 'required|string', // New Stripe Price ID
        ]);

        $subscription = $request->user()->subscription('default');

        if ($subscription) {
            $subscription->swap($request->input('new_plan'));

            return redirect()->route('subscription.show')
                ->with('success', 'Subscription updated successfully!');
        }

        return back()->withErrors(['error' => 'No active subscription found.']);
    }

    public function billingPortal(Request $request) {
        return $request->user()->redirectToBillingPortal();
    }

    // Show the subscription form
    public function showForm(Request $request) {
        $stripeProductPriceId = $request->query('stripe_product_price_id'); // Pass product ID via query
        return view('subscriptions.form', compact('stripeProductPriceId'));
    }

    // Process the subscription
    public function processForm(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'business_name' => 'required|string|max:255',
            'coupon' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        $user = auth()->user(); // Assuming the user is authenticated
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        try {
            // Handle subscription with coupon if provided
            if ($request->coupon) {
                $user->newSubscription('default', $request->stripe_product_price_id)
                    ->withCoupon($request->coupon)
                    ->create($request->payment_method);
            } else {
                $user->newSubscription('default', $request->stripe_product_price_id)
                    ->create($request->payment_method);
            }

            return redirect()->route('dashboard')->with('success', 'Subscription successful!');
        } catch (IncompletePayment $exception) {
            return redirect()->route('cashier.payment', [$exception->payment->id, 'redirect' => route('dashboard')]);
        }
    }

    /**
     * @throws ApiErrorException
     * @throws Exception
     */
    public function showSubscriptionForm(Request $request) {
        // Retrieve the price_id from the query parameter
        $stripe_product_price_id = $request->query('stripe_product_price_id');
        if (!$stripe_product_price_id) {
            throw new Exception('Price ID is required');
        }

        $price = Price::retrieve($stripe_product_price_id);
        $currency = strtoupper($price->currency);
        $productName = YobaProduct::findByStripeProductId($stripe_product_price_id)->name;
        $productPrice = $currency . ' $' . ($price->unit_amount / 100);

        return view('subscriptions.checkout',
            compact(
                'stripe_product_price_id',
                'productName',
                'productPrice'
            )
        );
    }

    public function processSubscription(Request $request) {
        $stripe_product_price_id = $request->stripe_product_price_id;

        // Check if price_id is provided
        if (!$stripe_product_price_id) {
            LogThis('error', 'Stripe product price ID not provided.');
            return response()->json(['error' => 'No valid price ID provided.'], 400);
        }

        $request->validate([
            'payment_method' => 'required', // Stripe payment method ID
            'coupon' => 'nullable|string', // Optional coupon code
            'stripe_product_price_id' => 'required|string',
        ]);

        try {
            // Retrieve the authenticated user
            $user = Auth::user();

            // Ensure the user is a Stripe customer
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
                LogThis('info', 'Stripe customer created:', ['stripe_id' => $user->stripe_id]);
            }

            // Prepare the subscription
            $subscription = $user->newSubscription('default', $stripe_product_price_id);

            // Apply coupon if provided
            if ($request->filled('coupon')) {
                try {
                    $promotionCodes = \Stripe\PromotionCode::all(['code' => $request->coupon]);
                    if (count($promotionCodes->data) === 0) {
                        throw new \Exception('Invalid or expired coupon.');
                    }
                    $promotionCode = $promotionCodes->data[0];
                    $subscription->withCoupon($promotionCode->coupon);
                } catch (\Exception $e) {
                    LogThis('error', 'Coupon error during subscription', ['error' => $e->getMessage()]);
                    return response()->json(['error' => 'Coupon error: ' . $e->getMessage()], 400);
                }
            }

            // Create the subscription with the provided payment method
            $subscription->create($request->payment_method);

            return response()->json([
                'message' => 'Subscription successful',
                'redirect' => route('dashboard'), // Redirect to the dashboard
            ], 201);
        } catch (\Exception $e) {
            LogThis('error', 'Subscription failed:', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Subscription failed: ' . $e->getMessage()], 500);
        }
    }

}

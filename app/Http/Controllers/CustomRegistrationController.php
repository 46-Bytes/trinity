<?php

namespace App\Http\Controllers;

use App\Models\Org;
use App\Models\User;
use App\Notifications\SubscriptionCreated;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Stripe\Coupon;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use App\Models\Product as YobaProduct;


class CustomRegistrationController extends Controller {
    public function __construct() {
        Stripe::setApiKey(config('services.stripe.secret')); // Use the secret key
//        LogThis('info','Stripe API Key', ['key' => config('services.stripe.secret')]);
    }

    public function register(Request $request) {
        $stripe_product_price_id = $request->stripe_product_price_id;

        // Check if price_id is provided
        if (!$stripe_product_price_id) {
            LogThis('error', 'Stripe product price ID not provided.');
            return response()->json(['error' => 'No valid price ID provided.'], 400);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'payment_method' => 'required', // Stripe payment method ID
            'coupon' => 'nullable|string', // Optional coupon code
            'stripe_product_price_id' => 'required|string',
            'business_name' => 'required|string|max:255',
            'business_description' => 'required|string|max:500',
            'business_website' => 'required|string|max:255',
        ]);

        try {
            // Log request data for debugging
            LogThis('info', 'Registering user with data:', $request->except(['password', 'password_confirmation']));

            // Create the user temporarily for Stripe
            $user = new User([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('client');

            // Log user creation process
            LogThis('info', 'User created for Stripe:', ['email' => $user->email]);

            // Create Stripe customer
            $user->createAsStripeCustomer();
            LogThis('info', 'Stripe customer created:', ['stripe_id' => $user->stripe_id]);

            // Prepare subscription
            $subscription = $user->newSubscription('default', $stripe_product_price_id);

            // Check if a coupon code is provided
            if ($request->filled('coupon')) {
                try {
                    $promotionCodes = \Stripe\PromotionCode::all(['code' => $request->coupon]);
                    if (count($promotionCodes->data) === 0) {
                        throw new \Exception('Invalid or expired coupon.');
                    }
                    $promotionCode = $promotionCodes->data[0];
                    $subscription->withCoupon($promotionCode->coupon);
                } catch (\Exception $e) {
                    LogThis('error', 'Coupon error during registration', ['error' => $e->getMessage()]);
                    return response()->json(['error' => 'Coupon error: ' . $e->getMessage()], 400);
                }
            }

            // Create subscription with the provided payment method
            $subscription->create($request->payment_method);

            // Save the user to the database after successful subscription
            $user->save();
            LogThis('info', 'User saved:', ['user_id' => $user->id]);

            // Create the organization entry
            Org::create([
                'user_id' => $user->id,
                'name' => $request->business_name,
                'slug' => slugify($request->business_name),
                'status' => 'active',
                'description' => $request->business_description,
                'website' => $request->business_website,
                'date_joined' => date('Y-m-d H:i:s'),
            ]);

            // Explicitly log in the user
            Auth::login($user);

            LogThis('info', 'Subscription created:', (array)$subscription);

            // Notify the user
            // Get the actual subscription object after creation
            $activeSubscription = $user->subscription('default');

            // Notify with the actual subscription object instead of the builder
            $user->notify(new SubscriptionCreated($activeSubscription));

            return response()->json([
                'message' => 'Registration and subscription successful',
                'redirect' => route('dashboard') // Use named route if possible
            ], 201);


            // return response()->json(['message' => 'Registration and subscription successful'], 201);
        } catch (Exception $e) {
            LogThis('error', 'Registration failed:', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public function register_duplicate(Request $request) {


        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'coupon' => 'nullable|string', // Optional coupon code
            'business_name' => 'required|string|max:255',
            'business_description' => 'required|string|max:500',
            'business_website' => 'required|string|max:255',
        ]);

        try {
            // Log request data for debugging
            LogThis('info', 'Registering user with data:', $request->except(['password', 'password_confirmation']));
            // Create the user temporarily for Stripe
            $user = new User([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('client');


            // Save the user to the database after successful subscription
            $user->save();
            LogThis('info', 'User saved:', ['user_id' => $user->id]);

            // Create the organization entry
            Org::create([
                'user_id' => $user->id,
                'name' => $request->business_name,
                'slug' => slugify($request->business_name),
                'status' => 'active',
                'description' => $request->business_description,
                'website' => $request->business_website,
                'date_joined' => date('Y-m-d H:i:s'),
            ]);

            // Explicitly log in the user
            Auth::login($user);

            return response()->json([
                'message' => 'Registration and subscription successful',
                'redirect' => route('dashboard') // Use named route if possible
            ], 201);


            // return response()->json(['message' => 'Registration and subscription successful'], 201);
        } catch (Exception $e) {
            LogThis('error', 'Registration failed:', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public function validateCoupon(Request $request) {
        $request->validate([
            'coupon' => 'required|string|regex:/^[a-zA-Z0-9-_]+$/',
            'price_id' => 'required|string|starts_with:price_',
        ]);

        LogThis('info', 'Validating coupon', ['coupon' => $request->coupon, 'price_id' => $request->price_id]);

        try {
            // Retrieve the promotion code
            $promotionCodes = \Stripe\PromotionCode::all([
                'code' => trim($request->coupon),
                'limit' => 1,
            ]);

            if (empty($promotionCodes->data)) {
                return response()->json(['error' => 'Invalid or expired coupon.'], 400);
            }

            $promotionCode = $promotionCodes->data[0];
            $coupon = \Stripe\Coupon::retrieve($promotionCode->coupon->id);

            if (!$coupon->valid) {
                return response()->json(['error' => 'Invalid or expired coupon.'], 400);
            }

            // Retrieve the original price
            $price = \Stripe\Price::retrieve($request->price_id);

            $originalAmount = $price->unit_amount;
            $currency = strtoupper($price->currency);

            // Calculate the discounted price
            $discountedAmount = $originalAmount;
            if ($coupon->percent_off) {
                $discountedAmount = $originalAmount * (1 - ($coupon->percent_off / 100));
            } elseif ($coupon->amount_off) {
                $discountedAmount = max(0, $originalAmount - $coupon->amount_off);
            }

            return response()->json([
                'original_price' => $originalAmount / 100,
                'discounted_price' => $discountedAmount / 100,
                'currency' => $currency,
            ]);
        } catch (\Exception $e) {
            LogThis('error', 'Error validating coupon', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error validating coupon: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @throws ApiErrorException
     * @throws Exception
     */
    public function showRegistrationForm(Request $request) {
        $stripe_product_price_id = $request->query('stripe_product_price_id');
        if (!$stripe_product_price_id) {
            //throw new Exception('Price ID is required');
            $stripe_product_price_id = YobaProduct::getStripePriceIdBySlug('basic'); // TODO: make this dynamic
        }

        $price = Price::retrieve($stripe_product_price_id);
        $currency = strtoupper($price->currency);

        // Find the product using the product associated with the price
        $product = YobaProduct::findByStripeProductId($price->product);

        if (!$product) {
            throw new Exception('Product not found');
        }

        $productName = $product->name;
        $productPrice = $currency . ' $' . ($price->unit_amount / 100);

        return view('auth.register', compact(
            'stripe_product_price_id',
            'productName',
            'productPrice'
        ));
    }

    public function showRegistrationFormDuplicate(Request $request) {
        return view('auth.register_duplicate');
    }
}

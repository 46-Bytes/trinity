<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class CouponController extends Controller {
    private StripeClient $stripe;

    public function __construct() {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    /**
     * Display a listing of the coupons.
     */
    public function index() {
        $coupons = Coupon::all();
        return response()->json($coupons);
    }

    /**
     * Store a newly created coupon and sync with Stripe.
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'percent_off' => 'nullable|integer|min:0|max:100',
            'amount_off' => 'nullable|integer|min:0',
            'currency' => 'nullable|string|size:3',
            'duration' => 'required|string|in:forever,once,repeating',
            'duration_in_months' => 'nullable|integer|min:1',
            'max_redemptions' => 'nullable|integer|min:1',
            'redeem_by' => 'nullable|date',
            'single_use' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        // Sync with Stripe
        $stripeCoupon = $this->stripe->coupons->create([
            'id' => $validated['code'],
            'percent_off' => $validated['percent_off'],
            'amount_off' => $validated['amount_off'],
            'currency' => $validated['currency'],
            'duration' => $validated['duration'],
            'duration_in_months' => $validated['duration_in_months'],
            'max_redemptions' => $validated['max_redemptions'],
            'redeem_by' => isset($validated['redeem_by']) ? strtotime($validated['redeem_by']) : null,
            'metadata' => $validated['metadata'] ?? [],
        ]);

        // Save in the local database
        $coupon = Coupon::create($validated);
        return response()->json($coupon, 201);
    }

    /**
     * Display the specified coupon.
     */
    public function show(Coupon $coupon) {
        return response()->json($coupon);
    }

    /**
     * Update the specified coupon and sync with Stripe.
     */
    public function update(Request $request, Coupon $coupon) {
        $validated = $request->validate([
            'code' => 'string|unique:coupons,code,' . $coupon->id,
            'percent_off' => 'nullable|integer|min:0|max:100',
            'amount_off' => 'nullable|integer|min:0',
            'currency' => 'nullable|string|size:3',
            'duration' => 'string|in:forever,once,repeating',
            'duration_in_months' => 'nullable|integer|min:1',
            'max_redemptions' => 'nullable|integer|min:1',
            'redeem_by' => 'nullable|date',
            'single_use' => 'boolean',
            'is_valid' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        // Sync with Stripe
        $this->stripe->coupons->update($coupon->code, [
            'percent_off' => $validated['percent_off'],
            'amount_off' => $validated['amount_off'],
            'currency' => $validated['currency'],
            'duration' => $validated['duration'],
            'duration_in_months' => $validated['duration_in_months'],
            'max_redemptions' => $validated['max_redemptions'],
            'redeem_by' => isset($validated['redeem_by']) ? strtotime($validated['redeem_by']) : null,
            'metadata' => $validated['metadata'] ?? [],
        ]);

        // Update in the local database
        $coupon->update($validated);
        return response()->json($coupon);
    }

    /**
     * Remove the specified coupon and delete from Stripe.
     */
    public function destroy(Coupon $coupon) {
        // Delete from Stripe
        $this->stripe->coupons->delete($coupon->code);

        // Delete from the local database
        $coupon->delete();
        return response()->json(null, 204);
    }
}

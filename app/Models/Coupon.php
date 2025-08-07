<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stripe\StripeClient;

class Coupon extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'promo_id',
        'coupon_id',
        'coupon_percent_off',
        'coupon_amount_off',
        'coupon_currency',
        'coupon_duration',
        'coupon_duration_months',
        'coupon_max_redemptions',
        'coupon_redeem_by',
        'coupon_single_use',
        'promo_active',
        'promo_max_codes',
        'promo_min_amount',
        'promo_customer_id',
        'promo_restriction',
        'is_valid',
        'metadata'
    ];

    protected $casts = [
        'coupon_percent_off' => 'integer',
        'coupon_amount_off' => 'integer',
        'coupon_duration_months' => 'integer',
        'coupon_max_redemptions' => 'integer',
        'coupon_redeem_by' => 'datetime',
        'coupon_single_use' => 'boolean',
        'promo_active' => 'boolean',
        'promo_max_codes' => 'integer',
        'promo_min_amount' => 'integer',
        'is_valid' => 'boolean',
        'metadata' => 'array'
    ];

    public static function syncWithStripe(): void {
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $dbCoupons = self::all();

        // Get both coupons and promo codes from Stripe
        $stripeCoupons = $stripe->coupons->all(['limit' => 100])->data;
        $stripePromoCodes = $stripe->promotionCodes->all(['limit' => 100])->data;

        $stripeCouponIds = collect($stripeCoupons)->pluck('id')->toArray();
        $stripePromoIds = collect($stripePromoCodes)->pluck('id')->toArray();

        // Delete local records not in Stripe
        self::whereNotIn('coupon_id', $stripeCouponIds)
            ->orWhereNotIn('promo_id', $stripePromoIds)
            ->delete();

        foreach ($dbCoupons as $coupon) {
            try {
                // Prepare coupon data
                $stripeData = [
                    'id' => $coupon->coupon_id,
                    'percent_off' => $coupon->coupon_percent_off,
                    'currency' => $coupon->coupon_currency,
                    'duration' => $coupon->coupon_duration,
                    'metadata' => $coupon->metadata,
                ];

                if ($coupon->coupon_duration_months) $stripeData['duration_in_months'] = $coupon->coupon_duration_months;
                if ($coupon->coupon_max_redemptions) $stripeData['max_redemptions'] = $coupon->coupon_max_redemptions;
                if ($coupon->coupon_amount_off) $stripeData['amount_off'] = $coupon->coupon_amount_off;
                if ($coupon->coupon_redeem_by) $stripeData['redeem_by'] = $coupon->coupon_redeem_by->timestamp;

                // Create/verify coupon
                try {
                    $stripe->coupons->retrieve($coupon->coupon_id);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    if ($e->getStripeCode() === 'resource_missing') {
                        $stripeCoupon = $stripe->coupons->create($stripeData);

                        // Create promotion code
                        $stripe->promotionCodes->create([
                            'coupon' => $stripeCoupon->id,
                            'code' => $coupon->code,
                            'active' => $coupon->promo_active,
                            'max_redemptions' => $coupon->promo_max_codes,
                            'restrictions' => [
                                'minimum_amount' => $coupon->promo_min_amount,
                                'first_time_transaction' => $coupon->promo_restriction === 'first_time_transaction'
                            ]
                        ]);
                    }
                }
            } catch (\Exception $e) {
                report($e);
            }
        }
    }
}

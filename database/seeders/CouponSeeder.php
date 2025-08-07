<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Exception;
use Illuminate\Database\Seeder;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CouponSeeder extends Seeder {
    private StripeClient $stripe;
    private bool $can_generate;
    private bool $reset_stripe_coupons;

    public function __construct() {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
        $this->can_generate = env('GENERATE_STRIPE_COUPONS') ?? false;
        $this->reset_stripe_coupons = env('RESET_STRIPE_COUPONS') ?? false;
    }

    public function run() {

        if ($this->reset_stripe_coupons) {
            $this->deleteAllStripeCoupons();
        }
        if (!$this->can_generate && !$this->reset_stripe_coupons) {
            $this->syncCouponsFromStripe();
        } else {


            // Create permanent team coupon
            $this->createCoupon(
                Coupon::factory()->state([
                    'code' => 'MYMTEAM',
                    'coupon_id' => 'MYMTEAM',
                    'coupon_percent_off' => 100,
                    'coupon_duration' => 'forever',
                    'metadata' => ['description' => 'Permanent 100% off coupon']
                ])
            );

            // Create 10 single-use coupons
            foreach (range(1, 10) as $i) {
                $this->createCoupon(Coupon::factory()->freebie());
            }
        }
    }

    public static function deleteAllStripeCoupons(): void {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $stripeCoupons = $stripe->coupons->all(['limit' => 100])->data;
            foreach ($stripeCoupons as $coupon) {
                $stripe->coupons->delete($coupon->id);
            }

            $promoCodesList = $stripe->promotionCodes->all(['limit' => 100])->data;
            foreach ($promoCodesList as $promoCode) {
                $stripe->promotionCodes->update($promoCode->id, ['active' => false]);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function syncCouponsFromStripe() {
        try {
            $stripeCoupons = $this->stripe->promotionCodes->all(['limit' => 100])->data;
            $this->command->info("Found " . count($stripeCoupons) . " coupons in Stripe");

            foreach ($stripeCoupons as $promoCode) {
                $stripeCoupon = $promoCode->coupon;

                Coupon::create([
                    'code' => $promoCode->code,
                    'promo_id' => $promoCode->id,
                    'coupon_id' => $stripeCoupon->id,
                    'coupon_percent_off' => $stripeCoupon->percent_off,
                    'coupon_amount_off' => $stripeCoupon->amount_off,
                    'coupon_currency' => $stripeCoupon->currency,
                    'coupon_duration' => $stripeCoupon->duration,
                    'coupon_duration_months' => $stripeCoupon->duration_in_months,
                    'coupon_max_redemptions' => $stripeCoupon->max_redemptions,
                    'coupon_redeem_by' => $stripeCoupon->redeem_by,
                    'metadata' => $stripeCoupon->metadata,
                ]);
            }

            $this->command->info("Sync complete. Total coupons in DB: " . Coupon::count());
        } catch (Exception $e) {
            $this->command->error("Failed to sync coupons: " . $e->getMessage());
        }
    }

    private function createCoupon($factory) {
        $couponData = $factory->raw();

        try {
            if ($this->can_generate) {
                try {
                    $stripeCoupon = $this->stripe->coupons->retrieve($couponData['coupon_id']);
                } catch (ApiErrorException $e) {
                    if ($e->getStripeCode() === 'resource_missing') {
                        $this->command->info("Creating coupon: {$couponData['coupon_id']}");
                        $stripeCoupon = $this->stripe->coupons->create($this->transformForStripe($couponData));

                        $this->command->info("Creating promo code for: {$couponData['code']}");
                        $promoCode = $this->stripe->promotionCodes->create([
                            'coupon' => $stripeCoupon->id,
                            'code' => $couponData['code']
                        ]);

                        $couponData['promo_id'] = $promoCode->id;
                        $this->command->info("Promo code created: {$promoCode->id}");
                    }
                }
            }

            // Use updateOrCreate instead of checking exists
            $factory->updateOrCreate(
                ['code' => $couponData['code']],
                ['promo_id' => $couponData['promo_id'] ?? null]
            );
        } catch (Exception $e) {
            $this->command->warn("Skipped coupon {$couponData['code']}: {$e->getMessage()}");
        }
    }

    private function transformForStripe(array $couponData): array {
        $stripeData = [
            'id' => $couponData['coupon_id'],
            'percent_off' => $couponData['coupon_percent_off'],
            'currency' => $couponData['coupon_currency'],
            'duration' => $couponData['coupon_duration'],
            'metadata' => $couponData['metadata'],
        ];

        if (!is_null($couponData['coupon_duration_months'])) {
            $stripeData['duration_in_months'] = $couponData['coupon_duration_months'];
        }
        if (!is_null($couponData['coupon_max_redemptions'])) {
            $stripeData['max_redemptions'] = $couponData['coupon_max_redemptions'];
        }
        if (!is_null($couponData['coupon_amount_off'])) {
            $stripeData['amount_off'] = $couponData['coupon_amount_off'];
        }
        if (!empty($couponData['coupon_redeem_by'])) {
            $stripeData['redeem_by'] = is_numeric($couponData['coupon_redeem_by'])
                ? (int)$couponData['coupon_redeem_by']
                : strtotime($couponData['coupon_redeem_by']);
        }
        return $stripeData;
    }
}

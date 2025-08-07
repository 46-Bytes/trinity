<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory {
    protected $model = Coupon::class;

    public function definition(): array {
        $code = strtoupper(Str::random(8));
        return [
            'code' => $code,
            'promo_id' => null,
            'coupon_id' => $code,
            'coupon_percent_off' => null,
            'coupon_amount_off' => null,
            'coupon_currency' => 'aud',
            'coupon_duration' => 'once',
            'coupon_duration_months' => null,
            'coupon_max_redemptions' => null,
            'coupon_redeem_by' => null,
            'coupon_single_use' => false,
            'promo_active' => true,
            'promo_max_codes' => null,
            'promo_min_amount' => null,
            'promo_customer_id' => null,
            'promo_restriction' => null,
            'is_valid' => true,
            'metadata' => [],
        ];
    }

    public function percentOff(int $percent = 20): static {
        return $this->state(fn(array $attributes) => [
            'coupon_percent_off' => $percent,
            'coupon_amount_off' => null,
        ]);
    }

    public function amountOff(int $amount = 1000): static {
        return $this->state(fn(array $attributes) => [
            'coupon_amount_off' => $amount,
            'coupon_percent_off' => null,
        ]);
    }

    public function singleUse(): static {
        return $this->state(fn(array $attributes) => [
            'coupon_single_use' => true,
            'coupon_max_redemptions' => 1,
            'promo_max_codes' => 1
        ]);
    }

    public function expiresIn(int $days = 30): static {
        return $this->state(fn(array $attributes) => [
            'coupon_redeem_by' => now()->addDays($days),
        ]);
    }

    public function freebie(): static {
        $code = strtoupper(Str::random(8));
        return $this->state(fn(array $attributes) => [
            'code' => $code,
            'coupon_id' => $code,
            'coupon_percent_off' => 100,
            'coupon_amount_off' => null,
            'coupon_currency' => 'aud',
            'coupon_duration' => 'once',
            'coupon_single_use' => true,
            'coupon_max_redemptions' => 1,
            'promo_active' => true,
            'promo_max_codes' => 1,
            'metadata' => ['description' => 'Single-use 100% off coupon']
        ]);
    }
}

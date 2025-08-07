<?php

namespace App\Enums;

enum Products: string {
    case BASIC_PLAN = 'price_1QROCXPcm1KjIvUYj7e5nMlt';
    case ADVANCED_PLAN = 'price_1QROMxPcm1KjIvUY7acvgHgw';

    public static function getStripeProductIds(): array
    {
        return [self::BASIC_PLAN->value];
        /**
         * TODO : Remove above line and uncomment below line when "Advanced plan" is ready for production
         */
        //return array_map(fn($product) => $product->value, self::cases());
    }

    public static function isValidStripePriceId(string $stripePriceId): bool {
        try {
            self::getByStripePriceId($stripePriceId);
            return true;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    /**
     * Get the product by Stripe price ID.
     */
    public static function getByStripePriceId(string $stripePriceId): self {
        return match ($stripePriceId) {
            'price_1QROCXPcm1KjIvUYj7e5nMlt' => self::BASIC_PLAN,
            'price_1QROMxPcm1KjIvUY7acvgHgw' => self::ADVANCED_PLAN,
            default => throw new \InvalidArgumentException('Invalid Stripe price ID.')
        };
    }

    public function label(): string {
        return self::labels()[$this->value];
    }

    public static function labels(): array {
        return [
            self::BASIC_PLAN->value => 'Basic',
            self::ADVANCED_PLAN->value => 'Advanced',
        ];
    }

    public function description(): string {
        return self::descriptions()[$this->value];
    }

    public static function descriptions(): array {
        return [
            self::BASIC_PLAN->value => 'Business Diagnostic with Chat',
            self::ADVANCED_PLAN->value => 'Business Diagnostic with Chat and Files',
        ];
    }

    public function summary(): string {
        return self::summaries()[$this->value];
    }

    public static function summaries(): array {
        return [
            self::BASIC_PLAN->value => 'Basic plan.',
            self::ADVANCED_PLAN->value => 'Advanced plan.',
        ];
    }
}

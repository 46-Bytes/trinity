<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Product extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stripe_sandbox_product_id',
        'stripe_sandbox_price_id',
        'stripe_production_product_id',
        'stripe_production_price_id',
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'is_active',
        'type',
        'metadata',
        'stripe_created_at',
        'stripe_updated_at',
        'image_url',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'metadata' => 'array',
        'stripe_created_at' => 'datetime',
        'stripe_updated_at' => 'datetime',
    ];

    public static function getStripePriceIds(?string $environment = null): array {
        $column = ($environment === 'production' || (!$environment && App::environment('production')))
            ? 'stripe_production_price_id'
            : 'stripe_sandbox_price_id';

        return self::whereNotNull($column)->pluck($column)->toArray();
    }

    public static function getStripePriceIdBySlug(string $slug): ?string {
        $product = self::where('slug', $slug)->first();
        if (!$product) {
            return null;
        }
        if (App::environment('production')) {
            return $product->stripe_production_price_id;
        }
        return $product->stripe_sandbox_price_id;
    }

    // get price id by slug

    /**
     * Get all Stripe product IDs based on the environment.
     *
     * @param string|null $environment Optional environment override (e.g., 'production', 'sandbox').
     * @return array
     */
    public static function getStripeProductIds(?string $environment = null): array {
        // Determine the column based on the provided environment or the current app environment
        if ($environment === 'production' || (!$environment && App::environment('production'))) {
            $column = 'stripe_production_product_id';
        } else {
            $column = 'stripe_sandbox_product_id';
        }

        // Fetch all products and return their Stripe product IDs as an array
        return self::whereNotNull($column)->pluck($column)->toArray();
    }

    /**
     * Find a product by Stripe product ID.
     *
     * @param string $stripeProductId
     * @return Product|null
     */
    public static function findByStripeProductId(string $stripeProductId): ?self {
        return self::where('stripe_sandbox_product_id', $stripeProductId)
            ->orWhere('stripe_production_product_id', $stripeProductId)
            ->first();
    }

    /**
     * Get the appropriate Stripe product ID based on the environment and product slug.
     *
     * @param string $slug
     * @return string|null
     */
    public static function getStripeProductIdBySlug(string $slug): ?string {
        // Find the product by slug
        $product = self::where('slug', $slug)->first();

        // Return null if the product doesn't exist
        if (!$product) {
            return null;
        }

        // Return the appropriate Stripe product ID based on the environment
        if (App::environment('production')) {
            return $product->stripe_production_product_id;
        }

        return $product->stripe_sandbox_product_id;
    }

    public function getPriceIdAttribute(): ?string {
        return App::environment('production')
            ? $this->stripe_production_price_id
            : $this->stripe_sandbox_price_id;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Load products from a JSON file
        $products = json_decode(File::get(database_path('data/products.json')), true);

        // Loop through the products and create each one
        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}

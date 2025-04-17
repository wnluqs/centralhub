<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 1200.99,
            'description' => 'A high-performance laptop',
        ]);

        Product::create([
            'name' => 'Smartphone',
            'price' => 799.50,
            'description' => 'Latest model with OLED display',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure suppliers exist first
        $appleSupplier = Supplier::where('Name', 'Apple Inc.')->first();
        $samsungSupplier = Supplier::where('Name', 'Samsung Electronics')->first();
        $googleSupplier = Supplier::where('Name', 'Google LLC')->first();
        $oneplusSupplier = Supplier::where('Name', 'OnePlus Technology')->first();
        $xiaomiSupplier = Supplier::where('Name', 'Xiaomi Corporation')->first();

        $products = [
            [
                'Name' => 'iPhone 15 Pro Max',
                'Description' => 'The most advanced iPhone with titanium design, A17 Pro chip, and revolutionary camera system. Features 6.7-inch Super Retina XDR display.',
                'Price' => 1199.00,
                'Stock' => 25,
                'SupplierID' => $appleSupplier?->SupplierID ?? 1,
                'image_url' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-max-naturaltitanium-select?wid=470&hei=556&fmt=png-alpha&.v=1692845699311'
            ],
            [
                'Name' => 'iPhone 15',
                'Description' => 'iPhone 15 with Dynamic Island, 48MP camera, and USB-C. Available in beautiful colors with aerospace-grade aluminum.',
                'Price' => 799.00,
                'Stock' => 40,
                'SupplierID' => $appleSupplier?->SupplierID ?? 1,
                'image_url' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pink-select-202309?wid=470&hei=556&fmt=png-alpha&.v=1692851973223'
            ],
            [
                'Name' => 'Samsung Galaxy S24 Ultra',
                'Description' => 'Ultimate Galaxy experience with S Pen, 200MP camera, Galaxy AI features, and titanium build. 6.8-inch Dynamic AMOLED 2X display.',
                'Price' => 1299.00,
                'Stock' => 18,
                'SupplierID' => $samsungSupplier?->SupplierID ?? 2,
                'image_url' => 'https://images.samsung.com/is/image/samsung/p6pim/in/2401/gallery/in-galaxy-s24-ultra-s928-490496-sm-s928bztqins-539572540?$650_519_PNG$'
            ],
            [
                'Name' => 'Samsung Galaxy S24',
                'Description' => 'Powerful Galaxy S24 with advanced AI capabilities, 50MP triple camera system, and all-day battery life.',
                'Price' => 799.00,
                'Stock' => 32,
                'SupplierID' => $samsungSupplier?->SupplierID ?? 2,
                'image_url' => 'https://images.samsung.com/is/image/samsung/p6pim/in/2401/gallery/in-galaxy-s24-s921-sm-s921bzveins-539572525?$650_519_PNG$'
            ],
            [
                'Name' => 'Google Pixel 8 Pro',
                'Description' => 'Google Pixel 8 Pro with Magic Eraser, Best Take, and Google AI. Features 6.7-inch LTPO OLED display and Tensor G3 chip.',
                'Price' => 999.00,
                'Stock' => 15,
                'SupplierID' => $googleSupplier?->SupplierID ?? 3,
                'image_url' => 'https://lh3.googleusercontent.com/OUhFTfPh2fPx8jNNz6YET8qjUi6wRAl5OzEWdPb-OVNzYyEDfEgS-CWKfKP5L1BNRC8v0dNjdEhGDcKwOqHXsZ0cL5L1bLnPSg=rw-e365-w1440'
            ],
            [
                'Name' => 'Google Pixel 8',
                'Description' => 'Pixel 8 with Google AI and computational photography. Features 6.2-inch Actua display and enhanced camera capabilities.',
                'Price' => 699.00,
                'Stock' => 28,
                'SupplierID' => $googleSupplier?->SupplierID ?? 3,
                'image_url' => 'https://lh3.googleusercontent.com/rEzaO7pPbHaKGUFNXhCflm5jOFXz9HzNqzCU3RTQO5PYLRs1mZ3-7K5QRZ5L1BNRC8v0dNjdEhGDcKwOqHXsZ0cL5L1bLnPSg=rw-e365-w1440'
            ],
            [
                'Name' => 'OnePlus 12',
                'Description' => 'Flagship OnePlus 12 with Snapdragon 8 Gen 3, 120Hz ProXDR display, 100W fast charging, and Hasselblad camera system.',
                'Price' => 899.00,
                'Stock' => 20,
                'SupplierID' => $oneplusSupplier?->SupplierID ?? 4,
                'image_url' => null
            ],
            [
                'Name' => 'Xiaomi 14 Ultra',
                'Description' => 'Premium Xiaomi 14 Ultra with Leica camera system, Snapdragon 8 Gen 3, and 6.73-inch C8 curved AMOLED display.',
                'Price' => 1099.00,
                'Stock' => 12,
                'SupplierID' => $xiaomiSupplier?->SupplierID ?? 5,
                'image_url' => null
            ],
            [
                'Name' => 'OnePlus 12R',
                'Description' => 'OnePlus 12R with flagship performance, 100W SUPERVOOC charging, and Trinity Engine for smooth gaming experience.',
                'Price' => 599.00,
                'Stock' => 35,
                'SupplierID' => $oneplusSupplier?->SupplierID ?? 4,
                'image_url' => null
            ],
            [
                'Name' => 'Xiaomi 14',
                'Description' => 'Xiaomi 14 with Snapdragon 8 Gen 3, Leica camera, HyperOS, and premium glass-metal design.',
                'Price' => 799.00,
                'Stock' => 22,
                'SupplierID' => $xiaomiSupplier?->SupplierID ?? 5,
                'image_url' => null
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestOrderSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get or create a test customer
        $customer = Customer::first();
        if (!$customer) {
            $customer = Customer::create([
                'Name' => 'Test Customer',
                'Email' => 'test@example.com',
                'Password' => bcrypt('password')
            ]);
        }

        // Get a product
        $product = Product::first();
        if (!$product) {
            echo "No products found. Please add products first.\n";
            return;
        }

        // Create a test order with proper shipping information
        $order = Order::create([
            'CustomerID' => $customer->CustomerID,
            'shipping_name' => 'John Doe',
            'shipping_email' => 'john@example.com',
            'shipping_phone' => '+63 912 345 6789',
            'shipping_address' => '123 Test Street, Barangay Test',
            'shipping_city' => 'Manila',
            'shipping_state' => 'Metro Manila',
            'shipping_zip' => '1000',
            'shipping_country' => 'Philippines',
            'notes' => 'Test order for debugging',
            'OrderDate' => now(),
            'TotalAmount' => $product->Price,
            'Status' => 'pending'
        ]);

        // Create order item
        OrderItem::create([
            'OrderID' => $order->OrderID,
            'ProductID' => $product->ProductID,
            'Quantity' => 1,
            'UnitPrice' => $product->Price
        ]);

        // Create payment record
        Payment::create([
            'OrderID' => $order->OrderID,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'amount' => $product->Price
        ]);

        echo "Test order created successfully with ID: {$order->OrderID}\n";
    }
}

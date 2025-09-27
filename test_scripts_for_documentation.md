# MobileHub E-commerce Application Test Scripts

## Overview
These test scripts cover the main functionalities of the MobileHub Laravel e-commerce application including product variants, cart operations, order management, payment processing, and staff dashboard features.

## 1. Product Variant Tests

### Test Case 1.1: Create Product Variant
```php
<?php
// Test: Creating a new product variant
public function test_can_create_product_variant()
{
    // Arrange
    $product = Product::factory()->create();
    $variantData = [
        'ProductID' => $product->ProductID,
        'Color' => 'Black',
        'RAM' => '8GB',
        'Storage' => '128GB',
        'Price' => 999.99,
        'SKU' => 'PHN-BLK-8GB-128GB',
        'Stock' => 50,
        'ImageURL' => 'images/phone-black.jpg',
        'IsActive' => true
    ];

    // Act
    $response = $this->post(route('staff.variants.store'), $variantData);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('product_variants', $variantData);
}
```

### Test Case 1.2: Validate Unique Variant Combinations
```php
<?php
// Test: Ensure variant combinations are unique per product
public function test_variant_combination_must_be_unique()
{
    // Arrange
    $product = Product::factory()->create();
    ProductVariant::factory()->create([
        'ProductID' => $product->ProductID,
        'Color' => 'Blue',
        'RAM' => '6GB',
        'Storage' => '64GB'
    ]);

    // Act - Try to create duplicate combination
    $response = $this->post(route('staff.variants.store'), [
        'ProductID' => $product->ProductID,
        'Color' => 'Blue',
        'RAM' => '6GB',
        'Storage' => '64GB',
        'Price' => 799.99,
        'SKU' => 'PHN-BLU-6GB-64GB-2',
        'Stock' => 25
    ]);

    // Assert
    $response->assertSessionHasErrors();
}
```

## 2. Shopping Cart Tests

### Test Case 2.1: Add Product Variant to Cart
```php
<?php
// Test: Adding a product variant to shopping cart
public function test_can_add_product_variant_to_cart()
{
    // Arrange
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'ProductID' => $product->ProductID,
        'Stock' => 10
    ]);

    // Act
    $response = $this->actingAs($user, 'web')
                    ->post(route('cart.add.variant'), [
                        'VariantID' => $variant->VariantID,
                        'quantity' => 2
                    ]);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('carts', [
        'CustomerID' => $user->CustomerID,
        'VariantID' => $variant->VariantID,
        'Quantity' => 2
    ]);
}
```

### Test Case 2.2: Update Cart Item Quantity
```php
<?php
// Test: Updating cart item quantity
public function test_can_update_cart_item_quantity()
{
    // Arrange
    $user = User::factory()->create();
    $variant = ProductVariant::factory()->create(['Stock' => 10]);
    $cartItem = Cart::factory()->create([
        'CustomerID' => $user->CustomerID,
        'VariantID' => $variant->VariantID,
        'Quantity' => 1
    ]);

    // Act
    $response = $this->actingAs($user, 'web')
                    ->put(route('cart.update', $cartItem->CartID), [
                        'quantity' => 3
                    ]);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('carts', [
        'CartID' => $cartItem->CartID,
        'Quantity' => 3
    ]);
}
```

## 3. Order Management Tests

### Test Case 3.1: Create Order with Variants
```php
<?php
// Test: Creating an order with product variants
public function test_can_create_order_with_variants()
{
    // Arrange
    $user = User::factory()->create();
    $variant1 = ProductVariant::factory()->create(['Price' => 799.99, 'Stock' => 5]);
    $variant2 = ProductVariant::factory()->create(['Price' => 899.99, 'Stock' => 3]);
    
    Cart::factory()->create([
        'CustomerID' => $user->CustomerID,
        'VariantID' => $variant1->VariantID,
        'Quantity' => 2
    ]);
    
    Cart::factory()->create([
        'CustomerID' => $user->CustomerID,
        'VariantID' => $variant2->VariantID,
        'Quantity' => 1
    ]);

    // Act
    $response = $this->actingAs($user, 'web')
                    ->post(route('orders.create'), [
                        'shipping_address' => '123 Main St, City, Country',
                        'payment_method' => 'Credit Card'
                    ]);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'CustomerID' => $user->CustomerID,
        'TotalAmount' => 2499.97 // (799.99 * 2) + (899.99 * 1)
    ]);
}
```

### Test Case 3.2: Update Order Status
```php
<?php
// Test: Staff updating order status
public function test_staff_can_update_order_status()
{
    // Arrange
    $staff = Staff::factory()->create();
    $order = Order::factory()->create(['Status' => 'Pending']);

    // Act
    $response = $this->actingAs($staff, 'staff')
                    ->put(route('staff.orders.update', $order->OrderID), [
                        'Status' => 'Processing'
                    ]);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'OrderID' => $order->OrderID,
        'Status' => 'Processing'
    ]);
}
```

## 4. Payment Processing Tests

### Test Case 4.1: Process Payment
```php
<?php
// Test: Processing a payment
public function test_can_process_payment()
{
    // Arrange
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'CustomerID' => $user->CustomerID,
        'TotalAmount' => 999.99,
        'Status' => 'Pending'
    ]);

    // Act
    $response = $this->actingAs($user, 'web')
                    ->post(route('payments.process'), [
                        'OrderID' => $order->OrderID,
                        'PaymentMethod' => 'Credit Card',
                        'Amount' => 999.99
                    ]);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('payments', [
        'OrderID' => $order->OrderID,
        'Amount' => 999.99,
        'Status' => 'Completed'
    ]);
}
```

## 5. Staff Dashboard Tests

### Test Case 5.1: View Sales Report
```php
<?php
// Test: Staff viewing sales reports
public function test_staff_can_view_sales_report()
{
    // Arrange
    $staff = Staff::factory()->create();
    Order::factory()->count(5)->create([
        'Status' => 'Completed',
        'OrderDate' => now()->subDays(7)
    ]);

    // Act
    $response = $this->actingAs($staff, 'staff')
                    ->get(route('staff.reports.sales', [
                        'start_date' => now()->subDays(30)->format('Y-m-d'),
                        'end_date' => now()->format('Y-m-d')
                    ]));

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('staff.reports.sales');
    $response->assertViewHas('orders');
}
```

### Test Case 5.2: Inventory Management
```php
<?php
// Test: Staff updating product stock
public function test_staff_can_update_product_stock()
{
    // Arrange
    $staff = Staff::factory()->create();
    $variant = ProductVariant::factory()->create(['Stock' => 10]);

    // Act
    $response = $this->actingAs($staff, 'staff')
                    ->put(route('staff.variants.update-stock', $variant->VariantID), [
                        'Stock' => 25
                    ]);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('product_variants', [
        'VariantID' => $variant->VariantID,
        'Stock' => 25
    ]);
}
```

## 6. Customer Authentication Tests

### Test Case 6.1: Customer Registration
```php
<?php
// Test: Customer registration process
public function test_customer_can_register()
{
    // Act
    $response = $this->post(route('customer.register'), [
        'Name' => 'John Doe',
        'Email' => 'john@example.com',
        'Password' => 'password123',
        'Password_confirmation' => 'password123',
        'Phone' => '1234567890',
        'Address' => '123 Main St'
    ]);

    // Assert
    $response->assertRedirect(route('customer.dashboard'));
    $this->assertDatabaseHas('customers', [
        'Email' => 'john@example.com',
        'Name' => 'John Doe'
    ]);
}
```

### Test Case 6.2: Customer Login
```php
<?php
// Test: Customer login functionality
public function test_customer_can_login()
{
    // Arrange
    $user = User::factory()->create([
        'Email' => 'test@example.com',
        'Password' => Hash::make('password123')
    ]);

    // Act
    $response = $this->post(route('customer.login'), [
        'Email' => 'test@example.com',
        'Password' => 'password123'
    ]);

    // Assert
    $response->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticated('web');
}
```

## 7. API Endpoint Tests (if applicable)

### Test Case 7.1: Get Products API
```php
<?php
// Test: API endpoint for retrieving products
public function test_api_can_retrieve_products()
{
    // Arrange
    $products = Product::factory()->count(3)->create();

    // Act
    $response = $this->getJson(route('api.products.index'));

    // Assert
    $response->assertStatus(200);
    $response->assertJsonCount(3, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => ['ProductID', 'Name', 'Price', 'Description']
        ]
    ]);
}
```

## 8. Validation Tests

### Test Case 8.1: Product Variant Validation
```php
<?php
// Test: Validation rules for product variants
public function test_product_variant_requires_valid_data()
{
    // Arrange
    $staff = Staff::factory()->create();

    // Act
    $response = $this->actingAs($staff, 'staff')
                    ->post(route('staff.variants.store'), [
                        'Color' => '', // Required field empty
                        'RAM' => 'invalid', // Invalid format
                        'Price' => -100 // Invalid price
                    ]);

    // Assert
    $response->assertSessionHasErrors(['Color', 'RAM', 'Price']);
}
```

## 9. Performance Tests

### Test Case 9.1: Homepage Load Time
```php
<?php
// Test: Homepage loads within acceptable time
public function test_homepage_loads_efficiently()
{
    // Arrange
    Product::factory()->count(20)->create();

    // Act
    $startTime = microtime(true);
    $response = $this->get(route('customer.homepage'));
    $endTime = microtime(true);

    // Assert
    $response->assertStatus(200);
    $this->assertLessThan(2.0, $endTime - $startTime); // Less than 2 seconds
}
```

## 10. Integration Tests

### Test Case 10.1: Complete Purchase Flow
```php
<?php
// Test: Full purchase workflow from cart to completion
public function test_complete_purchase_workflow()
{
    // Arrange
    $user = User::factory()->create();
    $variant = ProductVariant::factory()->create(['Stock' => 5, 'Price' => 699.99]);

    // Act & Assert
    // 1. Add to cart
    $this->actingAs($user, 'web')
         ->post(route('cart.add.variant'), [
             'VariantID' => $variant->VariantID,
             'quantity' => 1
         ])
         ->assertRedirect();

    // 2. View cart
    $this->get(route('cart.index'))
         ->assertStatus(200);

    // 3. Proceed to checkout
    $this->post(route('checkout.process'), [
        'shipping_address' => '123 Test St',
        'payment_method' => 'Credit Card'
    ])->assertRedirect();

    // 4. Verify order created
    $this->assertDatabaseHas('orders', [
        'CustomerID' => $user->CustomerID,
        'TotalAmount' => 699.99
    ]);

    // 5. Verify stock decreased
    $this->assertDatabaseHas('product_variants', [
        'VariantID' => $variant->VariantID,
        'Stock' => 4
    ]);
}
```

## Running the Tests

To run these tests in your Laravel application:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductVariantTest.php

# Run tests with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

## Test Database Setup

Make sure your `phpunit.xml` file includes:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Notes

1. These tests assume you have proper Factory classes set up for your models
2. Make sure to use database transactions or refresh the database between tests
3. Some tests may need additional setup depending on your specific implementation
4. Consider adding more edge cases and error scenarios
5. Performance benchmarks should be adjusted based on your server specifications

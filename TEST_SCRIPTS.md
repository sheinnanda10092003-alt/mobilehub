# MobileHub E-Commerce Application - Test Scripts

## Overview
This document contains comprehensive test scripts for the MobileHub Laravel e-commerce application, covering all major functionalities including authentication, product management, cart operations, checkout process, order management, and reporting systems.

## Test Environment Setup

### Prerequisites
- Laravel application running on local server
- MySQL database configured
- Sample data seeded (customers, products, staff)
- Web browser (Chrome, Firefox, or Edge recommended)

### Test Data Requirements
```sql
-- Ensure you have test data:
-- At least 5 products with different stock levels
-- At least 2 customer accounts
-- At least 1 staff account
-- Sample orders with different statuses
```

---

## 1. CUSTOMER AUTHENTICATION TESTS

### Test Case 1.1: Customer Registration
**Objective**: Verify customer can register successfully
**Steps**:
1. Navigate to `http://localhost:8000/customer/signup`
2. Fill in registration form:
   - First Name: "John"
   - Last Name: "Doe" 
   - Email: "john.doe@test.com"
   - Phone: "1234567890"
   - Password: "password123"
   - Confirm Password: "password123"
3. Click "Register" button

**Expected Results**:
- User is redirected to homepage
- Success message appears
- User is automatically logged in
- Navigation shows user name

**Test Status**: ☐ Pass ☐ Fail

### Test Case 1.2: Customer Login
**Objective**: Verify customer can login with valid credentials
**Steps**:
1. Navigate to `http://localhost:8000/customer/login`
2. Enter email: "john.doe@test.com"
3. Enter password: "password123"
4. Click "Login" button

**Expected Results**:
- User is redirected to homepage
- Welcome message displays
- Navigation shows logout option
- Cart icon appears in header

**Test Status**: ☐ Pass ☐ Fail

### Test Case 1.3: Customer Login - Invalid Credentials
**Objective**: Verify system handles invalid login attempts
**Steps**:
1. Navigate to `http://localhost:8000/customer/login`
2. Enter email: "invalid@test.com"
3. Enter password: "wrongpassword"
4. Click "Login" button

**Expected Results**:
- User remains on login page
- Error message displays: "Invalid credentials"
- No access to protected pages

**Test Status**: ☐ Pass ☐ Fail

---

## 2. PRODUCT BROWSING TESTS

### Test Case 2.1: Homepage Product Display
**Objective**: Verify products display correctly on homepage
**Steps**:
1. Navigate to `http://localhost:8000/`
2. Observe product listings

**Expected Results**:
- All products display with images
- Product names, prices visible
- "Add to Cart" buttons present
- Products show current stock status

**Test Status**: ☐ Pass ☐ Fail

### Test Case 2.2: Product Information Display
**Objective**: Verify product details are complete
**Steps**:
1. Navigate to homepage
2. Check each product card contains:
   - Product image (or placeholder)
   - Product name
   - Price formatted as currency
   - Stock status
   - Add to cart button

**Expected Results**:
- All product information displays correctly
- Images load properly
- Prices formatted as currency ($X.XX)
- Stock status accurate

**Test Status**: ☐ Pass ☐ Fail

---

## 3. SHOPPING CART TESTS

### Test Case 3.1: Add Product to Cart
**Objective**: Verify products can be added to cart
**Steps**:
1. Navigate to homepage
2. Click "Add to Cart" for any product
3. Observe cart counter in navigation
4. Check for success message

**Expected Results**:
- Cart counter increments
- Success message appears
- AJAX request completes successfully
- No page refresh occurs

**Test Status**: ☐ Pass ☐ Fail

### Test Case 3.2: View Cart Page
**Objective**: Verify cart page displays correctly
**Steps**:
1. Add items to cart
2. Navigate to `http://localhost:8000/cart`
3. Review cart contents

**Expected Results**:
- All added products display
- Quantities are correct
- Subtotal calculations accurate
- Update/Remove buttons functional

**Test Status**: ☐ Pass ☐ Fail

### Test Case 3.3: Update Cart Quantities
**Objective**: Verify cart quantities can be modified
**Steps**:
1. Navigate to cart page
2. Change quantity for an item
3. Click "Update" button
4. Observe changes

**Expected Results**:
- Quantity updates correctly
- Subtotal recalculates
- Total amount updates
- Success message displays

**Test Status**: ☐ Pass ☐ Fail

### Test Case 3.4: Remove Item from Cart
**Objective**: Verify items can be removed from cart
**Steps**:
1. Navigate to cart page
2. Click "Remove" button for an item
3. Confirm removal if prompted

**Expected Results**:
- Item disappears from cart
- Cart totals recalculate
- Cart counter decrements
- Success message displays

**Test Status**: ☐ Pass ☐ Fail

### Test Case 3.5: Clear Entire Cart
**Objective**: Verify entire cart can be cleared
**Steps**:
1. Navigate to cart page with items
2. Click "Clear Cart" button
3. Confirm action if prompted

**Expected Results**:
- All items removed from cart
- "Cart is empty" message displays
- Cart counter shows 0
- Checkout button disabled/hidden

**Test Status**: ☐ Pass ☐ Fail

---

## 4. CHECKOUT PROCESS TESTS

### Test Case 4.1: Checkout Page Access
**Objective**: Verify checkout page loads with cart items
**Steps**:
1. Add items to cart
2. Navigate to `http://localhost:8000/checkout`
3. Review checkout page

**Expected Results**:
- Order summary displays correctly
- Shipping form is present
- Payment options available
- Total amount matches cart

**Test Status**: ☐ Pass ☐ Fail

### Test Case 4.2: Complete Checkout Process
**Objective**: Verify full checkout process works
**Steps**:
1. Navigate to checkout page with items
2. Fill shipping information:
   - Name: "John Doe"
   - Email: "john.doe@test.com"
   - Phone: "1234567890"
   - Address: "123 Test Street"
   - City: "Test City"
   - State: "Test State"
   - ZIP: "12345"
3. Select payment method
4. Click "Place Order"

**Expected Results**:
- Order is processed successfully
- Redirected to success page
- Order confirmation displays
- Email notification sent (if configured)
- Cart is cleared

**Test Status**: ☐ Pass ☐ Fail

### Test Case 4.3: Checkout Validation
**Objective**: Verify checkout form validation
**Steps**:
1. Navigate to checkout page
2. Leave required fields empty
3. Click "Place Order"

**Expected Results**:
- Validation errors display
- Order is not processed
- User remains on checkout page
- Required fields highlighted

**Test Status**: ☐ Pass ☐ Fail

---

## 5. ORDER MANAGEMENT TESTS (Customer)

### Test Case 5.1: View Order History
**Objective**: Verify customers can view their orders
**Steps**:
1. Login as customer
2. Navigate to `http://localhost:8000/orders`
3. Review order list

**Expected Results**:
- All customer orders display
- Order details are accurate
- Order statuses are correct
- Orders sorted by date (newest first)

**Test Status**: ☐ Pass ☐ Fail

### Test Case 5.2: View Order Details
**Objective**: Verify order detail page works
**Steps**:
1. Navigate to order history
2. Click on any order to view details
3. Review order information

**Expected Results**:
- Complete order details display
- Product information accurate
- Shipping details present
- Payment information shown
- Order status current

**Test Status**: ☐ Pass ☐ Fail

---

## 6. STAFF AUTHENTICATION TESTS

### Test Case 6.1: Staff Login
**Objective**: Verify staff can login to admin panel
**Steps**:
1. Navigate to `http://localhost:8000/staff/login`
2. Enter staff credentials
3. Click "Login"

**Expected Results**:
- Redirected to staff dashboard
- Staff navigation menu appears
- Access to admin functions granted

**Test Status**: ☐ Pass ☐ Fail

### Test Case 6.2: Staff Dashboard Access
**Objective**: Verify staff dashboard loads correctly
**Steps**:
1. Login as staff member
2. Navigate to `http://localhost:8000/staff/dashboard`
3. Review dashboard content

**Expected Results**:
- Dashboard displays correctly
- Quick action buttons work
- Statistics cards show data
- Navigation menu functional

**Test Status**: ☐ Pass ☐ Fail

---

## 7. ORDER MANAGEMENT TESTS (Staff)

### Test Case 7.1: View All Orders
**Objective**: Verify staff can view all orders
**Steps**:
1. Login as staff
2. Navigate to `http://localhost:8000/staff/orders`
3. Review order list

**Expected Results**:
- All orders display correctly
- Filter and search options work
- Pagination functions properly
- Order details accessible

**Test Status**: ☐ Pass ☐ Fail

### Test Case 7.2: Update Order Status
**Objective**: Verify staff can update order status
**Steps**:
1. Navigate to staff orders page
2. Click on an order to view details
3. Change order status
4. Save changes

**Expected Results**:
- Status updates successfully
- Changes reflected immediately
- Status history maintained
- Customer notified (if configured)

**Test Status**: ☐ Pass ☐ Fail

---

## 8. CUSTOMER MANAGEMENT TESTS (Staff)

### Test Case 8.1: View Customer List
**Objective**: Verify staff can view customer list
**Steps**:
1. Login as staff
2. Navigate to `http://localhost:8000/staff/customers`
3. Review customer list

**Expected Results**:
- All customers display
- Customer information accurate
- Search and filter work
- Pagination functional

**Test Status**: ☐ Pass ☐ Fail

### Test Case 8.2: View Customer Details
**Objective**: Verify customer detail page works
**Steps**:
1. Navigate to customer list
2. Click on a customer to view details
3. Review customer information

**Expected Results**:
- Customer details display correctly
- Order history shows
- Statistics are accurate
- Related information present

**Test Status**: ☐ Pass ☐ Fail

---

## 9. PRODUCT MANAGEMENT TESTS (Staff)

### Test Case 9.1: View Product List
**Objective**: Verify staff can manage products
**Steps**:
1. Login as staff
2. Navigate to `http://localhost:8000/staff/products`
3. Review product list

**Expected Results**:
- All products display
- Product information complete
- Edit/Delete buttons functional
- Add new product option available

**Test Status**: ☐ Pass ☐ Fail

### Test Case 9.2: Add New Product
**Objective**: Verify new products can be added
**Steps**:
1. Navigate to staff products page
2. Click "Add New Product"
3. Fill product form:
   - Name: "Test Phone"
   - Description: "Test description"
   - Price: "599.99"
   - Stock: "10"
   - Supplier: Select from dropdown
4. Submit form

**Expected Results**:
- Product added successfully
- Redirected to product list
- New product appears in list
- Success message displays

**Test Status**: ☐ Pass ☐ Fail

### Test Case 9.3: Edit Product
**Objective**: Verify products can be edited
**Steps**:
1. Navigate to product list
2. Click "Edit" for any product
3. Modify product information
4. Save changes

**Expected Results**:
- Changes saved successfully
- Updates reflected in product list
- Success message displays
- Data integrity maintained

**Test Status**: ☐ Pass ☐ Fail

---

## 10. REPORTING TESTS (Staff)

### Test Case 10.1: Sales Report
**Objective**: Verify sales reports generate correctly
**Steps**:
1. Login as staff
2. Navigate to `http://localhost:8000/staff/reports/sales`
3. Review sales report

**Expected Results**:
- Report displays with correct data
- Charts render properly
- Date filters work
- Export function available

**Test Status**: ☐ Pass ☐ Fail

### Test Case 10.2: Inventory Report
**Objective**: Verify inventory reports work
**Steps**:
1. Navigate to `http://localhost:8000/staff/reports/inventory`
2. Review inventory report

**Expected Results**:
- All products listed with stock levels
- Low stock alerts work
- Stock distribution chart displays
- Filters function correctly

**Test Status**: ☐ Pass ☐ Fail

### Test Case 10.3: Customer Analytics
**Objective**: Verify customer analytics work
**Steps**:
1. Navigate to `http://localhost:8000/staff/reports/customers`
2. Review customer analytics

**Expected Results**:
- Customer statistics accurate
- Top customers display
- Charts render correctly
- Insights are meaningful

**Test Status**: ☐ Pass ☐ Fail

### Test Case 10.4: Product Performance Report
**Objective**: Verify product performance reports work
**Steps**:
1. Navigate to `http://localhost:8000/staff/reports/products`
2. Review product performance

**Expected Results**:
- Product sales data accurate
- Performance metrics correct
- Charts display properly
- Date filtering works

**Test Status**: ☐ Pass ☐ Fail

---

## 11. SECURITY TESTS

### Test Case 11.1: Access Control - Customer Areas
**Objective**: Verify unauthorized access is prevented
**Steps**:
1. Without logging in, try to access:
   - `http://localhost:8000/cart`
   - `http://localhost:8000/checkout`
   - `http://localhost:8000/orders`

**Expected Results**:
- Redirected to login page
- Access denied message
- No sensitive data exposed

**Test Status**: ☐ Pass ☐ Fail

### Test Case 11.2: Access Control - Staff Areas
**Objective**: Verify staff areas are protected
**Steps**:
1. Without staff login, try to access:
   - `http://localhost:8000/staff/dashboard`
   - `http://localhost:8000/staff/orders`
   - `http://localhost:8000/staff/customers`

**Expected Results**:
- Access denied
- Redirected to staff login
- No administrative data visible

**Test Status**: ☐ Pass ☐ Fail

### Test Case 11.3: Session Management
**Objective**: Verify session handling works correctly
**Steps**:
1. Login as customer
2. Close browser
3. Reopen and navigate to protected page
4. Test session timeout

**Expected Results**:
- Session maintained appropriately
- Timeout occurs after inactivity
- Secure session handling

**Test Status**: ☐ Pass ☐ Fail

---

## 12. PERFORMANCE TESTS

### Test Case 12.1: Page Load Times
**Objective**: Verify acceptable page load performance
**Steps**:
1. Measure load times for key pages:
   - Homepage
   - Product pages
   - Cart page
   - Checkout page
   - Staff dashboard

**Expected Results**:
- Pages load within 3 seconds
- No performance bottlenecks
- Responsive user experience

**Test Status**: ☐ Pass ☐ Fail

### Test Case 12.2: Database Query Performance
**Objective**: Check for efficient database queries
**Steps**:
1. Enable Laravel query logging
2. Navigate through application
3. Review query efficiency
4. Check for N+1 query problems

**Expected Results**:
- Minimal database queries
- No unnecessary duplicate queries
- Efficient data loading

**Test Status**: ☐ Pass ☐ Fail

---

## 13. MOBILE RESPONSIVENESS TESTS

### Test Case 13.1: Mobile Layout
**Objective**: Verify mobile-friendly design
**Steps**:
1. Open application in mobile browser or resize window
2. Test all major pages
3. Verify functionality on mobile

**Expected Results**:
- Layout adapts to mobile screens
- All functions accessible
- Touch-friendly interface
- Readable text and buttons

**Test Status**: ☐ Pass ☐ Fail

---

## 14. ERROR HANDLING TESTS

### Test Case 14.1: 404 Error Handling
**Objective**: Verify proper 404 error handling
**Steps**:
1. Navigate to non-existent pages:
   - `http://localhost:8000/nonexistent-page`
   - `http://localhost:8000/products/999999`

**Expected Results**:
- 404 error page displays
- User can navigate back
- No system errors exposed

**Test Status**: ☐ Pass ☐ Fail

### Test Case 14.2: Form Validation Error Handling
**Objective**: Verify form validation works properly
**Steps**:
1. Submit forms with invalid data
2. Test various validation scenarios
3. Check error message display

**Expected Results**:
- Clear validation messages
- Forms don't submit with invalid data
- User guidance provided

**Test Status**: ☐ Pass ☐ Fail

---

## 15. DATA INTEGRITY TESTS

### Test Case 15.1: Order Data Consistency
**Objective**: Verify order data remains consistent
**Steps**:
1. Create multiple orders
2. Verify data in database matches display
3. Check totals and calculations
4. Validate relationships

**Expected Results**:
- Order totals accurate
- Product quantities correct
- Customer data consistent
- No data corruption

**Test Status**: ☐ Pass ☐ Fail

### Test Case 15.2: Inventory Management
**Objective**: Verify stock levels update correctly
**Steps**:
1. Check current stock levels
2. Place orders
3. Verify stock decrements
4. Test low stock scenarios

**Expected Results**:
- Stock levels accurate
- Automatic decrements on orders
- Low stock warnings work
- No negative stock allowed

**Test Status**: ☐ Pass ☐ Fail

---

## TEST EXECUTION CHECKLIST

### Pre-Test Setup
- ☐ Laravel application running
- ☐ Database migrated and seeded
- ☐ Test environment configured
- ☐ Sample data available

### Test Environment
- ☐ PHP version compatible
- ☐ MySQL database accessible
- ☐ Web server running
- ☐ All dependencies installed

### Post-Test Cleanup
- ☐ Test data cleaned up
- ☐ Logs reviewed
- ☐ Performance metrics recorded
- ☐ Issues documented

---

## REPORTING TEMPLATE

### Test Summary Report
**Date**: _______________
**Tester**: ______________
**Environment**: _________

**Overall Results**:
- Total Tests: ___
- Passed: ___
- Failed: ___
- Skipped: ___

**Critical Issues Found**:
1. ________________________________
2. ________________________________
3. ________________________________

**Recommendations**:
1. ________________________________
2. ________________________________
3. ________________________________

**Sign-off**: 
Tester: _________________ Date: _______
Reviewer: _______________ Date: _______

---

## AUTOMATED TESTING COMMANDS

```bash
# Run Laravel feature tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Generate test coverage report
php artisan test --coverage

# Run tests with verbose output
php artisan test --verbose
```

## ADDITIONAL NOTES

- Always test on a clean database state
- Use consistent test data across test runs
- Document any deviations from expected results
- Take screenshots for visual verification
- Test with different user roles and permissions
- Verify both happy path and error scenarios

---

**Document Version**: 1.0
**Last Updated**: 2025-01-19
**Next Review**: 2025-02-19
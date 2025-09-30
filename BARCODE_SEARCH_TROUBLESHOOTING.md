# Barcode Search Troubleshooting Guide

## Issues Fixed:

### 1. **ORDER STATUS PROBLEM (CRITICAL)**
**BEFORE**: Only searched `'wc-processing'` orders
**AFTER**: Now searches `'wc-processing'`, `'wc-completed'`, `'wc-on-hold'` orders

### 2. **BETTER ERROR HANDLING**
- Added proper error checking for empty order results
- Improved sanitization using `sanitize_text_field()` instead of `sanitize_key()`
- Added debugging logs to track search process

### 3. **DEBUGGING ENABLED**
- Enabled `WP_DEBUG` and `WP_DEBUG_LOG` 
- Fixed typo: "Serach results" → "Search results"

## How to Test:

### Step 1: Check Your Orders
1. Go to **WooCommerce → Orders**
2. Look for orders that have Excel files uploaded
3. Note their **status** (Processing, Completed, On Hold, etc.)

### Step 2: Check Excel Data
1. Open an order that has barcode data
2. Look for the **Barcode Numbers** column
3. Note the barcode range (e.g., "123456789 to 123456799 (10)")

### Step 3: Test the Search
1. Go to your page with `[barcode_search]` shortcode
2. Enter one of the barcode numbers from Step 2
3. Click Submit

### Step 4: Check Debug Logs
1. Look in `/wp-content/debug.log` for entries like:
   - "Barcode Search: Looking for barcode - 123456789"
   - "Barcode Search: Found order ID - 12345" 
   - OR "Barcode Search: No orders found with barcode - 123456789"

## Common Issues & Solutions:

### Issue 1: "Barcode not found" but you know it exists
**Solution**: Check order status - might be 'wc-pending' or 'wc-cancelled'

### Issue 2: JavaScript not working  
**Solution**: Check browser console for errors, ensure jQuery is loaded

### Issue 3: AJAX not responding
**Solution**: Check if `admin-ajax.php` is accessible at `/wp-admin/admin-ajax.php`

### Issue 4: Wrong barcode format
**Solution**: Ensure barcode numbers match exactly (no extra spaces, leading zeros)

## Testing Checklist:
- [ ] Orders exist with uploaded Excel files
- [ ] Orders have correct status (processing/completed/on-hold)  
- [ ] Barcode data is stored in `_excel_file_data` meta
- [ ] Search form appears on page
- [ ] AJAX request reaches server
- [ ] Database query finds matching orders
- [ ] Results display correctly

## Next Steps:
1. Test with known barcode numbers
2. Check debug logs for detailed error info
3. Verify order statuses match search criteria
4. Test with different barcode formats

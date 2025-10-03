<?php
/**
 * QUICK FIX FOR THEME SESSION ERROR
 * This script provides a quick fix for the mydecor-child theme error
 */

echo "🚨 QUICK FIX FOR THEME SESSION ERROR\n";
echo "====================================\n\n";

echo "PROBLEM: Fatal error in mydecor-child/functions.php line 1117\n";
echo "ERROR: Call to a member function has_session() on null\n\n";

echo "SOLUTION: Add null checks before calling has_session()\n\n";

echo "STEP 1: Open this file in your WordPress admin:\n";
echo "wp-content/themes/mydecor-child/functions.php\n\n";

echo "STEP 2: Find this function (around line 1117):\n";
echo "function barcodemine_fix_woocommerce_sessions()\n\n";

echo "STEP 3: Replace the entire function with this code:\n";
echo "==================================================\n";

$fixed_code = 'function barcodemine_fix_woocommerce_sessions() {
    // Check if WooCommerce is active and session exists
    if (!class_exists("WooCommerce") || !function_exists("WC")) {
        return;
    }
    
    // Check if session object exists and is valid
    if (!WC()->session || !is_object(WC()->session)) {
        return;
    }
    
    // Check if has_session method exists before calling it
    if (!method_exists(WC()->session, "has_session")) {
        return;
    }
    
    // Now safely call has_session
    if (!WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }
}';

echo $fixed_code . "\n\n";

echo "STEP 4: Save the file\n\n";

echo "ALTERNATIVE: Use the automated fix script\n";
echo "=========================================\n";
echo "1. Upload fix-theme-session-error.php to WordPress root\n";
echo "2. Visit: https://barcodemine.com/fix-theme-session-error.php\n";
echo "3. The script will automatically fix the theme file\n\n";

echo "WHAT THIS FIX DOES:\n";
echo "===================\n";
echo "✅ Checks if WooCommerce is active\n";
echo "✅ Checks if session object exists\n";
echo "✅ Checks if has_session method exists\n";
echo "✅ Only calls has_session() if all checks pass\n";
echo "✅ Prevents fatal error from null object calls\n\n";

echo "EXPECTED RESULT:\n";
echo "===============\n";
echo "✅ WordPress will work normally\n";
echo "✅ No more fatal errors\n";
echo "✅ REST API will work\n";
echo "✅ You can edit and publish posts\n\n";

echo "If you need help, the automated fix script will handle everything!\n";
?>

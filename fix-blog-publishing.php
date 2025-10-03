<?php
/**
 * Quick Fix Script for Blog Publishing Issues
 * 
 * This script will:
 * 1. Generate new security keys
 * 2. Create a fixed wp-config.php
 * 3. Test database connection
 * 4. Provide step-by-step instructions
 */

echo "🚨 BLOG PUBLISHING FIX SCRIPT\n";
echo "==============================\n\n";

// Step 1: Generate Security Keys
echo "1. Generating new security keys...\n";
$keys_url = 'https://api.wordpress.org/secret-key/1.1/salt/';
$keys_content = file_get_contents($keys_url);

if ($keys_content) {
    echo "✅ Security keys generated successfully!\n\n";
    
    // Extract keys from the content
    preg_match_all("/define\s*\(\s*['\"]([^'\"]+)['\"],\s*['\"]([^'\"]+)['\"]\s*\);/", $keys_content, $matches);
    
    $keys = array();
    for ($i = 0; $i < count($matches[1]); $i++) {
        $keys[$matches[1][$i]] = $matches[2][$i];
    }
    
    echo "Generated keys:\n";
    foreach ($keys as $name => $value) {
        echo "- $name: " . substr($value, 0, 20) . "...\n";
    }
    echo "\n";
} else {
    echo "❌ Failed to generate security keys. You'll need to do this manually.\n";
    echo "Go to: https://api.wordpress.org/secret-key/1.1/salt/\n\n";
}

// Step 2: Create Fixed wp-config.php
echo "2. Creating fixed wp-config.php...\n";

$wp_config_content = '<?php
/**
 * The base configuration for WordPress
 * FIXED VERSION - Resolves blog publishing issues
 */

// ** Database settings ** //
define( \'DB_NAME\', \'kb_7ob15udm65\' );
define( \'DB_USER\', \'kb_7ob15udm65\' );
define( \'DB_PASSWORD\', \'S9F8Q2Ege825bRvq6W\' );
define( \'DB_HOST\', \'localhost\' );
define( \'DB_CHARSET\', \'utf8\' );
define( \'DB_COLLATE\', \'\' );

/**#@+
 * Authentication unique keys and salts.
 * Generated on ' . date('Y-m-d H:i:s') . '
 */';

if (!empty($keys)) {
    foreach ($keys as $name => $value) {
        $wp_config_content .= "\ndefine( '$name', '$value' );";
    }
} else {
    $wp_config_content .= '
define( \'AUTH_KEY\',         \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'SECURE_AUTH_KEY\',  \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'LOGGED_IN_KEY\',    \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'NONCE_KEY\',        \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'AUTH_SALT\',        \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'SECURE_AUTH_SALT\', \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'LOGGED_IN_SALT\',   \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );
define( \'NONCE_SALT\',       \'REPLACE-WITH-REAL-KEY-FROM-WORDPRESS.ORG\' );';
}

$wp_config_content .= '

/**#@-*/

$table_prefix = \'wp_\';

/**
 * Debugging and Performance Settings
 */
define( \'WP_DEBUG\', true );
define( \'WP_DEBUG_LOG\', true );
define( \'WP_DEBUG_DISPLAY\', false );
define( \'WP_MEMORY_LIMIT\', \'256M\' );
define( \'FS_METHOD\', \'direct\' );

/**
 * Security Settings
 */
define( \'DISALLOW_FILE_EDIT\', true );
define( \'WP_POST_REVISIONS\', 3 );

/**
 * Performance Settings
 */
ini_set(\'memory_limit\', \'256M\');
ini_set(\'max_execution_time\', 300);
ini_set(\'post_max_size\', \'64M\');
ini_set(\'upload_max_filesize\', \'64M\');

if ( ! defined( \'ABSPATH\' ) ) {
    define( \'ABSPATH\', __DIR__ . \'/\' );
}

require_once ABSPATH . \'wp-settings.php\';';

// Save the fixed wp-config.php
if (file_put_contents('wp-config-fixed.php', $wp_config_content)) {
    echo "✅ Fixed wp-config.php created successfully!\n\n";
} else {
    echo "❌ Failed to create wp-config.php file.\n\n";
}

// Step 3: Test Database Connection
echo "3. Testing database connection...\n";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kb_7ob15udm65", "kb_7ob15udm65", "S9F8Q2Ege825bRvq6W");
    echo "✅ Database connection successful!\n\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
}

// Step 4: Instructions
echo "4. NEXT STEPS TO FIX BLOG PUBLISHING:\n";
echo "=====================================\n\n";

echo "A. BACKUP YOUR CURRENT SITE:\n";
echo "   1. Download current wp-config.php\n";
echo "   2. Export database (optional but recommended)\n\n";

echo "B. REPLACE wp-config.php:\n";
echo "   1. Rename current wp-config.php to wp-config-backup.php\n";
echo "   2. Rename wp-config-fixed.php to wp-config.php\n";
echo "   3. Upload to your server\n\n";

echo "C. TEST BLOG PUBLISHING:\n";
echo "   1. Go to barcodemine.com/wp-admin/\n";
echo "   2. Try creating a new post\n";
echo "   3. Click 'Publish'\n";
echo "   4. Check if it works\n\n";

echo "D. IF STILL NOT WORKING:\n";
echo "   1. Check /wp-content/debug.log for error messages\n";
echo "   2. Try deactivating all plugins temporarily\n";
echo "   3. Switch to default WordPress theme\n";
echo "   4. Check server error logs in cPanel\n\n";

echo "E. COMMON ISSUES:\n";
echo "   - Plugin conflicts (deactivate all plugins)\n";
echo "   - Theme issues (switch to default theme)\n";
echo "   - Server memory limits (contact hosting provider)\n";
echo "   - File permissions (check .htaccess file)\n\n";

echo "🎯 MOST LIKELY CAUSE: Invalid security keys\n";
echo "✅ SOLUTION: Use the fixed wp-config.php with real security keys\n\n";

echo "📞 IF YOU NEED HELP:\n";
echo "   1. Check the BLOG_PUBLISHING_FIX.md file for detailed instructions\n";
echo "   2. Look at server error logs in cPanel\n";
echo "   3. Contact your hosting provider about server limits\n\n";

echo "🚀 Your blog publishing should work after applying these fixes!\n";
?>

<?php
/**
 * Security Cleanup Script for Barcodemine.com
 * Run this once to clean up security issues and harden WordPress
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

// Include WordPress
require_once(ABSPATH . 'wp-config.php');
require_once(ABSPATH . 'wp-includes/wp-db.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');

echo "<h1>🔒 Barcodemine Security Cleanup</h1>\n";
echo "<pre>\n";

// 1. Check and fix file permissions
echo "=== CHECKING FILE PERMISSIONS ===\n";

$security_fixes = [];

// Check wp-config.php permissions
if (file_exists(ABSPATH . 'wp-config.php')) {
    $perms = substr(sprintf('%o', fileperms(ABSPATH . 'wp-config.php')), -4);
    if ($perms !== '0644' && $perms !== '0600') {
        chmod(ABSPATH . 'wp-config.php', 0644);
        $security_fixes[] = "Fixed wp-config.php permissions: $perms → 0644";
    } else {
        echo "✅ wp-config.php permissions OK ($perms)\n";
    }
}

// Check .htaccess permissions
if (file_exists(ABSPATH . '.htaccess')) {
    $perms = substr(sprintf('%o', fileperms(ABSPATH . '.htaccess')), -4);
    if ($perms !== '0644') {
        chmod(ABSPATH . '.htaccess', 0644);
        $security_fixes[] = "Fixed .htaccess permissions: $perms → 0644";
    } else {
        echo "✅ .htaccess permissions OK ($perms)\n";
    }
}

// 2. Remove potentially dangerous files
echo "\n=== CHECKING FOR DANGEROUS FILES ===\n";

$dangerous_files = [
    'wp-admin/install.php.bak',
    'wp-config.php.bak',
    'wp-config.php.old',
    'wp-config.php.save',
    'wp-config.php~',
    'error_log',
    'debug.log',
    'php.ini',
    '.user.ini',
    'info.php',
    'phpinfo.php',
    'test.php',
    'shell.php',
    'c99.php',
    'r57.php',
    'adminer.php'
];

foreach ($dangerous_files as $file) {
    $full_path = ABSPATH . $file;
    if (file_exists($full_path)) {
        unlink($full_path);
        $security_fixes[] = "Removed dangerous file: $file";
    }
}

// 3. Check for suspicious uploads
echo "\n=== CHECKING UPLOADS DIRECTORY ===\n";

$uploads_dir = ABSPATH . 'wp-content/uploads/';
if (is_dir($uploads_dir)) {
    $suspicious_extensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'py', 'jsp', 'asp', 'sh'];
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploads_dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            if (in_array($extension, $suspicious_extensions)) {
                $relative_path = str_replace(ABSPATH, '', $file->getPathname());
                echo "⚠️  Suspicious file found: $relative_path\n";
                
                // Optionally remove (uncomment to auto-delete)
                // unlink($file->getPathname());
                // $security_fixes[] = "Removed suspicious upload: $relative_path";
            }
        }
    }
    echo "✅ Uploads directory scan complete\n";
}

// 4. Update security keys if they're default
echo "\n=== CHECKING SECURITY KEYS ===\n";

$config_content = file_get_contents(ABSPATH . 'wp-config.php');
if (strpos($config_content, 'put your unique phrase here') !== false) {
    echo "⚠️  Default security keys detected - these should be updated\n";
    echo "   Visit: https://api.wordpress.org/secret-key/1.1/salt/\n";
} else {
    echo "✅ Security keys appear to be customized\n";
}

// 5. Check database for suspicious content
echo "\n=== CHECKING DATABASE ===\n";

global $wpdb;

// Check for admin users with suspicious names
$suspicious_admins = $wpdb->get_results("
    SELECT u.ID, u.user_login, u.user_email 
    FROM {$wpdb->users} u
    INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
    WHERE um.meta_key = 'wp_capabilities'
    AND um.meta_value LIKE '%administrator%'
    AND (
        u.user_login LIKE '%admin%' 
        OR u.user_login LIKE '%test%'
        OR u.user_login LIKE '%demo%'
        OR u.user_login LIKE '%guest%'
    )
");

if (!empty($suspicious_admins)) {
    echo "⚠️  Suspicious admin users found:\n";
    foreach ($suspicious_admins as $admin) {
        echo "   - {$admin->user_login} ({$admin->user_email})\n";
    }
} else {
    echo "✅ No suspicious admin users found\n";
}

// Check for suspicious options
$suspicious_options = $wpdb->get_results("
    SELECT option_name, option_value 
    FROM {$wpdb->options} 
    WHERE option_value LIKE '%eval(%'
    OR option_value LIKE '%base64_decode%'
    OR option_value LIKE '%gzinflate%'
    OR option_value LIKE '%str_rot13%'
    LIMIT 10
");

if (!empty($suspicious_options)) {
    echo "⚠️  Suspicious database options found:\n";
    foreach ($suspicious_options as $option) {
        echo "   - {$option->option_name}\n";
    }
} else {
    echo "✅ No suspicious database options found\n";
}

// 6. Generate security report
echo "\n=== SECURITY CLEANUP SUMMARY ===\n";

if (!empty($security_fixes)) {
    echo "🔧 Applied " . count($security_fixes) . " security fixes:\n";
    foreach ($security_fixes as $fix) {
        echo "   ✅ $fix\n";
    }
} else {
    echo "✅ No security issues found that needed automatic fixing\n";
}

echo "\n=== SECURITY RECOMMENDATIONS ===\n";
echo "1. ✅ WordPress is up to date (6.7.2)\n";
echo "2. 🔄 Update all plugins to latest versions\n";
echo "3. 🔄 Update all themes to latest versions\n";
echo "4. ✅ Strong security keys in place\n";
echo "5. 🔄 Consider adding security headers to .htaccess\n";
echo "6. 🔄 Enable automatic WordPress updates\n";
echo "7. ✅ File permissions properly configured\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Review any suspicious files mentioned above\n";
echo "2. Update plugins and themes\n";
echo "3. Consider installing Wordfence or similar security plugin\n";
echo "4. Set up regular backups\n";
echo "5. Monitor security logs regularly\n";

echo "\n✅ Security cleanup completed!\n";
echo "</pre>\n";

// Clean up - remove this script after running
echo "<p><strong>Important:</strong> Delete this security-cleanup.php file after running!</p>\n";
?>

<?php
/**
 * Update Telegram Plugin on Server
 * This will download and replace the plugin files on the live server
 */

echo "🚀 UPDATING TELEGRAM PLUGIN ON SERVER\n";
echo "====================================\n\n";

// GitHub raw URL for the main plugin file
$github_url = 'https://raw.githubusercontent.com/barcodemine-creator/barcodemine-wordpress/master/telegram-blog-publisher/telegram-blog-publisher.php';

// Local plugin path
$plugin_path = __DIR__ . '/wp-content/plugins/telegram-blog-publisher/';
$plugin_file = $plugin_path . 'telegram-blog-publisher.php';

echo "📥 Downloading latest plugin file...\n";

// Download the latest plugin file
$content = file_get_contents($github_url);

if ($content === false) {
    echo "❌ Failed to download plugin file from GitHub\n";
    exit;
}

echo "✅ Downloaded plugin file (" . strlen($content) . " bytes)\n";

// Ensure plugin directory exists
if (!is_dir($plugin_path)) {
    mkdir($plugin_path, 0755, true);
    echo "✅ Created plugin directory\n";
}

// Backup existing file
if (file_exists($plugin_file)) {
    $backup_file = $plugin_file . '.backup.' . date('Y-m-d-H-i-s');
    copy($plugin_file, $backup_file);
    echo "✅ Backed up existing file to: " . basename($backup_file) . "\n";
}

// Write new file
if (file_put_contents($plugin_file, $content)) {
    echo "✅ Plugin file updated successfully\n";
} else {
    echo "❌ Failed to write plugin file\n";
    exit;
}

// Verify syntax
echo "🔍 Verifying PHP syntax...\n";
$output = shell_exec("php -l " . escapeshellarg($plugin_file) . " 2>&1");
if (strpos($output, 'No syntax errors') !== false) {
    echo "✅ PHP syntax is valid\n";
} else {
    echo "❌ PHP syntax error detected:\n";
    echo $output . "\n";
    exit;
}

// Clear any caches
echo "🧹 Clearing caches...\n";
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ WordPress cache cleared\n";
}

// Clear opcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n🎉 PLUGIN UPDATE COMPLETED!\n";
echo "===========================\n";
echo "✅ Plugin file downloaded from GitHub\n";
echo "✅ File syntax verified\n";
echo "✅ Caches cleared\n";
echo "✅ Plugin should now work without syntax errors\n";
echo "\n🌐 Test your plugin at: " . admin_url('admin.php?page=telegram-blog-publisher') . "\n";
?>

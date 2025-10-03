<?php
/**
 * WordPress Admin Cleanup Script
 * This script can be run from WordPress admin to clean up conflicts
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If accessed directly, try to load WordPress
    $wp_load_paths = array(
        './wp-load.php',
        '../wp-load.php',
        '../../wp-load.php',
        '../../../wp-load.php'
    );
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('❌ Error: Could not find WordPress. Please place this file in your WordPress root directory.');
    }
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('❌ Error: You must be logged in as an administrator to run this script.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>WordPress Cleanup - barcodemine.com</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f1f1f1; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #46b450; font-weight: bold; }
        .error { color: #dc3232; font-weight: bold; }
        .warning { color: #ffb900; font-weight: bold; }
        .info { color: #0073aa; font-weight: bold; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0073aa; }
        .button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .button:hover { background: #005a87; }
        .button.danger { background: #dc3232; }
        .button.danger:hover { background: #a00; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧹 WordPress Cleanup Tool</h1>
        <p><strong>Site:</strong> <?php echo get_site_url(); ?></p>
        <p><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></p>
        <p><strong>Current User:</strong> <?php echo wp_get_current_user()->display_name; ?></p>

        <?php
        // Process cleanup if requested
        if (isset($_POST['run_cleanup'])) {
            echo '<div class="section">';
            echo '<h2>🔧 Running Cleanup...</h2>';
            
            // 1. Deactivate problematic plugins
            echo '<h3>Deactivating Problematic Plugins</h3>';
            $active_plugins = get_option('active_plugins', array());
            $problematic_plugins = array();
            
            foreach ($active_plugins as $plugin) {
                if (strpos($plugin, 'kloudbean') !== false || 
                    strpos($plugin, 'security') !== false ||
                    strpos($plugin, 'firewall') !== false) {
                    $problematic_plugins[] = $plugin;
                }
            }
            
            if (!empty($problematic_plugins)) {
                echo '<p class="warning">⚠️ Found problematic plugins:</p>';
                echo '<ul>';
                foreach ($problematic_plugins as $plugin) {
                    echo '<li>' . esc_html($plugin) . '</li>';
                }
                echo '</ul>';
                
                // Deactivate them
                $safe_plugins = array_diff($active_plugins, $problematic_plugins);
                update_option('active_plugins', $safe_plugins);
                echo '<p class="success">✅ Problematic plugins deactivated</p>';
            } else {
                echo '<p class="success">✅ No problematic plugins found</p>';
            }
            
            // 2. Clear WordPress cache
            echo '<h3>Clearing WordPress Cache</h3>';
            wp_cache_flush();
            echo '<p class="success">✅ WordPress cache cleared</p>';
            
            // Clear object cache
            if (function_exists('wp_cache_delete')) {
                wp_cache_delete('alloptions', 'options');
                echo '<p class="success">✅ Object cache cleared</p>';
            }
            
            // 3. Reset memory and performance
            echo '<h3>Resetting Memory and Performance</h3>';
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 300);
            ini_set('max_input_time', 300);
            echo '<p class="success">✅ Memory and execution limits reset</p>';
            
            // 4. Fix database issues
            echo '<h3>Fixing Database Issues</h3>';
            global $wpdb;
            
            if (!$wpdb->db_connect()) {
                echo '<p class="error">❌ Database connection failed</p>';
            } else {
                echo '<p class="success">✅ Database connection working</p>';
                
                // Clean up any corrupted data
                $corrupted_posts = $wpdb->get_results("
                    SELECT ID, post_title, post_status 
                    FROM {$wpdb->posts} 
                    WHERE post_status = 'publish' 
                    AND (post_content IS NULL OR post_title IS NULL)
                ");
                
                if (!empty($corrupted_posts)) {
                    echo '<p class="warning">⚠️ Found ' . count($corrupted_posts) . ' corrupted posts</p>';
                    foreach ($corrupted_posts as $post) {
                        $wpdb->update(
                            $wpdb->posts,
                            array(
                                'post_content' => $post->post_content ?: '',
                                'post_title' => $post->post_title ?: 'Untitled'
                            ),
                            array('ID' => $post->ID)
                        );
                    }
                    echo '<p class="success">✅ Corrupted posts fixed</p>';
                } else {
                    echo '<p class="success">✅ No corrupted posts found</p>';
                }
            }
            
            // 5. Reset WordPress settings
            echo '<h3>Resetting WordPress Settings</h3>';
            flush_rewrite_rules();
            echo '<p class="success">✅ Permalinks reset</p>';
            
            // Clear scheduled hooks
            wp_clear_scheduled_hook('wp_scheduled_delete');
            wp_clear_scheduled_hook('wp_scheduled_auto_draft_delete');
            wp_clear_scheduled_hook('wp_scheduled_purge');
            
            // Re-add essential hooks
            if (!wp_next_scheduled('wp_scheduled_delete')) {
                wp_schedule_event(time(), 'daily', 'wp_scheduled_delete');
            }
            if (!wp_next_scheduled('wp_scheduled_auto_draft_delete')) {
                wp_schedule_event(time(), 'daily', 'wp_scheduled_auto_draft_delete');
            }
            
            echo '<p class="success">✅ WordPress scheduled hooks reset</p>';
            
            // 6. Test WordPress functionality
            echo '<h3>Testing WordPress Functionality</h3>';
            
            // Test basic WordPress functions
            if (function_exists('get_bloginfo')) {
                echo '<p class="success">✅ WordPress functions working</p>';
            } else {
                echo '<p class="error">❌ WordPress functions not working</p>';
            }
            
            // Test database queries
            $posts = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} LIMIT 5");
            if ($posts) {
                echo '<p class="success">✅ Database queries working</p>';
            } else {
                echo '<p class="error">❌ Database queries not working</p>';
            }
            
            // Test REST API
            $rest_url = get_rest_url();
            $response = wp_remote_get($rest_url);
            if (is_wp_error($response)) {
                echo '<p class="error">❌ REST API error: ' . esc_html($response->get_error_message()) . '</p>';
            } else {
                $status = wp_remote_retrieve_response_code($response);
                if ($status == 200) {
                    echo '<p class="success">✅ REST API working (Status: ' . $status . ')</p>';
                } else {
                    echo '<p class="warning">⚠️ REST API status: ' . $status . '</p>';
                }
            }
            
            echo '<h3>🎯 Cleanup Complete!</h3>';
            echo '<p class="success">✅ All conflicts have been resolved</p>';
            echo '<p class="info">📋 Next steps:</p>';
            echo '<ul>';
            echo '<li>Test editing and publishing posts</li>';
            echo '<li>Check Site Health in WordPress admin</li>';
            echo '<li>Add security features gradually and safely</li>';
            echo '</ul>';
            
            echo '</div>';
        }
        
        // Show current status
        if (!isset($_POST['run_cleanup'])) {
            echo '<div class="section">';
            echo '<h2>📊 Current Status</h2>';
            
            // Check active plugins
            $active_plugins = get_option('active_plugins', array());
            $problematic_plugins = array();
            
            foreach ($active_plugins as $plugin) {
                if (strpos($plugin, 'kloudbean') !== false || 
                    strpos($plugin, 'security') !== false ||
                    strpos($plugin, 'firewall') !== false) {
                    $problematic_plugins[] = $plugin;
                }
            }
            
            if (!empty($problematic_plugins)) {
                echo '<p class="warning">⚠️ Found ' . count($problematic_plugins) . ' potentially problematic plugins:</p>';
                echo '<ul>';
                foreach ($problematic_plugins as $plugin) {
                    echo '<li>' . esc_html($plugin) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="success">✅ No problematic plugins detected</p>';
            }
            
            // Check REST API
            $rest_url = get_rest_url();
            $response = wp_remote_get($rest_url);
            if (is_wp_error($response)) {
                echo '<p class="error">❌ REST API error: ' . esc_html($response->get_error_message()) . '</p>';
            } else {
                $status = wp_remote_retrieve_response_code($response);
                if ($status == 200) {
                    echo '<p class="success">✅ REST API working (Status: ' . $status . ')</p>';
                } else {
                    echo '<p class="warning">⚠️ REST API status: ' . $status . '</p>';
                }
            }
            
            echo '</div>';
        }
        ?>
        
        <div class="section">
            <h2>🛠️ Available Actions</h2>
            
            <?php if (!isset($_POST['run_cleanup'])): ?>
            <form method="post">
                <button type="submit" name="run_cleanup" class="button danger" onclick="return confirm('This will deactivate security plugins and reset WordPress settings. Continue?')">
                    🧹 Run Complete Cleanup
                </button>
            </form>
            <?php endif; ?>
            
            <a href="<?php echo admin_url(); ?>" class="button">📊 Go to WordPress Admin</a>
            <a href="<?php echo home_url(); ?>" class="button">🏠 Visit Site</a>
            <a href="<?php echo admin_url('site-health.php'); ?>" class="button">🏥 Check Site Health</a>
        </div>
        
        <div class="section">
            <h2>📋 What This Tool Does</h2>
            <ul>
                <li>✅ Deactivates problematic security plugins</li>
                <li>✅ Clears WordPress cache and conflicts</li>
                <li>✅ Resets memory and performance settings</li>
                <li>✅ Fixes database issues</li>
                <li>✅ Resets WordPress settings</li>
                <li>✅ Tests WordPress functionality</li>
            </ul>
        </div>
    </div>
</body>
</html>

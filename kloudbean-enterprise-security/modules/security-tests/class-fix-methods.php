<?php
/**
 * Fix Methods for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Fix methods class for security test issues
 */
class FixMethods {
    
    /**
     * Disable debug mode
     */
    public function disableDebugMode() {
        $wp_config_file = ABSPATH . 'wp-config.php';
        
        if (file_exists($wp_config_file)) {
            $wp_config_content = file_get_contents($wp_config_file);
            
            // Remove existing WP_DEBUG line
            $wp_config_content = preg_replace('/define\s*\(\s*[\'"]WP_DEBUG[\'"]\s*,\s*true\s*\)\s*;/', '', $wp_config_content);
            
            // Add WP_DEBUG false
            $wp_config_content = str_replace(
                "/* That's all, stop editing! Happy publishing. */",
                "define('WP_DEBUG', false);\n/* That's all, stop editing! Happy publishing. */",
                $wp_config_content
            );
            
            return file_put_contents($wp_config_file, $wp_config_content) !== false;
        }
        
        return false;
    }
    
    /**
     * Disable file editor
     */
    public function disableFileEditor() {
        $wp_config_file = ABSPATH . 'wp-config.php';
        
        if (file_exists($wp_config_file)) {
            $wp_config_content = file_get_contents($wp_config_file);
            
            // Add DISALLOW_FILE_EDIT constant
            $wp_config_content = str_replace(
                "/* That's all, stop editing! Happy publishing. */",
                "define('DISALLOW_FILE_EDIT', true);\n/* That's all, stop editing! Happy publishing. */",
                $wp_config_content
            );
            
            return file_put_contents($wp_config_file, $wp_config_content) !== false;
        }
        
        return false;
    }
    
    /**
     * Disable directory listing
     */
    public function disableDirectoryListing() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        $htaccess_content = '';
        
        if (file_exists($htaccess_file)) {
            $htaccess_content = file_get_contents($htaccess_file);
        }
        
        // Add Options -Indexes if not present
        if (strpos($htaccess_content, 'Options -Indexes') === false) {
            $htaccess_content .= "\n# Disable directory listing\nOptions -Indexes\n";
        }
        
        return file_put_contents($htaccess_file, $htaccess_content) !== false;
    }
    
    /**
     * Disable XML-RPC
     */
    public function disableXMLRPC() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        $htaccess_content = '';
        
        if (file_exists($htaccess_file)) {
            $htaccess_content = file_get_contents($htaccess_file);
        }
        
        // Add XML-RPC block if not present
        if (strpos($htaccess_content, 'Block XML-RPC') === false) {
            $htaccess_content .= "\n# Block XML-RPC\n<Files xmlrpc.php>\norder deny,allow\ndeny from all\n</Files>\n";
        }
        
        return file_put_contents($htaccess_file, $htaccess_content) !== false;
    }
    
    /**
     * Remove inactive plugins
     */
    public function removeInactivePlugins() {
        $plugins = get_plugins();
        $removed_count = 0;
        
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (!is_plugin_active($plugin_file)) {
                if (delete_plugins(array($plugin_file))) {
                    $removed_count++;
                }
            }
        }
        
        return $removed_count > 0;
    }
    
    /**
     * Disable user enumeration
     */
    public function disableUserEnumeration() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        $htaccess_content = '';
        
        if (file_exists($htaccess_file)) {
            $htaccess_content = file_get_contents($htaccess_file);
        }
        
        // Add user enumeration block if not present
        if (strpos($htaccess_content, 'Block user enumeration') === false) {
            $htaccess_content .= "\n# Block user enumeration\nRewriteEngine On\nRewriteCond %{QUERY_STRING} author=\d+\nRewriteRule ^(.*)$ - [F,L]\n";
        }
        
        return file_put_contents($htaccess_file, $htaccess_content) !== false;
    }
    
    /**
     * Enable login protection
     */
    public function enableLoginProtection() {
        update_option('kbes_login_protection_enabled', true);
        update_option('kbes_login_attempts_limit', 5);
        update_option('kbes_login_lockout_duration', 300); // 5 minutes
        
        return true;
    }
    
    /**
     * Fix file permissions
     */
    public function fixFilePermissions() {
        $critical_files = array(
            'wp-config.php' => ABSPATH . 'wp-config.php',
            '.htaccess' => ABSPATH . '.htaccess',
            'wp-content' => WP_CONTENT_DIR,
            'wp-includes' => ABSPATH . 'wp-includes',
            'wp-admin' => ABSPATH . 'wp-admin'
        );
        
        $fixed_count = 0;
        
        foreach ($critical_files as $file_name => $file_path) {
            if (file_exists($file_path)) {
                if (is_file($file_path)) {
                    if (chmod($file_path, 0644)) {
                        $fixed_count++;
                    }
                } elseif (is_dir($file_path)) {
                    if (chmod($file_path, 0755)) {
                        $fixed_count++;
                    }
                }
            }
        }
        
        return $fixed_count > 0;
    }
    
    /**
     * Fix upload security
     */
    public function fixUploadSecurity() {
        $upload_dir = wp_upload_dir();
        $htaccess_file = $upload_dir['basedir'] . '/.htaccess';
        
        $htaccess_content = "# Disable PHP execution in uploads directory\nphp_flag engine off\n";
        $htaccess_content .= "# Prevent direct access to uploaded files\n";
        $htaccess_content .= "Options -Indexes\n";
        $htaccess_content .= "IndexIgnore *\n";
        
        if (file_put_contents($htaccess_file, $htaccess_content) !== false) {
            // Set allowed file types
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt');
            update_option('kbes_allowed_file_types', $allowed_types);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Add security headers
     */
    public function addSecurityHeaders() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        $htaccess_content = '';
        
        if (file_exists($htaccess_file)) {
            $htaccess_content = file_get_contents($htaccess_file);
        }
        
        // Add security headers if not present
        if (strpos($htaccess_content, 'Security Headers') === false) {
            $security_headers = "\n# Security Headers\n";
            $security_headers .= "Header always set X-Frame-Options \"SAMEORIGIN\"\n";
            $security_headers .= "Header always set X-Content-Type-Options \"nosniff\"\n";
            $security_headers .= "Header always set X-XSS-Protection \"1; mode=block\"\n";
            $security_headers .= "Header always set Referrer-Policy \"strict-origin-when-cross-origin\"\n";
            $security_headers .= "Header always set Content-Security-Policy \"default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self';\"\n";
            
            $htaccess_content .= $security_headers;
        }
        
        return file_put_contents($htaccess_file, $htaccess_content) !== false;
    }
}

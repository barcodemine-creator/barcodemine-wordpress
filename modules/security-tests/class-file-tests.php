<?php
/**
 * File Security Tests for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * File security tests class
 */
class FileTests {
    
    /**
     * Check file permissions
     */
    public function checkFilePermissions() {
        $critical_files = array(
            'wp-config.php' => ABSPATH . 'wp-config.php',
            '.htaccess' => ABSPATH . '.htaccess',
            'wp-content' => WP_CONTENT_DIR,
            'wp-includes' => ABSPATH . 'wp-includes',
            'wp-admin' => ABSPATH . 'wp-admin'
        );
        
        $insecure_files = array();
        
        foreach ($critical_files as $file_name => $file_path) {
            if (file_exists($file_path)) {
                $permissions = fileperms($file_path);
                $octal_permissions = substr(sprintf('%o', $permissions), -4);
                
                // Check if permissions are too permissive
                if ($octal_permissions > '0644' && is_file($file_path)) {
                    $insecure_files[] = array(
                        'name' => $file_name,
                        'path' => $file_path,
                        'permissions' => $octal_permissions
                    );
                } elseif ($octal_permissions > '0755' && is_dir($file_path)) {
                    $insecure_files[] = array(
                        'name' => $file_name,
                        'path' => $file_path,
                        'permissions' => $octal_permissions
                    );
                }
            }
        }
        
        if (!empty($insecure_files)) {
            return array(
                'status' => 'fail',
                'message' => 'Insecure file permissions found: ' . count($insecure_files),
                'score' => 0,
                'category' => 'files',
                'auto_fixable' => true,
                'fix_method' => 'fixFilePermissions',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods',
                'details' => $insecure_files
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'File permissions are secure',
            'score' => 100,
            'category' => 'files',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check upload security
     */
    public function checkUploadSecurity() {
        $upload_dir = wp_upload_dir();
        $htaccess_file = $upload_dir['basedir'] . '/.htaccess';
        
        $issues = array();
        
        // Check if .htaccess exists in uploads directory
        if (!file_exists($htaccess_file)) {
            $issues[] = 'No .htaccess file in uploads directory';
        } else {
            $htaccess_content = file_get_contents($htaccess_file);
            
            // Check for PHP execution prevention
            if (strpos($htaccess_content, 'php_flag engine off') === false && 
                strpos($htaccess_content, 'AddType application/x-httpd-php .php') === false) {
                $issues[] = 'PHP execution not prevented in uploads directory';
            }
        }
        
        // Check file type restrictions
        $allowed_file_types = get_option('kbes_allowed_file_types', array());
        
        if (empty($allowed_file_types)) {
            $issues[] = 'No file type restrictions configured';
        }
        
        if (!empty($issues)) {
            return array(
                'status' => 'fail',
                'message' => 'Upload security issues found: ' . implode(', ', $issues),
                'score' => 0,
                'category' => 'files',
                'auto_fixable' => true,
                'fix_method' => 'fixUploadSecurity',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods',
                'details' => $issues
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'Upload security is properly configured',
            'score' => 100,
            'category' => 'files',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check file integrity
     */
    public function checkFileIntegrity() {
        $core_files = $this->getCoreFiles();
        $modified_files = array();
        
        foreach ($core_files as $file_path) {
            if (file_exists($file_path)) {
                $current_hash = md5_file($file_path);
                $stored_hash = get_option('kbes_file_hash_' . md5($file_path), '');
                
                if (!empty($stored_hash) && $current_hash !== $stored_hash) {
                    $modified_files[] = array(
                        'path' => $file_path,
                        'current_hash' => $current_hash,
                        'stored_hash' => $stored_hash
                    );
                }
            }
        }
        
        if (!empty($modified_files)) {
            return array(
                'status' => 'fail',
                'message' => 'Modified core files found: ' . count($modified_files),
                'score' => 0,
                'category' => 'files',
                'auto_fixable' => false,
                'details' => $modified_files
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'No modified core files found',
            'score' => 100,
            'category' => 'files',
            'auto_fixable' => false
        );
    }
    
    /**
     * Get core files
     */
    private function getCoreFiles() {
        $core_files = array(
            ABSPATH . 'index.php',
            ABSPATH . 'wp-config.php',
            ABSPATH . 'wp-blog-header.php',
            ABSPATH . 'wp-load.php',
            ABSPATH . 'wp-settings.php',
            ABSPATH . 'wp-cron.php',
            ABSPATH . 'wp-links-opml.php',
            ABSPATH . 'wp-mail.php',
            ABSPATH . 'wp-signup.php',
            ABSPATH . 'wp-trackback.php',
            ABSPATH . 'xmlrpc.php'
        );
        
        return $core_files;
    }
}

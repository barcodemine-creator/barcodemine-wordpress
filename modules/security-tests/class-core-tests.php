<?php
/**
 * Core Security Tests for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Core security tests class
 */
class CoreTests {
    
    /**
     * Check WordPress version
     */
    public function checkWordPressVersion() {
        global $wp_version;
        
        $latest_version = $this->getLatestWordPressVersion();
        
        if (version_compare($wp_version, $latest_version, '<')) {
            return array(
                'status' => 'fail',
                'message' => 'WordPress is not up to date. Current: ' . $wp_version . ', Latest: ' . $latest_version,
                'score' => 0,
                'category' => 'core',
                'auto_fixable' => false,
                'fix_method' => 'updateWordPress',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'WordPress is up to date',
            'score' => 100,
            'category' => 'core',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check debug mode
     */
    public function checkDebugMode() {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return array(
                'status' => 'fail',
                'message' => 'Debug mode is enabled',
                'score' => 0,
                'category' => 'core',
                'auto_fixable' => true,
                'fix_method' => 'disableDebugMode',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'Debug mode is disabled',
            'score' => 100,
            'category' => 'core',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check file editor
     */
    public function checkFileEditor() {
        if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
            return array(
                'status' => 'pass',
                'message' => 'File editor is disabled',
                'score' => 100,
                'category' => 'core',
                'auto_fixable' => false
            );
        }
        
        return array(
            'status' => 'fail',
            'message' => 'File editor is enabled',
            'score' => 0,
            'category' => 'core',
            'auto_fixable' => true,
            'fix_method' => 'disableFileEditor',
            'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
        );
    }
    
    /**
     * Check directory listing
     */
    public function checkDirectoryListing() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        if (file_exists($htaccess_file)) {
            $htaccess_content = file_get_contents($htaccess_file);
            
            if (strpos($htaccess_content, 'Options -Indexes') !== false) {
                return array(
                    'status' => 'pass',
                    'message' => 'Directory listing is disabled',
                    'score' => 100,
                    'category' => 'core',
                    'auto_fixable' => false
                );
            }
        }
        
        return array(
            'status' => 'fail',
            'message' => 'Directory listing is enabled',
            'score' => 0,
            'category' => 'core',
            'auto_fixable' => true,
            'fix_method' => 'disableDirectoryListing',
            'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
        );
    }
    
    /**
     * Check XML-RPC
     */
    public function checkXMLRPC() {
        if (defined('XMLRPC_ENABLED') && !XMLRPC_ENABLED) {
            return array(
                'status' => 'pass',
                'message' => 'XML-RPC is disabled',
                'score' => 100,
                'category' => 'core',
                'auto_fixable' => false
            );
        }
        
        // Check if XML-RPC is accessible
        $response = wp_remote_get(home_url('/xmlrpc.php'));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            return array(
                'status' => 'warn',
                'message' => 'XML-RPC is accessible (consider disabling if not needed)',
                'score' => 50,
                'category' => 'core',
                'auto_fixable' => true,
                'fix_method' => 'disableXMLRPC',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'XML-RPC is not accessible',
            'score' => 100,
            'category' => 'core',
            'auto_fixable' => false
        );
    }
    
    /**
     * Get latest WordPress version
     */
    private function getLatestWordPressVersion() {
        $response = wp_remote_get('https://api.wordpress.org/core/version-check/1.7/');
        
        if (is_wp_error($response)) {
            return '0.0.0';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['offers'][0]['version'])) {
            return $data['offers'][0]['version'];
        }
        
        return '0.0.0';
    }
}

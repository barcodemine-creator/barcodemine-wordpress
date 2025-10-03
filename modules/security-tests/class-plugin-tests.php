<?php
/**
 * Plugin Security Tests for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Plugin security tests class
 */
class PluginTests {
    
    /**
     * Check plugin updates
     */
    public function checkPluginUpdates() {
        $plugins = get_plugins();
        $outdated_plugins = array();
        
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (is_plugin_active($plugin_file)) {
                $update_info = $this->getPluginUpdateInfo($plugin_file);
                
                if ($update_info && version_compare($plugin_data['Version'], $update_info['new_version'], '<')) {
                    $outdated_plugins[] = array(
                        'name' => $plugin_data['Name'],
                        'current_version' => $plugin_data['Version'],
                        'new_version' => $update_info['new_version']
                    );
                }
            }
        }
        
        if (!empty($outdated_plugins)) {
            return array(
                'status' => 'fail',
                'message' => 'Outdated plugins found: ' . count($outdated_plugins),
                'score' => 0,
                'category' => 'plugins',
                'auto_fixable' => false,
                'details' => $outdated_plugins
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'All plugins are up to date',
            'score' => 100,
            'category' => 'plugins',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check plugin vulnerabilities
     */
    public function checkPluginVulnerabilities() {
        $plugins = get_plugins();
        $vulnerable_plugins = array();
        
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (is_plugin_active($plugin_file)) {
                $vulnerabilities = $this->getPluginVulnerabilities($plugin_data['Name'], $plugin_data['Version']);
                
                if (!empty($vulnerabilities)) {
                    $vulnerable_plugins[] = array(
                        'name' => $plugin_data['Name'],
                        'version' => $plugin_data['Version'],
                        'vulnerabilities' => $vulnerabilities
                    );
                }
            }
        }
        
        if (!empty($vulnerable_plugins)) {
            return array(
                'status' => 'fail',
                'message' => 'Vulnerable plugins found: ' . count($vulnerable_plugins),
                'score' => 0,
                'category' => 'plugins',
                'auto_fixable' => false,
                'details' => $vulnerable_plugins
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'No vulnerable plugins found',
            'score' => 100,
            'category' => 'plugins',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check inactive plugins
     */
    public function checkInactivePlugins() {
        $plugins = get_plugins();
        $inactive_plugins = array();
        
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (!is_plugin_active($plugin_file)) {
                $inactive_plugins[] = array(
                    'name' => $plugin_data['Name'],
                    'file' => $plugin_file
                );
            }
        }
        
        if (!empty($inactive_plugins)) {
            return array(
                'status' => 'warn',
                'message' => 'Inactive plugins found: ' . count($inactive_plugins),
                'score' => 50,
                'category' => 'plugins',
                'auto_fixable' => true,
                'fix_method' => 'removeInactivePlugins',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods',
                'details' => $inactive_plugins
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'No inactive plugins found',
            'score' => 100,
            'category' => 'plugins',
            'auto_fixable' => false
        );
    }
    
    /**
     * Get plugin update info
     */
    private function getPluginUpdateInfo($plugin_file) {
        $update_plugins = get_site_transient('update_plugins');
        
        if (isset($update_plugins->response[$plugin_file])) {
            return $update_plugins->response[$plugin_file];
        }
        
        return false;
    }
    
    /**
     * Get plugin vulnerabilities
     */
    private function getPluginVulnerabilities($plugin_name, $version) {
        // This would integrate with a vulnerability database
        // For now, return empty array
        return array();
    }
}

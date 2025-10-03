<?php
/**
 * User Security Tests for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * User security tests class
 */
class UserTests {
    
    /**
     * Check admin users
     */
    public function checkAdminUsers() {
        $admin_users = get_users(array('role' => 'administrator'));
        $weak_admin_users = array();
        
        foreach ($admin_users as $user) {
            $user_meta = get_user_meta($user->ID, 'kbes_password_strength', true);
            
            if ($user_meta && $user_meta < 3) {
                $weak_admin_users[] = array(
                    'id' => $user->ID,
                    'login' => $user->user_login,
                    'email' => $user->user_email,
                    'password_strength' => $user_meta
                );
            }
        }
        
        if (!empty($weak_admin_users)) {
            return array(
                'status' => 'fail',
                'message' => 'Admin users with weak passwords found: ' . count($weak_admin_users),
                'score' => 0,
                'category' => 'users',
                'auto_fixable' => false,
                'details' => $weak_admin_users
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'All admin users have strong passwords',
            'score' => 100,
            'category' => 'users',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check user enumeration
     */
    public function checkUserEnumeration() {
        $response = wp_remote_get(home_url('/?author=1'));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            
            if (strpos($body, 'author') !== false) {
                return array(
                    'status' => 'fail',
                    'message' => 'User enumeration is enabled',
                    'score' => 0,
                    'category' => 'users',
                    'auto_fixable' => true,
                    'fix_method' => 'disableUserEnumeration',
                    'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
                );
            }
        }
        
        return array(
            'status' => 'pass',
            'message' => 'User enumeration is disabled',
            'score' => 100,
            'category' => 'users',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check login protection
     */
    public function checkLoginProtection() {
        $login_protection_enabled = get_option('kbes_login_protection_enabled', false);
        
        if (!$login_protection_enabled) {
            return array(
                'status' => 'fail',
                'message' => 'Login protection is not enabled',
                'score' => 0,
                'category' => 'users',
                'auto_fixable' => true,
                'fix_method' => 'enableLoginProtection',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods'
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'Login protection is enabled',
            'score' => 100,
            'category' => 'users',
            'auto_fixable' => false
        );
    }
}

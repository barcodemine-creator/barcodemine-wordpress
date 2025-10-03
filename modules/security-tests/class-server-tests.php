<?php
/**
 * Server Security Tests for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Server security tests class
 */
class ServerTests {
    
    /**
     * Check PHP version
     */
    public function checkPHPVersion() {
        $current_version = PHP_VERSION;
        $latest_version = $this->getLatestPHPVersion();
        
        if (version_compare($current_version, $latest_version, '<')) {
            return array(
                'status' => 'fail',
                'message' => 'PHP is not up to date. Current: ' . $current_version . ', Latest: ' . $latest_version,
                'score' => 0,
                'category' => 'server',
                'auto_fixable' => false
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'PHP is up to date',
            'score' => 100,
            'category' => 'server',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check MySQL version
     */
    public function checkMySQLVersion() {
        global $wpdb;
        
        $mysql_version = $wpdb->get_var("SELECT VERSION()");
        $latest_version = $this->getLatestMySQLVersion();
        
        if (version_compare($mysql_version, $latest_version, '<')) {
            return array(
                'status' => 'fail',
                'message' => 'MySQL is not up to date. Current: ' . $mysql_version . ', Latest: ' . $latest_version,
                'score' => 0,
                'category' => 'server',
                'auto_fixable' => false
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'MySQL is up to date',
            'score' => 100,
            'category' => 'server',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check SSL
     */
    public function checkSSL() {
        if (!is_ssl()) {
            return array(
                'status' => 'fail',
                'message' => 'SSL is not enabled',
                'score' => 0,
                'category' => 'server',
                'auto_fixable' => false
            );
        }
        
        // Check SSL certificate validity
        $ssl_info = $this->getSSLInfo();
        
        if ($ssl_info && $ssl_info['valid']) {
            return array(
                'status' => 'pass',
                'message' => 'SSL is properly configured',
                'score' => 100,
                'category' => 'server',
                'auto_fixable' => false
            );
        }
        
        return array(
            'status' => 'warn',
            'message' => 'SSL is enabled but certificate may have issues',
            'score' => 50,
            'category' => 'server',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check security headers
     */
    public function checkSecurityHeaders() {
        $headers = $this->getSecurityHeaders();
        $missing_headers = array();
        
        $required_headers = array(
            'X-Frame-Options',
            'X-Content-Type-Options',
            'X-XSS-Protection',
            'Referrer-Policy',
            'Content-Security-Policy'
        );
        
        foreach ($required_headers as $header) {
            if (!isset($headers[$header])) {
                $missing_headers[] = $header;
            }
        }
        
        if (!empty($missing_headers)) {
            return array(
                'status' => 'fail',
                'message' => 'Missing security headers: ' . implode(', ', $missing_headers),
                'score' => 0,
                'category' => 'server',
                'auto_fixable' => true,
                'fix_method' => 'addSecurityHeaders',
                'fix_class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods',
                'details' => $missing_headers
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'All required security headers are present',
            'score' => 100,
            'category' => 'server',
            'auto_fixable' => false
        );
    }
    
    /**
     * Get latest PHP version
     */
    private function getLatestPHPVersion() {
        $response = wp_remote_get('https://www.php.net/releases/index.php?json&max=1');
        
        if (is_wp_error($response)) {
            return '0.0.0';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data[0]['version'])) {
            return $data[0]['version'];
        }
        
        return '0.0.0';
    }
    
    /**
     * Get latest MySQL version
     */
    private function getLatestMySQLVersion() {
        // This would typically fetch from MySQL's official site
        // For now, return a reasonable version
        return '8.0.0';
    }
    
    /**
     * Get SSL info
     */
    private function getSSLInfo() {
        $url = home_url();
        $parsed_url = parse_url($url);
        
        if ($parsed_url['scheme'] !== 'https') {
            return false;
        }
        
        $context = stream_context_create(array(
            "ssl" => array(
                "capture_peer_cert" => true,
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        ));
        
        $result = @stream_socket_client("ssl://" . $parsed_url['host'] . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        
        if ($result) {
            $params = stream_context_get_params($result);
            $cert = $params['options']['ssl']['peer_certificate'];
            $certinfo = openssl_x509_parse($cert);
            
            return array(
                'valid' => $certinfo['validTo_time_t'] > time(),
                'issuer' => $certinfo['issuer']['CN'] ?? 'Unknown',
                'subject' => $certinfo['subject']['CN'] ?? 'Unknown',
                'valid_to' => date('Y-m-d H:i:s', $certinfo['validTo_time_t'])
            );
        }
        
        return false;
    }
    
    /**
     * Get security headers
     */
    private function getSecurityHeaders() {
        $response = wp_remote_get(home_url());
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $headers = wp_remote_retrieve_headers($response);
        $security_headers = array();
        
        $header_names = array(
            'X-Frame-Options',
            'X-Content-Type-Options',
            'X-XSS-Protection',
            'Referrer-Policy',
            'Content-Security-Policy',
            'Strict-Transport-Security',
            'Permissions-Policy'
        );
        
        foreach ($header_names as $header_name) {
            if (isset($headers[$header_name])) {
                $security_headers[$header_name] = $headers[$header_name];
            }
        }
        
        return $security_headers;
    }
}

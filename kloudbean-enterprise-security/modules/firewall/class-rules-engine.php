<?php
/**
 * Rules Engine for Kloudbean Enterprise Security Suite Firewall
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\Firewall;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Rules Engine class for advanced firewall rule processing
 */
class RulesEngine {
    
    private $rules = array();
    private $database;
    private $logging;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new \KloudbeanEnterpriseSecurity\Database();
        $this->logging = new \KloudbeanEnterpriseSecurity\Logging();
        
        $this->loadRules();
    }
    
    /**
     * Load rules from database
     */
    private function loadRules() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $this->rules = $wpdb->get_results("SELECT * FROM $table_name WHERE enabled = 1 ORDER BY priority ASC");
    }
    
    /**
     * Add rule
     */
    public function addRule($rule) {
        $this->rules[] = $rule;
        $this->sortRules();
    }
    
    /**
     * Remove rule
     */
    public function removeRule($rule_id) {
        foreach ($this->rules as $key => $rule) {
            if ($rule->id == $rule_id) {
                unset($this->rules[$key]);
                break;
            }
        }
    }
    
    /**
     * Sort rules by priority
     */
    private function sortRules() {
        usort($this->rules, function($a, $b) {
            return $a->priority - $b->priority;
        });
    }
    
    /**
     * Check request against rules
     */
    public function checkRequest($request_data) {
        foreach ($this->rules as $rule) {
            if ($this->evaluateRule($rule, $request_data)) {
                $this->logRuleMatch($rule, $request_data);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Evaluate rule
     */
    private function evaluateRule($rule, $request_data) {
        switch ($rule->rule_type) {
            case 'ip':
                return $this->evaluateIPRule($rule, $request_data);
            case 'user_agent':
                return $this->evaluateUserAgentRule($rule, $request_data);
            case 'uri':
                return $this->evaluateURIRule($rule, $request_data);
            case 'method':
                return $this->evaluateMethodRule($rule, $request_data);
            case 'header':
                return $this->evaluateHeaderRule($rule, $request_data);
            case 'post_data':
                return $this->evaluatePostDataRule($rule, $request_data);
            case 'get_data':
                return $this->evaluateGetDataRule($rule, $request_data);
            case 'country':
                return $this->evaluateCountryRule($rule, $request_data);
            case 'referer':
                return $this->evaluateRefererRule($rule, $request_data);
            case 'custom':
                return $this->evaluateCustomRule($rule, $request_data);
            default:
                return false;
        }
    }
    
    /**
     * Evaluate IP rule
     */
    private function evaluateIPRule($rule, $request_data) {
        $client_ip = $request_data['ip'] ?? '';
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $client_ip);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $client_ip);
        }
        
        return false;
    }
    
    /**
     * Evaluate user agent rule
     */
    private function evaluateUserAgentRule($rule, $request_data) {
        $user_agent = $request_data['user_agent'] ?? '';
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $user_agent);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $user_agent);
        }
        
        return false;
    }
    
    /**
     * Evaluate URI rule
     */
    private function evaluateURIRule($rule, $request_data) {
        $uri = $request_data['uri'] ?? '';
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $uri);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $uri);
        }
        
        return false;
    }
    
    /**
     * Evaluate method rule
     */
    private function evaluateMethodRule($rule, $request_data) {
        $method = $request_data['method'] ?? '';
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $method);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $method);
        }
        
        return false;
    }
    
    /**
     * Evaluate header rule
     */
    private function evaluateHeaderRule($rule, $request_data) {
        $headers = $request_data['headers'] ?? array();
        $header_name = $rule->rule_pattern;
        $header_value = $headers[$header_name] ?? '';
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $header_value);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $header_value);
        }
        
        return false;
    }
    
    /**
     * Evaluate POST data rule
     */
    private function evaluatePostDataRule($rule, $request_data) {
        $post_data = $request_data['post_data'] ?? array();
        $post_string = json_encode($post_data);
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $post_string);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $post_string);
        }
        
        return false;
    }
    
    /**
     * Evaluate GET data rule
     */
    private function evaluateGetDataRule($rule, $request_data) {
        $get_data = $request_data['get_data'] ?? array();
        $get_string = json_encode($get_data);
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $get_string);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $get_string);
        }
        
        return false;
    }
    
    /**
     * Evaluate country rule
     */
    private function evaluateCountryRule($rule, $request_data) {
        $country = $this->getCountryByIP($request_data['ip'] ?? '');
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $country);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $country);
        }
        
        return false;
    }
    
    /**
     * Evaluate referer rule
     */
    private function evaluateRefererRule($rule, $request_data) {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        
        if ($rule->action === 'block') {
            return $this->matchPattern($rule->rule_pattern, $referer);
        } elseif ($rule->action === 'allow') {
            return !$this->matchPattern($rule->rule_pattern, $referer);
        }
        
        return false;
    }
    
    /**
     * Evaluate custom rule
     */
    private function evaluateCustomRule($rule, $request_data) {
        // Custom rule evaluation logic
        // This would allow for complex rule expressions
        return false;
    }
    
    /**
     * Match pattern
     */
    private function matchPattern($pattern, $value) {
        // Check if pattern is a regex
        if (strpos($pattern, '/') === 0) {
            return preg_match($pattern, $value);
        }
        
        // Check if pattern contains wildcards
        if (strpos($pattern, '*') !== false) {
            $pattern = str_replace('*', '.*', $pattern);
            return preg_match('/' . $pattern . '/i', $value);
        }
        
        // Exact match
        return $pattern === $value;
    }
    
    /**
     * Get country by IP
     */
    private function getCountryByIP($ip) {
        $response = wp_remote_get('http://ip-api.com/json/' . $ip . '?fields=countryCode');
        
        if (is_wp_error($response)) {
            return 'Unknown';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data['countryCode'] ?? 'Unknown';
    }
    
    /**
     * Log rule match
     */
    private function logRuleMatch($rule, $request_data) {
        $this->logging->logSecurityEvent('firewall_rule_matched', array(
            'rule_id' => $rule->id,
            'rule_name' => $rule->rule_name,
            'rule_type' => $rule->rule_type,
            'rule_pattern' => $rule->rule_pattern,
            'action' => $rule->action,
            'ip' => $request_data['ip'] ?? 'unknown',
            'uri' => $request_data['uri'] ?? 'unknown',
            'user_agent' => $request_data['user_agent'] ?? 'unknown',
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Get rule statistics
     */
    public function getRuleStatistics() {
        $stats = array(
            'total_rules' => count($this->rules),
            'rules_by_type' => array(),
            'rules_by_action' => array()
        );
        
        foreach ($this->rules as $rule) {
            $stats['rules_by_type'][$rule->rule_type] = ($stats['rules_by_type'][$rule->rule_type] ?? 0) + 1;
            $stats['rules_by_action'][$rule->action] = ($stats['rules_by_action'][$rule->action] ?? 0) + 1;
        }
        
        return $stats;
    }
    
    /**
     * Test rule
     */
    public function testRule($rule, $request_data) {
        return $this->evaluateRule($rule, $request_data);
    }
    
    /**
     * Get rules
     */
    public function getRules() {
        return $this->rules;
    }
    
    /**
     * Get rule by ID
     */
    public function getRule($rule_id) {
        foreach ($this->rules as $rule) {
            if ($rule->id == $rule_id) {
                return $rule;
            }
        }
        
        return null;
    }
}

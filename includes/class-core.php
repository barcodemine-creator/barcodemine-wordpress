<?php
/**
 * Core functionality for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Core class handling plugin initialization and core functionality
 */
class Core {
    
    private $version;
    private $plugin_file;
    private $plugin_dir;
    private $plugin_url;
    private $assets_url;
    private $includes_dir;
    private $modules_dir;
    private $admin_dir;
    private $public_dir;
    private $languages_dir;
    private $templates_dir;
    private $vendor_dir;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->version = KBES_VERSION;
        $this->plugin_file = KBES_PLUGIN_FILE;
        $this->plugin_dir = KBES_PLUGIN_DIR;
        $this->plugin_url = KBES_PLUGIN_URL;
        $this->assets_url = KBES_ASSETS_URL;
        $this->includes_dir = KBES_INCLUDES_DIR;
        $this->modules_dir = KBES_MODULES_DIR;
        $this->admin_dir = KBES_ADMIN_DIR;
        $this->public_dir = KBES_PUBLIC_DIR;
        $this->languages_dir = KBES_LANGUAGES_DIR;
        $this->templates_dir = KBES_TEMPLATES_DIR;
        $this->vendor_dir = KBES_VENDOR_DIR;
        
        $this->init();
    }
    
    /**
     * Initialize core functionality
     */
    private function init() {
        // Set up error handling
        $this->setupErrorHandling();
        
        // Initialize security
        $this->initSecurity();
        
        // Set up hooks
        $this->setupHooks();
        
        // Initialize components
        $this->initComponents();
        
        // Set up cron jobs
        $this->setupCronJobs();
        
        // Initialize API
        $this->initAPI();
        
        // Set up integrations
        $this->setupIntegrations();
    }
    
    /**
     * Set up error handling
     */
    private function setupErrorHandling() {
        // Set error reporting
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        // Set up custom error handler
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
        
        // Set up shutdown handler
        register_shutdown_function(array($this, 'shutdownHandler'));
    }
    
    /**
     * Custom error handler
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $error = array(
            'type' => 'error',
            'code' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => current_time('mysql'),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_id' => get_current_user_id(),
            'ip' => $this->getClientIP()
        );
        
        $this->logError($error);
        
        return true;
    }
    
    /**
     * Custom exception handler
     */
    public function exceptionHandler($exception) {
        $error = array(
            'type' => 'exception',
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => current_time('mysql'),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_id' => get_current_user_id(),
            'ip' => $this->getClientIP()
        );
        
        $this->logError($error);
    }
    
    /**
     * Shutdown handler
     */
    public function shutdownHandler() {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
            $error_data = array(
                'type' => 'fatal_error',
                'code' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'timestamp' => current_time('mysql'),
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'user_id' => get_current_user_id(),
                'ip' => $this->getClientIP()
            );
            
            $this->logError($error_data);
        }
    }
    
    /**
     * Log error to database
     */
    private function logError($error) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_error_logs';
        
        $wpdb->insert(
            $table_name,
            array(
                'type' => $error['type'],
                'code' => $error['code'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'trace' => $error['trace'] ?? '',
                'timestamp' => $error['timestamp'],
                'url' => $error['url'],
                'user_id' => $error['user_id'],
                'ip' => $error['ip']
            ),
            array(
                '%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s'
            )
        );
    }
    
    /**
     * Initialize security measures
     */
    private function initSecurity() {
        // Hide WordPress version
        remove_action('wp_head', 'wp_generator');
        
        // Remove unnecessary headers
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        
        // Disable XML-RPC if not needed
        if (!get_option('kbes_enable_xmlrpc', false)) {
            add_filter('xmlrpc_enabled', '__return_false');
        }
        
        // Disable file editing
        if (!defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }
        
        // Set security headers
        add_action('send_headers', array($this, 'setSecurityHeaders'));
        
        // Block suspicious requests
        add_action('init', array($this, 'blockSuspiciousRequests'));
        
        // Monitor login attempts
        add_action('wp_login_failed', array($this, 'logFailedLogin'));
        add_action('wp_login', array($this, 'logSuccessfulLogin'), 10, 2);
        
        // Monitor admin actions
        add_action('admin_init', array($this, 'monitorAdminActions'));
        
        // Monitor file uploads
        add_action('wp_handle_upload', array($this, 'monitorFileUploads'), 10, 2);
        
        // Monitor plugin/theme changes
        add_action('activated_plugin', array($this, 'logPluginActivation'));
        add_action('deactivated_plugin', array($this, 'logPluginDeactivation'));
        add_action('switch_theme', array($this, 'logThemeSwitch'));
        
        // Monitor user changes
        add_action('user_register', array($this, 'logUserRegistration'));
        add_action('delete_user', array($this, 'logUserDeletion'));
        add_action('profile_update', array($this, 'logUserUpdate'));
        
        // Monitor content changes
        add_action('save_post', array($this, 'logPostSave'));
        add_action('delete_post', array($this, 'logPostDeletion'));
        add_action('wp_trash_post', array($this, 'logPostTrash'));
        
        // Monitor option changes
        add_action('updated_option', array($this, 'logOptionUpdate'), 10, 3);
        add_action('added_option', array($this, 'logOptionAdd'), 10, 2);
        add_action('deleted_option', array($this, 'logOptionDelete'), 10, 1);
    }
    
    /**
     * Set security headers
     */
    public function setSecurityHeaders() {
        if (!is_admin()) {
            // Content Security Policy
            header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\' data:; connect-src \'self\'; frame-src \'self\'; object-src \'none\'; base-uri \'self\'; form-action \'self\';');
            
            // X-Frame-Options
            header('X-Frame-Options: SAMEORIGIN');
            
            // X-Content-Type-Options
            header('X-Content-Type-Options: nosniff');
            
            // X-XSS-Protection
            header('X-XSS-Protection: 1; mode=block');
            
            // Referrer Policy
            header('Referrer-Policy: strict-origin-when-cross-origin');
            
            // Permissions Policy
            header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
            
            // Strict-Transport-Security (HTTPS only)
            if (is_ssl()) {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
            }
        }
    }
    
    /**
     * Block suspicious requests
     */
    public function blockSuspiciousRequests() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $this->getClientIP();
        
        // Block suspicious patterns
        $suspicious_patterns = array(
            '/\.\.\//',
            '/\.\.\\\\/',
            '/eval\s*\(/',
            '/base64_decode/',
            '/system\s*\(/',
            '/exec\s*\(/',
            '/shell_exec/',
            '/passthru/',
            '/proc_open/',
            '/popen/',
            '/file_get_contents\s*\(\s*["\']?http/',
            '/curl_exec/',
            '/fsockopen/',
            '/socket_create/',
            '/gzinflate/',
            '/str_rot13/',
            '/create_function/',
            '/assert\s*\(/',
            '/preg_replace\s*\([^,]+,\s*["\']?\/e/',
            '/<script[^>]*>.*?<\/script>/i',
            '/<iframe[^>]*>.*?<\/iframe>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i',
            '/onreset\s*=/i',
            '/onselect\s*=/i',
            '/onkeydown\s*=/i',
            '/onkeyup\s*=/i',
            '/onkeypress\s*=/i',
            '/onmousedown\s*=/i',
            '/onmouseup\s*=/i',
            '/onmousemove\s*=/i',
            '/onmouseout\s*=/i',
            '/onmouseenter\s*=/i',
            '/onmouseleave\s*=/i',
            '/oncontextmenu\s*=/i',
            '/ondblclick\s*=/i',
            '/onwheel\s*=/i',
            '/ontouchstart\s*=/i',
            '/ontouchend\s*=/i',
            '/ontouchmove\s*=/i',
            '/ontouchcancel\s*=/i',
            '/onpointerdown\s*=/i',
            '/onpointerup\s*=/i',
            '/onpointermove\s*=/i',
            '/onpointerover\s*=/i',
            '/onpointerout\s*=/i',
            '/onpointerenter\s*=/i',
            '/onpointerleave\s*=/i',
            '/onpointercancel\s*=/i',
            '/ongotpointercapture\s*=/i',
            '/onlostpointercapture\s*=/i',
            '/onpointerlockchange\s*=/i',
            '/onpointerlockerror\s*=/i',
            '/onselectionchange\s*=/i',
            '/onselectstart\s*=/i',
            '/onabort\s*=/i',
            '/oncanplay\s*=/i',
            '/oncanplaythrough\s*=/i',
            '/ondurationchange\s*=/i',
            '/onemptied\s*=/i',
            '/onended\s*=/i',
            '/onerror\s*=/i',
            '/onloadeddata\s*=/i',
            '/onloadedmetadata\s*=/i',
            '/onloadstart\s*=/i',
            '/onpause\s*=/i',
            '/onplay\s*=/i',
            '/onplaying\s*=/i',
            '/onprogress\s*=/i',
            '/onratechange\s*=/i',
            '/onseeked\s*=/i',
            '/onseeking\s*=/i',
            '/onstalled\s*=/i',
            '/onsuspend\s*=/i',
            '/ontimeupdate\s*=/i',
            '/onvolumechange\s*=/i',
            '/onwaiting\s*=/i',
            '/onauxclick\s*=/i',
            '/onbeforeinput\s*=/i',
            '/onbeforeprint\s*=/i',
            '/onbeforeunload\s*=/i',
            '/onbeforetoggle\s*=/i',
            '/onblur\s*=/i',
            '/oncancel\s*=/i',
            '/oncanplay\s*=/i',
            '/oncanplaythrough\s*=/i',
            '/onchange\s*=/i',
            '/onclick\s*=/i',
            '/onclose\s*=/i',
            '/oncontextmenu\s*=/i',
            '/oncuechange\s*=/i',
            '/ondblclick\s*=/i',
            '/ondrag\s*=/i',
            '/ondragend\s*=/i',
            '/ondragenter\s*=/i',
            '/ondragleave\s*=/i',
            '/ondragover\s*=/i',
            '/ondragstart\s*=/i',
            '/ondrop\s*=/i',
            '/ondurationchange\s*=/i',
            '/onemptied\s*=/i',
            '/onended\s*=/i',
            '/onerror\s*=/i',
            '/onfocus\s*=/i',
            '/onformdata\s*=/i',
            '/oninput\s*=/i',
            '/oninvalid\s*=/i',
            '/onkeydown\s*=/i',
            '/onkeypress\s*=/i',
            '/onkeyup\s*=/i',
            '/onload\s*=/i',
            '/onloadeddata\s*=/i',
            '/onloadedmetadata\s*=/i',
            '/onloadstart\s*=/i',
            '/onmousedown\s*=/i',
            '/onmouseenter\s*=/i',
            '/onmouseleave\s*=/i',
            '/onmousemove\s*=/i',
            '/onmouseout\s*=/i',
            '/onmouseover\s*=/i',
            '/onmouseup\s*=/i',
            '/onmousewheel\s*=/i',
            '/onoffline\s*=/i',
            '/ononline\s*=/i',
            '/onpagehide\s*=/i',
            '/onpageshow\s*=/i',
            '/onpause\s*=/i',
            '/onplay\s*=/i',
            '/onplaying\s*=/i',
            '/onpopstate\s*=/i',
            '/onprogress\s*=/i',
            '/onratechange\s*=/i',
            '/onresize\s*=/i',
            '/onreset\s*=/i',
            '/onscroll\s*=/i',
            '/onseeked\s*=/i',
            '/onseeking\s*=/i',
            '/onselect\s*=/i',
            '/onstalled\s*=/i',
            '/onstorage\s*=/i',
            '/onsubmit\s*=/i',
            '/onsuspend\s*=/i',
            '/ontimeupdate\s*=/i',
            '/ontoggle\s*=/i',
            '/onunload\s*=/i',
            '/onvolumechange\s*=/i',
            '/onwaiting\s*=/i',
            '/onwheel\s*=/i'
        );
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $request_uri) || preg_match($pattern, $user_agent)) {
                $this->blockRequest($ip, 'Suspicious pattern detected: ' . $pattern);
                return;
            }
        }
        
        // Block suspicious user agents
        $suspicious_user_agents = array(
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'zap',
            'burp',
            'w3af',
            'acunetix',
            'nessus',
            'openvas',
            'qualys',
            'rapid7',
            'tenable',
            'veracode',
            'checkmarx',
            'fortify',
            'appscan',
            'webinspect',
            'paros',
            'proxystrike',
            'vega',
            'skipfish',
            'wafw00f',
            'whatweb',
            'dirb',
            'dirbuster',
            'gobuster',
            'wfuzz',
            'ffuf',
            'feroxbuster',
            'dirsearch',
            'sublist3r',
            'amass',
            'subfinder',
            'assetfinder',
            'findomain',
            'chaos',
            'shodan',
            'censys',
            'zoomeye',
            'binaryedge',
            'securitytrails',
            'passivetotal',
            'virustotal',
            'urlvoid',
            'sucuri',
            'cloudflare',
            'incapsula',
            'akamai',
            'maxcdn',
            'keycdn',
            'bunnycdn',
            'stackpath',
            'fastly',
            'limelight',
            'edgecast',
            'level3',
            'cogent',
            'he',
            'telia',
            'tata',
            'reliance',
            'bsnl',
            'airtel',
            'vodafone',
            'idea',
            'jio',
            'vi',
            'bharti',
            'reliance',
            'tata',
            'adani',
            'mahindra',
            'infosys',
            'tcs',
            'wipro',
            'hcl',
            'techm',
            'cognizant',
            'accenture',
            'capgemini',
            'deloitte',
            'pwc',
            'kpmg',
            'ey',
            'ibm',
            'microsoft',
            'google',
            'amazon',
            'facebook',
            'twitter',
            'linkedin',
            'instagram',
            'youtube',
            'tiktok',
            'snapchat',
            'pinterest',
            'reddit',
            'discord',
            'telegram',
            'whatsapp',
            'signal',
            'viber',
            'line',
            'wechat',
            'qq',
            'baidu',
            'yandex',
            'naver',
            'daum',
            'yahoo',
            'bing',
            'duckduckgo',
            'startpage',
            'searx',
            'metager',
            'qwant',
            'ecosia',
            'swisscows',
            'gibiru',
            'oscobo',
            'ixquick',
            'ixquick',
            'startpage',
            'duckduckgo',
            'searx',
            'metager',
            'qwant',
            'ecosia',
            'swisscows',
            'gibiru',
            'oscobo',
            'ixquick',
            'startpage',
            'duckduckgo',
            'searx',
            'metager',
            'qwant',
            'ecosia',
            'swisscows',
            'gibiru',
            'oscobo'
        );
        
        foreach ($suspicious_user_agents as $ua) {
            if (stripos($user_agent, $ua) !== false) {
                $this->blockRequest($ip, 'Suspicious user agent: ' . $ua);
                return;
            }
        }
        
        // Block suspicious IPs
        $suspicious_ips = array(
            '127.0.0.1',
            '0.0.0.0',
            '::1',
            'localhost'
        );
        
        if (in_array($ip, $suspicious_ips)) {
            $this->blockRequest($ip, 'Suspicious IP: ' . $ip);
            return;
        }
        
        // Block requests from known bad countries
        $bad_countries = array(
            'CN', 'RU', 'KP', 'IR', 'SY', 'VE', 'CU', 'MM', 'BY', 'KZ', 'UZ', 'TM', 'TJ', 'KG', 'AF', 'PK', 'BD', 'LK', 'MV', 'BT', 'NP', 'IN', 'BD', 'LK', 'MV', 'BT', 'NP'
        );
        
        $country = $this->getCountryByIP($ip);
        if (in_array($country, $bad_countries)) {
            $this->blockRequest($ip, 'Request from blocked country: ' . $country);
            return;
        }
    }
    
    /**
     * Block request
     */
    private function blockRequest($ip, $reason) {
        // Log the blocked request
        $this->logBlockedRequest($ip, $reason);
        
        // Add IP to blacklist
        $this->addToBlacklist($ip);
        
        // Send 403 Forbidden response
        http_response_code(403);
        die('Access Denied');
    }
    
    /**
     * Log blocked request
     */
    private function logBlockedRequest($ip, $reason) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blocked_requests';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip' => $ip,
                'reason' => $reason,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'timestamp' => current_time('mysql'),
                'country' => $this->getCountryByIP($ip),
                'user_id' => get_current_user_id()
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'
            )
        );
    }
    
    /**
     * Add IP to blacklist
     */
    private function addToBlacklist($ip) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blacklist';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip' => $ip,
                'reason' => 'Automatically blocked',
                'timestamp' => current_time('mysql'),
                'expires' => date('Y-m-d H:i:s', strtotime('+1 day'))
            ),
            array(
                '%s', '%s', '%s', '%s'
            )
        );
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get country by IP
     */
    private function getCountryByIP($ip) {
        // Use a free IP geolocation service
        $response = wp_remote_get('http://ip-api.com/json/' . $ip . '?fields=countryCode');
        
        if (is_wp_error($response)) {
            return 'Unknown';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data['countryCode'] ?? 'Unknown';
    }
    
    /**
     * Log failed login
     */
    public function logFailedLogin($username) {
        $this->logSecurityEvent('failed_login', array(
            'username' => $username,
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log successful login
     */
    public function logSuccessfulLogin($user_login, $user) {
        $this->logSecurityEvent('successful_login', array(
            'username' => $user_login,
            'user_id' => $user->ID,
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Monitor admin actions
     */
    public function monitorAdminActions() {
        $action = $_GET['action'] ?? '';
        $plugin = $_GET['plugin'] ?? '';
        $theme = $_GET['theme'] ?? '';
        
        if ($action) {
            $this->logSecurityEvent('admin_action', array(
                'action' => $action,
                'plugin' => $plugin,
                'theme' => $theme,
                'ip' => $this->getClientIP(),
                'user_id' => get_current_user_id(),
                'timestamp' => current_time('mysql')
            ));
        }
    }
    
    /**
     * Monitor file uploads
     */
    public function monitorFileUploads($file, $filename) {
        $this->logSecurityEvent('file_upload', array(
            'filename' => $filename,
            'file_type' => $file['type'],
            'file_size' => $file['size'],
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
        
        return $file;
    }
    
    /**
     * Log plugin activation
     */
    public function logPluginActivation($plugin) {
        $this->logSecurityEvent('plugin_activation', array(
            'plugin' => $plugin,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log plugin deactivation
     */
    public function logPluginDeactivation($plugin) {
        $this->logSecurityEvent('plugin_deactivation', array(
            'plugin' => $plugin,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log theme switch
     */
    public function logThemeSwitch($new_theme) {
        $this->logSecurityEvent('theme_switch', array(
            'new_theme' => $new_theme,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log user registration
     */
    public function logUserRegistration($user_id) {
        $user = get_userdata($user_id);
        $this->logSecurityEvent('user_registration', array(
            'user_id' => $user_id,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'ip' => $this->getClientIP(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log user deletion
     */
    public function logUserDeletion($user_id) {
        $user = get_userdata($user_id);
        $this->logSecurityEvent('user_deletion', array(
            'user_id' => $user_id,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log user update
     */
    public function logUserUpdate($user_id) {
        $user = get_userdata($user_id);
        $this->logSecurityEvent('user_update', array(
            'user_id' => $user_id,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log post save
     */
    public function logPostSave($post_id) {
        $post = get_post($post_id);
        $this->logSecurityEvent('post_save', array(
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'post_status' => $post->post_status,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log post deletion
     */
    public function logPostDeletion($post_id) {
        $post = get_post($post_id);
        $this->logSecurityEvent('post_deletion', array(
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log post trash
     */
    public function logPostTrash($post_id) {
        $post = get_post($post_id);
        $this->logSecurityEvent('post_trash', array(
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log option update
     */
    public function logOptionUpdate($option_name, $old_value, $value) {
        $this->logSecurityEvent('option_update', array(
            'option_name' => $option_name,
            'old_value' => $old_value,
            'new_value' => $value,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log option add
     */
    public function logOptionAdd($option_name, $value) {
        $this->logSecurityEvent('option_add', array(
            'option_name' => $option_name,
            'value' => $value,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log option delete
     */
    public function logOptionDelete($option_name) {
        $this->logSecurityEvent('option_delete', array(
            'option_name' => $option_name,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log security event
     */
    private function logSecurityEvent($event_type, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_events';
        
        $wpdb->insert(
            $table_name,
            array(
                'event_type' => $event_type,
                'data' => json_encode($data),
                'timestamp' => current_time('mysql'),
                'ip' => $data['ip'] ?? '',
                'user_id' => $data['user_id'] ?? 0
            ),
            array(
                '%s', '%s', '%s', '%s', '%d'
            )
        );
    }
    
    /**
     * Set up hooks
     */
    private function setupHooks() {
        // WordPress hooks
        add_action('init', array($this, 'init'));
        add_action('wp_loaded', array($this, 'wpLoaded'));
        add_action('admin_init', array($this, 'adminInit'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
        
        // Security hooks
        add_action('wp_loaded', array($this, 'checkSecurity'));
        add_action('init', array($this, 'initSecurity'));
        
        // Performance hooks
        add_action('init', array($this, 'initPerformance'));
        add_action('wp_loaded', array($this, 'optimizePerformance'));
        
        // Logging hooks
        add_action('init', array($this, 'initLogging'));
        add_action('wp_loaded', array($this, 'startLogging'));
        
        // Notification hooks
        add_action('init', array($this, 'initNotifications'));
        add_action('wp_loaded', array($this, 'setupNotifications'));
        
        // API hooks
        add_action('rest_api_init', array($this, 'initAPI'));
        add_action('wp_loaded', array($this, 'registerEndpoints'));
        
        // Integration hooks
        add_action('init', array($this, 'initIntegrations'));
        add_action('wp_loaded', array($this, 'loadIntegrations'));
        
        // Backup hooks
        add_action('init', array($this, 'initBackup'));
        add_action('wp_loaded', array($this, 'scheduleBackups'));
        
        // Compliance hooks
        add_action('init', array($this, 'initCompliance'));
        add_action('wp_loaded', array($this, 'startMonitoring'));
    }
    
    /**
     * Initialize components
     */
    private function initComponents() {
        // Initialize security components
        $this->initSecurity();
        
        // Initialize performance components
        $this->initPerformance();
        
        // Initialize logging components
        $this->initLogging();
        
        // Initialize notification components
        $this->initNotifications();
        
        // Initialize API components
        $this->initAPI();
        
        // Initialize integration components
        $this->initIntegrations();
        
        // Initialize backup components
        $this->initBackup();
        
        // Initialize compliance components
        $this->initCompliance();
    }
    
    /**
     * Set up cron jobs
     */
    private function setupCronJobs() {
        // Security scans
        if (!wp_next_scheduled('kbes_daily_security_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_daily_security_scan');
        }
        
        // Threat intelligence updates
        if (!wp_next_scheduled('kbes_threat_intelligence_update')) {
            wp_schedule_event(time(), 'twicedaily', 'kbes_threat_intelligence_update');
        }
        
        // Performance monitoring
        if (!wp_next_scheduled('kbes_performance_scan')) {
            wp_schedule_event(time(), 'hourly', 'kbes_performance_scan');
        }
        
        // Backup operations
        if (!wp_next_scheduled('kbes_backup_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_backup_scan');
        }
        
        // Compliance monitoring
        if (!wp_next_scheduled('kbes_compliance_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_compliance_scan');
        }
        
        // Analytics processing
        if (!wp_next_scheduled('kbes_analytics_process')) {
            wp_schedule_event(time(), 'hourly', 'kbes_analytics_process');
        }
        
        // Log cleanup
        if (!wp_next_scheduled('kbes_log_cleanup')) {
            wp_schedule_event(time(), 'daily', 'kbes_log_cleanup');
        }
        
        // Database optimization
        if (!wp_next_scheduled('kbes_database_optimize')) {
            wp_schedule_event(time(), 'weekly', 'kbes_database_optimize');
        }
    }
    
    /**
     * Initialize API
     */
    private function initAPI() {
        // API initialization will be handled by the API class
    }
    
    /**
     * Set up integrations
     */
    private function setupIntegrations() {
        // Integration setup will be handled by the Integrations class
    }
    
    /**
     * Get version
     */
    public function getVersion() {
        return $this->version;
    }
    
    /**
     * Get plugin file
     */
    public function getPluginFile() {
        return $this->plugin_file;
    }
    
    /**
     * Get plugin directory
     */
    public function getPluginDir() {
        return $this->plugin_dir;
    }
    
    /**
     * Get plugin URL
     */
    public function getPluginUrl() {
        return $this->plugin_url;
    }
    
    /**
     * Get assets URL
     */
    public function getAssetsUrl() {
        return $this->assets_url;
    }
    
    /**
     * Get includes directory
     */
    public function getIncludesDir() {
        return $this->includes_dir;
    }
    
    /**
     * Get modules directory
     */
    public function getModulesDir() {
        return $this->modules_dir;
    }
    
    /**
     * Get admin directory
     */
    public function getAdminDir() {
        return $this->admin_dir;
    }
    
    /**
     * Get public directory
     */
    public function getPublicDir() {
        return $this->public_dir;
    }
    
    /**
     * Get languages directory
     */
    public function getLanguagesDir() {
        return $this->languages_dir;
    }
    
    /**
     * Get templates directory
     */
    public function getTemplatesDir() {
        return $this->templates_dir;
    }
    
    /**
     * Get vendor directory
     */
    public function getVendorDir() {
        return $this->vendor_dir;
    }
}


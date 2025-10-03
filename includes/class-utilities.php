<?php
/**
 * Utilities for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Utilities class handling utility functions
 */
class Utilities {
    
    private $database;
    private $logging;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new Database();
        $this->logging = new Logging();
        
        $this->init();
    }
    
    /**
     * Initialize utilities
     */
    private function init() {
        add_action('init', array($this, 'initUtilities'));
    }
    
    /**
     * Initialize utilities
     */
    public function initUtilities() {
        // Set up utility hooks
        add_action('wp_loaded', array($this, 'checkForUpdates'));
    }
    
    /**
     * Check for updates
     */
    public function checkForUpdates() {
        $last_check = get_option('kbes_last_update_check', 0);
        $check_interval = 12 * HOUR_IN_SECONDS; // Check every 12 hours
        
        if (time() - $last_check > $check_interval) {
            $this->performUpdateCheck();
            update_option('kbes_last_update_check', time());
        }
    }
    
    /**
     * Perform update check
     */
    private function performUpdateCheck() {
        // Check for plugin updates
        $this->checkPluginUpdates();
        
        // Check for signature updates
        $this->checkSignatureUpdates();
        
        // Check for rule updates
        $this->checkRuleUpdates();
    }
    
    /**
     * Check plugin updates
     */
    private function checkPluginUpdates() {
        // Implementation for checking plugin updates
    }
    
    /**
     * Check signature updates
     */
    private function checkSignatureUpdates() {
        // Implementation for checking signature updates
    }
    
    /**
     * Check rule updates
     */
    private function checkRuleUpdates() {
        // Implementation for checking rule updates
    }
    
    /**
     * Create directories
     */
    public function createDirectories() {
        $upload_dir = wp_upload_dir();
        $kbes_dir = $upload_dir['basedir'] . '/kloudbean-enterprise-security';
        
        if (!file_exists($kbes_dir)) {
            wp_mkdir_p($kbes_dir);
        }
        
        // Create subdirectories
        $subdirs = array(
            'logs',
            'backups',
            'scans',
            'exports',
            'temp',
            'cache',
            'reports',
            'compliance',
            'analytics',
            'integrations'
        );
        
        foreach ($subdirs as $subdir) {
            $dir = $kbes_dir . '/' . $subdir;
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
            }
        }
        
        // Set file permissions
        chmod($kbes_dir, 0755);
    }
    
    /**
     * Cleanup
     */
    public function cleanup() {
        // Clean up temporary files
        $this->cleanupTempFiles();
        
        // Clean up old logs
        $this->cleanupOldLogs();
        
        // Clean up old backups
        $this->cleanupOldBackups();
        
        // Clean up old scans
        $this->cleanupOldScans();
    }
    
    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles() {
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/kloudbean-enterprise-security/temp';
        
        if (file_exists($temp_dir)) {
            $this->deleteDirectory($temp_dir);
        }
    }
    
    /**
     * Clean up old logs
     */
    private function cleanupOldLogs() {
        $this->database->cleanOldRecords(30);
    }
    
    /**
     * Clean up old backups
     */
    private function cleanupOldBackups() {
        $max_backups = get_option('kbes_max_backups', 10);
        $backup_path = $this->getBackupPath();
        
        $backups = glob($backup_path . '/backup_*');
        
        if (count($backups) > $max_backups) {
            // Sort by modification time
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove oldest backups
            $to_remove = count($backups) - $max_backups;
            
            for ($i = 0; $i < $to_remove; $i++) {
                $this->deleteDirectory($backups[$i]);
            }
        }
    }
    
    /**
     * Clean up old scans
     */
    private function cleanupOldScans() {
        $upload_dir = wp_upload_dir();
        $scans_dir = $upload_dir['basedir'] . '/kloudbean-enterprise-security/scans';
        
        if (file_exists($scans_dir)) {
            $scans = glob($scans_dir . '/scan_*');
            $max_scans = get_option('kbes_max_scans', 5);
            
            if (count($scans) > $max_scans) {
                // Sort by modification time
                usort($scans, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                
                // Remove oldest scans
                $to_remove = count($scans) - $max_scans;
                
                for ($i = 0; $i < $to_remove; $i++) {
                    $this->deleteDirectory($scans[$i]);
                }
            }
        }
    }
    
    /**
     * Delete directory
     */
    public function deleteDirectory($path) {
        if (is_dir($path)) {
            $dir = opendir($path);
            
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    $file_path = $path . '/' . $file;
                    
                    if (is_dir($file_path)) {
                        $this->deleteDirectory($file_path);
                    } else {
                        unlink($file_path);
                    }
                }
            }
            
            closedir($dir);
            rmdir($path);
        }
    }
    
    /**
     * Get backup path
     */
    private function getBackupPath() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/kloudbean-enterprise-security/backups';
    }
    
    /**
     * Generate random string
     */
    public function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * Generate API key
     */
    public function generateAPIKey() {
        return 'kbes_' . $this->generateRandomString(32);
    }
    
    /**
     * Hash password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Encrypt data
     */
    public function encryptData($data, $key = null) {
        if ($key === null) {
            $key = get_option('kbes_encryption_key');
            
            if (!$key) {
                $key = $this->generateRandomString(32);
                update_option('kbes_encryption_key', $key);
            }
        }
        
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data
     */
    public function decryptData($data, $key = null) {
        if ($key === null) {
            $key = get_option('kbes_encryption_key');
        }
        
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    /**
     * Get file hash
     */
    public function getFileHash($file_path, $algorithm = 'sha256') {
        if (file_exists($file_path)) {
            return hash_file($algorithm, $file_path);
        }
        
        return false;
    }
    
    /**
     * Get directory hash
     */
    public function getDirectoryHash($directory_path, $algorithm = 'sha256') {
        if (!is_dir($directory_path)) {
            return false;
        }
        
        $hashes = array();
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory_path));
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $hashes[] = hash_file($algorithm, $file->getPathname());
            }
        }
        
        return hash($algorithm, implode('', $hashes));
    }
    
    /**
     * Get file size
     */
    public function getFileSize($file_path) {
        if (file_exists($file_path)) {
            return filesize($file_path);
        }
        
        return 0;
    }
    
    /**
     * Get directory size
     */
    public function getDirectorySize($directory_path) {
        if (!is_dir($directory_path)) {
            return 0;
        }
        
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory_path));
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Format bytes
     */
    public function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get system info
     */
    public function getSystemInfo() {
        return array(
            'php_version' => PHP_VERSION,
            'wordpress_version' => get_bloginfo('version'),
            'plugin_version' => KBES_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_input_vars' => ini_get('max_input_vars'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_os' => PHP_OS,
            'server_arch' => php_uname('m'),
            'server_hostname' => php_uname('n'),
            'server_uptime' => $this->getServerUptime(),
            'disk_free_space' => disk_free_space(ABSPATH),
            'disk_total_space' => disk_total_space(ABSPATH)
        );
    }
    
    /**
     * Get server uptime
     */
    private function getServerUptime() {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return $load[0] . ', ' . $load[1] . ', ' . $load[2];
        }
        
        return 'Unknown';
    }
    
    /**
     * Check if file is writable
     */
    public function isFileWritable($file_path) {
        if (file_exists($file_path)) {
            return is_writable($file_path);
        }
        
        $directory = dirname($file_path);
        return is_writable($directory);
    }
    
    /**
     * Check if directory is writable
     */
    public function isDirectoryWritable($directory_path) {
        return is_writable($directory_path);
    }
    
    /**
     * Get file permissions
     */
    public function getFilePermissions($file_path) {
        if (file_exists($file_path)) {
            return substr(sprintf('%o', fileperms($file_path)), -4);
        }
        
        return '0000';
    }
    
    /**
     * Set file permissions
     */
    public function setFilePermissions($file_path, $permissions) {
        if (file_exists($file_path)) {
            return chmod($file_path, octdec($permissions));
        }
        
        return false;
    }
    
    /**
     * Get file owner
     */
    public function getFileOwner($file_path) {
        if (file_exists($file_path)) {
            $owner = posix_getpwuid(fileowner($file_path));
            return $owner['name'] ?? 'Unknown';
        }
        
        return 'Unknown';
    }
    
    /**
     * Get file group
     */
    public function getFileGroup($file_path) {
        if (file_exists($file_path)) {
            $group = posix_getgrgid(filegroup($file_path));
            return $group['name'] ?? 'Unknown';
        }
        
        return 'Unknown';
    }
    
    /**
     * Log utility event
     */
    private function logUtilityEvent($event_type, $data) {
        $this->logging->logSystemEvent($event_type, $data);
    }
}

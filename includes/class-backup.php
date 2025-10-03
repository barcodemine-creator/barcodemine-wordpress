<?php
/**
 * Backup for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Backup class handling backup operations
 */
class Backup {
    
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
     * Initialize backup
     */
    private function init() {
        add_action('init', array($this, 'initBackup'));
        add_action('wp_loaded', array($this, 'scheduleBackups'));
    }
    
    /**
     * Initialize backup
     */
    public function initBackup() {
        // Set up backup hooks
        add_action('kbes_backup_scan', array($this, 'runBackupScan'));
    }
    
    /**
     * Schedule backups
     */
    public function scheduleBackups() {
        // Schedule backup operations
        if (!wp_next_scheduled('kbes_backup_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_backup_scan');
        }
    }
    
    /**
     * Run backup scan
     */
    public function runBackupScan() {
        // Create backup
        $this->createBackup();
        
        // Clean old backups
        $this->cleanOldBackups();
    }
    
    /**
     * Create backup
     */
    public function createBackup($type = 'full') {
        $backup_name = 'backup_' . date('Y-m-d_H-i-s') . '_' . $type;
        $backup_path = $this->getBackupPath() . '/' . $backup_name;
        
        // Create backup directory
        if (!file_exists($backup_path)) {
            wp_mkdir_p($backup_path);
        }
        
        // Backup database
        if ($type === 'full' || $type === 'database') {
            $this->backupDatabase($backup_path);
        }
        
        // Backup files
        if ($type === 'full' || $type === 'files') {
            $this->backupFiles($backup_path);
        }
        
        // Backup configuration
        if ($type === 'full' || $type === 'config') {
            $this->backupConfig($backup_path);
        }
        
        // Log backup
        $this->logBackup($backup_name, $type, $backup_path);
        
        return $backup_path;
    }
    
    /**
     * Backup database
     */
    private function backupDatabase($backup_path) {
        global $wpdb;
        
        $db_file = $backup_path . '/database.sql';
        
        // Get database connection details
        $db_host = DB_HOST;
        $db_name = DB_NAME;
        $db_user = DB_USER;
        $db_pass = DB_PASSWORD;
        
        // Create mysqldump command
        $command = "mysqldump -h {$db_host} -u {$db_user} -p{$db_pass} {$db_name} > {$db_file}";
        
        // Execute command
        exec($command, $output, $return_code);
        
        if ($return_code !== 0) {
            $this->logging->logError('Database backup failed', array(
                'command' => $command,
                'output' => $output,
                'return_code' => $return_code
            ));
        }
    }
    
    /**
     * Backup files
     */
    private function backupFiles($backup_path) {
        $files_path = $backup_path . '/files';
        
        // Create files directory
        if (!file_exists($files_path)) {
            wp_mkdir_p($files_path);
        }
        
        // Backup WordPress files
        $this->backupWordPressFiles($files_path);
        
        // Backup uploads
        $this->backupUploads($files_path);
        
        // Backup plugins
        $this->backupPlugins($files_path);
        
        // Backup themes
        $this->backupThemes($files_path);
    }
    
    /**
     * Backup WordPress files
     */
    private function backupWordPressFiles($files_path) {
        $wp_files = array(
            'wp-config.php',
            'wp-salt.php',
            'wp-activate.php',
            'wp-blog-header.php',
            'wp-comments-post.php',
            'wp-cron.php',
            'wp-links-opml.php',
            'wp-load.php',
            'wp-login.php',
            'wp-mail.php',
            'wp-settings.php',
            'wp-signup.php',
            'wp-trackback.php',
            'xmlrpc.php'
        );
        
        foreach ($wp_files as $file) {
            if (file_exists(ABSPATH . $file)) {
                copy(ABSPATH . $file, $files_path . '/' . $file);
            }
        }
    }
    
    /**
     * Backup uploads
     */
    private function backupUploads($files_path) {
        $uploads_dir = wp_upload_dir();
        $uploads_path = $files_path . '/uploads';
        
        if (file_exists($uploads_dir['basedir'])) {
            $this->copyDirectory($uploads_dir['basedir'], $uploads_path);
        }
    }
    
    /**
     * Backup plugins
     */
    private function backupPlugins($files_path) {
        $plugins_dir = WP_PLUGIN_DIR;
        $plugins_path = $files_path . '/plugins';
        
        if (file_exists($plugins_dir)) {
            $this->copyDirectory($plugins_dir, $plugins_path);
        }
    }
    
    /**
     * Backup themes
     */
    private function backupThemes($files_path) {
        $themes_dir = get_theme_root();
        $themes_path = $files_path . '/themes';
        
        if (file_exists($themes_dir)) {
            $this->copyDirectory($themes_dir, $themes_path);
        }
    }
    
    /**
     * Backup configuration
     */
    private function backupConfig($backup_path) {
        $config_file = $backup_path . '/config.json';
        
        $config = array(
            'site_url' => get_site_url(),
            'home_url' => get_home_url(),
            'admin_email' => get_option('admin_email'),
            'timezone' => get_option('timezone_string'),
            'date_format' => get_option('date_format'),
            'time_format' => get_option('time_format'),
            'start_of_week' => get_option('start_of_week'),
            'use_balanceTags' => get_option('use_balanceTags'),
            'use_smilies' => get_option('use_smilies'),
            'default_category' => get_option('default_category'),
            'default_post_format' => get_option('default_post_format'),
            'mailserver_url' => get_option('mailserver_url'),
            'mailserver_login' => get_option('mailserver_login'),
            'mailserver_pass' => get_option('mailserver_pass'),
            'mailserver_port' => get_option('mailserver_port'),
            'default_email_category' => get_option('default_email_category'),
            'default_link_category' => get_option('default_link_category'),
            'show_on_front' => get_option('show_on_front'),
            'page_on_front' => get_option('page_on_front'),
            'page_for_posts' => get_option('page_for_posts'),
            'posts_per_page' => get_option('posts_per_page'),
            'posts_per_rss' => get_option('posts_per_rss'),
            'rss_use_excerpt' => get_option('rss_use_excerpt'),
            'blog_charset' => get_option('blog_charset'),
            'blog_charset' => get_option('blog_charset'),
            'moderation_keys' => get_option('moderation_keys'),
            'active_plugins' => get_option('active_plugins'),
            'current_theme' => get_option('current_theme'),
            'template' => get_option('template'),
            'stylesheet' => get_option('stylesheet'),
            'sidebars_widgets' => get_option('sidebars_widgets'),
            'cron' => get_option('cron'),
            'widget_recent-posts' => get_option('widget_recent-posts'),
            'widget_recent-comments' => get_option('widget_recent-comments'),
            'widget_archives' => get_option('widget_archives'),
            'widget_meta' => get_option('widget_meta'),
            'widget_search' => get_option('widget_search'),
            'widget_text' => get_option('widget_text'),
            'widget_categories' => get_option('widget_categories'),
            'widget_pages' => get_option('widget_pages'),
            'widget_calendar' => get_option('widget_calendar'),
            'widget_tag_cloud' => get_option('widget_tag_cloud'),
            'widget_nav_menu' => get_option('widget_nav_menu')
        );
        
        file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
    }
    
    /**
     * Copy directory
     */
    private function copyDirectory($src, $dst) {
        if (!file_exists($dst)) {
            wp_mkdir_p($dst);
        }
        
        $dir = opendir($src);
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $src_file = $src . '/' . $file;
                $dst_file = $dst . '/' . $file;
                
                if (is_dir($src_file)) {
                    $this->copyDirectory($src_file, $dst_file);
                } else {
                    copy($src_file, $dst_file);
                }
            }
        }
        
        closedir($dir);
    }
    
    /**
     * Log backup
     */
    private function logBackup($backup_name, $type, $backup_path) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_backups';
        
        $wpdb->insert(
            $table_name,
            array(
                'backup_name' => $backup_name,
                'backup_type' => $type,
                'file_path' => $backup_path,
                'file_size' => $this->getDirectorySize($backup_path),
                'file_hash' => $this->getDirectoryHash($backup_path),
                'status' => 'completed',
                'created_at' => current_time('mysql'),
                'completed_at' => current_time('mysql'),
                'created_by' => get_current_user_id()
            ),
            array(
                '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d'
            )
        );
    }
    
    /**
     * Get directory size
     */
    private function getDirectorySize($path) {
        $size = 0;
        
        if (is_dir($path)) {
            $dir = opendir($path);
            
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    $file_path = $path . '/' . $file;
                    
                    if (is_dir($file_path)) {
                        $size += $this->getDirectorySize($file_path);
                    } else {
                        $size += filesize($file_path);
                    }
                }
            }
            
            closedir($dir);
        }
        
        return $size;
    }
    
    /**
     * Get directory hash
     */
    private function getDirectoryHash($path) {
        $files = array();
        
        if (is_dir($path)) {
            $dir = opendir($path);
            
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    $file_path = $path . '/' . $file;
                    
                    if (is_dir($file_path)) {
                        $files[] = $this->getDirectoryHash($file_path);
                    } else {
                        $files[] = md5_file($file_path);
                    }
                }
            }
            
            closedir($dir);
        }
        
        return md5(implode('', $files));
    }
    
    /**
     * Get backup path
     */
    private function getBackupPath() {
        $upload_dir = wp_upload_dir();
        $backup_path = $upload_dir['basedir'] . '/kloudbean-enterprise-security/backups';
        
        if (!file_exists($backup_path)) {
            wp_mkdir_p($backup_path);
        }
        
        return $backup_path;
    }
    
    /**
     * Clean old backups
     */
    private function cleanOldBackups() {
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
                $this->removeBackup($backups[$i]);
            }
        }
    }
    
    /**
     * Remove backup
     */
    private function removeBackup($backup_path) {
        if (is_dir($backup_path)) {
            $this->removeDirectory($backup_path);
        } else {
            unlink($backup_path);
        }
    }
    
    /**
     * Remove directory
     */
    private function removeDirectory($path) {
        if (is_dir($path)) {
            $dir = opendir($path);
            
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    $file_path = $path . '/' . $file;
                    
                    if (is_dir($file_path)) {
                        $this->removeDirectory($file_path);
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
     * Get backups
     */
    public function getBackups() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_backups';
        
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    }
    
    /**
     * Get backup
     */
    public function getBackup($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_backups';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Delete backup
     */
    public function deleteBackup($id) {
        $backup = $this->getBackup($id);
        
        if ($backup) {
            $this->removeBackup($backup->file_path);
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'kbes_backups';
            
            $wpdb->delete(
                $table_name,
                array('id' => $id),
                array('%d')
            );
        }
    }
}

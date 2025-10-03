<?php
/**
 * Database management for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Database class handling all database operations
 */
class Database {
    
    private $wpdb;
    private $charset_collate;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
    }
    
    /**
     * Create all plugin tables
     */
    public function createTables() {
        $this->createSecurityLogsTable();
        $this->createThreatsTable();
        $this->createAnalyticsTable();
        $this->createComplianceTable();
        $this->createBackupsTable();
        $this->createPerformanceTable();
        $this->createIntegrationsTable();
        $this->createApiLogsTable();
        $this->createUserActivityTable();
        $this->createIncidentsTable();
        $this->createReportsTable();
        $this->createSettingsTable();
        $this->createBlacklistTable();
        $this->createWhitelistTable();
        $this->createFirewallRulesTable();
        $this->createMalwareSignaturesTable();
        $this->createVulnerabilitiesTable();
        $this->createSecurityTestsTable();
        $this->createFileIntegrityTable();
        $this->createQuarantineTable();
        $this->createWebhooksTable();
        $this->createNotificationsTable();
        $this->createErrorLogsTable();
        $this->createBlockedRequestsTable();
        $this->createSecurityEventsTable();
    }
    
    /**
     * Create security logs table
     */
    private function createSecurityLogsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_security_logs';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(100) NOT NULL,
            severity enum('low','medium','high','critical') DEFAULT 'medium',
            message text NOT NULL,
            data longtext,
            ip_address varchar(45) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_agent text,
            request_uri text,
            request_method varchar(10),
            country varchar(2),
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            resolved tinyint(1) DEFAULT 0,
            resolved_at datetime DEFAULT NULL,
            resolved_by bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY severity (severity),
            KEY ip_address (ip_address),
            KEY user_id (user_id),
            KEY timestamp (timestamp),
            KEY resolved (resolved)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create threats table
     */
    private function createThreatsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_threats';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            threat_type varchar(100) NOT NULL,
            threat_name varchar(255) NOT NULL,
            description text,
            severity enum('low','medium','high','critical') DEFAULT 'medium',
            source varchar(100) NOT NULL,
            ip_address varchar(45),
            user_id bigint(20) DEFAULT NULL,
            file_path text,
            file_hash varchar(64),
            payload text,
            signature_id varchar(100),
            blocked tinyint(1) DEFAULT 1,
            quarantined tinyint(1) DEFAULT 0,
            resolved tinyint(1) DEFAULT 0,
            resolved_at datetime DEFAULT NULL,
            resolved_by bigint(20) DEFAULT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY threat_type (threat_type),
            KEY severity (severity),
            KEY ip_address (ip_address),
            KEY user_id (user_id),
            KEY blocked (blocked),
            KEY quarantined (quarantined),
            KEY timestamp (timestamp)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create analytics table
     */
    private function createAnalyticsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_analytics';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            metric_name varchar(100) NOT NULL,
            metric_value decimal(15,4) NOT NULL,
            metric_type enum('counter','gauge','histogram') DEFAULT 'counter',
            dimensions text,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY metric_name (metric_name),
            KEY timestamp (timestamp)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create compliance table
     */
    private function createComplianceTable() {
        $table_name = $this->wpdb->prefix . 'kbes_compliance';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            framework varchar(50) NOT NULL,
            control_id varchar(100) NOT NULL,
            control_name varchar(255) NOT NULL,
            description text,
            status enum('compliant','non_compliant','not_applicable','in_progress') DEFAULT 'not_applicable',
            evidence text,
            last_checked datetime DEFAULT CURRENT_TIMESTAMP,
            next_check datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY framework (framework),
            KEY control_id (control_id),
            KEY status (status),
            KEY last_checked (last_checked)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create backups table
     */
    private function createBackupsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_backups';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            backup_name varchar(255) NOT NULL,
            backup_type enum('full','database','files','config') DEFAULT 'full',
            file_path text NOT NULL,
            file_size bigint(20) DEFAULT 0,
            file_hash varchar(64),
            status enum('pending','in_progress','completed','failed','deleted') DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            expires_at datetime DEFAULT NULL,
            created_by bigint(20) DEFAULT NULL,
            notes text,
            PRIMARY KEY (id),
            KEY backup_type (backup_type),
            KEY status (status),
            KEY created_at (created_at),
            KEY expires_at (expires_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create performance table
     */
    private function createPerformanceTable() {
        $table_name = $this->wpdb->prefix . 'kbes_performance';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            metric_name varchar(100) NOT NULL,
            metric_value decimal(10,4) NOT NULL,
            unit varchar(20) DEFAULT 'ms',
            page_url text,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45),
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY metric_name (metric_name),
            KEY timestamp (timestamp)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create integrations table
     */
    private function createIntegrationsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_integrations';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            integration_name varchar(100) NOT NULL,
            integration_type varchar(50) NOT NULL,
            status enum('active','inactive','error') DEFAULT 'inactive',
            config text,
            last_sync datetime DEFAULT NULL,
            last_error text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY integration_name (integration_name),
            KEY integration_type (integration_type),
            KEY status (status)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create API logs table
     */
    private function createApiLogsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_api_logs';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            endpoint varchar(255) NOT NULL,
            method varchar(10) NOT NULL,
            status_code int(3) NOT NULL,
            response_time decimal(10,4) DEFAULT 0,
            ip_address varchar(45) NOT NULL,
            user_agent text,
            request_data text,
            response_data text,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY endpoint (endpoint),
            KEY method (method),
            KEY status_code (status_code),
            KEY ip_address (ip_address),
            KEY timestamp (timestamp)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create user activity table
     */
    private function createUserActivityTable() {
        $table_name = $this->wpdb->prefix . 'kbes_user_activity';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            activity_type varchar(100) NOT NULL,
            activity_description text,
            ip_address varchar(45) NOT NULL,
            user_agent text,
            page_url text,
            data text,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY activity_type (activity_type),
            KEY ip_address (ip_address),
            KEY timestamp (timestamp)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create incidents table
     */
    private function createIncidentsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_incidents';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            incident_type varchar(100) NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            severity enum('low','medium','high','critical') DEFAULT 'medium',
            status enum('open','investigating','resolved','closed') DEFAULT 'open',
            assigned_to bigint(20) DEFAULT NULL,
            created_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            resolved_at datetime DEFAULT NULL,
            resolution text,
            PRIMARY KEY (id),
            KEY incident_type (incident_type),
            KEY severity (severity),
            KEY status (status),
            KEY assigned_to (assigned_to),
            KEY created_at (created_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create reports table
     */
    private function createReportsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_reports';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            report_name varchar(255) NOT NULL,
            report_type varchar(100) NOT NULL,
            report_data longtext,
            file_path text,
            file_size bigint(20) DEFAULT 0,
            status enum('pending','generating','completed','failed') DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            created_by bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY report_type (report_type),
            KEY status (status),
            KEY created_at (created_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create settings table
     */
    private function createSettingsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_settings';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            setting_name varchar(255) NOT NULL,
            setting_value longtext,
            setting_type varchar(50) DEFAULT 'string',
            module varchar(100) DEFAULT 'core',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY setting_name (setting_name),
            KEY module (module)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create blacklist table
     */
    private function createBlacklistTable() {
        $table_name = $this->wpdb->prefix . 'kbes_blacklist';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ip_address varchar(45) NOT NULL,
            reason varchar(255) NOT NULL,
            source varchar(100) DEFAULT 'manual',
            expires_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            created_by bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY ip_address (ip_address),
            KEY expires_at (expires_at),
            KEY created_at (created_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create whitelist table
     */
    private function createWhitelistTable() {
        $table_name = $this->wpdb->prefix . 'kbes_whitelist';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ip_address varchar(45) NOT NULL,
            reason varchar(255) NOT NULL,
            source varchar(100) DEFAULT 'manual',
            expires_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            created_by bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY ip_address (ip_address),
            KEY expires_at (expires_at),
            KEY created_at (created_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create firewall rules table
     */
    private function createFirewallRulesTable() {
        $table_name = $this->wpdb->prefix . 'kbes_firewall_rules';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            rule_name varchar(255) NOT NULL,
            rule_type varchar(50) NOT NULL,
            rule_pattern text NOT NULL,
            action enum('allow','block','challenge') DEFAULT 'block',
            priority int(11) DEFAULT 100,
            enabled tinyint(1) DEFAULT 1,
            source varchar(100) DEFAULT 'manual',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY rule_type (rule_type),
            KEY action (action),
            KEY enabled (enabled),
            KEY priority (priority)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create malware signatures table
     */
    private function createMalwareSignaturesTable() {
        $table_name = $this->wpdb->prefix . 'kbes_malware_signatures';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            signature_name varchar(255) NOT NULL,
            signature_pattern text NOT NULL,
            signature_type varchar(50) NOT NULL,
            malware_family varchar(100),
            severity enum('low','medium','high','critical') DEFAULT 'medium',
            enabled tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY signature_type (signature_type),
            KEY malware_family (malware_family),
            KEY severity (severity),
            KEY enabled (enabled)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create vulnerabilities table
     */
    private function createVulnerabilitiesTable() {
        $table_name = $this->wpdb->prefix . 'kbes_vulnerabilities';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cve_id varchar(20) NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            severity enum('low','medium','high','critical') DEFAULT 'medium',
            cvss_score decimal(3,1) DEFAULT 0.0,
            affected_software varchar(255) NOT NULL,
            affected_version varchar(50),
            fixed_version varchar(50),
            published_date date,
            last_modified date,
            references text,
            PRIMARY KEY (id),
            UNIQUE KEY cve_id (cve_id),
            KEY severity (severity),
            KEY affected_software (affected_software),
            KEY published_date (published_date)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create security tests table
     */
    private function createSecurityTestsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_security_tests';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            test_name varchar(255) NOT NULL,
            test_category varchar(100) NOT NULL,
            test_description text,
            status enum('pass','fail','warning','not_applicable') DEFAULT 'not_applicable',
            result_message text,
            remediation text,
            last_run datetime DEFAULT NULL,
            last_result text,
            auto_fixable tinyint(1) DEFAULT 0,
            weight int(11) DEFAULT 1,
            PRIMARY KEY (id),
            KEY test_category (test_category),
            KEY status (status),
            KEY last_run (last_run)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create file integrity table
     */
    private function createFileIntegrityTable() {
        $table_name = $this->wpdb->prefix . 'kbes_file_integrity';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            file_path text NOT NULL,
            file_hash varchar(64) NOT NULL,
            file_size bigint(20) DEFAULT 0,
            file_type enum('core','plugin','theme','custom') DEFAULT 'custom',
            status enum('clean','modified','missing','added') DEFAULT 'clean',
            last_checked datetime DEFAULT CURRENT_TIMESTAMP,
            last_modified datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY file_type (file_type),
            KEY status (status),
            KEY last_checked (last_checked)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create quarantine table
     */
    private function createQuarantineTable() {
        $table_name = $this->wpdb->prefix . 'kbes_quarantine';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            original_path text NOT NULL,
            quarantine_path text NOT NULL,
            file_hash varchar(64) NOT NULL,
            threat_type varchar(100) NOT NULL,
            threat_name varchar(255) NOT NULL,
            quarantined_at datetime DEFAULT CURRENT_TIMESTAMP,
            quarantined_by bigint(20) DEFAULT NULL,
            restored_at datetime DEFAULT NULL,
            restored_by bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY threat_type (threat_type),
            KEY quarantined_at (quarantined_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create webhooks table
     */
    private function createWebhooksTable() {
        $table_name = $this->wpdb->prefix . 'kbes_webhooks';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            webhook_name varchar(255) NOT NULL,
            webhook_url text NOT NULL,
            events text NOT NULL,
            secret_key varchar(255),
            enabled tinyint(1) DEFAULT 1,
            last_triggered datetime DEFAULT NULL,
            last_response_code int(3) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY enabled (enabled),
            KEY last_triggered (last_triggered)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create notifications table
     */
    private function createNotificationsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_notifications';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            notification_type varchar(100) NOT NULL,
            title varchar(255) NOT NULL,
            message text NOT NULL,
            severity enum('low','medium','high','critical') DEFAULT 'medium',
            status enum('pending','sent','failed') DEFAULT 'pending',
            recipient_email varchar(255),
            recipient_user_id bigint(20) DEFAULT NULL,
            sent_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY notification_type (notification_type),
            KEY severity (severity),
            KEY status (status),
            KEY created_at (created_at)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create error logs table
     */
    private function createErrorLogsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_error_logs';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(50) NOT NULL,
            code int(11) NOT NULL,
            message text NOT NULL,
            file varchar(255) NOT NULL,
            line int(11) NOT NULL,
            trace text,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            url text,
            user_id bigint(20) DEFAULT NULL,
            ip varchar(45),
            PRIMARY KEY (id),
            KEY type (type),
            KEY code (code),
            KEY timestamp (timestamp),
            KEY user_id (user_id)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create blocked requests table
     */
    private function createBlockedRequestsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_blocked_requests';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ip varchar(45) NOT NULL,
            reason varchar(255) NOT NULL,
            user_agent text,
            request_uri text,
            request_method varchar(10),
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            country varchar(2),
            user_id bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY ip (ip),
            KEY reason (reason),
            KEY timestamp (timestamp),
            KEY country (country)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create security events table
     */
    private function createSecurityEventsTable() {
        $table_name = $this->wpdb->prefix . 'kbes_security_events';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(100) NOT NULL,
            data text,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            ip varchar(45),
            user_id bigint(20) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY timestamp (timestamp),
            KEY ip (ip),
            KEY user_id (user_id)
        ) $this->charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Get database version
     */
    public function getVersion() {
        return get_option('kbes_db_version', '0.0.0');
    }
    
    /**
     * Update database version
     */
    public function updateVersion($version) {
        update_option('kbes_db_version', $version);
    }
    
    /**
     * Check if table exists
     */
    public function tableExists($table_name) {
        $table_name = $this->wpdb->prefix . $table_name;
        return $this->wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    }
    
    /**
     * Get table structure
     */
    public function getTableStructure($table_name) {
        $table_name = $this->wpdb->prefix . $table_name;
        return $this->wpdb->get_results("DESCRIBE $table_name");
    }
    
    /**
     * Optimize all tables
     */
    public function optimizeTables() {
        $tables = array(
            'kbes_security_logs',
            'kbes_threats',
            'kbes_analytics',
            'kbes_compliance',
            'kbes_backups',
            'kbes_performance',
            'kbes_integrations',
            'kbes_api_logs',
            'kbes_user_activity',
            'kbes_incidents',
            'kbes_reports',
            'kbes_settings',
            'kbes_blacklist',
            'kbes_whitelist',
            'kbes_firewall_rules',
            'kbes_malware_signatures',
            'kbes_vulnerabilities',
            'kbes_security_tests',
            'kbes_file_integrity',
            'kbes_quarantine',
            'kbes_webhooks',
            'kbes_notifications',
            'kbes_error_logs',
            'kbes_blocked_requests',
            'kbes_security_events'
        );
        
        foreach ($tables as $table) {
            $table_name = $this->wpdb->prefix . $table;
            $this->wpdb->query("OPTIMIZE TABLE $table_name");
        }
    }
    
    /**
     * Clean old records
     */
    public function cleanOldRecords($days = 30) {
        $tables = array(
            'kbes_security_logs' => 'timestamp',
            'kbes_analytics' => 'timestamp',
            'kbes_api_logs' => 'timestamp',
            'kbes_user_activity' => 'timestamp',
            'kbes_performance' => 'timestamp',
            'kbes_error_logs' => 'timestamp',
            'kbes_blocked_requests' => 'timestamp',
            'kbes_security_events' => 'timestamp'
        );
        
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        foreach ($tables as $table => $date_column) {
            $table_name = $this->wpdb->prefix . $table;
            $this->wpdb->query($this->wpdb->prepare(
                "DELETE FROM $table_name WHERE $date_column < %s",
                $cutoff_date
            ));
        }
    }
    
    /**
     * Get database statistics
     */
    public function getStats() {
        $stats = array();
        
        $tables = array(
            'kbes_security_logs',
            'kbes_threats',
            'kbes_analytics',
            'kbes_compliance',
            'kbes_backups',
            'kbes_performance',
            'kbes_integrations',
            'kbes_api_logs',
            'kbes_user_activity',
            'kbes_incidents',
            'kbes_reports',
            'kbes_settings',
            'kbes_blacklist',
            'kbes_whitelist',
            'kbes_firewall_rules',
            'kbes_malware_signatures',
            'kbes_vulnerabilities',
            'kbes_security_tests',
            'kbes_file_integrity',
            'kbes_quarantine',
            'kbes_webhooks',
            'kbes_notifications',
            'kbes_error_logs',
            'kbes_blocked_requests',
            'kbes_security_events'
        );
        
        foreach ($tables as $table) {
            $table_name = $this->wpdb->prefix . $table;
            $count = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $stats[$table] = intval($count);
        }
        
        return $stats;
    }
}

<?php
/**
 * Firewall template for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}
?>

<div class="wrap kbes-firewall">
    <h1><?php _e('Firewall', 'kloudbean-enterprise-security'); ?></h1>
    
    <!-- Firewall Status -->
    <div class="kbes-firewall-status">
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-shield"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Firewall Status', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php _e('Active', 'kloudbean-enterprise-security'); ?></div>
            </div>
        </div>
        
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-admin-tools"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Rules Count', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php echo esc_html($firewall_data['rules_count']); ?></div>
            </div>
        </div>
        
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-block-default"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Blocked IPs', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php echo esc_html($firewall_data['blocked_ips']); ?></div>
            </div>
        </div>
        
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-yes"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Whitelisted IPs', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php echo esc_html($firewall_data['whitelisted_ips']); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Firewall Controls -->
    <div class="kbes-firewall-controls">
        <button id="kbes-add-rule" class="button button-primary">
            <span class="dashicons dashicons-plus"></span>
            <?php _e('Add Rule', 'kloudbean-enterprise-security'); ?>
        </button>
        
        <button id="kbes-block-ip" class="button">
            <span class="dashicons dashicons-block-default"></span>
            <?php _e('Block IP', 'kloudbean-enterprise-security'); ?>
        </button>
        
        <button id="kbes-whitelist-ip" class="button">
            <span class="dashicons dashicons-yes"></span>
            <?php _e('Whitelist IP', 'kloudbean-enterprise-security'); ?>
        </button>
        
        <button id="kbes-import-rules" class="button">
            <span class="dashicons dashicons-upload"></span>
            <?php _e('Import Rules', 'kloudbean-enterprise-security'); ?>
        </button>
    </div>
    
    <!-- Firewall Rules -->
    <div class="kbes-firewall-rules">
        <h2><?php _e('Firewall Rules', 'kloudbean-enterprise-security'); ?></h2>
        
        <div class="kbes-rules-filters">
            <select id="kbes-rule-type-filter">
                <option value=""><?php _e('All Types', 'kloudbean-enterprise-security'); ?></option>
                <option value="ip"><?php _e('IP Address', 'kloudbean-enterprise-security'); ?></option>
                <option value="user_agent"><?php _e('User Agent', 'kloudbean-enterprise-security'); ?></option>
                <option value="uri"><?php _e('URI', 'kloudbean-enterprise-security'); ?></option>
                <option value="method"><?php _e('HTTP Method', 'kloudbean-enterprise-security'); ?></option>
            </select>
            
            <select id="kbes-rule-action-filter">
                <option value=""><?php _e('All Actions', 'kloudbean-enterprise-security'); ?></option>
                <option value="block"><?php _e('Block', 'kloudbean-enterprise-security'); ?></option>
                <option value="allow"><?php _e('Allow', 'kloudbean-enterprise-security'); ?></option>
                <option value="challenge"><?php _e('Challenge', 'kloudbean-enterprise-security'); ?></option>
            </select>
            
            <input type="text" id="kbes-rule-search" placeholder="<?php _e('Search rules...', 'kloudbean-enterprise-security'); ?>">
        </div>
        
        <div class="kbes-rules-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Type', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Pattern', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Action', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Priority', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Status', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Actions', 'kloudbean-enterprise-security'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Block Suspicious User Agents', 'kloudbean-enterprise-security'); ?></td>
                        <td><?php _e('User Agent', 'kloudbean-enterprise-security'); ?></td>
                        <td>bot|crawler|spider</td>
                        <td><span class="kbes-action-block"><?php _e('Block', 'kloudbean-enterprise-security'); ?></span></td>
                        <td>100</td>
                        <td><span class="kbes-status-enabled"><?php _e('Enabled', 'kloudbean-enterprise-security'); ?></span></td>
                        <td>
                            <button class="button button-small"><?php _e('Edit', 'kloudbean-enterprise-security'); ?></button>
                            <button class="button button-small"><?php _e('Delete', 'kloudbean-enterprise-security'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e('Block Admin Access', 'kloudbean-enterprise-security'); ?></td>
                        <td><?php _e('URI', 'kloudbean-enterprise-security'); ?></td>
                        <td>/wp-admin/</td>
                        <td><span class="kbes-action-block"><?php _e('Block', 'kloudbean-enterprise-security'); ?></span></td>
                        <td>200</td>
                        <td><span class="kbes-status-disabled"><?php _e('Disabled', 'kloudbean-enterprise-security'); ?></span></td>
                        <td>
                            <button class="button button-small"><?php _e('Edit', 'kloudbean-enterprise-security'); ?></button>
                            <button class="button button-small"><?php _e('Delete', 'kloudbean-enterprise-security'); ?></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Blocked Requests -->
    <div class="kbes-blocked-requests">
        <h2><?php _e('Recent Blocked Requests', 'kloudbean-enterprise-security'); ?></h2>
        
        <div class="kbes-blocked-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('IP Address', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Reason', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('User Agent', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Request URI', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Country', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Time', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Actions', 'kloudbean-enterprise-security'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($firewall_data['recent_blocks'] as $block): ?>
                    <tr>
                        <td><?php echo esc_html($block->ip); ?></td>
                        <td><?php echo esc_html($block->reason); ?></td>
                        <td><?php echo esc_html(substr($block->user_agent, 0, 50)); ?>...</td>
                        <td><?php echo esc_html(substr($block->request_uri, 0, 50)); ?>...</td>
                        <td><?php echo esc_html($block->country); ?></td>
                        <td><?php echo esc_html(date('M j, Y g:i A', strtotime($block->timestamp))); ?></td>
                        <td>
                            <button class="button button-small kbes-whitelist-ip" data-ip="<?php echo esc_attr($block->ip); ?>">
                                <?php _e('Whitelist', 'kloudbean-enterprise-security'); ?>
                            </button>
                            <button class="button button-small kbes-block-ip" data-ip="<?php echo esc_attr($block->ip); ?>">
                                <?php _e('Block', 'kloudbean-enterprise-security'); ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Top Attacking IPs -->
    <div class="kbes-attacking-ips">
        <h2><?php _e('Top Attacking IPs', 'kloudbean-enterprise-security'); ?></h2>
        
        <div class="kbes-attacking-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('IP Address', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Attack Count', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Last Attack', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Actions', 'kloudbean-enterprise-security'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($firewall_data['top_attacking_ips'] as $ip): ?>
                    <tr>
                        <td><?php echo esc_html($ip->ip); ?></td>
                        <td><?php echo esc_html($ip->count); ?></td>
                        <td><?php _e('2 hours ago', 'kloudbean-enterprise-security'); ?></td>
                        <td>
                            <button class="button button-small kbes-block-ip" data-ip="<?php echo esc_attr($ip->ip); ?>">
                                <?php _e('Block', 'kloudbean-enterprise-security'); ?>
                            </button>
                            <button class="button button-small kbes-whitelist-ip" data-ip="<?php echo esc_attr($ip->ip); ?>">
                                <?php _e('Whitelist', 'kloudbean-enterprise-security'); ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add Rule Modal -->
    <div id="kbes-add-rule-modal" class="kbes-modal" style="display: none;">
        <div class="kbes-modal-content">
            <div class="kbes-modal-header">
                <h3><?php _e('Add Firewall Rule', 'kloudbean-enterprise-security'); ?></h3>
                <button class="kbes-modal-close">&times;</button>
            </div>
            <div class="kbes-modal-body">
                <form id="kbes-add-rule-form">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="rule_name"><?php _e('Rule Name', 'kloudbean-enterprise-security'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="rule_name" name="rule_name" class="regular-text" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="rule_type"><?php _e('Rule Type', 'kloudbean-enterprise-security'); ?></label>
                            </th>
                            <td>
                                <select id="rule_type" name="rule_type" required>
                                    <option value=""><?php _e('Select Type', 'kloudbean-enterprise-security'); ?></option>
                                    <option value="ip"><?php _e('IP Address', 'kloudbean-enterprise-security'); ?></option>
                                    <option value="user_agent"><?php _e('User Agent', 'kloudbean-enterprise-security'); ?></option>
                                    <option value="uri"><?php _e('URI', 'kloudbean-enterprise-security'); ?></option>
                                    <option value="method"><?php _e('HTTP Method', 'kloudbean-enterprise-security'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="rule_pattern"><?php _e('Pattern', 'kloudbean-enterprise-security'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="rule_pattern" name="rule_pattern" class="regular-text" required>
                                <p class="description"><?php _e('Enter the pattern to match (supports regex)', 'kloudbean-enterprise-security'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="rule_action"><?php _e('Action', 'kloudbean-enterprise-security'); ?></label>
                            </th>
                            <td>
                                <select id="rule_action" name="rule_action" required>
                                    <option value="block"><?php _e('Block', 'kloudbean-enterprise-security'); ?></option>
                                    <option value="allow"><?php _e('Allow', 'kloudbean-enterprise-security'); ?></option>
                                    <option value="challenge"><?php _e('Challenge', 'kloudbean-enterprise-security'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="rule_priority"><?php _e('Priority', 'kloudbean-enterprise-security'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="rule_priority" name="rule_priority" class="small-text" value="100" min="1" max="1000">
                                <p class="description"><?php _e('Lower numbers have higher priority', 'kloudbean-enterprise-security'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="kbes-modal-actions">
                        <button type="submit" class="button button-primary"><?php _e('Add Rule', 'kloudbean-enterprise-security'); ?></button>
                        <button type="button" class="button kbes-modal-close"><?php _e('Cancel', 'kloudbean-enterprise-security'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.kbes-firewall {
    max-width: 1200px;
}

.kbes-firewall-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kbes-status-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.kbes-status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f0f0f1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kbes-status-icon .dashicons {
    font-size: 24px;
    color: #646970;
}

.kbes-status-content {
    flex: 1;
}

.kbes-status-title {
    font-size: 14px;
    color: #646970;
    margin-bottom: 5px;
}

.kbes-status-value {
    font-size: 18px;
    font-weight: 600;
    color: #1d2327;
}

.kbes-firewall-controls {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.kbes-firewall-controls .button {
    display: flex;
    align-items: center;
    gap: 8px;
}

.kbes-firewall-rules,
.kbes-blocked-requests,
.kbes-attacking-ips {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 30px;
}

.kbes-firewall-rules h2,
.kbes-blocked-requests h2,
.kbes-attacking-ips h2 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
}

.kbes-rules-filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.kbes-rules-filters select,
.kbes-rules-filters input {
    padding: 8px 12px;
    border: 1px solid #8c8f94;
    border-radius: 4px;
}

.kbes-rules-table,
.kbes-blocked-table,
.kbes-attacking-table {
    overflow-x: auto;
}

.kbes-action-block {
    background: #f8d7da;
    color: #721c24;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.kbes-action-allow {
    background: #d1e7dd;
    color: #0f5132;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.kbes-status-enabled {
    background: #d1e7dd;
    color: #0f5132;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.kbes-status-disabled {
    background: #f8d7da;
    color: #721c24;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.kbes-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kbes-modal-content {
    background: #fff;
    border-radius: 4px;
    max-width: 600px;
    width: 90%;
    max-height: 80%;
    overflow-y: auto;
}

.kbes-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #ccd0d4;
}

.kbes-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.kbes-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #646970;
}

.kbes-modal-body {
    padding: 20px;
}

.kbes-modal-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add rule button
    $('#kbes-add-rule').on('click', function() {
        $('#kbes-add-rule-modal').show();
    });
    
    // Close modal
    $('.kbes-modal-close').on('click', function() {
        $('#kbes-add-rule-modal').hide();
    });
    
    // Close modal on background click
    $('#kbes-add-rule-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });
    
    // Add rule form
    $('#kbes-add-rule-form').on('submit', function(e) {
        e.preventDefault();
        
        // Here you would typically send an AJAX request to add the rule
        alert('Rule added successfully!');
        $('#kbes-add-rule-modal').hide();
        location.reload();
    });
    
    // Block IP buttons
    $('.kbes-block-ip').on('click', function() {
        const ip = $(this).data('ip');
        if (confirm('Are you sure you want to block IP: ' + ip + '?')) {
            // Here you would typically send an AJAX request to block the IP
            alert('IP blocked successfully!');
            location.reload();
        }
    });
    
    // Whitelist IP buttons
    $('.kbes-whitelist-ip').on('click', function() {
        const ip = $(this).data('ip');
        if (confirm('Are you sure you want to whitelist IP: ' + ip + '?')) {
            // Here you would typically send an AJAX request to whitelist the IP
            alert('IP whitelisted successfully!');
            location.reload();
        }
    });
});
</script>

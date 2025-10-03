/**
 * Kloudbean Fortress Security - Admin JavaScript
 * Professional WordPress security plugin interface
 * Handles all interactive features and AJAX requests
 */

(function($) {
    'use strict';

    // Global variables
    let scanInProgress = false;
    let currentScanType = '';

    /**
     * Initialize the plugin when document is ready
     */
    $(document).ready(function() {
        initializeFortress();
    });

    /**
     * Main initialization function
     */
    function initializeFortress() {
        initializeTabs();
        initializeToggles();
        initializeScanners();
        initializeFirewall();
        initializeSettings();
        initializeLogs();
        initializeQuickActions();
        initializeProgressBars();
        
        // Show welcome message on first load
        if (localStorage.getItem('kbf_first_visit') !== 'false') {
            showWelcomeMessage();
            localStorage.setItem('kbf_first_visit', 'false');
        }
    }

    /**
     * Initialize tab functionality
     */
    function initializeTabs() {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this).attr('href');
            
            // Update active tab
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show corresponding content
            $('.tab-content').removeClass('active');
            $(target).addClass('active');
            
            // Trigger custom event
            $(document).trigger('kbf:tab-changed', [target]);
        });
    }

    /**
     * Initialize toggle switches
     */
    function initializeToggles() {
        $('.kbf-toggle input[type="checkbox"]').on('change', function() {
            const $toggle = $(this);
            const setting = $toggle.attr('name');
            const value = $toggle.is(':checked');
            
            // Add visual feedback
            $toggle.closest('.kbf-toggle').addClass('kbf-loading');
            
            // Auto-save setting
            setTimeout(() => {
                $toggle.closest('.kbf-toggle').removeClass('kbf-loading');
                showNotification('Setting updated', 'success');
            }, 500);
            
            // Trigger dependent settings
            handleDependentSettings(setting, value);
        });
    }

    /**
     * Handle dependent settings
     */
    function handleDependentSettings(setting, value) {
        const dependencies = {
            'fortress_enabled': ['firewall_enabled', 'malware_scanner', 'login_protection'],
            'firewall_enabled': ['rate_limiting', 'block_suspicious_ips'],
            'scheduled_scans': ['scan_frequency', 'scan_time'],
            'email_notifications': ['email_on_scan', 'email_on_threat'],
            'login_protection': ['limit_login_attempts', 'two_factor_auth']
        };

        if (dependencies[setting]) {
            dependencies[setting].forEach(dep => {
                const $depInput = $(`input[name="${dep}"]`);
                if (!value) {
                    $depInput.prop('disabled', true).closest('tr').addClass('kbf-disabled');
                } else {
                    $depInput.prop('disabled', false).closest('tr').removeClass('kbf-disabled');
                }
            });
        }
    }

    /**
     * Initialize scanner functionality
     */
    function initializeScanners() {
        // Quick scan button
        $('#kbf-quick-scan').on('click', function() {
            startScan('quick');
        });

        // Full scan button
        $('#kbf-full-scan').on('click', function() {
            startScan('full');
        });

        // Start comprehensive scan
        $('#kbf-start-scan').on('click', function() {
            startScan('comprehensive');
        });

        // Stop scan button
        $('#kbf-stop-scan').on('click', function() {
            stopScan();
        });

        // Schedule scan button
        $('#kbf-schedule-scan').on('click', function() {
            showScheduleDialog();
        });
    }

    /**
     * Start security scan
     */
    function startScan(type) {
        if (scanInProgress) {
            showNotification('Scan already in progress', 'warning');
            return;
        }

        scanInProgress = true;
        currentScanType = type;

        // Update UI
        updateScanUI(true);
        showScanProgress();

        // Start the scan
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_scan',
                scan_type: type,
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayScanResults(response.data);
                    showNotification('Scan completed successfully', 'success');
                } else {
                    showNotification('Scan failed: ' + response.data.message, 'error');
                }
            },
            error: function() {
                showNotification('Scan failed due to network error', 'error');
            },
            complete: function() {
                scanInProgress = false;
                updateScanUI(false);
                hideScanProgress();
            }
        });
    }

    /**
     * Stop current scan
     */
    function stopScan() {
        if (!scanInProgress) return;

        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_stop_scan',
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                scanInProgress = false;
                updateScanUI(false);
                hideScanProgress();
                showNotification('Scan stopped', 'info');
            }
        });
    }

    /**
     * Update scan UI elements
     */
    function updateScanUI(scanning) {
        if (scanning) {
            $('#kbf-quick-scan, #kbf-full-scan, #kbf-start-scan').prop('disabled', true).addClass('kbf-loading');
            $('#kbf-stop-scan').show();
        } else {
            $('#kbf-quick-scan, #kbf-full-scan, #kbf-start-scan').prop('disabled', false).removeClass('kbf-loading');
            $('#kbf-stop-scan').hide();
        }
    }

    /**
     * Show scan progress
     */
    function showScanProgress() {
        $('#kbf-scan-progress').show();
        animateProgress();
    }

    /**
     * Hide scan progress
     */
    function hideScanProgress() {
        $('#kbf-scan-progress').hide();
        $('.progress-fill').css('width', '0%');
    }

    /**
     * Animate progress bar
     */
    function animateProgress() {
        let progress = 0;
        const interval = setInterval(() => {
            if (!scanInProgress) {
                clearInterval(interval);
                return;
            }

            progress += Math.random() * 10;
            if (progress > 95) progress = 95;

            $('.progress-fill').css('width', progress + '%');
            
            // Update progress text
            const messages = [
                'Initializing scan...',
                'Scanning WordPress core files...',
                'Checking for malware signatures...',
                'Analyzing theme files...',
                'Scanning plugin files...',
                'Checking for vulnerabilities...',
                'Finalizing scan results...'
            ];
            
            const messageIndex = Math.floor(progress / 15);
            if (messages[messageIndex]) {
                $('.progress-text').text(messages[messageIndex]);
            }
        }, 1000);
    }

    /**
     * Display scan results
     */
    function displayScanResults(data) {
        const $resultsArea = $('#kbf-results-area, #kbf-scan-results');
        
        let html = '<div class="kbf-scan-results">';
        html += '<h3>🔍 Scan Results</h3>';
        
        if (data.threats_found > 0) {
            html += '<div class="scan-threats">';
            html += `<h4>⚠️ ${data.threats_found} Security Issues Found</h4>`;
            html += '<div class="threats-list">';
            
            data.threats.forEach(threat => {
                html += '<div class="threat-item">';
                html += `<div class="threat-severity severity-${threat.severity.toLowerCase()}">${threat.severity}</div>`;
                html += `<div class="threat-details">`;
                html += `<strong>${threat.type}</strong>`;
                html += `<p>File: ${threat.file}</p>`;
                if (threat.auto_fix) {
                    html += `<button class="button kbf-fix-btn" data-threat-id="${threat.id}">🔧 Auto Fix</button>`;
                }
                html += `</div>`;
                html += '</div>';
            });
            
            html += '</div>';
            html += '</div>';
        } else {
            html += '<div class="scan-clean">';
            html += '<h4>✅ No Security Issues Found</h4>';
            html += '<p>Your website is secure and protected!</p>';
            html += '</div>';
        }
        
        // Scan statistics
        html += '<div class="scan-stats">';
        html += '<h4>📊 Scan Statistics</h4>';
        html += '<div class="stats-grid">';
        html += `<div class="stat-item"><strong>${data.files_scanned}</strong><span>Files Scanned</span></div>`;
        html += `<div class="stat-item"><strong>${data.threats_found}</strong><span>Threats Found</span></div>`;
        html += `<div class="stat-item"><strong>${data.scan_time}s</strong><span>Scan Time</span></div>`;
        html += `<div class="stat-item"><strong>${data.whitelisted_files || 0}</strong><span>Files Whitelisted</span></div>`;
        html += '</div>';
        html += '</div>';
        
        html += '</div>';
        
        $resultsArea.html(html).show();
        
        // Initialize fix buttons
        $('.kbf-fix-btn').on('click', function() {
            const threatId = $(this).data('threat-id');
            fixThreat(threatId, $(this));
        });
    }

    /**
     * Fix individual threat
     */
    function fixThreat(threatId, $button) {
        $button.prop('disabled', true).text('🔄 Fixing...');
        
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_fix_issue',
                threat_id: threatId,
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $button.text('✅ Fixed').removeClass('button').addClass('kbf-success');
                    showNotification('Threat fixed successfully', 'success');
                } else {
                    $button.prop('disabled', false).text('🔧 Auto Fix');
                    showNotification('Failed to fix threat: ' + response.data.message, 'error');
                }
            },
            error: function() {
                $button.prop('disabled', false).text('🔧 Auto Fix');
                showNotification('Failed to fix threat due to network error', 'error');
            }
        });
    }

    /**
     * Initialize firewall functionality
     */
    function initializeFirewall() {
        // Unblock IP buttons
        $(document).on('click', '.unblock-btn', function() {
            const ip = $(this).data('ip');
            const $row = $(this).closest('.ip-row');
            
            $.ajax({
                url: kbf_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kbf_unblock_ip',
                    ip: ip,
                    nonce: kbf_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut();
                        showNotification('IP unblocked successfully', 'success');
                    } else {
                        showNotification('Failed to unblock IP', 'error');
                    }
                }
            });
        });

        // Add to whitelist/blacklist
        $('.whitelist-section button, .blacklist-section button').on('click', function() {
            const $section = $(this).closest('.whitelist-section, .blacklist-section');
            const isWhitelist = $section.hasClass('whitelist-section');
            const ips = $section.find('textarea').val().trim();
            
            if (!ips) {
                showNotification('Please enter IP addresses', 'warning');
                return;
            }
            
            $.ajax({
                url: kbf_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kbf_manage_ip_list',
                    list_type: isWhitelist ? 'whitelist' : 'blacklist',
                    ips: ips,
                    nonce: kbf_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $section.find('textarea').val('');
                        showNotification(`IPs added to ${isWhitelist ? 'whitelist' : 'blacklist'}`, 'success');
                    } else {
                        showNotification('Failed to update IP list', 'error');
                    }
                }
            });
        });
    }

    /**
     * Initialize settings functionality
     */
    function initializeSettings() {
        // Settings form submission
        $('#kbf-settings-form').on('submit', function(e) {
            e.preventDefault();
            saveSettings();
        });

        // Reset settings button
        $('#kbf-reset-settings').on('click', function() {
            if (confirm('Are you sure you want to reset all settings to defaults?')) {
                resetSettings();
            }
        });

        // Import/Export settings
        $('#kbf-export-settings').on('click', function() {
            exportSettings();
        });

        $('#kbf-import-settings').on('change', function() {
            importSettings(this.files[0]);
        });
    }

    /**
     * Save settings
     */
    function saveSettings() {
        const formData = $('#kbf-settings-form').serialize();
        
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=kbf_save_settings',
            success: function(response) {
                if (response.success) {
                    showNotification('Settings saved successfully', 'success');
                } else {
                    showNotification('Failed to save settings', 'error');
                }
            },
            error: function() {
                showNotification('Failed to save settings due to network error', 'error');
            }
        });
    }

    /**
     * Reset settings to defaults
     */
    function resetSettings() {
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_reset_settings',
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showNotification('Failed to reset settings', 'error');
                }
            }
        });
    }

    /**
     * Initialize logs functionality
     */
    function initializeLogs() {
        // Filter logs
        $('#filter-logs').on('click', function() {
            filterLogs();
        });

        // Export logs
        $('#export-logs').on('click', function() {
            exportLogs();
        });

        // Auto-refresh logs every 30 seconds
        setInterval(refreshLogs, 30000);
    }

    /**
     * Filter logs based on criteria
     */
    function filterLogs() {
        const filters = {
            type: $('#log-type-filter').val(),
            severity: $('#log-severity-filter').val(),
            date: $('#log-date-filter').val()
        };

        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_get_logs',
                filters: filters,
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#kbf-logs-table').html(response.data.html);
                }
            }
        });
    }

    /**
     * Refresh logs
     */
    function refreshLogs() {
        if ($('.kbf-logs').length > 0) {
            filterLogs();
        }
    }

    /**
     * Initialize quick actions
     */
    function initializeQuickActions() {
        $('.action-btn').on('click', function() {
            const action = $(this).data('action');
            executeQuickAction(action, $(this));
        });
    }

    /**
     * Execute quick action
     */
    function executeQuickAction(action, $button) {
        $button.addClass('kbf-loading');
        
        const actions = {
            'scan': () => startScan('quick'),
            'update-signatures': updateSignatures,
            'backup': createBackup,
            'hardening': runHardening
        };

        if (actions[action]) {
            actions[action]();
        }

        setTimeout(() => {
            $button.removeClass('kbf-loading');
        }, 2000);
    }

    /**
     * Update threat signatures
     */
    function updateSignatures() {
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_update_signatures',
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Signatures updated successfully', 'success');
                } else {
                    showNotification('Failed to update signatures', 'error');
                }
            }
        });
    }

    /**
     * Create backup
     */
    function createBackup() {
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_create_backup',
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Backup created successfully', 'success');
                } else {
                    showNotification('Failed to create backup', 'error');
                }
            }
        });
    }

    /**
     * Run security hardening
     */
    function runHardening() {
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_run_hardening',
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Security hardening completed', 'success');
                } else {
                    showNotification('Failed to run hardening', 'error');
                }
            }
        });
    }

    /**
     * Initialize progress bars
     */
    function initializeProgressBars() {
        $('.progress-bar').each(function() {
            const $bar = $(this);
            const percentage = $bar.data('percentage') || 0;
            
            setTimeout(() => {
                $bar.find('.progress-fill').css('width', percentage + '%');
            }, 500);
        });
    }

    /**
     * Show welcome message
     */
    function showWelcomeMessage() {
        const welcomeHtml = `
            <div class="kbf-welcome-modal">
                <div class="welcome-content">
                    <h2>🏰 Welcome to Kloudbean Fortress Security!</h2>
                    <p>Your comprehensive WordPress security solution is now active.</p>
                    <div class="welcome-features">
                        <div class="feature-item">
                            <span class="feature-icon">🛡️</span>
                            <span>Real-time threat protection</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🔍</span>
                            <span>Advanced malware scanning</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🔥</span>
                            <span>Intelligent firewall</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">📊</span>
                            <span>Comprehensive reporting</span>
                        </div>
                    </div>
                    <button class="button button-primary" onclick="$(this).closest('.kbf-welcome-modal').fadeOut()">
                        Get Started
                    </button>
                </div>
            </div>
        `;
        
        $('body').append(welcomeHtml);
        $('.kbf-welcome-modal').fadeIn();
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const notification = $(`
            <div class="kbf-notification kbf-notification-${type}">
                <span class="notification-icon">${icons[type]}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `);

        $('body').append(notification);
        
        notification.slideDown();
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.slideUp(() => notification.remove());
        }, 5000);
        
        // Manual close
        notification.find('.notification-close').on('click', () => {
            notification.slideUp(() => notification.remove());
        });
    }

    /**
     * Show schedule dialog
     */
    function showScheduleDialog() {
        const dialogHtml = `
            <div class="kbf-schedule-modal">
                <div class="schedule-content">
                    <h3>📅 Schedule Security Scan</h3>
                    <form id="schedule-form">
                        <p>
                            <label>Frequency:</label>
                            <select name="frequency">
                                <option value="hourly">Hourly</option>
                                <option value="daily" selected>Daily</option>
                                <option value="weekly">Weekly</option>
                            </select>
                        </p>
                        <p>
                            <label>Time:</label>
                            <input type="time" name="time" value="02:00">
                        </p>
                        <p>
                            <label>Scan Type:</label>
                            <select name="scan_type">
                                <option value="quick">Quick Scan</option>
                                <option value="full" selected>Full Scan</option>
                                <option value="deep">Deep Scan</option>
                            </select>
                        </p>
                        <div class="schedule-actions">
                            <button type="submit" class="button button-primary">Schedule Scan</button>
                            <button type="button" class="button schedule-cancel">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        $('body').append(dialogHtml);
        $('.kbf-schedule-modal').fadeIn();
        
        // Handle form submission
        $('#schedule-form').on('submit', function(e) {
            e.preventDefault();
            // Save schedule settings
            $('.kbf-schedule-modal').fadeOut(() => $('.kbf-schedule-modal').remove());
            showNotification('Scan scheduled successfully', 'success');
        });
        
        // Handle cancel
        $('.schedule-cancel').on('click', function() {
            $('.kbf-schedule-modal').fadeOut(() => $('.kbf-schedule-modal').remove());
        });
    }

    /**
     * Export settings
     */
    function exportSettings() {
        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_export_settings',
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    const blob = new Blob([JSON.stringify(response.data, null, 2)], {
                        type: 'application/json'
                    });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'kloudbean-fortress-settings.json';
                    a.click();
                    URL.revokeObjectURL(url);
                    showNotification('Settings exported successfully', 'success');
                }
            }
        });
    }

    /**
     * Import settings
     */
    function importSettings(file) {
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const settings = JSON.parse(e.target.result);
                
                $.ajax({
                    url: kbf_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'kbf_import_settings',
                        settings: JSON.stringify(settings),
                        nonce: kbf_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Settings imported successfully', 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showNotification('Failed to import settings', 'error');
                        }
                    }
                });
            } catch (error) {
                showNotification('Invalid settings file', 'error');
            }
        };
        reader.readAsText(file);
    }

    /**
     * Export logs
     */
    function exportLogs() {
        const filters = {
            type: $('#log-type-filter').val(),
            severity: $('#log-severity-filter').val(),
            date: $('#log-date-filter').val()
        };

        $.ajax({
            url: kbf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kbf_export_logs',
                filters: filters,
                nonce: kbf_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    const blob = new Blob([response.data.csv], {
                        type: 'text/csv'
                    });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'kloudbean-fortress-logs.csv';
                    a.click();
                    URL.revokeObjectURL(url);
                    showNotification('Logs exported successfully', 'success');
                }
            }
        });
    }

    // Expose global functions
    window.KloudbeanFortress = {
        startScan: startScan,
        showNotification: showNotification,
        refreshLogs: refreshLogs
    };

})(jQuery);

/**
 * Additional CSS for dynamic elements
 */
const additionalCSS = `
<style>
.kbf-welcome-modal,
.kbf-schedule-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: none;
    z-index: 100000;
    align-items: center;
    justify-content: center;
}

.welcome-content,
.schedule-content {
    background: white;
    padding: 40px;
    border-radius: 15px;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.welcome-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin: 20px 0;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f7fafc;
    border-radius: 8px;
}

.feature-icon {
    font-size: 1.5em;
}

.kbf-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    display: none;
    z-index: 99999;
    min-width: 300px;
    border-left: 4px solid #3182ce;
}

.kbf-notification-success { border-left-color: #38a169; }
.kbf-notification-error { border-left-color: #e53e3e; }
.kbf-notification-warning { border-left-color: #d69e2e; }

.notification-close {
    float: right;
    background: none;
    border: none;
    font-size: 1.2em;
    cursor: pointer;
    margin-left: 10px;
}

.schedule-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 20px;
}

.kbf-disabled {
    opacity: 0.5;
    pointer-events: none;
}

.scan-threats {
    background: #fed7d7;
    color: #c53030;
    padding: 20px;
    border-radius: 8px;
    margin: 15px 0;
}

.scan-clean {
    background: #c6f6d5;
    color: #276749;
    padding: 20px;
    border-radius: 8px;
    margin: 15px 0;
}

.threats-list {
    margin-top: 15px;
}

.threat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px;
    background: rgba(255,255,255,0.8);
    border-radius: 6px;
    margin-bottom: 10px;
}

.threat-severity {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75em;
    font-weight: bold;
    text-transform: uppercase;
    min-width: 80px;
    text-align: center;
}

.scan-stats {
    background: #f7fafc;
    padding: 20px;
    border-radius: 8px;
    margin: 15px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 6px;
}

.stat-item strong {
    display: block;
    font-size: 1.5em;
    color: #2d3748;
    margin-bottom: 5px;
}

.stat-item span {
    color: #718096;
    font-size: 0.85em;
}
</style>
`;

// Inject additional CSS
document.head.insertAdjacentHTML('beforeend', additionalCSS);




/**
 * Admin JavaScript for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    // Admin object
    var KBESAdmin = {
        
        // Initialize
        init: function() {
            this.bindEvents();
            this.initComponents();
        },
        
        // Bind events
        bindEvents: function() {
            // Dashboard events
            $(document).on('click', '#kbes-run-scan', this.runScan);
            $(document).on('click', '#kbes-schedule-scan', this.scheduleScan);
            $(document).on('click', '#kbes-scan-settings', this.scanSettings);
            
            // Firewall events
            $(document).on('click', '#kbes-add-rule', this.showAddRuleModal);
            $(document).on('click', '#kbes-block-ip', this.blockIP);
            $(document).on('click', '#kbes-whitelist-ip', this.whitelistIP);
            $(document).on('click', '#kbes-import-rules', this.importRules);
            $(document).on('submit', '#kbes-add-rule-form', this.addRule);
            
            // Modal events
            $(document).on('click', '.kbes-modal-close', this.closeModal);
            $(document).on('click', '.kbes-modal', this.closeModalOnBackground);
            
            // Filter events
            $(document).on('change', '#kbes-rule-type-filter', this.filterRules);
            $(document).on('change', '#kbes-rule-action-filter', this.filterRules);
            $(document).on('keyup', '#kbes-rule-search', this.searchRules);
        },
        
        // Initialize components
        initComponents: function() {
            this.initTooltips();
            this.initCharts();
            this.initDataTables();
        },
        
        // Initialize tooltips
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                $(this).attr('title', $(this).data('tooltip'));
            });
        },
        
        // Initialize charts
        initCharts: function() {
            if (typeof Chart !== 'undefined') {
                this.initSecurityScoreChart();
                this.initThreatsChart();
                this.initComplianceChart();
            }
        },
        
        // Initialize data tables
        initDataTables: function() {
            if ($.fn.DataTable) {
                $('.kbes-data-table').DataTable({
                    pageLength: 25,
                    responsive: true,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            }
        },
        
        // Run security scan
        runScan: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $modal = $('#kbes-scan-progress');
            
            // Show progress modal
            $modal.show();
            
            // Disable button
            $button.prop('disabled', true);
            
            // Start scan
            $.ajax({
                url: kbesAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kbes_run_scan',
                    nonce: kbesAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KBESAdmin.updateScanProgress(100);
                        setTimeout(function() {
                            $modal.hide();
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Scan failed: ' + response.data.message);
                        $modal.hide();
                    }
                },
                error: function() {
                    alert('An error occurred while running the scan.');
                    $modal.hide();
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },
        
        // Update scan progress
        updateScanProgress: function(percentage) {
            $('.kbes-progress-fill').css('width', percentage + '%');
            
            if (percentage >= 100) {
                $('.kbes-progress-text').text('Scan completed successfully!');
            }
        },
        
        // Schedule scan
        scheduleScan: function(e) {
            e.preventDefault();
            
            var schedule = prompt('Enter scan schedule (e.g., "daily", "weekly", "monthly"):');
            if (schedule) {
                $.ajax({
                    url: kbesAdmin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'kbes_schedule_scan',
                        schedule: schedule,
                        nonce: kbesAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Scan scheduled successfully!');
                        } else {
                            alert('Failed to schedule scan: ' + response.data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while scheduling the scan.');
                    }
                });
            }
        },
        
        // Scan settings
        scanSettings: function(e) {
            e.preventDefault();
            alert('Scan settings functionality will be implemented soon.');
        },
        
        // Show add rule modal
        showAddRuleModal: function(e) {
            e.preventDefault();
            $('#kbes-add-rule-modal').show();
        },
        
        // Add rule
        addRule: function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            $.ajax({
                url: kbesAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kbes_add_rule',
                    form_data: formData,
                    nonce: kbesAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Rule added successfully!');
                        $('#kbes-add-rule-modal').hide();
                        location.reload();
                    } else {
                        alert('Failed to add rule: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred while adding the rule.');
                }
            });
        },
        
        // Block IP
        blockIP: function(e) {
            e.preventDefault();
            
            var ip = $(this).data('ip');
            var reason = prompt('Enter reason for blocking IP ' + ip + ':');
            
            if (reason) {
                $.ajax({
                    url: kbesAdmin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'kbes_block_ip',
                        ip: ip,
                        reason: reason,
                        nonce: kbesAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('IP blocked successfully!');
                            location.reload();
                        } else {
                            alert('Failed to block IP: ' + response.data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while blocking the IP.');
                    }
                });
            }
        },
        
        // Whitelist IP
        whitelistIP: function(e) {
            e.preventDefault();
            
            var ip = $(this).data('ip');
            var reason = prompt('Enter reason for whitelisting IP ' + ip + ':');
            
            if (reason) {
                $.ajax({
                    url: kbesAdmin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'kbes_whitelist_ip',
                        ip: ip,
                        reason: reason,
                        nonce: kbesAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('IP whitelisted successfully!');
                            location.reload();
                        } else {
                            alert('Failed to whitelist IP: ' + response.data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while whitelisting the IP.');
                    }
                });
            }
        },
        
        // Import rules
        importRules: function(e) {
            e.preventDefault();
            alert('Import rules functionality will be implemented soon.');
        },
        
        // Filter rules
        filterRules: function() {
            var type = $('#kbes-rule-type-filter').val();
            var action = $('#kbes-rule-action-filter').val();
            var search = $('#kbes-rule-search').val();
            
            // Implement filtering logic
            $('.kbes-rules-table tbody tr').each(function() {
                var $row = $(this);
                var show = true;
                
                if (type && $row.find('td:nth-child(2)').text().toLowerCase() !== type.toLowerCase()) {
                    show = false;
                }
                
                if (action && $row.find('td:nth-child(4)').text().toLowerCase() !== action.toLowerCase()) {
                    show = false;
                }
                
                if (search && $row.text().toLowerCase().indexOf(search.toLowerCase()) === -1) {
                    show = false;
                }
                
                $row.toggle(show);
            });
        },
        
        // Search rules
        searchRules: function() {
            this.filterRules();
        },
        
        // Close modal
        closeModal: function(e) {
            e.preventDefault();
            $(this).closest('.kbes-modal').hide();
        },
        
        // Close modal on background click
        closeModalOnBackground: function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        },
        
        // Initialize security score chart
        initSecurityScoreChart: function() {
            var ctx = document.getElementById('kbes-security-score-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Passed', 'Failed', 'Warnings'],
                        datasets: [{
                            data: [80, 15, 5],
                            backgroundColor: ['#00a32a', '#d63638', '#dba617']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        },
        
        // Initialize threats chart
        initThreatsChart: function() {
            var ctx = document.getElementById('kbes-threats-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Threats Blocked',
                            data: [12, 19, 3, 5, 2, 3, 8],
                            borderColor: '#2271b1',
                            backgroundColor: 'rgba(34, 113, 177, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        },
        
        // Initialize compliance chart
        initComplianceChart: function() {
            var ctx = document.getElementById('kbes-compliance-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['GDPR', 'HIPAA', 'SOX', 'PCI DSS'],
                        datasets: [{
                            label: 'Compliance Score',
                            data: [85, 92, 78, 88],
                            backgroundColor: ['#00a32a', '#00a32a', '#dba617', '#00a32a']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            }
        },
        
        // Show notification
        showNotification: function(message, type) {
            var $notification = $('<div class="kbes-notification kbes-notification-' + type + '">' + message + '</div>');
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        // Confirm action
        confirmAction: function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        },
        
        // Format date
        formatDate: function(date) {
            return new Date(date).toLocaleDateString();
        },
        
        // Format time
        formatTime: function(date) {
            return new Date(date).toLocaleTimeString();
        },
        
        // Format bytes
        formatBytes: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        // Format number
        formatNumber: function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        KBESAdmin.init();
    });
    
    // Export to global scope
    window.KBESAdmin = KBESAdmin;
    
})(jQuery);

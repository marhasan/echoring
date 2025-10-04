<?php
/**
 * Improved template for the update management admin page
 * Uses WordPress WP_List_Table for better UX and standards compliance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the list table class
require_once plugin_dir_path(__FILE__) . '../includes/class-sites-list-table.php';

// Create an instance of our list table
$sites_table = new EchoRing_Sites_List_Table();
$sites_table->prepare_items();

$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    'all' => 'All Sites',
    'new' => 'New Sites', 
    'updated' => 'Updated Sites',
    'pending' => 'Pending Updates'
);

$descriptions = array(
    'all' => 'Showing all sites in the system including pending approval sites.',
    'new' => 'Showing sites marked as new sites. These will display the "NEW" badge.',
    'updated' => 'Showing sites that have been marked as updated.',
    'pending' => 'Showing sites with pending webmaster update requests.'
);
?>

<div class="wrap">
    <h1 class="wp-heading-inline">EchoRing Sites Update Management</h1>
    <a href="<?php echo admin_url('post-new.php?post_type=site'); ?>" class="page-title-action">Add New Site</a>
    <hr class="wp-header-end">
    
    <p class="description"><?php echo esc_html($descriptions[$current_tab]); ?></p>
    
    <!-- Tab Navigation -->
    <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
        <?php foreach ($tabs as $tab_key => $tab_label): ?>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=site&page=echoring-sites-updates&tab=' . $tab_key)); ?>" 
               class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>"
               <?php echo $current_tab === $tab_key ? 'aria-current="page"' : ''; ?>>
                <?php echo esc_html($tab_label); ?>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <!-- List Table Form -->
    <form id="sites-filter" method="post">
        <?php $sites_table->display(); ?>
    </form>
    
    <!-- Help Section -->
    <div class="notice notice-info">
        <h3>How it works:</h3>
        <ul>
            <li><strong>Pending Updates:</strong> Webmasters can submit update requests that appear here with a "PENDING" badge</li>
            <li><strong>Approve/Reject:</strong> Use "View Request" to review and approve or reject pending updates</li>
            <li><strong>Mark Updated:</strong> Once approved, sites are automatically marked as updated</li>
            <li><strong>Bulk Operations:</strong> Select multiple sites and update them all at once using the bulk actions dropdown</li>
            <li><strong>Sorting:</strong> Click column headers to sort by that field</li>
            <li><strong>Pagination:</strong> Use the pagination controls at the bottom to navigate through large lists</li>
        </ul>
    </div>
</div>

<!-- Modal for viewing pending updates -->
<div id="pending-update-modal" class="hidden">
    <div class="pending-update-details">
        <h4>Pending Update Request</h4>
        <div id="pending-update-content"></div>
        <div class="pending-update-actions">
            <button type="button" class="button button-primary" id="approve-update">Approve Update</button>
            <button type="button" class="button button-secondary" id="reject-update">Reject Update</button>
            <button type="button" class="button" id="close-modal">Close</button>
        </div>
    </div>
</div>

<style>
/* Enhanced styling for better WordPress integration */
.nav-tab-wrapper {
    margin-bottom: 20px;
}

.new-badge, .pending-badge, .approval-badge {
    background: #007cba;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 5px;
}

.pending-badge {
    background: #f39c12;
}

.approval-badge {
    background: #d63638;
}

.updated {
    color: #46b450;
    font-weight: 600;
}

.new-site {
    color: #007cba;
    font-weight: 600;
}

.pending-approval {
    color: #d63638;
    font-weight: 600;
}

.no-pending {
    color: #999;
}

/* Modal styling */
#pending-update-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

#pending-update-modal.hidden {
    display: none;
}

.pending-update-details {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px;
    border-radius: 4px;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
}

.pending-update-details h4 {
    margin-top: 0;
    color: #23282d;
    font-size: 18px;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.pending-update-details p {
    margin: 10px 0;
}

.pending-update-details strong {
    color: #23282d;
}

.pending-update-details table {
    margin: 15px 0;
    border: 1px solid #ccd0d4;
    width: 100%;
}

.pending-update-details table th {
    background: #f0f0f1;
    font-weight: 600;
    padding: 8px 10px;
}

.pending-update-details table td {
    padding: 8px 10px;
    border-top: 1px solid #eee;
}

.pending-update-details .highlight-change {
    background-color: #fff3cd;
    font-weight: bold;
}

.pending-update-actions {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    text-align: right;
}

.pending-update-actions .button {
    margin-left: 10px;
}

/* Responsive improvements */
@media screen and (max-width: 782px) {
    .pending-update-details {
        margin: 10px;
        padding: 15px;
    }
    
    .pending-update-actions {
        text-align: center;
    }
    
    .pending-update-actions .button {
        margin: 5px;
        display: block;
        width: 100%;
    }
}

/* Table enhancements */
.wp-list-table .column-actions {
    width: 200px;
}

.wp-list-table .column-status {
    width: 120px;
}

.wp-list-table .column-pending_update {
    width: 120px;
}

.wp-list-table .column-last_updated {
    width: 140px;
}

/* Button spacing in actions column */
.column-actions .button {
    margin-right: 5px;
    margin-bottom: 3px;
}
.compact-actions {
    white-space: nowrap;
    display: inline-block;
}
.compact-actions .compact-btn {
    padding: 0 6px !important;
    min-height: 24px !important;
    line-height: 22px !important;
    font-size: 12px !important;
    margin-right: 2px !important;
    display: inline-block;
    width: auto !important;
}
.compact-actions .compact-btn:last-child {
    margin-right: 0 !important;
}
.wp-list-table .column-actions {
    width: 120px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Enhanced AJAX handling with better error management
    function performAjaxAction(action, siteId, successCallback) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: action,
                site_id: siteId,
                nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
            },
            beforeSend: function() {
                // Show loading state
                $('button[data-site-id="' + siteId + '"]').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    if (successCallback) {
                        successCallback(response);
                    } else {
                        // Default: reload page
                        window.location.reload();
                    }
                } else {
                    alert('Error: ' + (response.data || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error processing request: ' + error);
            },
            complete: function() {
                // Re-enable buttons
                $('button[data-site-id="' + siteId + '"]').prop('disabled', false);
            }
        });
    }
    
    // Mark as updated
    $(document).on('click', '.mark-updated', function() {
        var siteId = $(this).data('site-id');
        var $button = $(this);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mark_site_updated',
                site_id: siteId,
                nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
            },
            beforeSend: function() {
                $button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $button.replaceWith('<button type="button" class="button button-secondary reset-updates compact-btn" data-site-id="' + siteId + '" title="Reset Updates">↻</button>');
                } else {
                    alert('Error: ' + (response.data || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error processing request: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Reset updates
    $(document).on('click', '.reset-updates', function() {
        var siteId = $(this).data('site-id');
        var $button = $(this);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'reset_site_updates',
                site_id: siteId,
                nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
            },
            beforeSend: function() {
                $button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $button.replaceWith('<button type="button" class="button button-primary mark-updated compact-btn" data-site-id="' + siteId + '" title="Mark as Updated">✓</button>');
                } else {
                    alert('Error: ' + (response.data || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error processing request: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Mark as new
    $(document).on('click', '.mark-new', function() {
        var siteId = $(this).data('site-id');
        var $button = $(this);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mark_as_new_site',
                site_id: siteId,
                nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
            },
            beforeSend: function() {
                $button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $button.replaceWith('<button type="button" class="button button-secondary reset-new compact-btn" data-site-id="' + siteId + '" title="Reset New">✕</button>');
                } else {
                    alert('Error: ' + (response.data || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error processing request: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Reset new status
    $(document).on('click', '.reset-new', function() {
        var siteId = $(this).data('site-id');
        var $button = $(this);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'reset_new_site_status',
                site_id: siteId,
                nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
            },
            beforeSend: function() {
                $button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $button.replaceWith('<button type="button" class="button button-primary mark-new compact-btn" data-site-id="' + siteId + '" title="Mark as New">★</button>');
                } else {
                    alert('Error: ' + (response.data || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error processing request: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // View pending update
    $(document).on('click', '.view-pending', function() {
        var siteId = $(this).data('site-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_pending_update',
                site_id: siteId,
                nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#pending-update-content').html(response.data.html);
                    $('#pending-update-modal').removeClass('hidden');
                    $('#approve-update, #reject-update').data('site-id', siteId);
                } else {
                    alert('Error loading pending update: ' + response.data);
                }
            },
            error: function() {
                alert('Error loading pending update.');
            }
        });
    });
    
    // Close modal
    $('#close-modal, #pending-update-modal').on('click', function(e) {
        if (e.target === this) {
            $('#pending-update-modal').addClass('hidden');
        }
    });
    
    // Approve update
    $('#approve-update').on('click', function() {
        var siteId = $(this).data('site-id');
        if (confirm('Are you sure you want to approve this update?')) {
            performAjaxAction('approve_pending_update', siteId, function() {
                $('#pending-update-modal').addClass('hidden');
                window.location.reload();
            });
        }
    });
    
    // Reject update
    $('#reject-update').on('click', function() {
        var siteId = $(this).data('site-id');
        if (confirm('Are you sure you want to reject this update?')) {
            performAjaxAction('reject_pending_update', siteId, function() {
                $('#pending-update-modal').addClass('hidden');
                window.location.reload();
            });
        }
    });
    
    // Approve site submission
    $(document).on('click', '.approve-site', function() {
        var siteId = $(this).data('site-id');
        var $button = $(this);
        
        if (confirm('Are you sure you want to approve this site submission?')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'approve_site_submission',
                    site_id: siteId,
                    nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
                },
                beforeSend: function() {
                    $button.prop('disabled', true).text('Approving...');
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.data || 'Unknown error occurred'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error processing request: ' + error);
                },
                complete: function() {
                    $button.prop('disabled', false).text('✓ Approve');
                }
            });
        }
    });
    
    // Reject site submission
    $(document).on('click', '.reject-site', function() {
        var siteId = $(this).data('site-id');
        var $button = $(this);
        
        if (confirm('Are you sure you want to reject this site submission?')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'reject_site_submission',
                    site_id: siteId,
                    nonce: '<?php echo wp_create_nonce("echoring_sites_nonce"); ?>'
                },
                beforeSend: function() {
                    $button.prop('disabled', true).text('Rejecting...');
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.data || 'Unknown error occurred'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error processing request: ' + error);
                },
                complete: function() {
                    $button.prop('disabled', false).text('✗ Reject');
                }
            });
        }
    });

    // Escape key closes modal
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            $('#pending-update-modal').addClass('hidden');
        }
    });
});
</script>
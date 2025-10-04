<?php
/**
 * Sites List Table using WordPress WP_List_Table
 * This provides a more standard WordPress admin experience
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load WP_List_Table if not already loaded
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class EchoRing_Sites_List_Table extends WP_List_Table {
    
    private $current_tab;
    
    public function __construct($args = array()) {
        parent::__construct(array(
            'singular' => 'site',
            'plural' => 'sites',
            'ajax' => false
        ));
        
        $this->current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
    }
    
    /**
     * Define the columns for the table
     */
    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Site Name',
            'webmaster' => 'Webmaster',
            'last_updated' => 'Last Updated',
            'status' => 'Status',
            'actions' => 'Actions'
        );
    }
    
    /**
     * Define sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'title' => array('title', false),
            'webmaster' => array('webmaster', false),
            'last_updated' => array('last_updated', false)
        );
    }
    
    /**
     * Define bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'approve_sites' => 'Approve Sites',
            'reject_sites' => 'Reject Sites',
            'mark_updated' => 'Mark as Updated',
            'reset_updates' => 'Reset Updates',
            'mark_new' => 'Mark as New',
            'reset_new' => 'Reset New Status'
        );
    }
    
    /**
     * Process bulk actions
     */
    public function process_bulk_action() {
        $action = $this->current_action();
        
        if (!$action) {
            return;
        }
        
        $site_ids = isset($_POST['site']) ? array_map('intval', $_POST['site']) : array();
        
        if (empty($site_ids)) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['_wpnonce'], 'bulk-sites')) {
            wp_die('Security check failed');
        }
        
        foreach ($site_ids as $site_id) {
            switch ($action) {
                case 'approve_sites':
                    // Approve site submission
                    wp_update_post(array(
                        'ID' => $site_id,
                        'post_status' => 'publish'
                    ));
                    update_post_meta($site_id, '_is_new_site', '1');
                    update_post_meta($site_id, '_approval_date', current_time('mysql'));
                    delete_post_meta($site_id, '_is_rejected');
                    break;
                case 'reject_sites':
                    // Reject site submission
                    wp_update_post(array(
                        'ID' => $site_id,
                        'post_status' => 'draft'
                    ));
                    update_post_meta($site_id, '_is_rejected', '1');
                    update_post_meta($site_id, '_rejection_date', current_time('mysql'));
                    delete_post_meta($site_id, '_is_new_site');
                    break;
                case 'mark_updated':
                    EchoRingSites::mark_site_updated($site_id);
                    break;
                case 'reset_updates':
                    EchoRingSites::reset_site_updates($site_id);
                    break;
                case 'mark_new':
                    update_post_meta($site_id, '_is_new_site', '1');
                    break;
                case 'reset_new':
                    delete_post_meta($site_id, '_is_new_site');
                    break;
            }
        }
        
        // Redirect to avoid resubmission
        $redirect_url = remove_query_arg(array('action', 'action2', 'site', '_wpnonce'), $_SERVER['REQUEST_URI']);
        wp_redirect($redirect_url);
        exit;
    }
    
    /**
     * Prepare the items for the table
     */
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Process bulk actions
        $this->process_bulk_action();
        
        // Get data
        $data = $this->get_sites_data();
        
        // Pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
        
        $this->items = array_slice($data, (($current_page - 1) * $per_page), $per_page);
    }
    
    /**
     * Get sites data based on current tab
     */
    private function get_sites_data() {
        $query_args = array(
            'post_type' => 'site',
            'posts_per_page' => -1,
            'post_status' => array('publish', 'pending'),
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        // Add tab-specific filtering
        switch ($this->current_tab) {
            case 'new':
                $query_args['meta_query'] = array(
                    array(
                        'key' => '_is_new_site',
                        'value' => '1',
                        'compare' => '='
                    )
                );
                break;
            case 'updated':
                $query_args['meta_query'] = array(
                    array(
                        'key' => '_is_updated',
                        'value' => '1',
                        'compare' => '='
                    )
                );
                break;
            case 'pending':
                $query_args['meta_query'] = array(
                    array(
                        'key' => '_has_pending_update',
                        'compare' => 'EXISTS'
                    )
                );
                break;
        }
        
        $sites = get_posts($query_args);
        $data = array();
        
        foreach ($sites as $site) {
            $data[] = array(
                'ID' => $site->ID,
                'title' => $site->post_title,
                'webmaster' => EchoRingSites::get_webmaster($site->ID),
                'last_updated' => EchoRingSites::get_last_updated($site->ID),
                'status' => $this->get_site_status($site),
                'post_status' => $site->post_status
            );
        }
        
        return $data;
    }
    
    /**
     * Get site status information
     */
    private function get_site_status($site) {
        $is_updated = EchoRingSites::is_site_updated($site->ID);
        $is_new_site = get_post_meta($site->ID, '_is_new_site', true);
        $has_pending_update = get_post_meta($site->ID, '_has_pending_update', true);
        $pub_date = get_the_time('Y-m-d', $site->ID);
        $today = current_time('Y-m-d');
        
        if ($site->post_status === 'pending') {
            return array('status' => 'Pending Approval', 'class' => 'pending-approval');
        } elseif ($has_pending_update) {
            return array('status' => 'Pending Update', 'class' => 'pending-update');
        } elseif ($is_new_site && $pub_date === $today) {
            return array('status' => 'New Site', 'class' => 'new-site');
        } elseif ($is_updated) {
            return array('status' => 'Updated', 'class' => 'updated');
        } else {
            return array('status' => 'Normal', 'class' => '');
        }
    }
    
    /**
     * Render the checkbox column
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="site[]" value="%s" />',
            $item['ID']
        );
    }
    
    /**
     * Render the title column
     */
    public function column_title($item) {
        $actions = array(
            'edit' => sprintf('<a href="%s">Edit</a>', get_edit_post_link($item['ID'])),
            'view' => sprintf('<a href="%s">View</a>', get_permalink($item['ID']))
        );
        
        $title = '<strong>' . esc_html($item['title']) . '</strong>';
        
        return $title . $this->row_actions($actions);
    }
    
    /**
     * Render the webmaster column
     */
    public function column_webmaster($item) {
        return esc_html($item['webmaster']);
    }
    
    /**
     * Render the last updated column
     */
    public function column_last_updated($item) {
        return $item['last_updated'] ? date('Y-m-d H:i', strtotime($item['last_updated'])) : 'Never';
    }
    
    /**
     * Render the status column
     */
    public function column_status($item) {
        $status_info = $item['status'];
        return sprintf(
            '<span class="%s">%s</span>',
            esc_attr($status_info['class']),
            esc_html($status_info['status'])
        );
    }
    
    /**
     * Render the pending update column
     */
    /**
     * Render the actions column
     */
    public function column_actions($item) {
        $actions = array();
        
        $is_updated = EchoRingSites::is_site_updated($item['ID']);
        $has_pending_update = get_post_meta($item['ID'], '_has_pending_update', true);
        $is_new_site = get_post_meta($item['ID'], '_is_new_site', true);
        $pub_date = get_the_time('Y-m-d', $item['ID']);
        $today = current_time('Y-m-d');
        
        // Approval buttons for pending sites
        if ($item['post_status'] === 'pending') {
            $actions[] = sprintf(
                '<button type="button" class="button button-primary approve-site compact-btn" data-site-id="%d" title="Approve Site">✓ Approve</button>',
                $item['ID']
            );
            $actions[] = sprintf(
                '<button type="button" class="button button-secondary reject-site compact-btn" data-site-id="%d" title="Reject Site">✗ Reject</button>',
                $item['ID']
            );
        } else {
            // View Request button for sites with pending updates
            if ($has_pending_update) {
                $actions[] = sprintf(
                    '<button type="button" class="button button-primary view-pending compact-btn" data-site-id="%d" title="View Pending Update">👁 View Request</button>',
                    $item['ID']
                );
            }
            
            // Update/Reset buttons for approved sites
            if (!$is_updated || $has_pending_update) {
                $actions[] = sprintf(
                    '<button type="button" class="button button-primary mark-updated compact-btn" data-site-id="%d" title="Mark as Updated">✓</button>',
                    $item['ID']
                );
            } else {
                $actions[] = sprintf(
                    '<button type="button" class="button button-secondary reset-updates compact-btn" data-site-id="%d" title="Reset Updates">↻</button>',
                    $item['ID']
                );
            }
            
            // New site buttons for approved sites
            if (!$is_new_site) {
                $actions[] = sprintf(
                    '<button type="button" class="button button-primary mark-new compact-btn" data-site-id="%d" title="Mark as New">★</button>',
                    $item['ID']
                );
            } else {
                $actions[] = sprintf(
                    '<button type="button" class="button button-secondary reset-new compact-btn" data-site-id="%d" title="Reset New">✕</button>',
                    $item['ID']
                );
            }
        }
        
        // Add CSS for compact buttons
        if (!has_action('admin_footer', 'echoring_compact_buttons_css')) {
            add_action('admin_footer', 'echoring_compact_buttons_css');
        }
        
        return '<div class="compact-actions">' . implode('', $actions) . '</div>';
    }
    
    /**
     * Display when no items are found
     */
    public function no_items() {
        $messages = array(
            'all' => 'No sites found. Please add a site first.',
            'new' => 'No sites are currently marked as new sites.',
            'updated' => 'No sites are currently marked as updated.',
            'pending' => 'No sites have pending update requests.'
        );
        
        echo esc_html($messages[$this->current_tab] ?? $messages['all']);
    }
}

if (!function_exists('echoring_compact_buttons_css')) {
    function echoring_compact_buttons_css() {
        ?>
        <style>
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
            width: 160px;
        }
        .compact-actions .approve-site,
        .compact-actions .reject-site {
            min-width: 70px !important;
            margin-bottom: 2px !important;
        }
        </style>
        <?php
    }
}
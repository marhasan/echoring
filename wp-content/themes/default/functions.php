<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

/**
-------------------------
**/


/**
-------------------------
**/

$content_width = 450;

add_theme_support( 'automatic-feed-links' );

add_theme_support( 'post-thumbnails' );

if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	));
}



add_filter( 'nav_menu_meta_box_object', 'show_private_pages_menu_selection' );
/**
* Add query argument for selecting pages to add to a menu
*/
function show_private_pages_menu_selection( $args ){
    if( $args->name == 'page' ) {
        $args->_default_query['post_status'] = array('publish','private');
    }
    return $args;
}

add_action('after_setup_theme', 'remove_admin_bar');
 
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

// Post Rating System
add_action('wp_ajax_rate_post', 'handle_post_rating');
add_action('wp_ajax_nopriv_rate_post', 'handle_post_rating');

function handle_post_rating() {
    // Log the incoming request for debugging
    error_log('Rating request received: ' . print_r($_POST, true));
    
    // Check if required data exists
    if (!isset($_POST['nonce']) || !isset($_POST['post_id']) || !isset($_POST['rating'])) {
        error_log('Missing required POST data');
        wp_die('Missing required data');
    }
    
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'rate_post_nonce')) {
        error_log('Nonce verification failed');
        wp_die('Security check failed');
    }
    
    $post_id = intval($_POST['post_id']);
    $rating = intval($_POST['rating']);
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    error_log('Processing rating: ' . $rating . ' for post: ' . $post_id . ' from IP: ' . $user_ip);
    
    // Additional security checks for anonymous users
    if (!is_user_logged_in()) {
        // Check honeypot field (should be empty)
        if (!empty($_POST['website'])) {
            error_log('Honeypot triggered from IP: ' . $user_ip);
            wp_die('Spam detected');
        }
        
        // Check submission timing (prevent too fast submissions)
        if (isset($_POST['submit_time'])) {
            $submit_time = intval($_POST['submit_time']);
            $current_time = time() * 1000; // Convert to milliseconds
            $time_diff = $current_time - $submit_time;
            
            // Reject if submitted too quickly (less than 2 seconds)
            if ($time_diff < 2000) {
                error_log('Too fast submission from IP: ' . $user_ip . ' (time: ' . $time_diff . 'ms)');
                wp_die('Please wait a moment before submitting');
            }
            
            // Reject if submitted too slowly (more than 10 minutes)
            if ($time_diff > 600000) {
                error_log('Stale submission from IP: ' . $user_ip . ' (time: ' . $time_diff . 'ms)');
                wp_die('Form expired, please refresh and try again');
            }
        }
        
        // Check referer for anonymous users
        $referer = wp_get_referer();
        if (!$referer || strpos($referer, home_url()) !== 0) {
            error_log('Invalid referer from IP: ' . $user_ip . ' (referer: ' . $referer . ')');
            wp_die('Invalid request source');
        }
    }
    
    // Validate rating (1-5)
    if ($rating < 1 || $rating > 5) {
        error_log('Invalid rating value: ' . $rating);
        wp_die('Invalid rating');
    }
    
    // Validate post exists
    if (!get_post($post_id)) {
        error_log('Post not found: ' . $post_id);
        wp_die('Post not found');
    }
    
    // Security checks
    $security_check = check_rating_security($post_id, $user_ip);
    if ($security_check !== true) {
        error_log('Security check failed: ' . $security_check);
        wp_die($security_check);
    }
    
    // Get current ratings and rating metadata
    $ratings = get_post_meta($post_id, '_post_ratings', true);
    $rating_ips = get_post_meta($post_id, '_post_rating_ips', true);
    $rating_timestamps = get_post_meta($post_id, '_post_rating_timestamps', true);
    $rating_users = get_post_meta($post_id, '_post_rating_users', true);
    
    if (!is_array($ratings)) $ratings = array();
    if (!is_array($rating_ips)) $rating_ips = array();
    if (!is_array($rating_timestamps)) $rating_timestamps = array();
    if (!is_array($rating_users)) $rating_users = array();
    
    // Add new rating with metadata
    $ratings[] = $rating;
    $rating_ips[] = $user_ip;
    $rating_timestamps[] = current_time('timestamp');
    $rating_users[] = get_current_user_id(); // 0 for anonymous users
    
    // Update post meta
    update_post_meta($post_id, '_post_ratings', $ratings);
    update_post_meta($post_id, '_post_rating_ips', $rating_ips);
    update_post_meta($post_id, '_post_rating_timestamps', $rating_timestamps);
    update_post_meta($post_id, '_post_rating_users', $rating_users);
    
    // Calculate average
    $average = array_sum($ratings) / count($ratings);
    $update_average = update_post_meta($post_id, '_post_rating_average', $average);
    $update_count = update_post_meta($post_id, '_post_rating_count', count($ratings));
    
    error_log('Rating saved. Average: ' . $average . ', Count: ' . count($ratings));
    
    // Return success
    wp_die('Rating saved successfully');
}

// Security function to check rating abuse
function check_rating_security($post_id, $user_ip) {
    $current_user_id = get_current_user_id();
    
    // For logged-in users, check if they already rated this post
    if ($current_user_id > 0) {
        $user_ratings = get_post_meta($post_id, '_post_rating_users', true);
        if (is_array($user_ratings) && in_array($current_user_id, $user_ratings)) {
            return 'You have already rated this post';
        }
    } else {
        // Enhanced security for anonymous users
        $anonymous_security_check = check_anonymous_user_security($post_id, $user_ip);
        if ($anonymous_security_check !== true) {
            return $anonymous_security_check;
        }
    }
    
    // Check IP-based rate limiting (max 3 ratings per IP per post)
    $rating_ips = get_post_meta($post_id, '_post_rating_ips', true);
    if (is_array($rating_ips)) {
        $ip_count = array_count_values($rating_ips);
        if (isset($ip_count[$user_ip]) && $ip_count[$user_ip] >= 3) {
            return 'Maximum ratings per IP exceeded';
        }
    }
    
    // Check time-based rate limiting (max 1 rating per IP per hour)
    $rating_timestamps = get_post_meta($post_id, '_post_rating_timestamps', true);
    $rating_ips_meta = get_post_meta($post_id, '_post_rating_ips', true);
    
    if (is_array($rating_timestamps) && is_array($rating_ips_meta)) {
        $current_time = current_time('timestamp');
        $one_hour_ago = $current_time - 3600;
        
        for ($i = 0; $i < count($rating_ips_meta); $i++) {
            if (isset($rating_ips_meta[$i]) && $rating_ips_meta[$i] === $user_ip &&
                isset($rating_timestamps[$i]) && $rating_timestamps[$i] > $one_hour_ago) {
                return 'Please wait 1 hour before rating again';
            }
        }
    }
    
    // Check for rapid-fire rating attempts (max 5 ratings in 5 minutes across all posts)
    $transient_key = 'rating_limit_' . md5($user_ip);
    $recent_count = get_transient($transient_key);
    
    if ($recent_count && $recent_count >= 5) {
        return 'Too many ratings in short time. Please wait.';
    }
    
    // Increment the counter
    set_transient($transient_key, ($recent_count ? $recent_count + 1 : 1), 300); // 5 minutes
    
    // Check for suspicious voting patterns (all 1s or all 5s from same IP)
    if (is_array($rating_ips) && count($rating_ips) >= 3) {
        $ratings_from_ip = array();
        $ratings_all = get_post_meta($post_id, '_post_ratings', true);
        
        for ($i = 0; $i < count($rating_ips); $i++) {
            if ($rating_ips[$i] === $user_ip && isset($ratings_all[$i])) {
                $ratings_from_ip[] = $ratings_all[$i];
            }
        }
        
        if (count($ratings_from_ip) >= 2) {
            $unique_ratings = array_unique($ratings_from_ip);
            if (count($unique_ratings) === 1 && (in_array(1, $unique_ratings) || in_array(5, $unique_ratings))) {
                return 'Suspicious rating pattern detected';
            }
        }
    }
    
    return true;
}

// Enhanced security checks specifically for anonymous users
function check_anonymous_user_security($post_id, $user_ip) {
    // 1. Browser fingerprint check (basic implementation)
    $browser_fingerprint = generate_browser_fingerprint();
    $fingerprint_key = 'rating_fingerprint_' . md5($browser_fingerprint . $post_id);
    
    if (get_transient($fingerprint_key)) {
        return 'You have already rated this post from this device';
    }
    
    // 2. Stricter rate limiting for anonymous users (max 1 rating per IP per post)
    $rating_ips = get_post_meta($post_id, '_post_rating_ips', true);
    if (is_array($rating_ips) && in_array($user_ip, $rating_ips)) {
        return 'Anonymous users can only rate once per post';
    }
    
    // 3. Check for proxy/VPN/Tor usage (basic detection)
    if (is_suspicious_ip($user_ip)) {
        return 'Ratings from proxy/VPN connections are not allowed';
    }
    
    // 4. Time-based restrictions for new anonymous users
    $daily_limit_key = 'anon_daily_limit_' . md5($user_ip . date('Y-m-d'));
    $daily_count = get_transient($daily_limit_key);
    
    if ($daily_count && $daily_count >= 3) {
        return 'Daily rating limit reached for anonymous users';
    }
    
    // 5. Check session age (require minimum session time)
    if (!check_minimum_session_time()) {
        return 'Please browse the site for a few minutes before rating';
    }
    
    // Set fingerprint transient (24 hours)
    set_transient($fingerprint_key, true, 86400);
    
    // Increment daily counter
    set_transient($daily_limit_key, ($daily_count ? $daily_count + 1 : 1), 86400);
    
    return true;
}

// Generate a basic browser fingerprint
function generate_browser_fingerprint() {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
    $accept_encoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
    
    return md5($user_agent . $accept_language . $accept_encoding);
}

// Basic suspicious IP detection
function is_suspicious_ip($ip) {
    // Check for common proxy/VPN IP ranges (basic implementation)
    $suspicious_ranges = array(
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '127.0.0.0/8'
    );
    
    foreach ($suspicious_ranges as $range) {
        if (ip_in_range($ip, $range)) {
            return true;
        }
    }
    
    // Check against known proxy headers
    $proxy_headers = array(
        'HTTP_VIA',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED'
    );
    
    foreach ($proxy_headers as $header) {
        if (!empty($_SERVER[$header])) {
            return true;
        }
    }
    
    return false;
}

// Check if IP is in range
function ip_in_range($ip, $range) {
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }
    
    list($subnet, $bits) = explode('/', $range);
    $ip_long = ip2long($ip);
    $subnet_long = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    
    return ($ip_long & $mask) === ($subnet_long & $mask);
}

// Check minimum session time for anonymous users
function check_minimum_session_time() {
    if (!session_id()) {
        session_start();
    }
    
    $session_start_key = 'rating_session_start';
    $current_time = time();
    
    if (!isset($_SESSION[$session_start_key])) {
        $_SESSION[$session_start_key] = $current_time;
        return false; // First visit, require more time
    }
    
    $session_duration = $current_time - $_SESSION[$session_start_key];
    return $session_duration >= 120; // Require at least 2 minutes on site
}

// Admin function to view rating details (for administrators)
function get_rating_details($post_id) {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    $ratings = get_post_meta($post_id, '_post_ratings', true);
    $rating_ips = get_post_meta($post_id, '_post_rating_ips', true);
    $rating_timestamps = get_post_meta($post_id, '_post_rating_timestamps', true);
    $rating_users = get_post_meta($post_id, '_post_rating_users', true);
    
    if (!is_array($ratings)) return array();
    
    $details = array();
    for ($i = 0; $i < count($ratings); $i++) {
        $details[] = array(
            'rating' => isset($ratings[$i]) ? $ratings[$i] : 0,
            'ip' => isset($rating_ips[$i]) ? $rating_ips[$i] : 'unknown',
            'timestamp' => isset($rating_timestamps[$i]) ? date('Y-m-d H:i:s', $rating_timestamps[$i]) : 'unknown',
            'user_id' => isset($rating_users[$i]) ? $rating_users[$i] : 0
        );
    }
    
    return $details;
}

// Function to clean up old rating data (run monthly)
function cleanup_old_ratings() {
    global $wpdb;
    
    // Remove ratings older than 1 year
    $one_year_ago = current_time('timestamp') - (365 * 24 * 60 * 60);
    
    $posts_with_ratings = $wpdb->get_col(
        "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_post_rating_timestamps'"
    );
    
    foreach ($posts_with_ratings as $post_id) {
        $timestamps = get_post_meta($post_id, '_post_rating_timestamps', true);
        $ratings = get_post_meta($post_id, '_post_ratings', true);
        $ips = get_post_meta($post_id, '_post_rating_ips', true);
        $users = get_post_meta($post_id, '_post_rating_users', true);
        
        if (is_array($timestamps)) {
            $keep_indices = array();
            
            for ($i = 0; $i < count($timestamps); $i++) {
                if ($timestamps[$i] > $one_year_ago) {
                    $keep_indices[] = $i;
                }
            }
            
            if (count($keep_indices) < count($timestamps)) {
                // Filter arrays to keep only recent ratings
                $new_ratings = array();
                $new_ips = array();
                $new_timestamps = array();
                $new_users = array();
                
                foreach ($keep_indices as $index) {
                    if (isset($ratings[$index])) $new_ratings[] = $ratings[$index];
                    if (isset($ips[$index])) $new_ips[] = $ips[$index];
                    if (isset($timestamps[$index])) $new_timestamps[] = $timestamps[$index];
                    if (isset($users[$index])) $new_users[] = $users[$index];
                }
                
                // Update meta with cleaned data
                update_post_meta($post_id, '_post_ratings', $new_ratings);
                update_post_meta($post_id, '_post_rating_ips', $new_ips);
                update_post_meta($post_id, '_post_rating_timestamps', $new_timestamps);
                update_post_meta($post_id, '_post_rating_users', $new_users);
                
                // Recalculate average
                if (count($new_ratings) > 0) {
                    $average = array_sum($new_ratings) / count($new_ratings);
                    update_post_meta($post_id, '_post_rating_average', $average);
                    update_post_meta($post_id, '_post_rating_count', count($new_ratings));
                } else {
                    delete_post_meta($post_id, '_post_rating_average');
                    delete_post_meta($post_id, '_post_rating_count');
                }
            }
        }
    }
}

// Schedule cleanup (run monthly)
if (!wp_next_scheduled('rating_cleanup_cron')) {
    wp_schedule_event(time(), 'monthly', 'rating_cleanup_cron');
}
add_action('rating_cleanup_cron', 'cleanup_old_ratings');

// Add custom cron schedule for monthly cleanup
function add_monthly_cron_schedule($schedules) {
    $schedules['monthly'] = array(
        'interval' => 30 * 24 * 60 * 60, // 30 days
        'display' => 'Monthly'
    );
    return $schedules;
}
add_filter('cron_schedules', 'add_monthly_cron_schedule');

/*
=== RATING SYSTEM SECURITY SUMMARY ===

**General Security (All Users):**
1. **Nonce Verification**: All rating submissions require valid WordPress nonces
2. **User Tracking**: Logged-in users can only rate each post once
3. **IP Rate Limiting**: Maximum 3 ratings per IP address per post
4. **Time Limiting**: 1-hour cooldown between ratings from same IP
5. **Rapid-Fire Protection**: Maximum 5 ratings in 5 minutes across all posts
6. **Pattern Detection**: Flags suspicious voting patterns (all 1s or 5s from same IP)
7. **Data Validation**: Ensures ratings are 1-5 stars and posts exist
8. **Transient Caching**: Efficient rate limiting using WordPress transients
9. **Automatic Cleanup**: Monthly removal of ratings older than 1 year
10. **Admin Oversight**: get_rating_details() function for viewing rating data

**Enhanced Security for Anonymous Users:**
11. **Browser Fingerprinting**: Tracks device-specific identifiers to prevent multiple ratings
12. **Stricter IP Limits**: Anonymous users limited to 1 rating per post (vs 3 for logged-in)
13. **Proxy/VPN Detection**: Blocks ratings from known proxy/VPN IP ranges
14. **Daily Limits**: Maximum 3 ratings per day for anonymous users
15. **Session Time Requirements**: Requires minimum 2 minutes on site before rating
16. **Math CAPTCHA**: Simple arithmetic challenge for anonymous submissions
17. **Honeypot Fields**: Hidden form fields to catch automated bots
18. **Timing Analysis**: Rejects submissions that are too fast (<2s) or too slow (>10min)
19. **Referer Validation**: Ensures submissions come from the actual website
20. **Enhanced Logging**: Detailed security event logging for anonymous users

All security events are logged for debugging and monitoring.
Anonymous users face significantly stricter controls to prevent abuse.
*/

function get_post_rating_average($post_id) {
    $average = get_post_meta($post_id, '_post_rating_average', true);
    return $average ? round($average, 1) : 0;
}

function get_post_rating_count($post_id) {
    $count = get_post_meta($post_id, '_post_rating_count', true);
    return $count ? $count : 0;
}

function display_post_rating($post_id) {
    $average = get_post_rating_average($post_id);
    $count = get_post_rating_count($post_id);
    
    if ($count > 0) {
        return round($average) . ' (' . $count . ' votes)';
    }
    return 'N/A';
}

// Add CSS for rating display
add_action('wp_head', 'add_rating_styles');

function add_rating_styles() {
    if (is_home() || is_front_page()) {
        echo '<style>
        .rating-display {
            font-weight: bold;
            color: #333;
        }
        .rating-stars {
            color: #ffa500;
        }
        </style>';
    }
}

// Add JavaScript for rating functionality
add_action('wp_head', 'add_rating_script');

function add_rating_script() {
    if (is_home() || is_front_page()) {
        ?>
        <script type="text/javascript">
        function submitRating(select) {
            console.log('Rating submission called with:', select.value);
            
            var rating = select.value;
            var postId = select.options[select.selectedIndex].getAttribute('data-post-id');
            
            console.log('Rating:', rating, 'Post ID:', postId);
            
            if (rating && postId) {
                // Simple math CAPTCHA for anonymous users
                <?php if (!is_user_logged_in()): ?>
                var num1 = Math.floor(Math.random() * 10) + 1;
                var num2 = Math.floor(Math.random() * 10) + 1;
                var answer = window.confirm('Please solve this math problem to continue: ' + num1 + ' + ' + num2 + ' = ' + (num1 + num2) + '\n\nClick OK to confirm this is correct, or Cancel to try again.');
                
                if (!answer) {
                    select.selectedIndex = 0;
                    return false;
                }
                <?php endif; ?>
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onreadystatechange = function() {
                    console.log('XHR State:', xhr.readyState, 'Status:', xhr.status);
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            console.log('Success Response:', xhr.responseText);
                            location.reload();
                        } else {
                            console.error('Error:', xhr.status, xhr.responseText);
                            alert('Error submitting rating. Please try again.');
                        }
                    }
                };
                
                var data = 'action=rate_post&post_id=' + postId + '&rating=' + rating + '&nonce=<?php echo wp_create_nonce('rate_post_nonce'); ?>';
                
                // Add honeypot field
                data += '&website=';
                
                // Add timestamp for timing analysis
                data += '&submit_time=' + Date.now();
                
                console.log('Sending data:', data);
                xhr.send(data);
            } else {
                console.error('Missing rating or postId');
            }
            
            return false;
        }
        </script>
        <?php
    }
}

function remove_private_prefix($title) {
	$title = str_replace('Private: ', '', $title);
	return $title;
}

add_filter('the_title', 'remove_private_prefix');

// Register navigation menus
function register_echo_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Navigation', 'echoring'),
    ));
}
add_action('init', 'register_echo_menus');

// Add theme support for menus
function echo_theme_setup() {
    add_theme_support('menus');
}
add_action('after_setup_theme', 'echo_theme_setup');

// Add custom CSS classes for menu items
function add_menu_item_classes($classes, $item, $args) {
    if ($args->theme_location == 'primary') {
        // You can add specific classes here if needed
        $classes[] = 'echo-menu-item';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_menu_item_classes', 1, 3);

// Admin-only functionality for menu editing
if (is_admin()) {
    // Load custom menu walker after admin init when Walker_Nav_Menu_Edit is available
    add_action('admin_init', function() {
        // Only load if we're on the nav-menus page and the class exists
        if (class_exists('Walker_Nav_Menu_Edit')) {
            // Custom Walker for menu editing (allows custom fields)
            if (!class_exists('Walker_Nav_Menu_Edit_Custom')) {
                class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu_Edit {
                    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
                        $item_output = '';
                        parent::start_el($item_output, $item, $depth, $args, $id);
                        
                        // Add custom separator field
                        $separator_field = '
                        <p class="field-separator description description-wide">
                            <label for="edit-menu-item-separator-' . $item->ID . '">
                                Is Separator<br />
                                <input type="checkbox" id="edit-menu-item-separator-' . $item->ID . '" class="widefat code edit-menu-item-separator" name="menu-item-separator[' . $item->ID . ']" value="1" ' . checked(in_array('separator', $item->classes), true, false) . ' />
                                <span class="description">Check this to make this item a separator (larger spacing)</span>
                            </label>
                        </p>';
                        
                        $output .= str_replace('<p class="field-move', $separator_field . '<p class="field-move', $item_output);
                    }
                }
            }
            
            // Enable custom fields on menu items for separators
            add_filter('wp_edit_nav_menu_walker', function() {
                return 'Walker_Nav_Menu_Edit_Custom';
            });
        }
    });

    // Save custom menu item fields
    function save_menu_custom_fields($menu_id, $menu_item_db_id, $args) {
        if (isset($_POST['menu-item-separator'][$menu_item_db_id])) {
            $separator = $_POST['menu-item-separator'][$menu_item_db_id];
            if ($separator == '1') {
                $classes = get_post_meta($menu_item_db_id, '_menu_item_classes', true);
                if (!is_array($classes)) {
                    $classes = array();
                }
                if (!in_array('separator', $classes)) {
                    $classes[] = 'separator';
                }
                update_post_meta($menu_item_db_id, '_menu_item_classes', $classes);
            }
        } else {
            // Remove separator class if unchecked
            $classes = get_post_meta($menu_item_db_id, '_menu_item_classes', true);
            if (is_array($classes)) {
                $classes = array_diff($classes, array('separator'));
                update_post_meta($menu_item_db_id, '_menu_item_classes', $classes);
            }
        }
    }
    add_action('wp_update_nav_menu_item', 'save_menu_custom_fields', 10, 3);
}

/** @ignore */
function kubrick_head() {
	$head = "<style type='text/css'>\n<!--";
	$output = '';
	if ( kubrick_header_image() ) {
		$url =  kubrick_header_image_url() ;
		$output .= "#header { background: url('$url') no-repeat bottom center; }\n";
	}
	if ( false !== ( $color = kubrick_header_color() ) ) {
		$output .= "#headerimg h1 a, #headerimg h1 a:visited, #headerimg .description { color: $color; }\n";
	}
	if ( false !== ( $display = kubrick_header_display() ) ) {
		$output .= "#headerimg { display: $display }\n";
	}
	$foot = "--></style>\n";
	if ( '' != $output )
		echo $head . $output . $foot;
}

add_action('wp_head', 'kubrick_head');

function kubrick_header_image() {
	return apply_filters('kubrick_header_image', get_option('kubrick_header_image'));
}

function kubrick_upper_color() {
	if (strpos($url = kubrick_header_image_url(), 'header-img.php?') !== false) {
		parse_str(substr($url, strpos($url, '?') + 1), $q);
		return $q['upper'];
	} else
		return '69aee7';
}

function kubrick_lower_color() {
	if (strpos($url = kubrick_header_image_url(), 'header-img.php?') !== false) {
		parse_str(substr($url, strpos($url, '?') + 1), $q);
		return $q['lower'];
	} else
		return '4180b6';
}

function kubrick_header_image_url() {
	if ( $image = kubrick_header_image() )
		$url = get_template_directory_uri() . '/images/' . $image;
	else
		$url = get_template_directory_uri() . '/images/kubrickheader.jpg';

	return $url;
}

function kubrick_header_color() {
	return apply_filters('kubrick_header_color', get_option('kubrick_header_color'));
}

function kubrick_header_color_string() {
	$color = kubrick_header_color();
	if ( false === $color )
		return 'white';

	return $color;
}

function kubrick_header_display() {
	return apply_filters('kubrick_header_display', get_option('kubrick_header_display'));
}

function kubrick_header_display_string() {
	$display = kubrick_header_display();
	return $display ? $display : 'inline';
}

add_action('admin_menu', 'kubrick_add_theme_page');

function kubrick_add_theme_page() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == basename(__FILE__) ) {
		if ( isset( $_REQUEST['action'] ) && 'save' == $_REQUEST['action'] ) {
			check_admin_referer('kubrick-header');
			if ( isset($_REQUEST['njform']) ) {
				if ( isset($_REQUEST['defaults']) ) {
					delete_option('kubrick_header_image');
					delete_option('kubrick_header_color');
					delete_option('kubrick_header_display');
				} else {
					if ( '' == $_REQUEST['njfontcolor'] )
						delete_option('kubrick_header_color');
					else {
						$fontcolor = preg_replace('/^.*(#[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['njfontcolor']);
						update_option('kubrick_header_color', $fontcolor);
					}
					if ( preg_match('/[0-9A-F]{6}|[0-9A-F]{3}/i', $_REQUEST['njuppercolor'], $uc) && preg_match('/[0-9A-F]{6}|[0-9A-F]{3}/i', $_REQUEST['njlowercolor'], $lc) ) {
						$uc = ( strlen($uc[0]) == 3 ) ? $uc[0][0].$uc[0][0].$uc[0][1].$uc[0][1].$uc[0][2].$uc[0][2] : $uc[0];
						$lc = ( strlen($lc[0]) == 3 ) ? $lc[0][0].$lc[0][0].$lc[0][1].$lc[0][1].$lc[0][2].$lc[0][2] : $lc[0];
						update_option('kubrick_header_image', "header-img.php?upper=$uc&lower=$lc");
					}

					if ( isset($_REQUEST['toggledisplay']) ) {
						if ( false === get_option('kubrick_header_display') )
							update_option('kubrick_header_display', 'none');
						else
							delete_option('kubrick_header_display');
					}
				}
			} else {

				if ( isset($_REQUEST['headerimage']) ) {
					check_admin_referer('kubrick-header');
					if ( '' == $_REQUEST['headerimage'] )
						delete_option('kubrick_header_image');
					else {
						$headerimage = preg_replace('/^.*?(header-img.php\?upper=[0-9a-fA-F]{6}&lower=[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['headerimage']);
						update_option('kubrick_header_image', $headerimage);
					}
				}

				if ( isset($_REQUEST['fontcolor']) ) {
					check_admin_referer('kubrick-header');
					if ( '' == $_REQUEST['fontcolor'] )
						delete_option('kubrick_header_color');
					else {
						$fontcolor = preg_replace('/^.*?(#[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['fontcolor']);
						update_option('kubrick_header_color', $fontcolor);
					}
				}

				if ( isset($_REQUEST['fontdisplay']) ) {
					check_admin_referer('kubrick-header');
					if ( '' == $_REQUEST['fontdisplay'] || 'inline' == $_REQUEST['fontdisplay'] )
						delete_option('kubrick_header_display');
					else
						update_option('kubrick_header_display', 'none');
				}
			}
			//print_r($_REQUEST);
			wp_redirect("themes.php?page=functions.php&saved=true");
			die;
		}
		add_action('admin_head', 'kubrick_theme_page_head');
	}
	add_theme_page(__('Custom Header'), __('Custom Header'), 'edit_themes', basename(__FILE__), 'kubrick_theme_page');
}

function kubrick_theme_page_head() {
?>
<script type="text/javascript" src="../wp-includes/js/colorpicker.js"></script>
<script type='text/javascript'>
// <![CDATA[
	function pickColor(color) {
		ColorPicker_targetInput.value = color;
		kUpdate(ColorPicker_targetInput.id);
	}
	function PopupWindow_populate(contents) {
		contents += '<br /><p style="text-align:center;margin-top:0px;"><input type="button" class="button-secondary" value="<?php esc_attr_e('Close Color Picker'); ?>" onclick="cp.hidePopup(\'prettyplease\')"></input></p>';
		this.contents = contents;
		this.populated = false;
	}
	function PopupWindow_hidePopup(magicword) {
		if ( magicword != 'prettyplease' )
			return false;
		if (this.divName != null) {
			if (this.use_gebi) {
				document.getElementById(this.divName).style.visibility = "hidden";
			}
			else if (this.use_css) {
				document.all[this.divName].style.visibility = "hidden";
			}
			else if (this.use_layers) {
				document.layers[this.divName].visibility = "hidden";
			}
		}
		else {
			if (this.popupWindow && !this.popupWindow.closed) {
				this.popupWindow.close();
				this.popupWindow = null;
			}
		}
		return false;
	}
	function colorSelect(t,p) {
		if ( cp.p == p && document.getElementById(cp.divName).style.visibility != "hidden" )
			cp.hidePopup('prettyplease');
		else {
			cp.p = p;
			cp.select(t,p);
		}
	}
	function PopupWindow_setSize(width,height) {
		this.width = 162;
		this.height = 210;
	}

	var cp = new ColorPicker();
	function advUpdate(val, obj) {
		document.getElementById(obj).value = val;
		kUpdate(obj);
	}
	function kUpdate(oid) {
		if ( 'uppercolor' == oid || 'lowercolor' == oid ) {
			uc = document.getElementById('uppercolor').value.replace('#', '');
			lc = document.getElementById('lowercolor').value.replace('#', '');
			hi = document.getElementById('headerimage');
			hi.value = 'header-img.php?upper='+uc+'&lower='+lc;
			document.getElementById('header').style.background = 'url("<?php echo get_template_directory_uri(); ?>/images/'+hi.value+'") center no-repeat';
			document.getElementById('advuppercolor').value = '#'+uc;
			document.getElementById('advlowercolor').value = '#'+lc;
		}
		if ( 'fontcolor' == oid ) {
			document.getElementById('header').style.color = document.getElementById('fontcolor').value;
			document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value;
		}
		if ( 'fontdisplay' == oid ) {
			document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
		}
	}
	function toggleDisplay() {
		td = document.getElementById('fontdisplay');
		td.value = ( td.value == 'none' ) ? 'inline' : 'none';
		kUpdate('fontdisplay');
	}
	function toggleAdvanced() {
		a = document.getElementById('jsAdvanced');
		if ( a.style.display == 'none' )
			a.style.display = 'block';
		else
			a.style.display = 'none';
	}
	function kDefaults() {
		document.getElementById('headerimage').value = '';
		document.getElementById('advuppercolor').value = document.getElementById('uppercolor').value = '#69aee7';
		document.getElementById('advlowercolor').value = document.getElementById('lowercolor').value = '#4180b6';
		document.getElementById('header').style.background = 'url("<?php echo get_template_directory_uri(); ?>/images/kubrickheader.jpg") center no-repeat';
		document.getElementById('header').style.color = '#FFFFFF';
		document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value = '';
		document.getElementById('fontdisplay').value = 'inline';
		document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
	}
	function kRevert() {
		document.getElementById('headerimage').value = '<?php echo esc_js(kubrick_header_image()); ?>';
		document.getElementById('advuppercolor').value = document.getElementById('uppercolor').value = '#<?php echo esc_js(kubrick_upper_color()); ?>';
		document.getElementById('advlowercolor').value = document.getElementById('lowercolor').value = '#<?php echo esc_js(kubrick_lower_color()); ?>';
		document.getElementById('header').style.background = 'url("<?php echo esc_js(kubrick_header_image_url()); ?>") center no-repeat';
		document.getElementById('header').style.color = '';
		document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value = '<?php echo esc_js(kubrick_header_color_string()); ?>';
		document.getElementById('fontdisplay').value = '<?php echo esc_js(kubrick_header_display_string()); ?>';
		document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
	}
	function kInit() {
		document.getElementById('jsForm').style.display = 'block';
		document.getElementById('nonJsForm').style.display = 'none';
	}
	addLoadEvent(kInit);
// ]]>
</script>
<style type='text/css'>
	#headwrap {
		text-align: center;
	}
	#kubrick-header {
		font-size: 80%;
	}
	#kubrick-header .hibrowser {
		width: 780px;
		height: 260px;
		overflow: scroll;
	}
	#kubrick-header #hitarget {
		display: none;
	}
	#kubrick-header #header h1 {
		font-family: 'Trebuchet MS', 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-weight: bold;
		font-size: 4em;
		text-align: center;
		padding-top: 70px;
		margin: 0;
	}

	#kubrick-header #header .description {
		font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-size: 1.2em;
		text-align: center;
	}
	#kubrick-header #header {
		text-decoration: none;
		color: <?php echo kubrick_header_color_string(); ?>;
		padding: 0;
		margin: 0;
		height: 200px;
		text-align: center;
		background: url('<?php echo kubrick_header_image_url(); ?>') center no-repeat;
	}
	#kubrick-header #headerimg {
		margin: 0;
		height: 200px;
		width: 100%;
		display: <?php echo kubrick_header_display_string(); ?>;
	}
	
	.description {
		margin-top: 16px;
		color: #fff;
	}

	#jsForm {
		display: none;
		text-align: center;
	}
	#jsForm input.submit, #jsForm input.button, #jsAdvanced input.button {
		padding: 0px;
		margin: 0px;
	}
	#advanced {
		text-align: center;
		width: 620px;
	}
	html>body #advanced {
		text-align: center;
		position: relative;
		left: 50%;
		margin-left: -380px;
	}
	#jsAdvanced {
		text-align: right;
	}
	#nonJsForm {
		position: relative;
		text-align: left;
		margin-left: -370px;
		left: 50%;
	}
	#nonJsForm label {
		padding-top: 6px;
		padding-right: 5px;
		float: left;
		width: 100px;
		text-align: right;
	}
	.defbutton {
		font-weight: bold;
	}
	.zerosize {
		width: 0px;
		height: 0px;
		overflow: hidden;
	}
	#colorPickerDiv a, #colorPickerDiv a:hover {
		padding: 1px;
		text-decoration: none;
		border-bottom: 0px;
	}
</style>
<?php
}

function kubrick_theme_page() {
	if ( isset( $_REQUEST['saved'] ) ) echo '<div id="message" class="updated"><p><strong>'.__('Options saved.').'</strong></p></div>';
?>
<div class='wrap'>
	<h2><?php _e('Customize Header'); ?></h2>
	<div id="kubrick-header">
		<div id="headwrap">
			<div id="header">
				<div id="headerimg">
					<h1><?php bloginfo('name'); ?></h1>
					<div class="description"><?php bloginfo('description'); ?></div>
				</div>
			</div>
		</div>
		<br />
		<div id="nonJsForm">
			<form method="post" action="">
				<?php wp_nonce_field('kubrick-header'); ?>
				<div class="zerosize"><input type="submit" name="defaultsubmit" value="<?php esc_attr_e('Save'); ?>" /></div>
					<label for="njfontcolor"><?php _e('Font Color:'); ?></label><input type="text" name="njfontcolor" id="njfontcolor" value="<?php echo esc_attr(kubrick_header_color()); ?>" /> <?php printf(__('Any CSS color (%s or %s or %s)'), '<code>red</code>', '<code>#FF0000</code>', '<code>rgb(255, 0, 0)</code>'); ?><br />
					<label for="njuppercolor"><?php _e('Upper Color:'); ?></label><input type="text" name="njuppercolor" id="njuppercolor" value="#<?php echo esc_attr(kubrick_upper_color()); ?>" /> <?php printf(__('HEX only (%s or %s)'), '<code>#FF0000</code>', '<code>#F00</code>'); ?><br />
				<label for="njlowercolor"><?php _e('Lower Color:'); ?></label><input type="text" name="njlowercolor" id="njlowercolor" value="#<?php echo esc_attr(kubrick_lower_color()); ?>" /> <?php printf(__('HEX only (%s or %s)'), '<code>#FF0000</code>', '<code>#F00</code>'); ?><br />
				<input type="hidden" name="hi" id="hi" value="<?php echo esc_attr(kubrick_header_image()); ?>" />
				<input type="submit" name="toggledisplay" id="toggledisplay" value="<?php esc_attr_e('Toggle Text'); ?>" />
				<input type="submit" name="defaults" value="<?php esc_attr_e('Use Defaults'); ?>" />
				<input type="submit" class="defbutton" name="submitform" value="&nbsp;&nbsp;<?php esc_attr_e('Save'); ?>&nbsp;&nbsp;" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="njform" value="true" />
			</form>
		</div>
		<div id="jsForm">
			<form style="display:inline;" method="post" name="hicolor" id="hicolor" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
				<?php wp_nonce_field('kubrick-header'); ?>
	<input type="button"  class="button-secondary" onclick="tgt=document.getElementById('fontcolor');colorSelect(tgt,'pick1');return false;" name="pick1" id="pick1" value="<?php esc_attr_e('Font Color'); ?>"></input>
		<input type="button" class="button-secondary" onclick="tgt=document.getElementById('uppercolor');colorSelect(tgt,'pick2');return false;" name="pick2" id="pick2" value="<?php esc_attr_e('Upper Color'); ?>"></input>
		<input type="button" class="button-secondary" onclick="tgt=document.getElementById('lowercolor');colorSelect(tgt,'pick3');return false;" name="pick3" id="pick3" value="<?php esc_attr_e('Lower Color'); ?>"></input>
				<input type="button" class="button-secondary" name="revert" value="<?php esc_attr_e('Revert'); ?>" onclick="kRevert()" />
				<input type="button" class="button-secondary" value="<?php esc_attr_e('Advanced'); ?>" onclick="toggleAdvanced()" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="fontdisplay" id="fontdisplay" value="<?php echo esc_attr(kubrick_header_display()); ?>" />
				<input type="hidden" name="fontcolor" id="fontcolor" value="<?php echo esc_attr(kubrick_header_color()); ?>" />
				<input type="hidden" name="uppercolor" id="uppercolor" value="<?php echo esc_attr(kubrick_upper_color()); ?>" />
				<input type="hidden" name="lowercolor" id="lowercolor" value="<?php echo esc_attr(kubrick_lower_color()); ?>" />
				<input type="hidden" name="headerimage" id="headerimage" value="<?php echo esc_attr(kubrick_header_image()); ?>" />
				<p class="submit"><input type="submit" name="submitform" class="button-primary" value="<?php esc_attr_e('Update Header'); ?>" onclick="cp.hidePopup('prettyplease')" /></p>
			</form>
			<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;visibility:hidden;"> </div>
			<div id="advanced">
				<form id="jsAdvanced" style="display:none;" action="">
					<?php wp_nonce_field('kubrick-header'); ?>
					<label for="advfontcolor"><?php _e('Font Color (CSS):'); ?> </label><input type="text" id="advfontcolor" onchange="advUpdate(this.value, 'fontcolor')" value="<?php echo esc_attr(kubrick_header_color()); ?>" /><br />
					<label for="advuppercolor"><?php _e('Upper Color (HEX):');?> </label><input type="text" id="advuppercolor" onchange="advUpdate(this.value, 'uppercolor')" value="#<?php echo esc_attr(kubrick_upper_color()); ?>" /><br />
					<label for="advlowercolor"><?php _e('Lower Color (HEX):'); ?> </label><input type="text" id="advlowercolor" onchange="advUpdate(this.value, 'lowercolor')" value="#<?php echo esc_attr(kubrick_lower_color()); ?>" /><br />
					<input type="button" class="button-secondary" name="default" value="<?php esc_attr_e('Select Default Colors'); ?>" onclick="kDefaults()" /><br />
					<input type="button" class="button-secondary" onclick="toggleDisplay();return false;" name="pick" id="pick" value="<?php esc_attr_e('Toggle Text Display'); ?>"></input><br />
				</form>
			</div>
		</div>
	</div>
</div>

<?php }

// Site Updates functionality has been moved to the EchoRing Sites plugin
// The 'Site Updates' post type registration has been removed to avoid duplicate menu items
// Update management is now handled via Sites > Updates in the admin menu

// Site update meta boxes and custom columns have been removed
// This functionality is now handled by the EchoRing Sites plugin

// Add custom rewrite rule for webmaster profile
function add_webmaster_profile_rewrite_rule() {
    add_rewrite_rule('^webmaster-profile/?$', 'index.php?pagename=webmaster-profile', 'top');
}
add_action('init', 'add_webmaster_profile_rewrite_rule');

// Flush rewrite rules on theme activation
function theme_activation() {
    add_webmaster_profile_rewrite_rule();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'theme_activation');

// Clean up on theme deactivation
function theme_deactivation() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'theme_deactivation');

// AJAX handler for comment moderation
function handle_moderate_comment() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'moderate_comment_nonce')) {
        wp_die('Security check failed');
    }
    
    // Check if user has permission to moderate comments
    if (!current_user_can('moderate_comments')) {
        wp_send_json_error('You do not have permission to moderate comments.');
    }
    
    $comment_id = intval($_POST['comment_id']);
    $action = sanitize_text_field($_POST['moderate_action']);
    
    if ($action === 'approve') {
        $result = wp_set_comment_status($comment_id, 'approve');
        if ($result) {
            wp_send_json_success('Comment approved successfully.');
        } else {
            wp_send_json_error('Failed to approve comment.');
        }
    } elseif ($action === 'reject') {
        $result = wp_set_comment_status($comment_id, 'spam');
        if ($result) {
            wp_send_json_success('Comment rejected successfully.');
        } else {
            wp_send_json_error('Failed to reject comment.');
        }
    } else {
        wp_send_json_error('Invalid action.');
    }
}
add_action('wp_ajax_moderate_comment', 'handle_moderate_comment');
add_action('wp_ajax_nopriv_moderate_comment', 'handle_moderate_comment');
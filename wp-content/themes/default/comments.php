<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) { ?>
    <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'kubrick'); ?></p> 
<?php
    return;
}
?>

<!-- Custom EchoRing Comments Table Layout -->


<?php
// Check if a comment was just submitted and show success message
if ( ! empty( $_GET['unapproved'] ) && ! empty( $_GET['moderation-hash'] ) ) {
    $comment_id = (int) $_GET['unapproved'];
    $comment = get_comment( $comment_id );
    
    if ( $comment && hash_equals( $_GET['moderation-hash'], wp_hash( $comment->comment_date_gmt ) ) ) {
        echo '<div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">';
        echo '<font face="verdana" size="1"><strong>Thank you for your comment!</strong> Your comment has been submitted and is awaiting moderation. It will appear once approved.</font>';
        echo '</div>';
    }
} elseif ( isset( $_GET['comment-submitted'] ) ) {
    // Show success message for approved comments
    echo '<div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">';
    echo '<font face="verdana" size="1"><strong>Thank you for your comment!</strong> Your comment has been posted successfully.</font>';
    echo '</div>';
}
?>

<table border="0" align="CENTER" width="575px" cellpadding="0" cellspacing="0">
<tbody>
<?php if ( have_comments() ) : ?>
    <?php 
    // Get all comments including pending ones for admins
    $all_comments = current_user_can('moderate_comments') ? 
        get_comments(array('post_id' => get_the_ID(), 'status' => 'all')) : 
        get_comments(array('post_id' => get_the_ID(), 'status' => 'approve'));
    
    foreach ($all_comments as $comment) : 
    ?>
    <tr>
        <td valign="top" width="125px">
            <font face="verdana" size="1">
                Author: <b><?php echo get_comment_author($comment); ?></b>
                <?php if ($comment->comment_approved == '0') : ?>
                    <br><span style="color: orange;"><b>[PENDING]</b></span>
                <?php endif; ?>
            </font>
        </td>
        <td>
            <font face="verdana" size="1">
                <p align="JUSTIFY"><?php echo esc_html(get_comment_text($comment)); ?></p>
                <?php if (current_user_can('moderate_comments') && $comment->comment_approved == '0') : ?>
                    <div style="margin-top: 5px;">
                        <button onclick="moderateComment(<?php echo $comment->comment_ID; ?>, 'approve')" style="font-size: 10px; margin-right: 5px;">Approve</button>
                        <button onclick="moderateComment(<?php echo $comment->comment_ID; ?>, 'reject')" style="font-size: 10px;">Reject</button>
                    </div>
                <?php endif; ?>
                <center><img src="<?php echo get_template_directory_uri(); ?>/images/spacer.png" height="2px"></center>
            </font>
        </td>
    </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr><td colspan="2"><font face="verdana" size="1">No comments yet.</font></td></tr>
<?php endif; ?>

</tbody></table>

<script type="text/javascript">
function moderateComment(commentId, action) {
    if (confirm('Are you sure you want to ' + action + ' this comment?')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload(); // Refresh to show updated comments
                    } else {
                        alert('Error: ' + response.data);
                    }
                } else {
                    alert('Error processing request');
                }
            }
        };
        
        var params = 'action=moderate_comment&comment_id=' + commentId + '&moderate_action=' + action + '&nonce=<?php echo wp_create_nonce('moderate_comment_nonce'); ?>';
        xhr.send(params);
    }
}
</script>
<hr>
<?php if ( comments_open() ) : ?>
<form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" id="commentform">
<input type="hidden" name="redirect_to" value="<?php echo esc_url( add_query_arg( 'comment-submitted', '1', get_permalink() ) ); ?>" />
<table align="CENTER" border="0">
<tbody>
<?php if ( !is_user_logged_in() ) : ?>
<tr><td><font face="verdana" size="1"><b>Your Name:</b></font></td><td><input type="text" size="20" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" required /></td></tr>
<tr><td><font face="verdana" size="1"><b>Your Email:</b></font></td><td><input type="email" size="20" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" required /></td></tr>
<?php endif; ?>
<tr><td><font face="verdana" size="1"><b>Comment:</b></font></td><td><textarea name="comment" id="comment" cols="40" rows="4" required></textarea></td></tr>
<tr><td></td><td><input name="submit" type="submit" id="submit" value="Post" /><?php comment_id_fields(); ?></td></tr>
<?php do_action('comment_form', $post->ID); ?>
</tbody>
</table>
</form>
<?php endif; ?>


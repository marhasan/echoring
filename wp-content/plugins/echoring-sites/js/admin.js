jQuery(document).ready(function($) {
    // Screenshot management
    var screenshotIndex = 0;
    
    $('#add_screenshot').on('click', function() {
        var frame = wp.media({
            title: 'Select Screenshot',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var html = '<div class="screenshot-item">';
            html += '<img src="' + attachment.sizes.thumbnail.url + '" style="max-width:100px;" />';
            html += '<input type="hidden" name="screenshots[]" value="' + attachment.id + '" />';
            html += '<button type="button" class="button remove-screenshot">Remove</button>';
            html += '</div>';
            $('#screenshots_container').append(html);
        });
        
        frame.open();
    });
    
    $(document).on('click', '.remove-screenshot', function() {
        $(this).closest('.screenshot-item').remove();
    });
    
    // Review items management
    $('#add_good_item').on('click', function() {
        var html = '<div class="review-item">';
        html += '<textarea name="the_good[]" rows="2" class="large-text"></textarea>';
        html += '<button type="button" class="button remove-review-item">Remove</button>';
        html += '</div>';
        $('#the_good_container').append(html);
    });
    
    $('#add_bad_item').on('click', function() {
        var html = '<div class="review-item">';
        html += '<textarea name="the_bad[]" rows="2" class="large-text"></textarea>';
        html += '<button type="button" class="button remove-review-item">Remove</button>';
        html += '</div>';
        $('#the_bad_container').append(html);
    });
    
    $(document).on('click', '.remove-review-item', function() {
        $(this).closest('.review-item').remove();
    });
    
    // Update management
    $(document).on('click', '.mark-updated, .mark-updated-now', function() {
        var siteId = $(this).data('site-id');
        var button = $(this);
        
        $.ajax({
            url: echoring_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mark_site_updated',
                site_id: siteId,
                nonce: echoring_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var row = button.closest('tr');
                    if (row.length) {
                        row.removeClass('needs-update new-site').addClass('updated');
                        row.find('td:nth-child(5)').text('Updated');
                        row.find('.new-badge').remove();
                        button.replaceWith('<button type="button" class="button button-secondary reset-updates compact-btn" data-site-id="' + siteId + '" title="Reset Updates">↻</button>');
                    } else {
                        // Meta box context
                        var metaBox = button.closest('#site_update_status, .postbox');
                        metaBox.find('.last-updated-text').text(new Date().toISOString().slice(0, 16).replace('T', ' '));
                        button.prop('disabled', true);
                        metaBox.find('.reset-updates-now').prop('disabled', false);
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error occurred while updating site status.');
            }
        });
    });
    
    $(document).on('click', '.reset-updates, .reset-updates-now', function() {
        var siteId = $(this).data('site-id');
        var button = $(this);
        
        $.ajax({
            url: echoring_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'reset_site_updates',
                site_id: siteId,
                nonce: echoring_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var row = button.closest('tr');
                    if (row.length) {
                        row.removeClass('updated new-site needs-update');
                        row.find('td:nth-child(5)').text('Normal');
                        button.replaceWith('<button type="button" class="button button-primary mark-updated compact-btn" data-site-id="' + siteId + '" title="Mark as Updated">✓</button>');
                    } else {
                        // Meta box context
                        var metaBox = button.closest('#site_update_status, .postbox');
                        metaBox.find('.last-updated-text').text('');
                        button.prop('disabled', true);
                        metaBox.find('.mark-updated-now').prop('disabled', false);
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error occurred while resetting site status.');
            }
        });
    });
    
    // New site status management
    $(document).on('click', '.mark-new-now', function() {
        var siteId = $(this).data('site-id');
        var button = $(this);
        
        $.ajax({
            url: echoring_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mark_as_new_site',
                site_id: siteId,
                nonce: echoring_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var row = button.closest('tr');
                    if (row.length) {
                        row.find('.new-badge').remove();
                        row.find('td:nth-child(2)').append('<span class="new-badge">NEW</span>');
                        button.replaceWith('<button type="button" class="button button-secondary reset-new-now compact-btn" data-site-id="' + siteId + '" title="Reset New">✕</button>');
                    } else {
                        // Meta box context
                        var metaBox = button.closest('#site_update_status, .postbox');
                        button.prop('disabled', true);
                        metaBox.find('.reset-new-now').prop('disabled', false);
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error occurred while updating new site status.');
            }
        });
    });
    
    $(document).on('click', '.reset-new-now', function() {
        var siteId = $(this).data('site-id');
        var button = $(this);
        
        $.ajax({
            url: echoring_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'reset_new_site_status',
                site_id: siteId,
                nonce: echoring_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var row = button.closest('tr');
                    if (row.length) {
                        row.find('.new-badge').remove();
                        row.removeClass('new-site updated needs-update');
                        row.find('td:nth-child(5)').text('Normal');
                        button.replaceWith('<button type="button" class="button button-primary mark-new-now compact-btn" data-site-id="' + siteId + '" title="Mark as New">★</button>');
                    } else {
                        // Meta box context
                        var metaBox = button.closest('#site_update_status, .postbox');
                        button.prop('disabled', true);
                        metaBox.find('.mark-new-now').prop('disabled', false);
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error occurred while resetting new site status.');
            }
        });
    });
});
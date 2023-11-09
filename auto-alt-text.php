<?php
/*
Plugin Name: Auto Alt Text
Description: Automatically fills the ALT text of images with their titles and provides a button to update existing images.
Version: 1.0
Author: Chris Kotza
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Hook for adding admin menus
add_action('admin_menu', 'auto_alt_text_menu');

// Action function for the above hook
function auto_alt_text_menu() {
    add_options_page('Auto Alt Text Settings', 'Auto Alt Text', 'manage_options', 'auto-alt-text', 'auto_alt_text_page');
}

// The page content
function auto_alt_text_page() {
    ?>
    <div class="wrap">
        <h2>Auto Alt Text Settings</h2>
        <form method="post" action="">
            <?php wp_nonce_field('update_alt_text', 'auto_alt_text_nonce'); ?>
            <input type="submit" name="update_alt_text" value="Update Alt Text for All Images" class="button button-primary"/>
        </form>
    </div>
    <?php
    // Check if the button is clicked
    if (isset($_POST['update_alt_text']) && check_admin_referer('update_alt_text', 'auto_alt_text_nonce')) {
        auto_update_alt_text();
        echo '<div class="updated"><p>Alt text updated for all images.</p></div>';
    }
}

// Function to automatically update alt text for existing images
function auto_update_alt_text() {
    $args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
    );
    $images = get_posts($args);
    foreach ($images as $image) {
        update_post_meta($image->ID, '_wp_attachment_image_alt', $image->post_title);
    }
}

// Hook to fill alt text on image upload
add_action('add_attachment', 'auto_fill_alt_text');

// Function to fill alt text on image upload
function auto_fill_alt_text($attachment_id) {
    $attachment = get_post($attachment_id);
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $attachment->post_title);
}

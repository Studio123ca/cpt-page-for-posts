<?php
/*
Plugin Name: Custom Post Type Archive Pages
Description: Select a specific Page as the main archive page for custom post types.
Version: 1.3.0
Author: Studio123
Author URI: https://studio123.ca
*/

// Includes
require_once('inc/functions.php');

if (function_exists('acf_add_options_page')) {
    require_once('inc/acf.php');
}

// Main function - Get the page ID for a post type archive
function cpt_page_for_posts($post_type)
{
    if (empty($post_type)) {
        return false;
    }

    return get_option('cpt_page_for_posts_' . $post_type);
}

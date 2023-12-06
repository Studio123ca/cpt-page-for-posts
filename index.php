<?php
/*
Plugin Name: Custom Post Type Archive Page Selector
Description: Select a specific Page as the main archive page for custom post types.
Version: 1.0.0
Author: Studio123
Author URI: https://studio123.ca
*/

// Register the setting for each post type
function register_post_type_page_settings()
{
    $post_types = get_post_types(['has_archive' => true], 'objects');

    foreach ($post_types as $post_type) {
        register_setting('custom_post_type_page_for_posts', 'cpt_page_for_posts_' . $post_type->name);
    }
}
add_action('admin_init', 'register_post_type_page_settings');

// Add settings sections and fields
function add_settings_sections()
{
    $post_types = get_post_types(['has_archive' => true], 'objects');

    foreach ($post_types as $post_type) {
        add_settings_section(
            'cpt_page_for_posts_section_' . $post_type->name,
            '',
            function () use ($post_type) {
                display_post_type_page_fields($post_type->name);
            },
            'custom_post_type_page_for_posts'
        );
    }
}
add_action('admin_init', 'add_settings_sections');

// Create an admin page for setting page associations
function custom_post_type_page_page()
{
?>
    <div class="wrap" id="poststuff">
        <h1><?php esc_html_e('Custom Post Type Archive Pages', 'cpt-page-for-posts'); ?></h1>

        <p><?php esc_html_e('This plugin allows you to select a Page to act as the archive page for each custom post type, similar to setting a Page for regular blog posts.', 'cpt-page-for-posts'); ?></p>

        <p><strong><?php esc_html_e('Note:', 'cpt-page-for-posts'); ?></strong> <?php esc_html_e('This plugin is tailored for custom post types with the has_archive parameter set to true.', 'cpt-page-for-posts'); ?></p>

        <form method="post" action="options.php">
            <?php
            settings_fields('custom_post_type_page_for_posts');
            do_settings_sections('custom_post_type_page_for_posts');
            submit_button();
            ?>
        </form>
    </div>
<?php
}
add_action('admin_menu', 'load_page_for_posts_settings_page');

// Display the settings fields for associating a page with each post type
function display_post_type_page_fields($post_type)
{
    $option_name = 'cpt_page_for_posts_' . $post_type;
    $current_value = get_option($option_name);
    $pages = get_pages();
    $post_type_object = get_post_type_object($post_type);

    // Sort pages alphabetically
    usort($pages, function ($a, $b) {
        return strcmp($a->post_title, $b->post_title);
    });
?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr($option_name); ?>">
                        <?php echo esc_html($post_type_object->labels->singular_name); ?>
                    </label>
                </th>
                <td>
                    <select id="<?php echo esc_attr($option_name); ?>" name="<?php echo esc_attr($option_name); ?>">
                        <option value="0" <?php selected($current_value, 0); ?>><?php esc_html_e('None (Default)', 'cpt-page-for-posts'); ?></option>
                        <?php foreach ($pages as $page) : ?>
                            <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($current_value, $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
<?php
}

// Load the settings page
function load_page_for_posts_settings_page()
{
    add_options_page(
        esc_html__('Custom Post Type Archive Pages', 'cpt-page-for-posts'),
        esc_html__('CPT Archive Pages', 'cpt-page-for-posts'),
        'manage_options',
        'custom_post_type_page_for_posts',
        'custom_post_type_page_page'
    );
}
add_action('admin_menu', 'load_page_for_posts_settings_page');

function get_cpt_page_for_posts($post_type)
{
    if (empty($post_type)) {
        return false;
    }

    return get_option('cpt_page_for_posts_' . $post_type);
}

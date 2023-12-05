<?php
/*
Plugin Name: Custom Post Type Archive Page Selector
Description: This plugin lets you easily choose a specific Page to serve as the main archive page for your custom post types. It works just like how you set the Posts page for your regular blog posts.
Version: 1.0.0
Author: Studio123
Author URI: https://studio123.ca
*/

// Register the setting for each post type
function register_post_type_page_for_posts_settings()
{
    $post_types = get_post_types(['has_archive' => true], 'objects');

    foreach ($post_types as $post_type) {
        register_setting('custom_post_type_page_for_posts', 'cpt_page_for_posts_' . $post_type->name);
    }
}
add_action('admin_init', 'register_post_type_page_for_posts_settings');

// Add settings sections and fields
function add_settings_sections()
{
    $post_types = get_post_types(['has_archive' => true], 'objects');

    foreach ($post_types as $post_type) {
        add_settings_section(
            'cpt_page_for_posts_section_' . $post_type->name,
            '',
            function () use ($post_type) {
                display_post_type_page_for_posts_fields($post_type->name);
            },
            'custom_post_type_page_for_posts'
        );
    }
}

// Create an admin page where you can set the page associations for each post type
function custom_post_type_page_for_posts_page()
{
?>
    <div class="wrap">
        <h1>Custom Post Type Archive Pages</h1>

        <p>This plugin allows you to select a Page top act as the archive page for each of your custom post types, similar to how you can set a Page to act as the Posts page for your regular blog posts.</p>

        <p><strong>Note:</strong> This plugin is tailored for custom post types with the <code>has_archive</code> parameter set to <code>true</code>. They will not show up in the list below if they do not have this parameter set.</p>

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

// Display the settings fields for associating a page with each post type on the admin page
function display_post_type_page_for_posts_fields($post_type)
{
    $option_name = 'cpt_page_for_posts_' . $post_type;
    $current_value = get_option($option_name);
    $pages = get_pages();
    $post_type_object = get_post_type_object($post_type);
?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr($option_name); ?>">
                    <?php echo esc_html($post_type_object->labels->singular_name); ?>
                </label>
            </th>
            <td>
                <select id="<?php echo esc_attr($option_name); ?>" name="<?php echo esc_attr($option_name); ?>">
                    <option value="0" <?php selected($current_value, 0); ?>>None (Default)</option>
                    <?php foreach ($pages as $page) : ?>
                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($current_value, $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
<?php
}

// Load the settings page
function load_page_for_posts_settings_page()
{
    add_options_page(
        'Custom Post Type Archive Pages',
        'CPT Archive Pages',
        'manage_options',
        'custom_post_type_page_for_posts',
        'custom_post_type_page_for_posts_page'
    );
}
add_action('admin_menu', 'load_page_for_posts_settings_page');
add_action('admin_init', 'add_settings_sections');

function get_cpt_page_for_posts($post_type)
{
    if (empty($post_type)) {
        return false;
    }

    return get_option('cpt_page_for_posts_' . $post_type);
}

<?php

/**
 * Main Functions
 */

// Register the setting for each post type
function cpfp_register_post_type_page_settings()
{
    $post_types = get_post_types(['has_archive' => true], 'objects');

    foreach ($post_types as $post_type) {
        register_setting('cpfp_custom_post_type_page_for_posts', 'cpt_page_for_posts_' . $post_type->name);
    }

    // Register the setting for the checkbox
    register_setting('cpfp_custom_post_type_page_for_posts', 'cpt_page_for_posts_enable_uninstall_cleanup');

    add_option('cpt_page_for_posts_enable_uninstall_cleanup', 1);
}
add_action('admin_init', 'cpfp_register_post_type_page_settings');

// Add settings sections and fields
function cpfp_add_settings_sections()
{
    $post_types = get_post_types(['has_archive' => true], 'objects');

    usort($post_types, function ($a, $b) {
        return strcmp($a->labels->singular_name, $b->labels->singular_name);
    });

    add_settings_section(
        'cpt_page_for_posts_section_cpt_archives',
        '',
        function () use ($post_types) {
            cpfp_display_post_type_page_fields($post_types);
        },
        'cpfp_custom_post_type_page_for_posts'
    );

    add_settings_section(
        'cpt_page_for_posts_section_uninstall',
        'Plugin Settings',
        'cpfp_display_uninstall_checkbox',
        'cpfp_custom_post_type_page_for_posts'
    );
}
add_action('admin_init', 'cpfp_add_settings_sections');

// Create an admin page for setting page associations
function cpfp_custom_post_type_page_page()
{
?>
    <div class="wrap">
        <h1><?php esc_html_e('Custom Post Type Archive Pages', 'cpt-page-for-posts'); ?></h1>
        <form method="post" action="options.php" style="margin-top: 20px" class="metabox-holder">
            <?php
            settings_fields('cpfp_custom_post_type_page_for_posts');
            do_settings_sections('cpfp_custom_post_type_page_for_posts');
            submit_button();
            ?>
        </form>
    </div>
<?php
}
add_action('admin_menu', 'cpfp_load_page_for_posts_settings_page');

// Display the settings fields for associating a page with each post type
function cpfp_display_post_type_page_fields($post_types)
{ ?>
    <div id="cpt_archive_pages_metabox" class="postbox" style="margin-bottom: 50px">
        <div class="postbox-header">
            <h2 class="hndle ui-sortable-handle" style="cursor: default;">
                <span><?php esc_html_e('Custom Post Types', 'cpt-page-for-posts'); ?></span>
            </h2>
        </div>
        <div class="inside">
            <p>Select a Page to act as the archive page for each custom post type. Only public post types with an <code>has_archive</code> enabled will be displayed here.</p>
            <p>The archive pages can be accessed by using the <code>cpt_page_for_posts($post_type)</code> function.</p>
            <?php
            if ($post_types) {
                foreach ($post_types as $post_type) {
                    $post_type = $post_type->name;
                    $option_name = 'cpt_page_for_posts_' . $post_type;
                    $current_value = get_option($option_name);
                    $page_for_posts = get_option('page_for_posts');
                    $front_page = get_option('page_on_front');
                    $pages = get_pages(array(
                        'sort_order' => 'ASC',
                        'sort_column' => 'post_title',
                        'hierarchical' => 0,
                        'exclude' => array($page_for_posts, $front_page)
                    ));
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
            } else { ?>
                <p style="text-align:center;padding: 50px 0;"><?php esc_html_e('No public custom post types with an archive page were found.', 'cpt-page-for-posts'); ?></p>
            <?php } ?>
        </div>
    </div>
<?php
}

function cpfp_display_uninstall_checkbox()
{
    $option_name = 'cpt_page_for_posts_enable_uninstall_cleanup';
    $enabled = get_option($option_name, 0);
?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr($option_name); ?>">
                        <?php esc_html_e('Enable Uninstall Cleanup', 'cpt-page-for-posts'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" id="<?php echo esc_attr($option_name); ?>" name="<?php echo esc_attr($option_name); ?>" value="1" <?php checked($enabled, 1); ?>>
                    <span class="description">
                        <?php esc_html_e('If this box is checked, all settings will be deleted when the plugin is uninstalled.', 'cpt-page-for-posts'); ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
<?php
}

// Load the settings page
function cpfp_load_page_for_posts_settings_page()
{
    add_options_page(
        esc_html__('Custom Post Type Archive Pages', 'cpt-page-for-posts'),
        esc_html__('CPT Archive Pages', 'cpt-page-for-posts'),
        'manage_options',
        'cpfp_custom_post_type_page_for_posts',
        'cpfp_custom_post_type_page_page'
    );
}
add_action('admin_menu', 'cpfp_load_page_for_posts_settings_page');

// Uninstall cleanup
function custom_post_type_page_uninstall()
{
    // Check if uninstall cleanup is enabled
    $enable_cleanup = get_option('cpt_page_for_posts_enable_uninstall_cleanup', 0);

    // Delete options only if uninstall cleanup is enabled
    if ($enable_cleanup) {
        $post_types = get_post_types(['has_archive' => true], 'objects');

        foreach ($post_types as $post_type) {
            $option_name = 'cpt_page_for_posts_' . $post_type->name;
            delete_option($option_name);
        }
    }
}

// Add post state to the page list table
add_filter('display_post_states', 'cpfp_add_post_state', 10, 2);
function cpfp_add_post_state($post_states, $post)
{
    $post_types = get_post_types(['has_archive' => true], 'objects');
    $archive_names = array();

    foreach ($post_types as $post_type) {
        $option_name = 'cpt_page_for_posts_' . $post_type->name;
        $page_id = get_option($option_name);

        if ($page_id == $post->ID) {
            $archive_names[] = $post_type->labels->name . ' Page';
        }
    }

    if (!empty($archive_names)) {
        $post_states['cpt_page_for_posts'] = implode(', ', $archive_names);
    }

    return $post_states;
}

// Display a message above the content editor but below the title when editing the archive page
add_action('edit_form_after_title', 'cpfp_display_edit_page_message');

function cpfp_display_edit_page_message($post)
{
    $post_types = get_post_types(['has_archive' => true], 'objects');
    $archive_names = array();
    $admin_page_link = admin_url('options-general.php?page=cpfp_custom_post_type_page_for_posts');

    foreach ($post_types as $post_type) {
        $option_name = 'cpt_page_for_posts_' . $post_type->name;
        $page_id = get_option($option_name);

        if ($page_id == $post->ID) {
            $archive_names[] = $post_type->labels->name;
        }
    }

    // display notice for each
    if (!empty($archive_names)) {
        foreach ($archive_names as $archive_name) {
            echo '<div class="notice notice-warning is-dismissible inline"><p>';
            printf(
                __('You are currently editing the page that shows your latest %s.', 'cpt-page-for-posts'),
                esc_html(strtolower($archive_name))
            );

            printf(
                ' <a href="%s" style="text-decoration:none;">%s</a>',
                esc_url($admin_page_link),
                __('<span class="dashicons dashicons-info" style="transform:scale(0.8)"></span>', 'cpt-page-for-posts')
            );
            echo '</p>';
            echo '</div>';
        }
    }
};

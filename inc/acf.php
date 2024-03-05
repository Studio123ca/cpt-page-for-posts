<?php

/**
 * ACF Integration
 */

// Use ACF_Location to add archive pages to the field group locations conditional
class My_ACF_Location_Archive_Pages extends ACF_Location
{

    public function initialize()
    {
        $this->name = 'archive_page';
        $this->label = __('Archive Page', 'acf');
        $this->category = 'page';
        $this->object_type = 'post';
    }

    public function get_values($rule)
    {
        $choices = array();

        $post_types = get_post_types(['has_archive' => true], 'objects');

        foreach ($post_types as $post_type) {
            $post_type_object = get_post_type_object($post_type->name);
            $option_label = $post_type_object->labels->name . ' Page';
            $option_name = 'cpt_page_for_posts_' . $post_type->name;
            $page_id = get_option($option_name);

            if ($page_id) {
                $choices[$page_id] = $option_label;
            }
        }

        return $choices;
    }

    public function match($rule, $screen, $field_group)
    {
        if (isset($screen['post_id'])) {
            $post_id = $screen['post_id'];
        } else {
            return false;
        }

        $post_types = get_post_types(['has_archive' => true], 'objects');

        foreach ($post_types as $post_type) {
            $option_name = 'cpt_page_for_posts_' . $post_type->name;
            $page_id = get_option($option_name);

            if ($page_id == $post_id) {
                if ($rule['operator'] == '==') {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }
}

// Add the location to the ACF Location rules
add_action('acf/init', 'my_acf_init_location_types');
function my_acf_init_location_types()
{
    // Check function exists, then include and register the custom location type class.
    if (function_exists('acf_register_location_type')) {
        acf_register_location_type('My_ACF_Location_Archive_Pages');
    }
}

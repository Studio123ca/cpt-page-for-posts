# Custom Post Type Archive Pages Plugin for WordPress

## Description

The **Custom Post Type Archive Pages** plugin allows you to select a specific page as the main archive page for custom post types in WordPress, similar to how you can select a page for the main blog posts archive.

## Installation

1. Download the plugin ZIP file.
2. Upload the ZIP file via the WordPress admin dashboard or extract it into the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

### Setting Archive Pages

1. Navigate to the **Custom Post Type Archive Pages** settings page under the 'Settings' menu in the WordPress admin dashboard.
2. Select a page for each custom post type from the provided dropdown list. Please note that the post type must have the `has_archive` argument set to `true` in order to be displayed in the list.
3. Save your changes.

### Retrieving Data from Archive Pages
Utilize the cpt_page_for_posts($post_type) function to obtain the page ID linked to a particular post type. This function proves beneficial when extracting data from archives within your theme customization, similar to how you would use the `get_option('page_for_posts')` function to obtain the page ID linked to the main blog posts archive.

```php
<?php
$archive_page_id = cpt_page_for_posts('your_post_type');

// Use $page_id to customize the display of the archive for 'your_post_type'.
?>
```
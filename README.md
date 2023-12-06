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
2. Select a page for each custom post type from the provided dropdown list.
3. Save your changes.

### Displaying Archive Pages

Use the `cpt_page_for_posts($post_type)` function to retrieve the page ID associated with a specific post type. This function can be useful when customizing the display of archives in your theme.

```php
<?php
$archive_page_id = cpt_page_for_posts('your_post_type');

// Use $page_id to customize the display of the archive for 'your_post_type'.
?>
```
<?php
/*
Plugin Name: Meta for blogs
Plugin URI: http://www.beapi.fr
Description: Add table for blog meta and some methods for use it. Inspiration from core post meta.
Author: Be API
Author URI: http://beapi.fr
Version: 1.0
*/

// 1. Setup table name for blog meta
global $wpdb, $table_prefix;
$wpdb->ms_global_tables[] = 'blogmeta';
$wpdb->blogmeta = $wpdb->base_prefix . 'blogmeta';

// 2. Library
require( dirname(__FILE__) . '/inc/functions.meta.php' );
require( dirname(__FILE__) . '/inc/functions.meta.ext.php' );

// 3. Functions
require( dirname(__FILE__) . '/inc/functions.hook.php' );
require( dirname(__FILE__) . '/inc/functions.inc.php' );
require( dirname(__FILE__) . '/inc/functions.tpl.php' );

// 4. Meta API hook
register_activation_hook( __FILE__, 'install_table_blogmeta' );
add_action ( 'delete_blog', 'remove_blogmeta_during_delete', 10, 2 );
?>
<?php
/**
 * This function is called when the plugin is activated, it allow to create the SQL table.
 *
 * @return void
 * @author Amaury Balmer
 */
function install_table_blogmeta() {
	global $wpdb;
	
	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";
	
	// Add one library admin function for next function
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	// Try to create the meta table
	return maybe_create_table( $wpdb->blogmeta , "CREATE TABLE " . $wpdb->blogmeta . " (
			`meta_id` int(20) NOT NULL auto_increment,
			`blog_id` INT( 20 ) NOT NULL ,
			`meta_key` VARCHAR( 255 ) NOT NULL ,
			`meta_value` LONGTEXT NOT NULL,
			PRIMARY KEY  (`meta_id`),
			KEY `blog_id` (`blog_id`),
			KEY `meta_key` (`meta_key`)
		) $charset_collate;" );
}
?>
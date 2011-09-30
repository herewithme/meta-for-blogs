<?php
/**
 * Delete a blog by key/value
 *
 * @param string $key 
 * @param string $value 
 * @return boolean
 * @author Amaury Balmer
 */
function delete_blogmeta_by_key_and_value($key = '', $value = '') {
	global $wpdb;

	// expected_slashed ($key, $value)
	$key 	= stripslashes( $key );
	$value 	= stripslashes( $value );

	// Meta exist ?
	if ( empty( $value ) )
		$blog_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT blog_id FROM $wpdb->blogmeta WHERE meta_key = %s", $key ) );
	else
		$blog_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT blog_id FROM $wpdb->blogmeta WHERE meta_key = %s AND meta_value = %s", $key, $value ) );
	
	if ( $blog_ids ) {
		// Get meta id to delete
		if ( empty( $value ) )
			$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_id FROM $wpdb->blogmeta WHERE meta_key = %s", $key ) );
		else
			$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_id FROM $wpdb->blogmeta WHERE meta_key = %s AND meta_value = %s", $key, $value ) );

		$in = implode( ',', array_fill(1, count($meta_ids), '%d'));
		
		do_action( 'delete_blog_meta', $meta_ids );
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->blogmeta WHERE meta_id IN ($in)", $meta_ids ));
		do_action( 'delete_blog_meta', $meta_ids );
		
		// Delete cache
		foreach ( $blog_ids as $blog_id )
			wp_cache_delete($blog_id, 'blog_meta');
		
		return true;
	}
	
	return false;
}

/**
 * Delete everything from blog ID matching $blog_id
 *
 * @param integer $blog_id What to search for when deleting
 * @return bool Whether the blog meta key was deleted from the database
 */
function delete_meta_by_blog_id( $blog_id = 0 ) {
	global $wpdb;
	if ( $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->blogmeta WHERE blog_id = %s", (int) $blog_id)) ) {
		wp_cache_delete($blog_id, 'blog_meta');
		return true;
	}
	return false;
}

/**
 * Retrieve blog ID by meta_key/meta_value
 *
 * @param string $meta_key meta key
 * @param string $meta_value meta value
 * @param boolean $single flag for return one line or one col
 * @return mixed {@internal Missing Description}}
 */
function get_blog_id_from_meta( $meta_key = '', $meta_value = '', $single = false ) {
	global $wpdb;
	
	$key = md5( $meta_key . $meta_value . $single );
	
	$result = wp_cache_get( $key, 'blog_meta' );
	if ( false === $result ) {
		if ( $single == true ) {
			$result = (int) $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $meta_value ) );
		} else {
			$result = $wpdb->get_col( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $meta_value ) );
		}
		
		wp_cache_set( $key, $result, 'blog_meta' );
	}
	
	return $result;
}

/**
 * Allow to get meta datas for a specificied key.
 *
 * @param string $key
 * @return array
 */
function get_blogmeta_by_key( $meta_key = '' ) {
	global $wpdb;
	
	$key = md5( 'key-'.$meta_key );
	
	$result = wp_cache_get( $key, 'blog_meta' );
	if ( false === $result ) {
	 	$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->blogmeta WHERE meta_key = %s", $meta_key ) );
		wp_cache_set( $key, $result, 'blog_meta' );
	}

	return $result;
}
?>
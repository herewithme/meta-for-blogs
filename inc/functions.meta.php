<?php
/**
 * Add metadata for blog
 *
 * @param int $blog_id blog ID
 * @param string $key {@internal Missing Description}}
 * @param mixed $value {@internal Missing Description}}
 * @param bool $unique whether to check for a value with the same key
 * @return bool {@internal Missing Description}}
 */
function add_blog_meta( $blog_id = 0, $meta_key = '', $meta_value = '', $unique = false ) {
	return add_metadata( 'blog', $blog_id, $meta_key, $meta_value, $unique );
}

/**
 * Delete blog metadata
 *
 * @param int $blog_id blog ID
 * @param string $key {@internal Missing Description}}
 * @param mixed $value {@internal Missing Description}}
 * @return bool {@internal Missing Description}}
 */
function delete_blog_meta( $blog_id = 0, $key = '', $value = '', $delete_all = false ) {
	return delete_metadata( 'blog', $blog_id, $key, $value, $delete_all );
}

/**
 * Get a blog meta field
 *
 * @param int $blog_id blog ID
 * @param string $meta_key The meta key to retrieve
 * @param bool $single Whether to return a single value
 * @return mixed {@internal Missing Description}}
 */
function get_blog_meta($blog_id, $meta_key = '', $single = false) {
	return get_metadata( 'blog', $blog_id, $meta_key, $single );
}

/**
 * Update a blog meta field
 *
 * @param int $blog_id blog ID
 * @param string $key {@internal Missing Description}}
 * @param mixed $value {@internal Missing Description}}
 * @param mixed $prev_value previous value (for differentiating between meta fields with the same key and blog ID)
 * @return bool {@internal Missing Description}}
 */
function update_blog_meta($blog_id, $meta_key, $meta_value, $prev_value = '') {
	return update_metadata( 'blog', $blog_id, $meta_key, $meta_value, $prev_value ); 
}

/**
 * Updates metadata cache for list of blog IDs.
 *
 * Performs SQL query to retrieve the metadata for the blog IDs and updates the
 * metadata cache for the blog. Therefore, the functions, which call this
 * function, do not need to perform SQL queries on their own.
 *
 * @param array $blog_ids List of blog IDs.
 * @return bool|array Returns false if there is nothing to update or an array of metadata.
 */
function update_blog_meta_cache($blog_ids) {
	return update_meta_cache('blog', $blog_ids);
}

/**
 * Retrieve blog custom fields
 *
 * @param int $blog_id blog ID
 * @return array {@internal Missing Description}}
 */
function get_blog_custom($blog_id = 0) {
	global $id;

	if ( !$blog_id )
		$blog_id = (int) $id;

	$blog_id = (int) $blog_id;
	
	if ( ! wp_cache_get($blog_id, 'blog_meta') )
		update_blog_meta_cache($blog_id);

	return wp_cache_get($blog_id, 'blog_meta');
}

/**
 * Retrieve meta field names for a blog.
 *
 * If there are no meta fields, then nothing (null) will be returned.
 *
 * @param int $blog_id blog ID
 * @return array|null Either array of the keys, or null if keys could not be retrieved.
 */
function get_blog_custom_keys( $blog_id = 0 ) {
	$custom = get_blog_custom( $blog_id );

	if ( !is_array($custom) )
		return false;

	if ( $keys = array_keys($custom) )
		return $keys;
		
	return false;
}

/**
 * Retrieve values for a custom blog field.
 *
 * The parameters must not be considered optional. All of the blog meta fields
 * will be retrieved and only the meta field key values returned.
 *
 * @param string $key Meta field key.
 * @param int $blog_id blog ID
 * @return array Meta field values.
 */
function get_blog_custom_values( $key = '', $blog_id = 0 ) {
	if ( !$key )
		return null;

	$custom = get_blog_custom($blog_id);

	return isset($custom[$key]) ? $custom[$key] : null;
}

/**
 * Delete everything from blog meta matching $blog_meta_key
 *
 * @uses $wpdb
 *
 * @param string $blog_meta_key What to search for when deleting
 * @return bool Whether the blog meta key was deleted from the database
 */
function delete_blog_meta_by_key( $blog_meta_key = '' ) {
	if ( !$blog_meta_key )
		return false;
	
	global $wpdb;
	
	$blog_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT blog_id FROM $wpdb->blogmeta WHERE meta_key = %s", $blog_meta_key));
	if ( $blog_ids ) {
		$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->blogmeta WHERE meta_key = %s", $blog_meta_key ) );
		$in = implode( ',', array_fill(1, count($meta_ids), '%d'));
		
		do_action( 'delete_blog_meta', $meta_ids );
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->blogmeta WHERE meta_id IN ($in)", $meta_ids ));
		do_action( 'delete_blog_meta', $meta_ids );
		
		foreach ( $blog_ids as $blog_id )
			wp_cache_delete($blog_id, 'blog_meta');
			
		return true;
	}
	return false;
}
?>
<?php
/**
 * Meta function called by hook when a blog are delete.
 * 
 * @param integer $blog_id
 * @param boolean $drop
 * @return boolean
 */
function remove_blogmeta_during_delete( $blog_id, $drop ) {
	if ( $drop == true )
		return delete_meta_by_blog_id( $blog_id );
	
	return false;
}
?>
<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 *

 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin options and clean up database
 */
function asm_uninstall_cleanup() {
	// Check if we should remove data on uninstall
	$remove_data = get_option( 'asm_remove_data_on_uninstall', false );
	
	if ( $remove_data ) {
		// Delete global settings
		delete_option( 'asm_global_settings' );
		delete_option( 'asm_version' );
		delete_option( 'asm_remove_data_on_uninstall' );
		
		// Delete all post meta for page-specific scripts
		global $wpdb;
		
		// Delete header scripts meta
		$wpdb->delete(
			$wpdb->postmeta,
			array( 'meta_key' => '_asm_header_scripts' ),
			array( '%s' )
		);
		
		// Delete body scripts meta
		$wpdb->delete(
			$wpdb->postmeta,
			array( 'meta_key' => '_asm_body_scripts' ),
			array( '%s' )
		);
		
		// Delete footer scripts meta
		$wpdb->delete(
			$wpdb->postmeta,
			array( 'meta_key' => '_asm_footer_scripts' ),
			array( '%s' )
		);
		
		// Clean up any orphaned meta
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_asm_%'" );
		
		// Clear any cached data
		wp_cache_flush();
	}
}

/**
 * Multisite cleanup
 */
function asm_uninstall_multisite_cleanup() {
	if ( is_multisite() ) {
		global $wpdb;
		
		// Get all blog IDs
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			asm_uninstall_cleanup();
			restore_current_blog();
		}
	} else {
		asm_uninstall_cleanup();
	}
}

// Run the cleanup
asm_uninstall_multisite_cleanup();

// Clear any remaining cache
if ( function_exists( 'wp_cache_flush' ) ) {
	wp_cache_flush();
}

// Clear object cache if available
if ( function_exists( 'wp_cache_delete' ) ) {
	wp_cache_delete( 'asm_global_settings', 'options' );
}
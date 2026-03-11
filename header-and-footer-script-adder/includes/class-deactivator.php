<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/advanced-scripts-manager
 * @since      2.0.3
 *
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.3
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/includes
 * @author     Header Footer Script Adder Team
 */
class ASM_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.3
	 */
	public static function deactivate() {
		// Flush rewrite rules to clean up
		flush_rewrite_rules();
		
		// Clear any cached data
		wp_cache_flush();
	}
}
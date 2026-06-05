<?php
/**
 * Helper utilities for Header Footer Script Adder Pro
 *
 * @package    HeaderFooterScriptAdderPro
 * @subpackage HeaderFooterScriptAdderPro/includes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class ASM_Pro_Helpers {

	/**
	 * Detect visitor device type
	 * 
	 * @return string 'mobile'|'tablet'|'desktop'
	 */
	public static function get_device_type() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return 'desktop';
		}

		$ua = strtolower( $_SERVER['HTTP_USER_AGENT'] );

		// Tablet detection
		if ( preg_match( '/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua ) ) {
			return 'tablet';
		}

		// Mobile detection
		if ( preg_match( '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $ua ) || 
			 preg_match( '/(sdk|tosh|palm|ipad|iphone|ipod|blackberry|opera mini|opera mobi|windows phone|iemobile|fennec)/i', $ua ) ) {
			return 'mobile';
		}

		return 'desktop';
	}
}

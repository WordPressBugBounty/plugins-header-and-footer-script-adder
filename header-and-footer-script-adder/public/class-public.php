<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/advanced-scripts-manager
 * @since      2.0.3
 *
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/public
 * @author     Header Footer Script Adder Team
 */
class ASM_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.3
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.3
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The global settings for the plugin.
	 *
	 * @since    2.0.3
	 * @access   private
	 * @var      array    $settings    The global settings.
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.3
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings = get_option('asm_global_settings', array());
	}

	/**
	 * Inject header scripts
	 *
	 * @since    2.0.3
	 */
	public function inject_header_scripts()
	{
		// Inject global header scripts
		$this->inject_global_scripts('header');

		// Inject page-specific header scripts
		$this->inject_page_scripts('header');
	}

	/**
	 * Inject body scripts
	 *
	 * @since    2.0.3
	 */
	public function inject_body_scripts()
	{
		// Inject global body scripts
		$this->inject_global_scripts('body');

		// Inject page-specific body scripts
		$this->inject_page_scripts('body');
	}

	/**
	 * Inject footer scripts
	 *
	 * @since    2.0.3
	 */
	public function inject_footer_scripts()
	{
		// Inject global footer scripts
		$this->inject_global_scripts('footer');

		// Inject page-specific footer scripts
		$this->inject_page_scripts('footer');
	}

	/**
	 * Inject global scripts based on conditions
	 *
	 * @since    2.0.3
	 * @param    string    $location    The script location (header, body, footer)
	 */
	private function inject_global_scripts($location)
	{
		$scripts_key = $location . '_scripts';
		$condition_key = $location . '_condition';

		// Get scripts and condition
		$scripts = isset($this->settings[$scripts_key]) ? $this->settings[$scripts_key] : '';
		$condition = isset($this->settings[$condition_key]) ? $this->settings[$condition_key] : 'sitewide';

		// Check if scripts should be loaded based on condition
		if (empty($scripts) || ! $this->should_load_scripts($condition, $location)) {
			return;
		}

		// Output scripts with proper formatting
		echo "\n<!-- Advanced Scripts Manager - Global " . ucfirst($location) . " Scripts -->\n";
		echo $scripts . "\n";
		echo "<!-- End Advanced Scripts Manager - Global " . ucfirst($location) . " Scripts -->\n";
	}

	/**
	 * Inject page-specific scripts
	 *
	 * @since    2.0.3
	 * @param    string    $location    The script location (header, body, footer)
	 */
	private function inject_page_scripts($location)
	{
		// Only load on singular posts/pages
		if (! is_singular()) {
			return;
		}

		$post_id = get_the_ID();
		if (! $post_id) {
			return;
		}

		$meta_key = '_asm_' . $location . '_scripts';
		$scripts = get_post_meta($post_id, $meta_key, true);

		if (empty($scripts)) {
			return;
		}

		// Output scripts with proper formatting
		echo "\n<!-- Advanced Scripts Manager - Page-Specific " . ucfirst($location) . " Scripts -->\n";
		echo $scripts . "\n";
		echo "<!-- End Advanced Scripts Manager - Page-Specific " . ucfirst($location) . " Scripts -->\n";
	}

	/**
	 * Check if scripts should be loaded based on condition
	 *
	 * @since    2.0.3
	 * @param    string    $condition    The loading condition
	 * @param    string    $location     The script location (header, body, footer)
	 * @return   bool                   Whether scripts should be loaded
	 */
	private function should_load_scripts($condition, $location = '')
	{
		switch ($condition) {
			case 'homepage':
				return is_front_page();

			case 'singular':
				return is_singular();

			case 'specific':
				return $this->should_load_specific_scripts($location);

			case 'archive':
				return is_archive() || is_category() || is_tag() || is_author() || is_date() || is_search();

			case 'sitewide':
			default:
				return true;
		}
	}

	/**
	 * Check if scripts should be loaded for specific posts/pages
	 *
	 * @since    2.0.3
	 * @param    string    $location    The script location (header, body, footer)
	 * @return   bool                  Whether scripts should be loaded
	 */
	private function should_load_specific_scripts($location)
	{
		if (! is_singular()) {
			return false;
		}

		$post_id = get_the_ID();
		if (! $post_id) {
			return false;
		}

		$specific_posts_key = $location . '_specific_posts';
		$specific_posts = isset($this->settings[$specific_posts_key]) ? $this->settings[$specific_posts_key] : array();

		return in_array($post_id, (array) $specific_posts);
	}

	/**
	 * Get the current post ID safely
	 *
	 * @since    2.0.3
	 * @return   int|false    The post ID or false if not available
	 */
	private function get_current_post_id()
	{
		global $post;

		if (is_singular() && isset($post->ID)) {
			return $post->ID;
		}

		return false;
	}

	/**
	 * Sanitize output for security
	 *
	 * @since    2.0.3
	 * @param    string    $content    The content to sanitize
	 * @return   string               The sanitized content
	 */
	private function sanitize_output($content)
	{
		// Define allowed HTML tags for output
		$allowed_html = array(
			'script' => array(
				'type' => array(),
				'src' => array(),
				'async' => array(),
				'defer' => array(),
				'crossorigin' => array(),
				'integrity' => array(),
				'id' => array(),
				'data-*' => true,
				'referrerpolicy' => array(),
				'nonce' => array(),
			),
			'style' => array(
				'type' => array(),
				'media' => array(),
				'id' => array(),
				'scoped' => array(),
			),
			'link' => array(
				'rel' => array(),
				'href' => array(),
				'type' => array(),
				'media' => array(),
				'crossorigin' => array(),
				'integrity' => array(),
				'hreflang' => array(),
				'sizes' => array(),
				'as' => array(),
				'title' => array(),
				'referrerpolicy' => array(),
				'fetchpriority' => array(), // ✅ modern attribute
				'color' => array(), // for theme-color links
			),
			'meta' => array(
				'name' => array(),
				'content' => array(),
				'property' => array(),
				'http-equiv' => array(),
				'charset' => array(),
				'itemprop' => array(),
				'content-language' => array(),
			),
			'noscript' => array(
				'id' => array(),
			),
			'div' => array(
				'id' => array(),
				'class' => array(),
				'data-*' => true,
				'style' => array(),
				'role' => array(),
				'aria-*' => true,
			),
			'span' => array(
				'id' => array(),
				'class' => array(),
				'data-*' => true,
				'style' => array(),
				'aria-*' => true,
			),
			'iframe' => array(
				'src' => array(),
				'width' => array(),
				'height' => array(),
				'frameborder' => array(),
				'allow' => array(),
				'allowfullscreen' => array(),
				'referrerpolicy' => array(),
				'loading' => array(),
				'title' => array(),
				'sandbox' => array(),
			),
			'img' => array(
				'src' => array(),
				'srcset' => array(),
				'sizes' => array(),
				'alt' => array(),
				'width' => array(),
				'height' => array(),
				'loading' => array(),
				'decoding' => array(),
				'referrerpolicy' => array(),
				'crossorigin' => array(),
				'fetchpriority' => array(),
			),
			'base' => array(
				'href' => array(),
				'target' => array(),
			),
			'picture' => array(),
			'source' => array(
				'src' => array(),
				'srcset' => array(),
				'type' => array(),
				'sizes' => array(),
				'media' => array(),
			),
		);


		return wp_kses($content, $allowed_html);
	}

	/**
	 * Check if we're in admin area or doing AJAX
	 *
	 * @since    2.0.3
	 * @return   bool    Whether we should skip script injection
	 */
	private function should_skip_injection()
	{
		// Skip in admin area
		if (is_admin()) {
			return true;
		}

		// Skip for AJAX requests
		if (defined('DOING_AJAX') && DOING_AJAX) {
			return true;
		}

		// Skip for REST API requests
		if (defined('REST_REQUEST') && REST_REQUEST) {
			return true;
		}

		// Skip for cron jobs
		if (defined('DOING_CRON') && DOING_CRON) {
			return true;
		}

		// Skip for feeds
		if (is_feed()) {
			return true;
		}

		// Skip for robots.txt
		if (is_robots()) {
			return true;
		}

		// Skip for trackbacks
		if (is_trackback()) {
			return true;
		}

		return false;
	}
}

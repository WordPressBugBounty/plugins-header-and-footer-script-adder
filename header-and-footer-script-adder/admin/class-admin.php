<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/advanced-scripts-manager
 * @since      2.0.0
 *
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/admin
 * @author     Header Footer Script Adder Team
 */
class ASM_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles()
	{
		// Only load on our plugin pages
		$screen = get_current_screen();
		if (! $screen || strpos($screen->id, 'custom-scripts') === false) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name,
			ASM_PLUGIN_URL . 'admin/css/admin-styles.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts()
	{
		// Only load on our plugin pages
		$screen = get_current_screen();
		if (! $screen || strpos($screen->id, 'custom-scripts') === false) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			ASM_PLUGIN_URL . 'admin/js/admin-scripts.js',
			array('jquery', 'wp-codemirror'),
			$this->version,
			false
		);

		// Enqueue code editor
		wp_enqueue_code_editor(array('type' => 'text/html'));
	}

	/**
	 * Add admin menu
	 *
	 * @since    2.0.0
	 */
	public function add_admin_menu()
	{
		// Add top-level menu page
		add_menu_page(
			__('Header Footer Script Adder', 'header-footer-script-adder'),
			__('Header Footer Script Adder', 'header-footer-script-adder'),
			'manage_options',
			'custom-scripts',
			array($this, 'display_admin_page'),
			$this->get_menu_icon(),
			30
		);
	}

	/**
	 * Get the menu icon SVG
	 *
	 * @since    2.0.0
	 * @return   string    Base64 encoded SVG icon
	 */
	private function get_menu_icon()
	{
		return 'data:image/svg+xml;base64,' . base64_encode(
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
				<path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0L19.2 12l-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
			</svg>'
		);
	}

	/**
	 * Display the admin page
	 *
	 * @since    2.0.0
	 */
	public function display_admin_page()
	{
		include_once ASM_PLUGIN_DIR . 'admin/partials/admin-display.php';
	}

	/**
	 * Initialize settings
	 *
	 * @since    2.0.0
	 */
	public function init_settings()
	{
		// Register settings
		register_setting(
			'asm_settings_group',
			'asm_global_settings',
			array($this, 'sanitize_settings')
		);

		// Add settings section
		add_settings_section(
			'asm_global_section',
			__('Global Script Settings', 'advanced-scripts-manager'),
			array($this, 'settings_section_callback'),
			'asm_settings'
		);

		// Header scripts field
		add_settings_field(
			'header_scripts',
			__('Header Scripts', 'advanced-scripts-manager'),
			array($this, 'header_scripts_callback'),
			'asm_settings',
			'asm_global_section'
		);

		// Body scripts field
		add_settings_field(
			'body_scripts',
			__('Body Scripts', 'advanced-scripts-manager'),
			array($this, 'body_scripts_callback'),
			'asm_settings',
			'asm_global_section'
		);

		// Footer scripts field
		add_settings_field(
			'footer_scripts',
			__('Footer Scripts', 'advanced-scripts-manager'),
			array($this, 'footer_scripts_callback'),
			'asm_settings',
			'asm_global_section'
		);
	}

	/**
	 * Settings section callback
	 *
	 * @since    2.0.0
	 */
	public function settings_section_callback()
	{
		echo '<p style="float: left;">' . esc_html__('Configure global scripts that will be injected into your website.', 'advanced-scripts-manager') . '</p>';
		submit_button(__('Save Scripts', 'advanced-scripts-manager'), '', 'asm_save_button', false, array('style' => 'float: right;'));
	}

	/**
	 * Header scripts field callback
	 *
	 * @since    2.0.0
	 */
	public function header_scripts_callback()
	{
		$settings = get_option('asm_global_settings', array());
		$header_scripts = isset($settings['header_scripts']) ? $settings['header_scripts'] : '';
		$header_condition = isset($settings['header_condition']) ? $settings['header_condition'] : 'sitewide';

		echo '<div class="asm-field-group">';
		echo '<textarea id="header_scripts" name="asm_global_settings[header_scripts]" rows="10" cols="80" class="asm-code-editor">' . esc_textarea($header_scripts) . '</textarea>';
		echo '<p class="description">' . esc_html__('Scripts added here will be injected into the &lt;head&gt; section.', 'advanced-scripts-manager') . '</p>';

		echo '<label for="header_condition">' . esc_html__('Load on:', 'header-footer-script-adder') . '</label>';
		echo '<select id="header_condition" name="asm_global_settings[header_condition]" onchange="toggleSpecificSelection(this, \'header\')">';
		echo '<option value="sitewide"' . selected($header_condition, 'sitewide', false) . '>' . esc_html__('Sitewide', 'header-footer-script-adder') . '</option>';
		echo '<option value="homepage"' . selected($header_condition, 'homepage', false) . '>' . esc_html__('Homepage Only', 'header-footer-script-adder') . '</option>';
		echo '<option value="singular"' . selected($header_condition, 'singular', false) . '>' . esc_html__('Posts & Pages', 'header-footer-script-adder') . '</option>';
		echo '<option value="specific"' . selected($header_condition, 'specific', false) . '>' . esc_html__('Specific Posts/Pages', 'header-footer-script-adder') . '</option>';
		echo '<option value="archive"' . selected($header_condition, 'archive', false) . '>' . esc_html__('Archive Pages', 'header-footer-script-adder') . '</option>';
		echo '</select>';

		// Add specific post/page selection
		$header_specific_posts = isset($settings['header_specific_posts']) ? $settings['header_specific_posts'] : array();
		echo '<div id="header_specific_selection" class="asm-specific-selection" style="' . ($header_condition === 'specific' ? 'display:block;' : 'display:none;') . '">';
		echo '<label>' . esc_html__('Select specific posts/pages:', 'header-footer-script-adder') . '</label>';
		echo $this->render_post_page_selector('asm_global_settings[header_specific_posts][]', $header_specific_posts);
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Body scripts field callback
	 *
	 * @since    2.0.0
	 */
	public function body_scripts_callback()
	{
		$settings = get_option('asm_global_settings', array());
		$body_scripts = isset($settings['body_scripts']) ? $settings['body_scripts'] : '';
		$body_condition = isset($settings['body_condition']) ? $settings['body_condition'] : 'sitewide';

		echo '<div class="asm-field-group">';
		echo '<textarea id="body_scripts" name="asm_global_settings[body_scripts]" rows="10" cols="80" class="asm-code-editor">' . esc_textarea($body_scripts) . '</textarea>';
		echo '<p class="description">' . esc_html__('Scripts added here will be injected immediately after the opening &lt;body&gt; tag.', 'advanced-scripts-manager') . '</p>';

		echo '<label for="body_condition">' . esc_html__('Load on:', 'header-footer-script-adder') . '</label>';
		echo '<select id="body_condition" name="asm_global_settings[body_condition]" onchange="toggleSpecificSelection(this, \'body\')">';
		echo '<option value="sitewide"' . selected($body_condition, 'sitewide', false) . '>' . esc_html__('Sitewide', 'header-footer-script-adder') . '</option>';
		echo '<option value="homepage"' . selected($body_condition, 'homepage', false) . '>' . esc_html__('Homepage Only', 'header-footer-script-adder') . '</option>';
		echo '<option value="singular"' . selected($body_condition, 'singular', false) . '>' . esc_html__('Posts & Pages', 'header-footer-script-adder') . '</option>';
		echo '<option value="specific"' . selected($body_condition, 'specific', false) . '>' . esc_html__('Specific Posts/Pages', 'header-footer-script-adder') . '</option>';
		echo '<option value="archive"' . selected($body_condition, 'archive', false) . '>' . esc_html__('Archive Pages', 'header-footer-script-adder') . '</option>';
		echo '</select>';

		// Add specific post/page selection
		$body_specific_posts = isset($settings['body_specific_posts']) ? $settings['body_specific_posts'] : array();
		echo '<div id="body_specific_selection" class="asm-specific-selection" style="' . ($body_condition === 'specific' ? 'display:block;' : 'display:none;') . '">';
		echo '<label>' . esc_html__('Select specific posts/pages:', 'header-footer-script-adder') . '</label>';
		echo $this->render_post_page_selector('asm_global_settings[body_specific_posts][]', $body_specific_posts);
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Footer scripts field callback
	 *
	 * @since    2.0.0
	 */
	public function footer_scripts_callback()
	{
		$settings = get_option('asm_global_settings', array());
		$footer_scripts = isset($settings['footer_scripts']) ? $settings['footer_scripts'] : '';
		$footer_condition = isset($settings['footer_condition']) ? $settings['footer_condition'] : 'sitewide';

		echo '<div class="asm-field-group">';
		echo '<textarea id="footer_scripts" name="asm_global_settings[footer_scripts]" rows="10" cols="80" class="asm-code-editor">' . esc_textarea($footer_scripts) . '</textarea>';
		echo '<p class="description">' . esc_html__('Scripts added here will be injected before the closing &lt;/body&gt; tag.', 'advanced-scripts-manager') . '</p>';

		echo '<label for="footer_condition">' . esc_html__('Load on:', 'header-footer-script-adder') . '</label>';
		echo '<select id="footer_condition" name="asm_global_settings[footer_condition]" onchange="toggleSpecificSelection(this, \'footer\')">';
		echo '<option value="sitewide"' . selected($footer_condition, 'sitewide', false) . '>' . esc_html__('Sitewide', 'header-footer-script-adder') . '</option>';
		echo '<option value="homepage"' . selected($footer_condition, 'homepage', false) . '>' . esc_html__('Homepage Only', 'header-footer-script-adder') . '</option>';
		echo '<option value="singular"' . selected($footer_condition, 'singular', false) . '>' . esc_html__('Posts & Pages', 'header-footer-script-adder') . '</option>';
		echo '<option value="specific"' . selected($footer_condition, 'specific', false) . '>' . esc_html__('Specific Posts/Pages', 'header-footer-script-adder') . '</option>';
		echo '<option value="archive"' . selected($footer_condition, 'archive', false) . '>' . esc_html__('Archive Pages', 'header-footer-script-adder') . '</option>';
		echo '</select>';

		// Add specific post/page selection
		$footer_specific_posts = isset($settings['footer_specific_posts']) ? $settings['footer_specific_posts'] : array();
		echo '<div id="footer_specific_selection" class="asm-specific-selection" style="' . ($footer_condition === 'specific' ? 'display:block;' : 'display:none;') . '">';
		echo '<label>' . esc_html__('Select specific posts/pages:', 'header-footer-script-adder') . '</label>';
		echo $this->render_post_page_selector('asm_global_settings[footer_specific_posts][]', $footer_specific_posts);
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Sanitize settings
	 *
	 * @since    2.0.0
	 * @param    array    $input    The input array to sanitize
	 * @return   array              The sanitized array
	 */
	public function sanitize_settings($input)
	{
		$sanitized = array();

		// Define allowed HTML tags and attributes for scripts
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


		// Sanitize scripts while preserving valid HTML
		if (isset($input['header_scripts'])) {
			$sanitized['header_scripts'] = wp_kses($input['header_scripts'], $allowed_html);
		}

		if (isset($input['body_scripts'])) {
			$sanitized['body_scripts'] = wp_kses($input['body_scripts'], $allowed_html);
		}

		if (isset($input['footer_scripts'])) {
			$sanitized['footer_scripts'] = wp_kses($input['footer_scripts'], $allowed_html);
		}

		// Sanitize conditions
		$valid_conditions = array('sitewide', 'homepage', 'singular', 'specific', 'archive');

		if (isset($input['header_condition']) && in_array($input['header_condition'], $valid_conditions)) {
			$sanitized['header_condition'] = $input['header_condition'];
		} else {
			$sanitized['header_condition'] = 'sitewide';
		}

		if (isset($input['body_condition']) && in_array($input['body_condition'], $valid_conditions)) {
			$sanitized['body_condition'] = $input['body_condition'];
		} else {
			$sanitized['body_condition'] = 'sitewide';
		}

		if (isset($input['footer_condition']) && in_array($input['footer_condition'], $valid_conditions)) {
			$sanitized['footer_condition'] = $input['footer_condition'];
		} else {
			$sanitized['footer_condition'] = 'sitewide';
		}

		// Sanitize specific post selections
		if (isset($input['header_specific_posts']) && is_array($input['header_specific_posts'])) {
			$sanitized['header_specific_posts'] = array_map('intval', $input['header_specific_posts']);
		} else {
			$sanitized['header_specific_posts'] = array();
		}

		if (isset($input['body_specific_posts']) && is_array($input['body_specific_posts'])) {
			$sanitized['body_specific_posts'] = array_map('intval', $input['body_specific_posts']);
		} else {
			$sanitized['body_specific_posts'] = array();
		}

		if (isset($input['footer_specific_posts']) && is_array($input['footer_specific_posts'])) {
			$sanitized['footer_specific_posts'] = array_map('intval', $input['footer_specific_posts']);
		} else {
			$sanitized['footer_specific_posts'] = array();
		}

		return $sanitized;
	}



	/**
	 * Add meta boxes
	 *
	 * @since    2.0.0
	 */
	public function add_meta_boxes()
	{
		add_meta_box(
			'asm_page_scripts',
			__('Page-Specific Scripts - Header Footer Script Adder', 'advanced-scripts-manager'),
			array($this, 'meta_box_callback'),
			array('post', 'page'),
			'normal',
			'default'
		);
	}

	/**
	 * Meta box callback
	 *
	 * @since    2.0.0
	 * @param    WP_Post    $post    The post object
	 */
	public function meta_box_callback($post)
	{
		// Add nonce for security
		wp_nonce_field('asm_save_meta_box_data', 'asm_meta_box_nonce');

		// Get current values
		$header_scripts = get_post_meta($post->ID, '_asm_header_scripts', true);
		$body_scripts = get_post_meta($post->ID, '_asm_body_scripts', true);
		$footer_scripts = get_post_meta($post->ID, '_asm_footer_scripts', true);

		include_once ASM_PLUGIN_DIR . 'admin/partials/meta-box-display.php';
	}

	/**
	 * Save meta box data
	 *
	 * @since    2.0.0
	 * @param    int    $post_id    The post ID
	 */
	public function save_meta_box_data($post_id)
	{
		// Verify nonce
		if (
			! isset($_POST['asm_meta_box_nonce']) ||
			! wp_verify_nonce($_POST['asm_meta_box_nonce'], 'asm_save_meta_box_data')
		) {
			return;
		}

		// Check permissions
		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		// Skip autosaves/revisions
		if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
			return;
		}

		// ✅ Restrict to admins or users who can post unfiltered HTML
		$can_use_scripts = current_user_can('unfiltered_html') || current_user_can('manage_options');

		// Helper to sanitize script boxes
		$sanitize_field = function ($field_name) use ($post_id, $can_use_scripts) {
			if (isset($_POST[$field_name])) {
				$value = wp_unslash($_POST[$field_name]);
				if ($can_use_scripts) {
					// Admins: save as is (optional strip unwanted tags)
					update_post_meta($post_id, '_' . $field_name, $value);
				} else {
					// Non-admins: remove <script>, <iframe>, etc.
					$cleaned = wp_kses_post($value);
					update_post_meta($post_id, '_' . $field_name, $cleaned);
				}
			}
		};

		$sanitize_field('asm_header_scripts');
		$sanitize_field('asm_body_scripts');
		$sanitize_field('asm_footer_scripts');
	}


	/**
	 * Render post/page selector for specific targeting
	 *
	 * @since    2.0.0
	 * @param    string    $name           The input name attribute
	 * @param    array     $selected_ids   Array of selected post/page IDs
	 * @return   string                    The HTML for the selector
	 */
	private function render_post_page_selector($name, $selected_ids = array())
	{
		$output = '<div class="asm-post-page-selector">';

		// Get posts and pages
		$posts = get_posts(array(
			'post_type' => array('post', 'page'),
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));

		if (empty($posts)) {
			$output .= '<p>' . esc_html__('No posts or pages found.', 'header-footer-script-adder') . '</p>';
		} else {
			$output .= '<select name="' . esc_attr($name) . '" multiple="multiple" class="asm-multiselect" size="8">';

			$current_type = '';
			foreach ($posts as $post) {
				// Add optgroup headers
				if ($current_type !== $post->post_type) {
					if ($current_type !== '') {
						$output .= '</optgroup>';
					}
					$type_label = $post->post_type === 'post' ? __('Posts', 'header-footer-script-adder') : __('Pages', 'header-footer-script-adder');
					$output .= '<optgroup label="' . esc_attr($type_label) . '">';
					$current_type = $post->post_type;
				}

				$selected = in_array($post->ID, (array) $selected_ids) ? ' selected="selected"' : '';
				$output .= '<option value="' . esc_attr($post->ID) . '"' . $selected . '>';
				$output .= esc_html($post->post_title);
				$output .= '</option>';
			}
			$output .= '</optgroup></select>';

			$output .= '<p class="description">' . esc_html__('Hold Ctrl (Cmd on Mac) to select multiple items.', 'header-footer-script-adder') . '</p>';
		}

		$output .= '</div>';

		return $output;
	}
}
<?php
/**
 * Admin functionality for Header Footer Script Adder Pro
 *
 * @package    HeaderFooterScriptAdderPro
 * @subpackage HeaderFooterScriptAdderPro/admin
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class ASM_Pro_Admin {

	public function init() {
		add_action( 'init', array( $this, 'register_snippet_cpt' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'parent_file', array( $this, 'menu_highlight' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_asm_snippet', array( $this, 'save_snippet_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'register_pixel_settings' ) );
		
		// Customize CPT column headers
		add_filter( 'manage_asm_snippet_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_asm_snippet_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
	}

	/**
	 * Register the Snippet Custom Post Type
	 */
	public function register_snippet_cpt() {
		$labels = array(
			'name'               => _x( 'Snippets', 'post type general name', 'header-footer-script-adder-pro' ),
			'singular_name'      => _x( 'Snippet', 'post type singular name', 'header-footer-script-adder-pro' ),
			'menu_name'          => _x( 'Snippets', 'admin menu', 'header-footer-script-adder-pro' ),
			'name_admin_bar'     => _x( 'Snippet', 'add new on admin bar', 'header-footer-script-adder-pro' ),
			'add_new'            => _x( 'Add New', 'snippet', 'header-footer-script-adder-pro' ),
			'add_new_item'       => __( 'Add New Snippet', 'header-footer-script-adder-pro' ),
			'new_item'           => __( 'New Snippet', 'header-footer-script-adder-pro' ),
			'edit_item'          => __( 'Edit Snippet', 'header-footer-script-adder-pro' ),
			'view_item'          => __( 'View Snippet', 'header-footer-script-adder-pro' ),
			'all_items'          => __( 'All Snippets', 'header-footer-script-adder-pro' ),
			'search_items'       => __( 'Search Snippets', 'header-footer-script-adder-pro' ),
			'not_found'          => __( 'No snippets found.', 'header-footer-script-adder-pro' ),
			'not_found_in_trash' => __( 'No snippets found in Trash.', 'header-footer-script-adder-pro' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false, // Manually mapped under parent menu
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'asm_snippet' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title' )
		);

		register_post_type( 'asm_snippet', $args );
	}

	/**
	 * Map CPT menu under Free plugin parent menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'custom-scripts',
			__( 'All Snippets', 'header-footer-script-adder-pro' ),
			__( 'All Snippets', 'header-footer-script-adder-pro' ),
			'manage_options',
			'edit.php?post_type=asm_snippet'
		);

		add_submenu_page(
			'custom-scripts',
			__( 'Add New Snippet', 'header-footer-script-adder-pro' ),
			__( 'Add New Snippet', 'header-footer-script-adder-pro' ),
			'manage_options',
			'post-new.php?post_type=asm_snippet'
		);

		add_submenu_page(
			'custom-scripts',
			__( 'Pixel Integrations', 'header-footer-script-adder-pro' ),
			__( 'Pixel Integrations', 'header-footer-script-adder-pro' ),
			'manage_options',
			'asm-pixels',
			array( $this, 'display_pixels_page' )
		);
	}

	/**
	 * Keep the parent menu highlighted when editing snippets
	 */
	public function menu_highlight( $parent_file ) {
		global $current_screen;
		if ( isset( $current_screen->post_type ) && 'asm_snippet' === $current_screen->post_type ) {
			return 'custom-scripts';
		}
		return $parent_file;
	}

	/**
	 * Add Meta Boxes to CPT screen
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'asm_snippet_code_box',
			__( 'Snippet Code', 'header-footer-script-adder-pro' ),
			array( $this, 'render_code_meta_box' ),
			'asm_snippet',
			'normal',
			'high'
		);

		add_meta_box(
			'asm_snippet_location_box',
			__( 'Location & Timing Options', 'header-footer-script-adder-pro' ),
			array( $this, 'render_location_meta_box' ),
			'asm_snippet',
			'normal',
			'default'
		);

		add_meta_box(
			'asm_snippet_conditional_box',
			__( 'Advanced Conditional Targeting', 'header-footer-script-adder-pro' ),
			array( $this, 'render_conditional_meta_box' ),
			'asm_snippet',
			'normal',
			'default'
		);

		add_meta_box(
			'asm_snippet_optimization_box',
			__( 'Core Web Vitals & Optimization', 'header-footer-script-adder-pro' ),
			array( $this, 'render_optimization_meta_box' ),
			'asm_snippet',
			'side',
			'default'
		);
	}

	/**
	 * Meta Box Renders
	 */
	public function render_code_meta_box( $post ) {
		wp_nonce_field( 'asm_save_snippet_meta', 'asm_snippet_nonce' );
		$code = get_post_meta( $post->ID, '_asm_code', true );
		?>
		<textarea id="asm_snippet_code" name="asm_code" rows="15" style="width:100%; font-family:monospace;"><?php echo esc_textarea( $code ); ?></textarea>
		<p class="description"><?php esc_html_e( 'Enter your HTML, CSS, JavaScript, or custom scripts here.', 'header-footer-script-adder-pro' ); ?></p>
		<script>
			jQuery(document).ready(function($) {
				if (typeof wp !== 'undefined' && wp.codeEditor) {
					wp.codeEditor.initialize('asm_snippet_code', {
						codemirror: {
							mode: 'htmlmixed',
							lineNumbers: true,
							lineWrapping: true,
							styleActiveLine: true,
							theme: 'default'
						}
					});
				}
			});
		</script>
		<?php
	}

	public function render_location_meta_box( $post ) {
		$location = get_post_meta( $post->ID, '_asm_location', true );
		$priority = get_post_meta( $post->ID, '_asm_priority', true );
		$status   = get_post_meta( $post->ID, '_asm_status', true );

		if ( empty( $location ) ) { $location = 'header'; }
		if ( empty( $priority ) && '0' !== $priority ) { $priority = 10; }
		if ( empty( $status ) ) { $status = 'active'; }
		?>
		<table class="form-table">
			<tr>
				<th><label for="asm_status"><?php esc_html_e( 'Status', 'header-footer-script-adder-pro' ); ?></label></th>
				<td>
					<select id="asm_status" name="asm_status">
						<option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'header-footer-script-adder-pro' ); ?></option>
						<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'header-footer-script-adder-pro' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="asm_location"><?php esc_html_e( 'Injection Hook / Location', 'header-footer-script-adder-pro' ); ?></label></th>
				<td>
					<select id="asm_location" name="asm_location">
						<option value="header" <?php selected( $location, 'header' ); ?>><?php esc_html_e( 'wp_head (Header)', 'header-footer-script-adder-pro' ); ?></option>
						<option value="body_open" <?php selected( $location, 'body_open' ); ?>><?php esc_html_e( 'wp_body_open (After opening body)', 'header-footer-script-adder-pro' ); ?></option>
						<option value="footer" <?php selected( $location, 'footer' ); ?>><?php esc_html_e( 'wp_footer (Footer)', 'header-footer-script-adder-pro' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="asm_priority"><?php esc_html_e( 'Execution Priority', 'header-footer-script-adder-pro' ); ?></label></th>
				<td>
					<input type="number" id="asm_priority" name="asm_priority" value="<?php echo esc_attr( $priority ); ?>" min="1" max="1000" />
					<p class="description"><?php esc_html_e( 'Lower priority runs earlier. Default is 10.', 'header-footer-script-adder-pro' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	public function render_conditional_meta_box( $post ) {
		$roles      = get_post_meta( $post->ID, '_asm_cond_roles', true );
		$devices    = get_post_meta( $post->ID, '_asm_cond_devices', true );
		$pages_type = get_post_meta( $post->ID, '_asm_cond_pages_type', true );
		$cpts       = get_post_meta( $post->ID, '_asm_cond_cpts', true );
		$woo_pages  = get_post_meta( $post->ID, '_asm_cond_woo_pages', true );

		if ( empty( $roles ) ) { $roles = array( 'all' ); }
		if ( empty( $devices ) ) { $devices = array( 'desktop', 'tablet', 'mobile' ); }
		if ( empty( $pages_type ) ) { $pages_type = 'sitewide'; }
		if ( ! is_array( $cpts ) ) { $cpts = array(); }
		if ( ! is_array( $woo_pages ) ) { $woo_pages = array(); }

		global $wp_roles;
		$all_roles = $wp_roles->get_names();

		// Fetch all registered public custom post types
		$registered_cpts = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );
		?>
		<table class="form-table asm-pro-conditional-logic">
			<!-- User Role Conditions -->
			<tr>
				<th><?php esc_html_e( 'Target User Roles', 'header-footer-script-adder-pro' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="asm_cond_roles[]" value="all" <?php checked( in_array( 'all', $roles ) ); ?> />
						<strong><?php esc_html_e( 'Everyone (Sitewide)', 'header-footer-script-adder-pro' ); ?></strong>
					</label><br/><br/>
					<div class="asm-sub-checkboxes" style="margin-left: 20px;">
						<label>
							<input type="checkbox" name="asm_cond_roles[]" value="logged_in" <?php checked( in_array( 'logged_in', $roles ) ); ?> />
							<?php esc_html_e( 'Logged-In Users Only', 'header-footer-script-adder-pro' ); ?>
						</label><br/>
						<label>
							<input type="checkbox" name="asm_cond_roles[]" value="logged_out" <?php checked( in_array( 'logged_out', $roles ) ); ?> />
							<?php esc_html_e( 'Logged-Out Users Only', 'header-footer-script-adder-pro' ); ?>
						</label><br/>
						
						<p style="margin-top: 10px; margin-bottom: 5px;"><strong><?php esc_html_e( 'Or target specific roles:', 'header-footer-script-adder-pro' ); ?></strong></p>
						<?php foreach ( $all_roles as $role_key => $role_name ) : ?>
							<label style="display:inline-block; width:150px;">
								<input type="checkbox" name="asm_cond_roles[]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $roles ) ); ?> />
								<?php echo esc_html( $role_name ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</td>
			</tr>

			<!-- Device Targeting -->
			<tr>
				<th><?php esc_html_e( 'Target Devices', 'header-footer-script-adder-pro' ); ?></th>
				<td>
					<label style="margin-right:15px;">
						<input type="checkbox" name="asm_cond_devices[]" value="desktop" <?php checked( in_array( 'desktop', $devices ) ); ?> />
						<?php esc_html_e( 'Desktop', 'header-footer-script-adder-pro' ); ?>
					</label>
					<label style="margin-right:15px;">
						<input type="checkbox" name="asm_cond_devices[]" value="tablet" <?php checked( in_array( 'tablet', $devices ) ); ?> />
						<?php esc_html_e( 'Tablet', 'header-footer-script-adder-pro' ); ?>
					</label>
					<label>
						<input type="checkbox" name="asm_cond_devices[]" value="mobile" <?php checked( in_array( 'mobile', $devices ) ); ?> />
						<?php esc_html_e( 'Mobile', 'header-footer-script-adder-pro' ); ?>
					</label>
				</td>
			</tr>

			<!-- Page Targeting Types -->
			<tr>
				<th><?php esc_html_e( 'Page Conditions', 'header-footer-script-adder-pro' ); ?></th>
				<td>
					<select name="asm_cond_pages_type" id="asm_cond_pages_type" onchange="toggleProConditionals()">
						<option value="sitewide" <?php selected( $pages_type, 'sitewide' ); ?>><?php esc_html_e( 'Entire Site', 'header-footer-script-adder-pro' ); ?></option>
						<option value="homepage" <?php selected( $pages_type, 'homepage' ); ?>><?php esc_html_e( 'Homepage Only', 'header-footer-script-adder-pro' ); ?></option>
						<option value="singular" <?php selected( $pages_type, 'singular' ); ?>><?php esc_html_e( 'Singular Posts & Pages', 'header-footer-script-adder-pro' ); ?></option>
						<option value="cpts" <?php selected( $pages_type, 'cpts' ); ?>><?php esc_html_e( 'Custom Post Types', 'header-footer-script-adder-pro' ); ?></option>
						<option value="woocommerce" <?php selected( $pages_type, 'woocommerce' ); ?>><?php esc_html_e( 'WooCommerce Pages', 'header-footer-script-adder-pro' ); ?></option>
						<option value="archive" <?php selected( $pages_type, 'archive' ); ?>><?php esc_html_e( 'Archives & Search Pages', 'header-footer-script-adder-pro' ); ?></option>
					</select>

					<!-- Custom Post Type list selection -->
					<div id="asm_cpt_selection" style="margin-top:15px; display:<?php echo ( 'cpts' === $pages_type ? 'block' : 'none' ); ?>;">
						<p><strong><?php esc_html_e( 'Select Post Types:', 'header-footer-script-adder-pro' ); ?></strong></p>
						<?php if ( empty( $registered_cpts ) ) : ?>
							<p class="description"><?php esc_html_e( 'No custom post types registered on this site.', 'header-footer-script-adder-pro' ); ?></p>
						<?php else : ?>
							<?php foreach ( $registered_cpts as $cpt_obj ) : ?>
								<label style="margin-right:15px;">
									<input type="checkbox" name="asm_cond_cpts[]" value="<?php echo esc_attr( $cpt_obj->name ); ?>" <?php checked( in_array( $cpt_obj->name, $cpts ) ); ?> />
									<?php echo esc_html( $cpt_obj->label ); ?>
								</label>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

					<!-- WooCommerce options selection -->
					<div id="asm_woo_selection" style="margin-top:15px; display:<?php echo ( 'woocommerce' === $pages_type ? 'block' : 'none' ); ?>;">
						<p><strong><?php esc_html_e( 'Select WooCommerce Targets:', 'header-footer-script-adder-pro' ); ?></strong></p>
						<label style="margin-right:15px;">
							<input type="checkbox" name="asm_cond_woo_pages[]" value="shop" <?php checked( in_array( 'shop', $woo_pages ) ); ?> />
							<?php esc_html_e( 'Shop Page', 'header-footer-script-adder-pro' ); ?>
						</label>
						<label style="margin-right:15px;">
							<input type="checkbox" name="asm_cond_woo_pages[]" value="cart" <?php checked( in_array( 'cart', $woo_pages ) ); ?> />
							<?php esc_html_e( 'Cart Page', 'header-footer-script-adder-pro' ); ?>
						</label>
						<label style="margin-right:15px;">
							<input type="checkbox" name="asm_cond_woo_pages[]" value="checkout" <?php checked( in_array( 'checkout', $woo_pages ) ); ?> />
							<?php esc_html_e( 'Checkout Page', 'header-footer-script-adder-pro' ); ?>
						</label>
						<label>
							<input type="checkbox" name="asm_cond_woo_pages[]" value="single_product" <?php checked( in_array( 'single_product', $woo_pages ) ); ?> />
							<?php esc_html_e( 'Single Product Pages', 'header-footer-script-adder-pro' ); ?>
						</label>
					</div>
				</td>
			</tr>
		</table>

		<script>
			function toggleProConditionals() {
				var val = jQuery('#asm_cond_pages_type').val();
				jQuery('#asm_cpt_selection').toggle(val === 'cpts');
				jQuery('#asm_woo_selection').toggle(val === 'woocommerce');
			}
		</script>
		<?php
	}

	public function render_optimization_meta_box( $post ) {
		$strategy  = get_post_meta( $post->ID, '_asm_opt_strategy', true );
		$minify    = get_post_meta( $post->ID, '_asm_opt_minify', true );
		$mode      = get_post_meta( $post->ID, '_asm_opt_mode', true );

		if ( empty( $strategy ) ) { $strategy = 'default'; }
		if ( empty( $minify ) ) { $minify = 'no'; }
		if ( empty( $mode ) ) { $mode = 'inline'; }
		?>
		<p>
			<label for="asm_opt_strategy"><strong><?php esc_html_e( 'Loading Strategy', 'header-footer-script-adder-pro' ); ?></strong></label><br/>
			<select id="asm_opt_strategy" name="asm_opt_strategy" style="width:100%; margin-top:5px;">
				<option value="default" <?php selected( $strategy, 'default' ); ?>><?php esc_html_e( 'Default (Blocking)', 'header-footer-script-adder-pro' ); ?></option>
				<option value="async" <?php selected( $strategy, 'async' ); ?>><?php esc_html_e( 'Async (Non-blocking)', 'header-footer-script-adder-pro' ); ?></option>
				<option value="defer" <?php selected( $strategy, 'defer' ); ?>><?php esc_html_e( 'Defer (Delayed execution)', 'header-footer-script-adder-pro' ); ?></option>
			</select>
		</p>

		<p style="margin-top:15px;">
			<label for="asm_opt_mode"><strong><?php esc_html_e( 'Inject Method', 'header-footer-script-adder-pro' ); ?></strong></label><br/>
			<select id="asm_opt_mode" name="asm_opt_mode" style="width:100%; margin-top:5px;">
				<option value="inline" <?php selected( $mode, 'inline' ); ?>><?php esc_html_e( 'Inline (Echo to page)', 'header-footer-script-adder-pro' ); ?></option>
				<option value="enqueue" <?php selected( $mode, 'enqueue' ); ?>><?php esc_html_e( 'Enqueued File (Better caching)', 'header-footer-script-adder-pro' ); ?></option>
			</select>
		</p>

		<p style="margin-top:15px;">
			<label for="asm_opt_minify"><strong><?php esc_html_e( 'Minification', 'header-footer-script-adder-pro' ); ?></strong></label><br/>
			<select id="asm_opt_minify" name="asm_opt_minify" style="width:100%; margin-top:5px;">
				<option value="no" <?php selected( $minify, 'no' ); ?>><?php esc_html_e( 'Disable Minification', 'header-footer-script-adder-pro' ); ?></option>
				<option value="yes" <?php selected( $minify, 'yes' ); ?>><?php esc_html_e( 'Enable Minification', 'header-footer-script-adder-pro' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Save Meta Box Data
	 */
	public function save_snippet_meta( $post_id ) {
		if ( ! isset( $_POST['asm_snippet_nonce'] ) || ! wp_verify_nonce( $_POST['asm_snippet_nonce'], 'asm_save_snippet_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save fields
		if ( isset( $_POST['asm_code'] ) ) {
			// Save raw code safely (rely on wp_kses or administrator check during execution)
			update_post_meta( $post_id, '_asm_code', $_POST['asm_code'] );
		}

		if ( isset( $_POST['asm_location'] ) ) {
			update_post_meta( $post_id, '_asm_location', sanitize_text_field( $_POST['asm_location'] ) );
		}

		if ( isset( $_POST['asm_priority'] ) ) {
			update_post_meta( $post_id, '_asm_priority', intval( $_POST['asm_priority'] ) );
		}

		if ( isset( $_POST['asm_status'] ) ) {
			update_post_meta( $post_id, '_asm_status', sanitize_text_field( $_POST['asm_status'] ) );
		}

		// Save targeting fields
		if ( isset( $_POST['asm_cond_roles'] ) && is_array( $_POST['asm_cond_roles'] ) ) {
			$roles = array_map( 'sanitize_text_field', $_POST['asm_cond_roles'] );
			update_post_meta( $post_id, '_asm_cond_roles', $roles );
		} else {
			update_post_meta( $post_id, '_asm_cond_roles', array() );
		}

		if ( isset( $_POST['asm_cond_devices'] ) && is_array( $_POST['asm_cond_devices'] ) ) {
			$devices = array_map( 'sanitize_text_field', $_POST['asm_cond_devices'] );
			update_post_meta( $post_id, '_asm_cond_devices', $devices );
		} else {
			update_post_meta( $post_id, '_asm_cond_devices', array() );
		}

		if ( isset( $_POST['asm_cond_pages_type'] ) ) {
			update_post_meta( $post_id, '_asm_cond_pages_type', sanitize_text_field( $_POST['asm_cond_pages_type'] ) );
		}

		if ( isset( $_POST['asm_cond_cpts'] ) && is_array( $_POST['asm_cond_cpts'] ) ) {
			$cpts = array_map( 'sanitize_text_field', $_POST['asm_cond_cpts'] );
			update_post_meta( $post_id, '_asm_cond_cpts', $cpts );
		} else {
			update_post_meta( $post_id, '_asm_cond_cpts', array() );
		}

		if ( isset( $_POST['asm_cond_woo_pages'] ) && is_array( $_POST['asm_cond_woo_pages'] ) ) {
			$woo = array_map( 'sanitize_text_field', $_POST['asm_cond_woo_pages'] );
			update_post_meta( $post_id, '_asm_cond_woo_pages', $woo );
		} else {
			update_post_meta( $post_id, '_asm_cond_woo_pages', array() );
		}

		// Save optimization fields
		if ( isset( $_POST['asm_opt_strategy'] ) ) {
			update_post_meta( $post_id, '_asm_opt_strategy', sanitize_text_field( $_POST['asm_opt_strategy'] ) );
		}

		if ( isset( $_POST['asm_opt_minify'] ) ) {
			update_post_meta( $post_id, '_asm_opt_minify', sanitize_text_field( $_POST['asm_opt_minify'] ) );
		}

		if ( isset( $_POST['asm_opt_mode'] ) ) {
			update_post_meta( $post_id, '_asm_opt_mode', sanitize_text_field( $_POST['asm_opt_mode'] ) );
		}
	}

	/**
	 * Register Pixel Integrations Settings Group
	 */
	public function register_pixel_settings() {
		register_setting( 'asm_pro_pixels_group', 'asm_pro_pixel_settings' );
	}

	/**
	 * Render Pixel Integrations Settings Page
	 */
	public function display_pixels_page() {
		$options = get_option( 'asm_pro_pixel_settings', array() );
		$gtm_id  = isset( $options['gtm_id'] ) ? $options['gtm_id'] : '';
		$ga4_id  = isset( $options['ga4_id'] ) ? $options['ga4_id'] : '';
		$fb_id   = isset( $options['fb_id'] ) ? $options['fb_id'] : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'One-Click Pixel Integrations', 'header-footer-script-adder-pro' ); ?></h1>
			<p><?php esc_html_e( 'Enter your Tracking IDs below to automatically inject standard, optimized scripts onto your site.', 'header-footer-script-adder-pro' ); ?></p>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'asm_pro_pixels_group' ); ?>
				
				<table class="form-table">
					<tr>
						<th><label for="gtm_id"><?php esc_html_e( 'Google Tag Manager ID', 'header-footer-script-adder-pro' ); ?></label></th>
						<td>
							<input type="text" id="gtm_id" name="asm_pro_pixel_settings[gtm_id]" value="<?php echo esc_attr( $gtm_id ); ?>" class="regular-text" placeholder="GTM-XXXXXXX" />
							<p class="description"><?php esc_html_e( 'Automatically injects Tag Manager scripts in both header and body sections.', 'header-footer-script-adder-pro' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="ga4_id"><?php esc_html_e( 'Google Analytics 4 Measurement ID', 'header-footer-script-adder-pro' ); ?></label></th>
						<td>
							<input type="text" id="ga4_id" name="asm_pro_pixel_settings[ga4_id]" value="<?php echo esc_attr( $ga4_id ); ?>" class="regular-text" placeholder="G-XXXXXXX" />
							<p class="description"><?php esc_html_e( 'Automatically enqueues the Global Site Tag (gtag.js) for analytics tracking.', 'header-footer-script-adder-pro' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="fb_id"><?php esc_html_e( 'Facebook Pixel ID', 'header-footer-script-adder-pro' ); ?></label></th>
						<td>
							<input type="text" id="fb_id" name="asm_pro_pixel_settings[fb_id]" value="<?php echo esc_attr( $fb_id ); ?>" class="regular-text" placeholder="1234567890" />
							<p class="description"><?php esc_html_e( 'Injects Meta Facebook Pixel scripts in header and noscript tag in body open.', 'header-footer-script-adder-pro' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Customize Snippet table list columns
	 */
	public function set_custom_columns( $columns ) {
		$new_columns = array(
			'cb'         => $columns['cb'],
			'title'      => $columns['title'],
			'location'   => __( 'Location', 'header-footer-script-adder-pro' ),
			'priority'   => __( 'Priority', 'header-footer-script-adder-pro' ),
			'conditions' => __( 'Pages targeting', 'header-footer-script-adder-pro' ),
			'status'     => __( 'Status', 'header-footer-script-adder-pro' ),
			'date'       => $columns['date']
		);
		return $new_columns;
	}

	public function custom_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'location':
				$location = get_post_meta( $post_id, '_asm_location', true );
				echo esc_html( ucfirst( $location ?: 'header' ) );
				break;
			case 'priority':
				echo esc_html( get_post_meta( $post_id, '_asm_priority', true ) ?: '10' );
				break;
			case 'conditions':
				echo esc_html( ucfirst( get_post_meta( $post_id, '_asm_cond_pages_type', true ) ?: 'sitewide' ) );
				break;
			case 'status':
				$status = get_post_meta( $post_id, '_asm_status', true ) ?: 'active';
				$color = ( 'active' === $status ) ? 'green' : 'red';
				echo '<strong style="color:' . esc_attr( $color ) . ';">' . esc_html( ucfirst( $status ) ) . '</strong>';
				break;
		}
	}

	/**
	 * Enqueue Styles and Scripts for Admin Screen
	 */
	public function enqueue_admin_assets( $hook ) {
		global $current_screen;
		if ( isset( $current_screen->post_type ) && 'asm_snippet' === $current_screen->post_type ) {
			wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
			wp_enqueue_style( 'asm-pro-admin-style', ASM_PLUGIN_URL . 'pro/css/admin-pro.css', array(), ASM_VERSION );
		}
	}
}

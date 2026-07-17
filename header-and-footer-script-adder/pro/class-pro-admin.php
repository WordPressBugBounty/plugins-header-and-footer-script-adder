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
			'name'               => _x( 'Snippets', 'post type general name', 'header-and-footer-script-adder' ),
			'singular_name'      => _x( 'Snippet', 'post type singular name', 'header-and-footer-script-adder' ),
			'menu_name'          => _x( 'Snippets', 'admin menu', 'header-and-footer-script-adder' ),
			'name_admin_bar'     => _x( 'Snippet', 'add new on admin bar', 'header-and-footer-script-adder' ),
			'add_new'            => _x( 'Add New', 'snippet', 'header-and-footer-script-adder' ),
			'add_new_item'       => __( 'Add New Snippet', 'header-and-footer-script-adder' ),
			'new_item'           => __( 'New Snippet', 'header-and-footer-script-adder' ),
			'edit_item'          => __( 'Edit Snippet', 'header-and-footer-script-adder' ),
			'view_item'          => __( 'View Snippet', 'header-and-footer-script-adder' ),
			'all_items'          => __( 'All Snippets', 'header-and-footer-script-adder' ),
			'search_items'       => __( 'Search Snippets', 'header-and-footer-script-adder' ),
			'not_found'          => __( 'No snippets found.', 'header-and-footer-script-adder' ),
			'not_found_in_trash' => __( 'No snippets found in Trash.', 'header-and-footer-script-adder' )
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
			__( 'All Snippets', 'header-and-footer-script-adder' ),
			__( 'All Snippets', 'header-and-footer-script-adder' ),
			'manage_options',
			'edit.php?post_type=asm_snippet'
		);

		add_submenu_page(
			'custom-scripts',
			__( 'Add New Snippet', 'header-and-footer-script-adder' ),
			__( 'Add New Snippet', 'header-and-footer-script-adder' ),
			'manage_options',
			'post-new.php?post_type=asm_snippet'
		);

		add_submenu_page(
			'custom-scripts',
			__( 'Pixel Integrations', 'header-and-footer-script-adder' ),
			__( 'Pixel Integrations', 'header-and-footer-script-adder' ),
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
			__( 'Snippet Code', 'header-and-footer-script-adder' ),
			array( $this, 'render_code_meta_box' ),
			'asm_snippet',
			'normal',
			'high'
		);

		add_meta_box(
			'asm_snippet_location_box',
			__( 'Location & Timing Options', 'header-and-footer-script-adder' ),
			array( $this, 'render_location_meta_box' ),
			'asm_snippet',
			'normal',
			'default'
		);

		add_meta_box(
			'asm_snippet_conditional_box',
			__( 'Advanced Conditional Targeting', 'header-and-footer-script-adder' ),
			array( $this, 'render_conditional_meta_box' ),
			'asm_snippet',
			'normal',
			'default'
		);

		add_meta_box(
			'asm_snippet_optimization_box',
			__( 'Core Web Vitals & Optimization', 'header-and-footer-script-adder' ),
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
		<p class="description"><?php esc_html_e( 'Enter your HTML, CSS, JavaScript, or custom scripts here.', 'header-and-footer-script-adder' ); ?></p>
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
				<th><label for="asm_status"><?php esc_html_e( 'Status', 'header-and-footer-script-adder' ); ?></label></th>
				<td>
					<select id="asm_status" name="asm_status">
						<option value="active" <?php echo esc_attr( selected( $status, 'active', false ) ); ?>><?php esc_html_e( 'Active', 'header-and-footer-script-adder' ); ?></option>
						<option value="inactive" <?php echo esc_attr( selected( $status, 'inactive', false ) ); ?>><?php esc_html_e( 'Inactive', 'header-and-footer-script-adder' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="asm_location"><?php esc_html_e( 'Injection Hook / Location', 'header-and-footer-script-adder' ); ?></label></th>
				<td>
					<select id="asm_location" name="asm_location">
						<option value="header" <?php echo esc_attr( selected( $location, 'header', false ) ); ?>><?php esc_html_e( 'wp_head (Header)', 'header-and-footer-script-adder' ); ?></option>
						<option value="body_open" <?php echo esc_attr( selected( $location, 'body_open', false ) ); ?>><?php esc_html_e( 'wp_body_open (After opening body)', 'header-and-footer-script-adder' ); ?></option>
						<option value="footer" <?php echo esc_attr( selected( $location, 'footer', false ) ); ?>><?php esc_html_e( 'wp_footer (Footer)', 'header-and-footer-script-adder' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="asm_priority"><?php esc_html_e( 'Execution Priority', 'header-and-footer-script-adder' ); ?></label></th>
				<td>
					<input type="number" id="asm_priority" name="asm_priority" value="<?php echo esc_attr( $priority ); ?>" min="1" max="1000" />
					<p class="description"><?php esc_html_e( 'Lower priority runs earlier. Default is 10.', 'header-and-footer-script-adder' ); ?></p>
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
				<th><?php esc_html_e( 'Target User Roles', 'header-and-footer-script-adder' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="asm_cond_roles[]" value="all" <?php echo esc_attr( checked( in_array( 'all', $roles ), true, false ) ); ?> />
						<strong><?php esc_html_e( 'Everyone (Sitewide)', 'header-and-footer-script-adder' ); ?></strong>
					</label><br/><br/>
					<div class="asm-sub-checkboxes" style="margin-left: 20px;">
						<label>
							<input type="checkbox" name="asm_cond_roles[]" value="logged_in" <?php echo esc_attr( checked( in_array( 'logged_in', $roles ), true, false ) ); ?> />
							<?php esc_html_e( 'Logged-In Users Only', 'header-and-footer-script-adder' ); ?>
						</label><br/>
						<label>
							<input type="checkbox" name="asm_cond_roles[]" value="logged_out" <?php echo esc_attr( checked( in_array( 'logged_out', $roles ), true, false ) ); ?> />
							<?php esc_html_e( 'Logged-Out Users Only', 'header-and-footer-script-adder' ); ?>
						</label><br/>
						
						<p style="margin-top: 10px; margin-bottom: 5px;"><strong><?php esc_html_e( 'Or target specific roles:', 'header-and-footer-script-adder' ); ?></strong></p>
						<?php foreach ( $all_roles as $role_key => $role_name ) : ?>
							<label style="display:inline-block; width:150px;">
								<input type="checkbox" name="asm_cond_roles[]" value="<?php echo esc_attr( $role_key ); ?>" <?php echo esc_attr( checked( in_array( $role_key, $roles ), true, false ) ); ?> />
								<?php echo esc_html( $role_name ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</td>
			</tr>

			<!-- Device Targeting -->
			<tr>
				<th><?php esc_html_e( 'Target Devices', 'header-and-footer-script-adder' ); ?></th>
				<td>
					<label style="margin-right:15px;">
						<input type="checkbox" name="asm_cond_devices[]" value="desktop" <?php echo esc_attr( checked( in_array( 'desktop', $devices ), true, false ) ); ?> />
						<?php esc_html_e( 'Desktop', 'header-and-footer-script-adder' ); ?>
					</label>
					<label style="margin-right:15px;">
						<input type="checkbox" name="asm_cond_devices[]" value="tablet" <?php echo esc_attr( checked( in_array( 'tablet', $devices ), true, false ) ); ?> />
						<?php esc_html_e( 'Tablet', 'header-and-footer-script-adder' ); ?>
					</label>
					<label>
						<input type="checkbox" name="asm_cond_devices[]" value="mobile" <?php echo esc_attr( checked( in_array( 'mobile', $devices ), true, false ) ); ?> />
						<?php esc_html_e( 'Mobile', 'header-and-footer-script-adder' ); ?>
					</label>
				</td>
			</tr>

			<!-- Page Targeting Types -->
			<tr>
				<th><?php esc_html_e( 'Page Conditions', 'header-and-footer-script-adder' ); ?></th>
				<td>
					<select name="asm_cond_pages_type" id="asm_cond_pages_type" onchange="toggleProConditionals()">
						<option value="sitewide" <?php echo esc_attr( selected( $pages_type, 'sitewide', false ) ); ?>><?php esc_html_e( 'Entire Site', 'header-and-footer-script-adder' ); ?></option>
						<option value="homepage" <?php echo esc_attr( selected( $pages_type, 'homepage', false ) ); ?>><?php esc_html_e( 'Homepage Only', 'header-and-footer-script-adder' ); ?></option>
						<option value="singular" <?php echo esc_attr( selected( $pages_type, 'singular', false ) ); ?>><?php esc_html_e( 'Singular Posts & Pages', 'header-and-footer-script-adder' ); ?></option>
						<option value="cpts" <?php echo esc_attr( selected( $pages_type, 'cpts', false ) ); ?>><?php esc_html_e( 'Custom Post Types', 'header-and-footer-script-adder' ); ?></option>
						<option value="woocommerce" <?php echo esc_attr( selected( $pages_type, 'woocommerce', false ) ); ?>><?php esc_html_e( 'WooCommerce Pages', 'header-and-footer-script-adder' ); ?></option>
						<option value="archive" <?php echo esc_attr( selected( $pages_type, 'archive', false ) ); ?>><?php esc_html_e( 'Archives & Search Pages', 'header-and-footer-script-adder' ); ?></option>
					</select>

					<!-- Custom Post Type list selection -->
					<div id="asm_cpt_selection" style="margin-top:15px; display:<?php echo ( 'cpts' === $pages_type ? 'block' : 'none' ); ?>;">
						<p><strong><?php esc_html_e( 'Select Post Types:', 'header-and-footer-script-adder' ); ?></strong></p>
						<?php if ( empty( $registered_cpts ) ) : ?>
							<p class="description"><?php esc_html_e( 'No custom post types registered on this site.', 'header-and-footer-script-adder' ); ?></p>
						<?php else : ?>
							<?php foreach ( $registered_cpts as $cpt_obj ) : ?>
								<label style="margin-right:15px;">
									<input type="checkbox" name="asm_cond_cpts[]" value="<?php echo esc_attr( $cpt_obj->name ); ?>" <?php echo esc_attr( checked( in_array( $cpt_obj->name, $cpts ), true, false ) ); ?> />
									<?php echo esc_html( $cpt_obj->label ); ?>
								</label>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

					<!-- WooCommerce options selection -->
					<div id="asm_woo_selection" style="margin-top:15px; display:<?php echo ( 'woocommerce' === $pages_type ? 'block' : 'none' ); ?>;">
						<p><strong><?php esc_html_e( 'Select WooCommerce Targets:', 'header-and-footer-script-adder' ); ?></strong></p>
						<label style="margin-right:15px;">
							<input type="checkbox" name="asm_cond_woo_pages[]" value="shop" <?php echo esc_attr( checked( in_array( 'shop', $woo_pages ), true, false ) ); ?> />
							<?php esc_html_e( 'Shop Page', 'header-and-footer-script-adder' ); ?>
						</label>
						<label style="margin-right:15px;">
							<input type="checkbox" name="asm_cond_woo_pages[]" value="cart" <?php echo esc_attr( checked( in_array( 'cart', $woo_pages ), true, false ) ); ?> />
							<?php esc_html_e( 'Cart Page', 'header-and-footer-script-adder' ); ?>
						</label>
						<label style="margin-right:15px;">
							<input type="checkbox" name="asm_cond_woo_pages[]" value="checkout" <?php echo esc_attr( checked( in_array( 'checkout', $woo_pages ), true, false ) ); ?> />
							<?php esc_html_e( 'Checkout Page', 'header-and-footer-script-adder' ); ?>
						</label>
						<label>
							<input type="checkbox" name="asm_cond_woo_pages[]" value="single_product" <?php echo esc_attr( checked( in_array( 'single_product', $woo_pages ), true, false ) ); ?> />
							<?php esc_html_e( 'Single Product Pages', 'header-and-footer-script-adder' ); ?>
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
			<label for="asm_opt_strategy"><strong><?php esc_html_e( 'Loading Strategy', 'header-and-footer-script-adder' ); ?></strong></label><br/>
			<select id="asm_opt_strategy" name="asm_opt_strategy" style="width:100%; margin-top:5px;">
				<option value="default" <?php echo esc_attr( selected( $strategy, 'default', false ) ); ?>><?php esc_html_e( 'Default (Blocking)', 'header-and-footer-script-adder' ); ?></option>
				<option value="async" <?php echo esc_attr( selected( $strategy, 'async', false ) ); ?>><?php esc_html_e( 'Async (Non-blocking)', 'header-and-footer-script-adder' ); ?></option>
				<option value="defer" <?php echo esc_attr( selected( $strategy, 'defer', false ) ); ?>><?php esc_html_e( 'Defer (Delayed execution)', 'header-and-footer-script-adder' ); ?></option>
			</select>
		</p>

		<p style="margin-top:15px;">
			<label for="asm_opt_mode"><strong><?php esc_html_e( 'Inject Method', 'header-and-footer-script-adder' ); ?></strong></label><br/>
			<select id="asm_opt_mode" name="asm_opt_mode" style="width:100%; margin-top:5px;">
				<option value="inline" <?php echo esc_attr( selected( $mode, 'inline', false ) ); ?>><?php esc_html_e( 'Inline (Echo to page)', 'header-and-footer-script-adder' ); ?></option>
				<option value="enqueue" <?php echo esc_attr( selected( $mode, 'enqueue', false ) ); ?>><?php esc_html_e( 'Enqueued File (Better caching)', 'header-and-footer-script-adder' ); ?></option>
			</select>
		</p>

		<p style="margin-top:15px;">
			<label for="asm_opt_minify"><strong><?php esc_html_e( 'Minification', 'header-and-footer-script-adder' ); ?></strong></label><br/>
			<select id="asm_opt_minify" name="asm_opt_minify" style="width:100%; margin-top:5px;">
				<option value="no" <?php echo esc_attr( selected( $minify, 'no', false ) ); ?>><?php esc_html_e( 'Disable Minification', 'header-and-footer-script-adder' ); ?></option>
				<option value="yes" <?php echo esc_attr( selected( $minify, 'yes', false ) ); ?>><?php esc_html_e( 'Enable Minification', 'header-and-footer-script-adder' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Save Meta Box Data
	 */
	public function save_snippet_meta( $post_id ) {
		if ( ! isset( $_POST['asm_snippet_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['asm_snippet_nonce'] ) ), 'asm_save_snippet_meta' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save fields
		if ( isset( $_POST['asm_code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$raw_code = wp_unslash( $_POST['asm_code'] ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( current_user_can( 'unfiltered_html' ) ) {
				update_post_meta( $post_id, '_asm_code', $raw_code );
				update_post_meta( $post_id, '_asm_code_author_can_unfiltered_html', '1' );
			} else {
				update_post_meta( $post_id, '_asm_code', wp_kses_post( $raw_code ) );
				update_post_meta( $post_id, '_asm_code_author_can_unfiltered_html', '0' );
			}
		}

		if ( isset( $_POST['asm_location'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_location', sanitize_text_field( wp_unslash( $_POST['asm_location'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		if ( isset( $_POST['asm_priority'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_priority', intval( wp_unslash( $_POST['asm_priority'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		if ( isset( $_POST['asm_status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_status', sanitize_text_field( wp_unslash( $_POST['asm_status'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// Save targeting fields
		if ( isset( $_POST['asm_cond_roles'] ) && is_array( $_POST['asm_cond_roles'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$roles = array_map( 'sanitize_text_field', wp_unslash( $_POST['asm_cond_roles'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_cond_roles', $roles );
		} else {
			update_post_meta( $post_id, '_asm_cond_roles', array() );
		}

		if ( isset( $_POST['asm_cond_devices'] ) && is_array( $_POST['asm_cond_devices'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$devices = array_map( 'sanitize_text_field', wp_unslash( $_POST['asm_cond_devices'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_cond_devices', $devices );
		} else {
			update_post_meta( $post_id, '_asm_cond_devices', array() );
		}

		if ( isset( $_POST['asm_cond_pages_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_cond_pages_type', sanitize_text_field( wp_unslash( $_POST['asm_cond_pages_type'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		if ( isset( $_POST['asm_cond_cpts'] ) && is_array( $_POST['asm_cond_cpts'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$cpts = array_map( 'sanitize_text_field', wp_unslash( $_POST['asm_cond_cpts'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_cond_cpts', $cpts );
		} else {
			update_post_meta( $post_id, '_asm_cond_cpts', array() );
		}

		if ( isset( $_POST['asm_cond_woo_pages'] ) && is_array( $_POST['asm_cond_woo_pages'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$woo = array_map( 'sanitize_text_field', wp_unslash( $_POST['asm_cond_woo_pages'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_cond_woo_pages', $woo );
		} else {
			update_post_meta( $post_id, '_asm_cond_woo_pages', array() );
		}

		// Save optimization fields
		if ( isset( $_POST['asm_opt_strategy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_opt_strategy', sanitize_text_field( wp_unslash( $_POST['asm_opt_strategy'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		if ( isset( $_POST['asm_opt_minify'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_opt_minify', sanitize_text_field( wp_unslash( $_POST['asm_opt_minify'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		if ( isset( $_POST['asm_opt_mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $post_id, '_asm_opt_mode', sanitize_text_field( wp_unslash( $_POST['asm_opt_mode'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
	}

	/**
	 * Register Pixel Integrations Settings Group
	 */
	public function register_pixel_settings() {
		register_setting( 'asm_pro_pixels_group', 'asm_pro_pixel_settings', array(
			'sanitize_callback' => array( $this, 'sanitize_pixel_settings' ),
		) );
	}

	/**
	 * Sanitize Pixel Settings
	 */
	public function sanitize_pixel_settings( $input ) {
		$sanitized = array();
		if ( isset( $input['gtm_id'] ) ) {
			$sanitized['gtm_id'] = sanitize_text_field( $input['gtm_id'] );
		}
		if ( isset( $input['ga4_id'] ) ) {
			$sanitized['ga4_id'] = sanitize_text_field( $input['ga4_id'] );
		}
		if ( isset( $input['fb_id'] ) ) {
			$sanitized['fb_id'] = sanitize_text_field( $input['fb_id'] );
		}
		return $sanitized;
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
			<h1><?php esc_html_e( 'One-Click Pixel Integrations', 'header-and-footer-script-adder' ); ?></h1>
			<p><?php esc_html_e( 'Enter your Tracking IDs below to automatically inject standard, optimized scripts onto your site.', 'header-and-footer-script-adder' ); ?></p>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'asm_pro_pixels_group' ); ?>
				
				<table class="form-table">
					<tr>
						<th><label for="gtm_id"><?php esc_html_e( 'Google Tag Manager ID', 'header-and-footer-script-adder' ); ?></label></th>
						<td>
							<input type="text" id="gtm_id" name="asm_pro_pixel_settings[gtm_id]" value="<?php echo esc_attr( $gtm_id ); ?>" class="regular-text" placeholder="GTM-XXXXXXX" />
							<p class="description"><?php esc_html_e( 'Automatically injects Tag Manager scripts in both header and body sections.', 'header-and-footer-script-adder' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="ga4_id"><?php esc_html_e( 'Google Analytics 4 Measurement ID', 'header-and-footer-script-adder' ); ?></label></th>
						<td>
							<input type="text" id="ga4_id" name="asm_pro_pixel_settings[ga4_id]" value="<?php echo esc_attr( $ga4_id ); ?>" class="regular-text" placeholder="G-XXXXXXX" />
							<p class="description"><?php esc_html_e( 'Automatically enqueues the Global Site Tag (gtag.js) for analytics tracking.', 'header-and-footer-script-adder' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="fb_id"><?php esc_html_e( 'Facebook Pixel ID', 'header-and-footer-script-adder' ); ?></label></th>
						<td>
							<input type="text" id="fb_id" name="asm_pro_pixel_settings[fb_id]" value="<?php echo esc_attr( $fb_id ); ?>" class="regular-text" placeholder="1234567890" />
							<p class="description"><?php esc_html_e( 'Injects Meta Facebook Pixel scripts in header and noscript tag in body open.', 'header-and-footer-script-adder' ); ?></p>
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
			'location'   => __( 'Location', 'header-and-footer-script-adder' ),
			'priority'   => __( 'Priority', 'header-and-footer-script-adder' ),
			'conditions' => __( 'Pages targeting', 'header-and-footer-script-adder' ),
			'status'     => __( 'Status', 'header-and-footer-script-adder' ),
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


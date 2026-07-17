<?php
/**
 * Public frontend functionalities for Header Footer Script Adder Pro
 *
 * @package    HeaderFooterScriptAdderPro
 * @subpackage HeaderFooterScriptAdderPro/public
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class ASM_Pro_Public {

	private $pixel_settings;

	public function init() {
		$this->pixel_settings = get_option( 'asm_pro_pixel_settings', array() );

		// Hook into standard WordPress head, body, and footer actions
		add_action( 'wp_head', array( $this, 'inject_header_items' ), 15 );
		add_action( 'wp_body_open', array( $this, 'inject_body_items' ), 15 );
		add_action( 'wp_footer', array( $this, 'inject_footer_items' ), 15 );
	}

	/**
	 * Header Injections
	 */
	public function inject_header_items() {
		if ( is_admin() || is_feed() || is_robots() ) {
			return;
		}

		// 1. One-click tracking pixels
		$this->inject_pixel_gtm_head();
		$this->inject_pixel_ga4();
		$this->inject_pixel_fb_head();

		// 2. Custom snippets targeting Header
		$this->inject_custom_snippets( 'header' );
	}

	/**
	 * Body Open Injections
	 */
	public function inject_body_items() {
		if ( is_admin() || is_feed() || is_robots() ) {
			return;
		}

		// 1. One-click tracking pixels (noscript elements)
		$this->inject_pixel_gtm_body();
		$this->inject_pixel_fb_body();

		// 2. Custom snippets targeting Body Open
		$this->inject_custom_snippets( 'body_open' );
	}

	/**
	 * Footer Injections
	 */
	public function inject_footer_items() {
		if ( is_admin() || is_feed() || is_robots() ) {
			return;
		}

		// Custom snippets targeting Footer
		$this->inject_custom_snippets( 'footer' );
	}

	/**
	 * Main Snippet Injection Engine
	 */
	private function inject_custom_snippets( $location ) {
		// Query all active CPT asm_snippet posts targeting this location
		$args = array(
			'post_type'      => 'asm_snippet',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_asm_location',
					'value' => $location,
				),
				array(
					'key'   => '_asm_status',
					'value' => 'active',
				),
			),
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return;
		}

		// Store snippets to sort by priority
		$snippets = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			
			$priority = get_post_meta( $post_id, '_asm_priority', true );
			$priority = ( empty( $priority ) && '0' !== $priority ) ? 10 : intval( $priority );

			$snippets[] = array(
				'id'       => $post_id,
				'title'    => get_the_title(),
				'code'     => get_post_meta( $post_id, '_asm_code', true ),
				'priority' => $priority,
			);
		}
		wp_reset_postdata();

		// Sort by priority ascending
		usort( $snippets, function( $a, $b ) {
			return $a['priority'] <=> $b['priority'];
		});

		foreach ( $snippets as $snippet ) {
			if ( $this->should_load_snippet( $snippet['id'] ) ) {
				// Refuse to render snippets whose author cannot post unfiltered HTML
				$author_can_unfiltered = get_post_meta( $snippet['id'], '_asm_code_author_can_unfiltered_html', true );
				if ( '' === $author_can_unfiltered ) {
					// Fallback for legacy snippets: check post author capability
					$author_id = get_post_field( 'post_author', $snippet['id'] );
					if ( ! user_can( $author_id, 'unfiltered_html' ) ) {
						continue;
					}
				} elseif ( '1' !== $author_can_unfiltered ) {
					continue;
				}

				$code = $snippet['code'];

				// Apply Minification if enabled
				$minify = get_post_meta( $snippet['id'], '_asm_opt_minify', true );
				if ( 'yes' === $minify ) {
					$code = $this->safe_minify( $code );
				}

				// Apply Loading Strategy (Async / Defer)
				$strategy = get_post_meta( $snippet['id'], '_asm_opt_strategy', true );
				if ( 'default' !== $strategy && ! empty( $strategy ) ) {
					$code = $this->apply_loading_strategy( $code, $strategy );
				}

				// Apply Injection Method (Inline or Enqueue)
				$mode = get_post_meta( $snippet['id'], '_asm_opt_mode', true );
				if ( 'enqueue' === $mode ) {
					$this->enqueue_code_style_or_script( $snippet['id'], $code );
				} else {
					echo "\n<!-- Header Footer Script Adder Pro: " . esc_html( $snippet['title'] ) . " -->\n";
					echo $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}

	/**
	 * Check if the snippet passes targeting rules
	 */
	private function should_load_snippet( $post_id ) {
		// 1. Check User Roles
		$roles = get_post_meta( $post_id, '_asm_cond_roles', true );
		if ( ! empty( $roles ) && ! in_array( 'all', $roles ) ) {
			$user_matches = false;
			if ( in_array( 'logged_in', $roles ) && is_user_logged_in() ) {
				$user_matches = true;
			} elseif ( in_array( 'logged_out', $roles ) && ! is_user_logged_in() ) {
				$user_matches = true;
			} elseif ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				foreach ( $current_user->roles as $role ) {
					if ( in_array( $role, $roles ) ) {
						$user_matches = true;
						break;
					}
				}
			}

			if ( ! $user_matches ) {
				return false;
			}
		}

		// 2. Check Device Types
		$devices = get_post_meta( $post_id, '_asm_cond_devices', true );
		if ( ! empty( $devices ) ) {
			$current_device = ASM_Pro_Helpers::get_device_type();
			if ( ! in_array( $current_device, $devices ) ) {
				return false;
			}
		}

		// 3. Check Page targeting
		$pages_type = get_post_meta( $post_id, '_asm_cond_pages_type', true );
		switch ( $pages_type ) {
			case 'homepage':
				return is_front_page();

			case 'singular':
				return is_singular();

			case 'cpts':
				$cpts = get_post_meta( $post_id, '_asm_cond_cpts', true );
				if ( ! empty( $cpts ) && is_array( $cpts ) ) {
					return is_singular( $cpts );
				}
				return false;

			case 'woocommerce':
				if ( ! class_exists( 'WooCommerce' ) ) {
					return false;
				}
				$woo_pages = get_post_meta( $post_id, '_asm_cond_woo_pages', true );
				if ( ! empty( $woo_pages ) && is_array( $woo_pages ) ) {
					if ( in_array( 'shop', $woo_pages ) && is_shop() ) {
						return true;
					}
					if ( in_array( 'cart', $woo_pages ) && is_cart() ) {
						return true;
					}
					if ( in_array( 'checkout', $woo_pages ) && is_checkout() ) {
						return true;
					}
					if ( in_array( 'single_product', $woo_pages ) && is_product() ) {
						return true;
					}
				}
				return false;

			case 'archive':
				return is_archive() || is_search() || is_category() || is_tag() || is_author() || is_date();

			case 'sitewide':
			default:
				return true;
		}
	}

	/**
	 * Inject inline custom style or script code enqueued safely
	 */
	private function enqueue_code_style_or_script( $id, $code ) {
		// Clean script or style tags
		$has_style  = ( stripos( $code, '<style' ) !== false );
		$has_script = ( stripos( $code, '<script' ) !== false );

		$clean_code = preg_replace( '/<\/?(script|style)[^>]*>/i', '', $code );

		if ( $has_style ) {
			// Enqueue inline style hooked onto a core style sheet handle
			wp_register_style( 'asm-pro-dummy-' . $id, false, array(), ASM_VERSION );
			wp_enqueue_style( 'asm-pro-dummy-' . $id );
			wp_add_inline_style( 'asm-pro-dummy-' . $id, $clean_code );
		} elseif ( $has_script ) {
			// Enqueue inline script hooked onto jquery
			wp_add_inline_script( 'jquery', $clean_code );
		} else {
			// fallback output
			echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Safe comment & whitespace minifier
	 */
	private function safe_minify( $content ) {
		// Remove block comments
		$content = preg_replace( '!/\*[^*]*\*+([^/*][^*]*\*+)*/!', '', $content );
		// Remove HTML comments
		$content = preg_replace( '/<!--.*?-->/s', '', $content );
		
		// Split by lines and trim each line
		$lines = explode( "\n", $content );
		$trimmed_lines = array();
		foreach ( $lines as $line ) {
			$trimmed = trim( $line );
			if ( ! empty( $trimmed ) ) {
				$trimmed_lines[] = $trimmed;
			}
		}
		
		return implode( "\n", $trimmed_lines );
	}

	/**
	 * Parse script tags and inject async or defer attributes
	 */
	private function apply_loading_strategy( $code, $strategy ) {
		// Check if it's a script tag
		if ( stripos( $code, '<script' ) !== false ) {
			// Replace <script with <script async or <script defer
			return preg_replace( '/<script(\s|>)/i', '<script ' . esc_attr( $strategy ) . '$1', $code );
		}
		return $code;
	}

	// ==========================================
	// Pixel Template Injections
	// ==========================================

	private function inject_pixel_gtm_head() {
		if ( empty( $this->pixel_settings['gtm_id'] ) ) {
			return;
		}
		$gtm_id = sanitize_text_field( $this->pixel_settings['gtm_id'] );
		?>
<!-- Google Tag Manager (Header) -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
<!-- End Google Tag Manager (Header) -->
		<?php
	}

	private function inject_pixel_gtm_body() {
		if ( empty( $this->pixel_settings['gtm_id'] ) ) {
			return;
		}
		$gtm_id = sanitize_text_field( $this->pixel_settings['gtm_id'] );
		?>
<!-- Google Tag Manager (noscript - Body Open) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript - Body Open) -->
		<?php
	}

	private function inject_pixel_ga4() {
		if ( empty( $this->pixel_settings['ga4_id'] ) ) {
			return;
		}
		$ga4_id = sanitize_text_field( $this->pixel_settings['ga4_id'] );
		?>
<!-- Google Analytics 4 (Header) -->
<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php echo esc_js( $ga4_id ); ?>');
</script>
<!-- End Google Analytics 4 (Header) -->
		<?php
	}

	private function inject_pixel_fb_head() {
		if ( empty( $this->pixel_settings['fb_id'] ) ) {
			return;
		}
		$fb_id = sanitize_text_field( $this->pixel_settings['fb_id'] );
		?>
<!-- Meta Facebook Pixel (Header) -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?php echo esc_js( $fb_id ); ?>');
fbq('track', 'PageView');
</script>
<!-- End Meta Facebook Pixel (Header) -->
		<?php
	}

	private function inject_pixel_fb_body() {
		if ( empty( $this->pixel_settings['fb_id'] ) ) {
			return;
		}
		$fb_id = sanitize_text_field( $this->pixel_settings['fb_id'] );
		?>
<!-- Meta Facebook Pixel (noscript - Body Open) -->
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?php echo esc_attr( $fb_id ); ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Facebook Pixel (noscript - Body Open) -->
		<?php
	}
}

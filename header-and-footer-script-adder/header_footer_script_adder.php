<?php

/**
 * Header Footer Script Adder
 *
 * @package           HeaderFooterScriptAdder
 * @author            mahethekiller
 * @copyright         2025 mahethekiller
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Header Footer Script Adder
 * Plugin URI:        https://wordpress.org/plugins/header-and-footer-script-adder
 * Description:       Plugin for adding scripts in header and footer
 * Version:           2.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            mahethekiller
 * Author URI:        https://wordpress.org/support/users/mahethekiller
 * Text Domain:       header-footer-script-adder
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Network:           false
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 2.0.3 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ASM_VERSION', '2.1' );
/**
 * Define plugin constants
 */
define( 'ASM_PLUGIN_FILE', __FILE__ );
define( 'ASM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ASM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ASM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( !function_exists( 'hafsa_fs' ) ) {
    // Create a helper function for easy SDK access.
    function hafsa_fs() {
        global $hafsa_fs;
        if ( !isset( $hafsa_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
            $hafsa_fs = fs_dynamic_init( array(
                'id'               => '31302',
                'slug'             => 'header-and-footer-script-adder',
                'type'             => 'plugin',
                'public_key'       => 'pk_fb1ac589818279dabd61451172db1',
                'is_premium'       => false,
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => true,
                'menu'             => array(
                    'slug'    => 'custom-scripts',
                    'support' => false,
                ),
                'is_live'          => true,
            ) );
        }
        return $hafsa_fs;
    }

    // Init Freemius.
    hafsa_fs();
    // Signal that SDK was initiated.
    do_action( 'hafsa_fs_loaded' );
}
require_once ASM_PLUGIN_DIR . 'includes/helpers.php';
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function asm_activate() {
    require_once ASM_PLUGIN_DIR . 'includes/class-activator.php';
    ASM_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function asm_deactivate() {
    require_once ASM_PLUGIN_DIR . 'includes/class-deactivator.php';
    ASM_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'asm_activate' );
register_deactivation_hook( __FILE__, 'asm_deactivate' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ASM_PLUGIN_DIR . 'includes/class-core.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.3
 */
add_action(
    'upgrader_process_complete',
    'asm_on_plugin_update',
    10,
    2
);
function asm_on_plugin_update(  $upgrader_object, $options  ) {
    if ( $options['action'] === 'update' && $options['type'] === 'plugin' ) {
        if ( !empty( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
            foreach ( $options['plugins'] as $plugin ) {
                if ( strpos( $plugin, 'header-footer-script-adder.php' ) !== false ) {
                    if ( function_exists( 'asm_send_site_info_to_google_sheet' ) ) {
                        asm_send_site_info_to_google_sheet( 'update' );
                    }
                }
            }
        }
    }
}

add_action( 'admin_head', function () {
    echo '<style>
        /* Style the whole meta box */
        #asm_page_scripts {
            background: linear-gradient(135deg, #f9f9ff, #e0f7fa);
            border: 2px solid #4cafef;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* Style the meta box title */
        #asm_page_scripts .hndle {
            background-color: #4cafef;
            color: white;
            font-weight: bold;
            font-size: 16px;
            padding: 10px;
            border-radius: 8px 8px 0 0;
        }

        /* Inside content padding */
        #asm_page_scripts .inside {
            padding: 15px;
            font-size: 14px;
            background-color: #ffffff;
            border-radius: 0 0 8px 8px;
        }
    </style>';
} );
function asm_run() {
    $plugin = new ASM_Core();
    $plugin->run();
}

asm_run();
/**
 * Load Pro features if premium code is available
 */
function hafsa_is_pro_active() {
    return function_exists( 'hafsa_fs' ) && hafsa_fs()->can_use_premium_code();
}

if ( hafsa_is_pro_active() && file_exists( ASM_PLUGIN_DIR . 'pro/class-pro-helpers.php' ) ) {
    require_once ASM_PLUGIN_DIR . 'pro/class-pro-helpers.php';
    if ( is_admin() ) {
        require_once ASM_PLUGIN_DIR . 'pro/class-pro-admin.php';
        $pro_admin = new ASM_Pro_Admin();
        $pro_admin->init();
    }
    require_once ASM_PLUGIN_DIR . 'pro/class-pro-public.php';
    $pro_public = new ASM_Pro_Public();
    $pro_public->init();
}
/**
 * Uninstall logic for Freemius
 */
hafsa_fs()->add_action( 'after_uninstall', 'hafsa_fs_uninstall_cleanup' );
function hafsa_fs_uninstall_cleanup() {
    if ( is_multisite() ) {
        global $wpdb;
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            hafsa_fs_uninstall_cleanup_single();
            restore_current_blog();
        }
    } else {
        hafsa_fs_uninstall_cleanup_single();
    }
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
    if ( function_exists( 'wp_cache_delete' ) ) {
        wp_cache_delete( 'asm_global_settings', 'options' );
    }
}

function hafsa_fs_uninstall_cleanup_single() {
    $remove_data = get_option( 'asm_remove_data_on_uninstall', false );
    if ( $remove_data ) {
        delete_option( 'asm_global_settings' );
        delete_option( 'asm_version' );
        delete_option( 'asm_remove_data_on_uninstall' );
        global $wpdb;
        $wpdb->delete( $wpdb->postmeta, array(
            'meta_key' => '_asm_header_scripts',
        ), array('%s') );
        $wpdb->delete( $wpdb->postmeta, array(
            'meta_key' => '_asm_body_scripts',
        ), array('%s') );
        $wpdb->delete( $wpdb->postmeta, array(
            'meta_key' => '_asm_footer_scripts',
        ), array('%s') );
        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_asm_%'" );
        wp_cache_flush();
        delete_option( 'asm_pro_pixel_settings' );
        $args = array(
            'post_type'      => 'asm_snippet',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        );
        $snippets = get_posts( $args );
        foreach ( $snippets as $snippet ) {
            wp_delete_post( $snippet->ID, true );
        }
    }
}

/**
 * Add Affiliate Program external link to the submenu
 */
add_action( 'admin_menu', 'hafsa_add_affiliate_menu_link', 999 );
function hafsa_add_affiliate_menu_link() {
    add_submenu_page(
        'custom-scripts',
        __( 'Affiliate Program', 'header-footer-script-adder' ),
        __( 'Affiliate Program', 'header-footer-script-adder' ),
        'manage_options',
        'asm-affiliate',
        'hafsa_render_affiliate_page'
    );
}

function hafsa_render_affiliate_page() {
    echo '<div class="wrap">';
    echo '<h1 style="margin-bottom: 20px;">' . esc_html__( 'Join Our Affiliate Program', 'header-footer-script-adder' ) . '</h1>';
    echo '<iframe src="https://docs.google.com/forms/d/e/1FAIpQLSfq0cqK6Y6XycTH36ioiuou3b2fpDiSu4Ez9UQauA-1Z9OiOQ/viewform?embedded=true" width="100%" height="1750" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>';
    echo '</div>';
}

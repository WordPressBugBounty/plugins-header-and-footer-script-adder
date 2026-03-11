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
 * Version:           2.0.6
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
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 2.0.3 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ASM_VERSION', '2.0.3');

/**
 * Define plugin constants
 */
define('ASM_PLUGIN_FILE', __FILE__);
define('ASM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ASM_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once ASM_PLUGIN_DIR . 'includes/helpers.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function asm_activate()
{

    require_once ASM_PLUGIN_DIR . 'includes/class-activator.php';
    ASM_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function asm_deactivate()
{
    require_once ASM_PLUGIN_DIR . 'includes/class-deactivator.php';
    ASM_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'asm_activate');
register_deactivation_hook(__FILE__, 'asm_deactivate');


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



add_action('upgrader_process_complete', 'asm_on_plugin_update', 10, 2);

function asm_on_plugin_update($upgrader_object, $options)
{
    if ($options['action'] === 'update' && $options['type'] === 'plugin') {
        if (!empty($options['plugins']) && is_array($options['plugins'])) {
            foreach ($options['plugins'] as $plugin) {
                if (strpos($plugin, 'header-footer-script-adder.php') !== false) {
                    if (function_exists('asm_send_site_info_to_google_sheet')) {
                        asm_send_site_info_to_google_sheet('update');
                    }
                }
            }
        }
    }
}


add_action('admin_head', function () {
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
});




function asm_run()
{
    $plugin = new ASM_Core();
    $plugin->run();
}

asm_run();

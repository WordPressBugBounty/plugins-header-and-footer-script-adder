<?php
/**
 * Fired during plugin activation
 *
 * @link       https://github.com/advanced-scripts-manager
 * @since      2.0.3
 *
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.3
 * @package    HeaderFooterScriptAdder
 * @subpackage HeaderFooterScriptAdder/includes
 * @author     Header Footer Script Adder
 */
class ASM_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    2.0.3
     */
    public static function activate()
    {
        // Set default options
        $default_settings = [
            'header_scripts'   => '',
            'header_condition' => 'sitewide',
            'body_scripts'     => '',
            'body_condition'   => 'sitewide',
            'footer_scripts'   => '',
            'footer_condition' => 'sitewide',
        ];

        // Add default settings if they don't exist
        if (! get_option('asm_global_settings')) {
            add_option('asm_global_settings', $default_settings);
        }

        // Set plugin version
        add_option('asm_version', ASM_VERSION);

        // Flush rewrite rules
        flush_rewrite_rules();

        // Send data
        if (function_exists('asm_send_site_info_to_google_sheet')) {
            asm_send_site_info_to_google_sheet('activation');
        }

    }
}

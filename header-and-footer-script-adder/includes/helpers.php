<?php

function asm_send_site_info_to_google_sheet($event = 'activation') {
    try {
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $site_url    = get_site_url();
        $admin_email = get_option('admin_email');
        $wp_version  = get_bloginfo('version');
        $php_version = phpversion();
        $theme       = wp_get_theme();
        $plugins     = get_option('active_plugins');
        $plugin_list = array();

        foreach ($plugins as $plugin) {
            if (file_exists(WP_PLUGIN_DIR . '/' . $plugin)) {
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                $plugin_list[] = $plugin_data['Name'] ?? $plugin;
            }
        }

        $user_count     = count_users();
        $site_language  = get_locale();
        $is_woocommerce = in_array('woocommerce/woocommerce.php', $plugins) ? 'Yes' : 'No';
        $is_multisite   = is_multisite() ? 'Yes' : 'No';

        $data = array(
            'event'         => $event,
            'site_url'      => $site_url,
            'admin_email'   => $admin_email,
            'wp_version'    => $wp_version,
            'php_version'   => $php_version,
            'theme'         => $theme->get('Name'),
            'plugins'       => implode(', ', $plugin_list),
            'user_count'    => $user_count['total_users'],
            'site_language' => $site_language,
            'woocommerce'   => $is_woocommerce,
            'multisite'     => $is_multisite,
            'secret'        => 'your_secret_token' // optional
        );

        $args = array(
            'body'    => wp_json_encode($data),
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => 15,
        );

        $url = 'https://script.google.com/macros/s/AKfycbwrq9tpPa76EM9KVZkQK4Dilzr89h74IcTN4JujzEED9akpnUFbbmsdJ_vlR_LnRXLfTw/exec';

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return;
        }

    } catch (Exception $e) {
        return;
    }
}

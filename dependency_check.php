<?php

namespace odoo_conn\dependency_check;

function odoo_conn_contact_7_plugin_active()
{

    if (!function_exists("is_plugin_active_for_network")) {
        include_once(ABSPATH . "/wp-admin/includes/plugin.php");
    }

    if (current_user_can("activate_plugins") && !class_exists("WPCF7")) {
        deactivate_plugins(plugin_basename("odoo_conn.php"));
        $error_message = (
            esc_html__("This plugin requires ", "odoo_conn") .
            "<a href='" . esc_url("https://wordpress.org/plugins/odoo_conn/") . "'>WPCF7</a>"
            . esc_html__(" plugin to be active.", "odoo_conn") .
            "</p>"
        );
        die($error_message);
    }
}

?>
<?php
/*
Plugin Name: Contact 7 to Odoo connector
Plugin URI: https://www.jackdane.co.uk
Description: Connect your WordPress Contact 7 Forms to Odoo
Version: 0.1.2
Requires PHP: 7.3
Author: Jack Dane
Author URI: https://www.jackdane.co.uk
*/

add_option("odoo_conn_db_version");

require("vendor/autoload.php");

require("encryption.php");

require("odoo_connector/odoo_connector.php");

require("dependency_check.php");

register_activation_hook(__FILE__, "\\odoo_conn\\dependency_check\\odoo_conn_contact_7_plugin_active");

require("activation.php");

register_activation_hook(__FILE__, "odoo_conn_activation_function");

require("deactivation.php");

register_deactivation_hook(__FILE__, "odoo_conn_deactivation_function");

require("loaded.php");

require("admin/main.php");

?>
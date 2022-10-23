<?php
/*
Plugin Name: Contact 7 to Odoo connector
Plugin URI: https://www.jackdane.co.uk
Description: Connect your WordPress Contact 7 Forms to Odoo
Version: 0.0.1
*/

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

require("encryption.php");

require("odoo_connector/odoo_connector.php" );

require("dependency_check.php");

register_activation_hook( __FILE__, "contact_7_plugin_active" );

require("activation.php");

register_activation_hook(__FILE__,  "odoo_conn_activation_function");

require("admin/main.php");

?>
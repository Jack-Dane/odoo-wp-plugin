<?php
/**
 * Plugin Name: Contact Form 7 to Odoo connector
 * Description: When submitting a form through Contact Form 7, the data is extracted and mapped to the designated fields in Odoo.
 * Author: Jack Dane
 * Author URI: https://www.jackdane.co.uk
 * Plugin URI: https://github.com/Jack-Dane/odoo-wp-plugin
 * Requires PHP: 8.1
 * Text Domain: cf7-odoo-connector
 * Requires Plugins: contact-form-7
 * Version: 0.1.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Add a new option to the database.
add_option( 'odoo_conn_db_version' );

// Require necessary files.
require_once 'vendor/autoload.php';
require_once 'encryption.php';

require_once 'activation.php';
register_activation_hook( __FILE__, 'odoo_conn_activation_function' );

require_once 'loaded.php';
require_once 'admin/main.php';

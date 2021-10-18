<?php
/*
Plugin Name: Contact 7 to Odoo connector
Plugin URI: https://www.jackdane.co.uk
Description: Connect your WordPress Contact 7 Forms to Odoo
Version: 0.0.1
*/

include("activation.php");

register_activation_hook(__FILE__,  "odoo_conn_activation_function");

?>
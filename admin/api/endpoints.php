<?php 

require_once("endpoint_methods.php");

add_action( "rest_api_init", function () {
  	register_rest_route( "odoo-conn/v1", "/create-odoo-connection", array(
    	"methods" => "POST",
    	"callback" => "create_odoo_connection",
	));
});

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-connections", array(
		"methods" => "GET",
		"callback" => "get_odoo_connections",
	));
});

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-form", array(
		"methods" => "POST",
		"callback" => "create_odoo_form",
	));
});

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-form", array(
		"methods" => "GET",
		"callback" => "get_odoo_forms"
	));
});

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-form-mapping", array(
		"methods" => "POST",
		"callback" => "create_odoo_form_mapping",
	));
});

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-form-mappings", array(
		"methods" => "GET",
		"callback" => "get_odoo_from_mappings"
	));
});

?>
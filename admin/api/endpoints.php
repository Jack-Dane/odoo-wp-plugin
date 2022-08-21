<?php 

require_once("endpoint_methods.php");

add_action( "rest_api_init", function () {
  	register_rest_route( "odoo-conn/v1", "/create-connection", array(
    	"methods" => "POST",
    	"callback" => "create_connection",
	));
});

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-connections", array(
		"methods" => "get",
		"callback" => "get_connections",
	));
});

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-form-connection", array(
		"methods" => "POST",
		"callback" => "create_form_connection",
	));
});

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-form-connections", array(
		"methods" => "get",
		"callback" => "get_form_connections"
	));
});

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-field-mapping", array(
		"methods" => "post",
		"callback" => "create_mapping",
	));
});

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-field-mappings", array(
		"methods" => "get",
		"callback" => "get_mappings"
	));
});

?>
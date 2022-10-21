<?php

class GetOdooConnection extends GetBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_connection";
	}

}


class PostOdooConnection extends PostBaseSchema { 
	
	protected function get_table_name () {
		return "odoo_conn_connection";
	}

	protected function parse_data ($data) {
		return array (
			"name" => $data["name"],
			"username" => $data["username"],
			"api_key" => $data["api_key"],
			"url" => $data["url"],
			"database_name" => $data["database_name"],
		);
	}

	protected function insert_data_types () {
		return array ("%s", "%s", "%s", "%s", "%s");
	}

}


class PutOdooConnection extends PutBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_connection";
	}

	protected function update_data ($data) {
		return array (
			"name" => $data["name"],
			"username" => $data["username"],
			"api_key" => $data["api_key"],
			"url" => $data["url"],
			"database_name" => $data["database_name"]
		);
	}

}


class DeleteOdooConnection extends DeleteBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_connection";
	}

}

function base_odoo_connections_schema ($properties) {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => "Odoo Connection",
		"type" => "object",
		"properties" => $properties,
	);
}

function base_odoo_connections_schema_properties () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Connection"),
		),
		"name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the Odoo Connection instance"),
		),
		"api_key" => array(
			"type" => "string",
			"description" => esc_html__("The API Key used to authenticate the Odoo Connection"),
		),
		"url" => array(
			"type" => "string",
			"description" => esc_html__("The URL of the Odoo database"),
		),
		"database_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the database to connect to"),
		),
	);
}

function base_odoo_connections_arguments () {
	return array(
		"name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the Odoo Connection instance"),
			"required" => true,
		),
		"api_key" => array(
			"type" => "string",
			"description" => esc_html__("The API Key used to authenticate the Odoo Connection"),
			"required" => true,
		),
		"url" => array(
			"type" => "string",
			"description" => esc_html__("The URL of the Odoo database"),
			"required" => true,
		),
		"database_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the database to connect to"),
			"required" => true,
		),
	);
}

function get_odoo_connections ($data) {
	$get_odoo_connection = new GetOdooConnection();
	$response = $get_odoo_connection->request($data);
	return $response;
}

function get_odoo_connections_schema () {
	return base_odoo_connections_schema(
		array(
			"type" => "array",
			"items" => array(
				"type" => "object",
				"properties" => base_odoo_connections_schema_properties(),
			),
		)
	);
	return $schema;
}

function get_odoo_connections_arguments () {
	return base_get_request_arguments();
}

function create_odoo_connection ($data) {
	$post_odoo_connection = new PostOdooConnection();
	$response = $post_odoo_connection->request($data);
	return $response;
}

function create_odoo_connection_schema () {
	return base_odoo_connections_schema(base_odoo_connections_schema_properties());
}

function create_odoo_connection_arguments () {
	return base_odoo_connections_arguments();
}

function update_odoo_connection ($data) {
	$id = $data["id"];
	$put_odoo_connection = new PutOdooConnection($id);
	$response = $put_odoo_connection->request($data);
	return $response;
}

function update_odoo_connection_schema () {
	return base_odoo_connections_schema(base_odoo_connections_schema_properties());
}

function update_odoo_connection_arguments () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Connection"),
			"required" => true,
		),
	) + base_odoo_connections_arguments();
}

function delete_odoo_connection ($data) {
	$delete_odoo_connection = new DeleteOdooConnection();
	$response = $delete_odoo_connection->request($data);
	return $response;
}

function delete_odoo_connection_schema () {
	return base_delete_request_schema("Odoo Connection");
}

function delete_odoo_connection_arguments () {
	return base_delete_arguments();
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-connections", array(
		array(
			"methods" => "GET",
			"callback" => "get_odoo_connections",
			"args" => get_odoo_connections_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "get_odoo_connections_schema",
	));

  	register_rest_route( "odoo-conn/v1", "/create-odoo-connection", array(
  		array(
  			"methods" => "POST",
    		"callback" => "create_odoo_connection",
    		"args" => create_odoo_connection_arguments(),
  		),
    	"permission_callback" => "is_authorised_to_request_data",
    	"schema" => "create_odoo_connection_schema",
	));

  	register_rest_route( "odoo-conn/v1", "/update-odoo-connection", array(
  		array(
  			"methods" => "PUT",
    		"callback" => "update_odoo_connection",
    		"args" => update_odoo_connection_arguments(),
  		),
    	"permission_callback" => "is_authorised_to_request_data",
    	"schema" => "update_odoo_connection_schema",
	));

	register_rest_route( "odoo-conn/v1", "/delete-odoo-connection", array(
		array(
			"methods" => "DELETE",
			"callback" => "delete_odoo_connection",
			"args" => delete_odoo_connection_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "delete_odoo_connection_schema",
	));
});

?>

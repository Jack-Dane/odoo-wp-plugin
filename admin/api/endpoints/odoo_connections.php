<?php

namespace odoo_conn\admin\api\endpoints;


trait OdooConnOdooConnectionTableName {

	protected function get_table_name () {
		return "odoo_conn_connection";
	}

}


trait OdooConnOdooConnectionColumns {

	protected function get_columns () {
		$columns = 	[
			"id",
			"name",
			"username",
			"url",
			"database_name"
		];

		return implode(", ", $columns);
	}

}


class OdooConnGetOdooConnection extends OdooConnGetBaseSchema {

	use OdooConnOdooConnectionTableName;
	use OdooConnOdooConnectionColumns;

}


class OdooConnPostOdooConnection extends OdooConnPostBaseSchema { 

	use OdooConnOdooConnectionTableName;
	use OdooConnOdooConnectionColumns;

	protected function parse_data ($data) {
		$api_key = $data["api_key"];
		$encrypted_api_key = odoo_conn_encrypt_data($api_key);

		return array(
			"name" => $data["name"],
			"username" => $data["username"],
			"api_key" => $encrypted_api_key,
			"url" => $data["url"],
			"database_name" => $data["database_name"],
		);
	}

	protected function insert_data_types () {
		return array("%s", "%s", "%s", "%s", "%s");
	}

}


class OdooConnPutOdooConnection extends OdooConnPutBaseSchema {

	use OdooConnOdooConnectionTableName;
	use OdooConnOdooConnectionColumns;

	protected function update_data ($data) {
		return array(
			"name" => $data["name"],
			"username" => $data["username"],
			"url" => $data["url"],
			"database_name" => $data["database_name"]
		);
	}

}


class OdooConnDeleteOdooConnection extends OdooConnDeleteBaseSchema {

	use OdooConnOdooConnectionTableName;

}

function odoo_conn_base_odoo_connections_schema ($properties) {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => "Odoo Connection",
		"type" => "object",
		"properties" => $properties,
	);
}

function odoo_conn_base_odoo_connections_schema_properties () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Connection"),
		),
		"name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the Odoo Connection instance"),
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

function odoo_conn_base_odoo_connections_arguments () {
	return array(
		"name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the Odoo Connection instance"),
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

function odoo_conn_get_odoo_connections ($data) {
	$get_odoo_connection = new OdooConnGetOdooConnection();
	$response = $get_odoo_connection->request($data);
	return $response;
}

function odoo_conn_get_odoo_connections_schema () {
	return odoo_conn_base_odoo_connections_schema(
		array(
			"type" => "array",
			"items" => array(
				"type" => "object",
				"properties" => odoo_conn_base_odoo_connections_schema_properties(),
			),
		)
	);
	return $schema;
}

function odoo_conn_get_odoo_connections_arguments () {
	return odoo_conn_base_get_request_arguments();
}

function odoo_conn_create_odoo_connection ($data) {
	$post_odoo_connection = new OdooConnPostOdooConnection();
	$response = $post_odoo_connection->request($data);
	return $response;
}

function odoo_conn_create_odoo_connection_schema () {
	return odoo_conn_base_odoo_connections_schema(base_odoo_connections_schema_properties());
}

function odoo_conn_create_odoo_connection_arguments () {
	return odoo_conn_base_odoo_connections_arguments() + array(
		"api_key" => array(
			"type" => "string",
			"description" => esc_html__("The API Key used to authenticate the Odoo Connection"),
			"required" => true,
		),
	);
}

function odoo_conn_update_odoo_connection ($data) {
	$id = $data["id"];
	$put_odoo_connection = new OdooConnPutOdooConnection($id);
	$response = $put_odoo_connection->request($data);
	return $response;
}

function odoo_conn_update_odoo_connection_schema () {
	return odoo_conn_base_odoo_connections_schema(odoo_conn_base_odoo_connections_schema_properties());
}

function odoo_conn_update_odoo_connection_arguments () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Connection"),
			"required" => true,
		),
	) + odoo_conn_base_odoo_connections_arguments();
}

function odoo_conn_delete_odoo_connection ($data) {
	$delete_odoo_connection = new OdooConnDeleteOdooConnection();
	$response = $delete_odoo_connection->request($data);
	return $response;
}

function odoo_conn_delete_odoo_connection_schema () {
	return odoo_conn_base_delete_request_schema("Odoo Connection");
}

function odoo_conn_delete_odoo_connection_arguments () {
	return odoo_conn_base_delete_arguments();
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-connections", array(
		array(
			"methods" => "GET",
			"callback" => "odoo_conn_get_odoo_connections",
			"args" => odoo_conn_get_odoo_connections_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "odoo_conn_get_odoo_connections_schema",
	));

  	register_rest_route( "odoo-conn/v1", "/create-odoo-connection", array(
  		array(
  			"methods" => "POST",
    		"callback" => "odoo_conn_create_odoo_connection",
    		"args" => odoo_conn_create_odoo_connection_arguments(),
  		),
    	"permission_callback" => "is_authorised_to_request_data",
    	"schema" => "odoo_conn_create_odoo_connection_schema",
	));

  	register_rest_route( "odoo-conn/v1", "/update-odoo-connection", array(
  		array(
  			"methods" => "PUT",
    		"callback" => "odoo_conn_update_odoo_connection",
    		"args" => odoo_conn_update_odoo_connection_arguments(),
  		),
    	"permission_callback" => "is_authorised_to_request_data",
    	"schema" => "odoo_conn_update_odoo_connection_schema",
	));

	register_rest_route( "odoo-conn/v1", "/delete-odoo-connection", array(
		array(
			"methods" => "DELETE",
			"callback" => "odoo_conn_delete_odoo_connection",
			"args" => odoo_conn_delete_odoo_connection_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "odoo_conn_delete_odoo_connection_schema",
	));
});

?>

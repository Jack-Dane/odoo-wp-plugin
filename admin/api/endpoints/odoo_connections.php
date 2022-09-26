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


function get_odoo_connections ($data) {
	$get_odoo_connection = new GetOdooConnection();
	$response = $get_odoo_connection->request($data);
	return $response;
}

function create_odoo_connection ($data) {
	$post_odoo_connection = new PostOdooConnection();
	$response = $post_odoo_connection->request($data);
	return $response;
}

function update_odoo_connection ($data) {
	$id = $data["id"];
	$put_odoo_connection = new PutOdooConnection($id);
	$response = $put_odoo_connection->request($data);
	return $response;
}

function delete_odoo_connection ($data) {
	$delete_odoo_connection = new DeleteOdooConnection();
	$response = $delete_odoo_connection->request($data);
	return $response;
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-connections", array(
		"methods" => "GET",
		"callback" => "get_odoo_connections",
		"permission_callback" => "is_authorised_to_request_data",
	));

  	register_rest_route( "odoo-conn/v1", "/create-odoo-connection", array(
    	"methods" => "POST",
    	"callback" => "create_odoo_connection",
    	"permission_callback" => "is_authorised_to_request_data",
	));

  	register_rest_route( "odoo-conn/v1", "/update-odoo-connection", array(
    	"methods" => "PUT",
    	"callback" => "update_odoo_connection",
    	"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route( "odoo-conn/v1", "/delete-odoo-connection", array(
		"methods" => "DELETE",
		"callback" => "delete_odoo_connection",
		"permission_callback" => "is_authorised_to_request_data",
	));
});

?>

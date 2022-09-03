<?php

class GetOdooForm extends GetBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form";
	}

}


class PostOdooForm extends PostBaseSchema { 
	
	protected function get_table_name () {
		return "odoo_conn_form";
	}

	protected function parse_data ($data) {
		return array (
			"odoo_connection_id" => $data["odoo_connection_id"],
			"odoo_model" => $data["odoo_model"],
			"name" => $data["name"],
			"contact_7_id" => $data["contact_7_id"]
		);
	}

	protected function insert_data_types () {
		return array ("%d", "%s", "%s", "%d");
	}

}


class PutOdooForm extends PutBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form";
	}

	protected function update_data ($data) {
		return array(
			"odoo_connection_id" => $data["odoo_connection_id"],
			"name" => $data["name"],
			"contact_7_id" => $data["contact_7_id"]
		);
	}

}


function get_odoo_forms ($data) {
	$get_odoo_connection = new GetOdooForm();
	$response = $get_odoo_connection->request($data);
	return $response;
}

function create_odoo_form ($data) {
	$post_odoo_connection = new PostOdooForm();
	$response = $post_odoo_connection->request($data);
	return $response;
}

function update_odoo_form ($data) {
	$id = $data["id"];
	$put_odoo_connection = new PutOdooForm($id);
	$response = $put_odoo_connection->request($data);
	return $response;
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-form", array(
		"methods" => "POST",
		"callback" => "create_odoo_form",
	));

	register_rest_route ( "odoo-conn/v1", "/get-odoo-forms", array(
		"methods" => "GET",
		"callback" => "get_odoo_forms"
	));

	register_rest_route ( "odoo-conn/v1", "/update-odoo-form", array(
		"methods" => "PUT",
		"callback" => "update_odoo_form"
	));
});

?>
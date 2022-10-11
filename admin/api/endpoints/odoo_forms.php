<?php

class GetOdooForm extends GetBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form";
	}

	protected function get_columns () {
		global $table_prefix;

		$columns = [
			$table_prefix . "odoo_conn_form.id", 
			$table_prefix . "odoo_conn_connection.name as 'connection_name'", 
			$table_prefix . "odoo_conn_form.odoo_model", 
			$table_prefix . "odoo_conn_form.name", 
			$table_prefix . "posts.post_title"
		];
		
		return implode(", ", $columns);
	}

	protected function foreign_keys () {
		global $table_prefix;

		return [
			"odoo_connection_id" => [
				"table_name" => $table_prefix . "odoo_conn_connection",
				"column_name" => "id"
			], 
			"contact_7_id" => [
				"table_name" => $table_prefix . "posts",
				"column_name" => "ID"
			]
		];
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


class DeleteOdooForm extends DeleteBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form";
	}

}


function get_odoo_forms ($data) {
	$get_odoo_forms = new GetOdooForm();
	$response = $get_odoo_forms->request($data);
	return $response;
}

function create_odoo_form ($data) {
	$post_odoo_form = new PostOdooForm();
	$response = $post_odoo_form->request($data);
	return $response;
}

function update_odoo_form ($data) {
	$id = $data["id"];
	$put_odoo_form = new PutOdooForm($id);
	$response = $put_odoo_form->request($data);
	return $response;
}

function delete_odoo_form ($data) {
	$delete_odoo_form = new DeleteOdooForm();
	$response = $delete_odoo_form->request($data);
	return $response;
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-form", array(
		"methods" => "POST",
		"callback" => "create_odoo_form",
		"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route ( "odoo-conn/v1", "/get-odoo-forms", array(
		"methods" => "GET",
		"callback" => "get_odoo_forms",
		"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route ( "odoo-conn/v1", "/update-odoo-form", array(
		"methods" => "PUT",
		"callback" => "update_odoo_form",
		"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route ( "odoo-conn/v1", "/delete-odoo-form", array(
		"methods" => "DELETE",
		"callback" => "delete_odoo_form",
		"permission_callback" => "is_authorised_to_request_data",
	));
});

?>
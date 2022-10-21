<?php

class GetOdooForm extends GetBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form";
	}

	protected function get_columns () {
		global $table_prefix;

		$columns = [
			$table_prefix . "odoo_conn_form.id", 
			$table_prefix . "odoo_conn_form.odoo_connection_id",
			$table_prefix . "odoo_conn_connection.name as 'odoo_connection_name'", 
			$table_prefix . "odoo_conn_form.odoo_model", 
			$table_prefix . "odoo_conn_form.name", 
			$table_prefix . "posts.ID as 'contact_7_id'",
			$table_prefix . "posts.post_title as 'contact_7_title'"
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

function base_odoo_forms_schema ($properties) {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => "Odoo Form",
		"type" => "object",
		"properties" => $properties,
	);
}

function base_odoo_forms_schema_properties () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Form"),
		),
		"odoo_connection_id" => array(
			"type" => "integer",
			"description" => esc_html__("Foreign key for the connection that relates to the Odoo Form"),
		),
		"odoo_model" => array(
			"type" => "string",
			"description" => esc_html__("The name of the Odoo model that will be created when the form is submitted"),
		),
		"name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the Odoo Form instance"),
		),
		"contact_7_id" => array(
			"type" => "integer",
			"description" => esc_html__("Foreign key for the Contact 7 Form that is submitted"),
		),
	);
}

function base_odoo_forms_arguments () {
	return array(
		"odoo_connection_id" => array(
			"type" => "integer",
			"description" => esc_html__("Foreign key for the connection that relates to the form"),
			"required" => true,
		),
		"odoo_model" => array(
			"type" => "string",
			"description" => esc_html__("The name of the odoo model that will be created when the form is submitted"),
			"required" => true,
		), 
		"name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the odoo form instance"),
			"required" => true,
		),
		"contact_7_id" => array(
			"type" => "integer",
			"description" => esc_html__("Foreign key for the Contact 7 Form that is submitted"),
			"required" => true,
		)
	);
}

function get_odoo_forms ($data) {
	$get_odoo_forms = new GetOdooForm();
	$response = $get_odoo_forms->request($data);
	return $response;
}

function get_odoo_forms_schema () {
	return base_odoo_forms_schema(
		array(
			"type" => "array",
			"items" => array(
				"type" => "object",
				"properties" => base_odoo_forms_schema_properties() + array(
					"odoo_connection_name" => array(
						"type" => "string",
						"description" => esc_html__("The name of the connection name from the Connection object")
					),
					"contact_7_title" => array(
						"type" => "string",
						"description" => esc_html__("Title of the Contact 7 Form that is submitted")
					),
				),
			),
		),
	);
}

function get_odoo_forms_arguments () {
	return base_get_request_arguments();
}

function create_odoo_form ($data) {
	$post_odoo_form = new PostOdooForm();
	$response = $post_odoo_form->request($data);
	return $response;
}

function create_odoo_form_schema () {
	return base_odoo_forms_schema(base_odoo_forms_schema_properties());
}

function create_odoo_form_arguments () {
	return base_odoo_forms_arguments();
}

function update_odoo_form ($data) {
	$id = $data["id"];
	$put_odoo_form = new PutOdooForm($id);
	$response = $put_odoo_form->request($data);
	return $response;
}

function updated_odoo_form_schema () {
	return base_odoo_forms_schema(base_odoo_forms_schema_properties());
}

function updated_odoo_form_arguments () {
	return (
		array(
			"id" => array(
				"type" => "integer",
				"description" => esc_html__("Primary key for an Odoo Form"),
				"required" => true,
			),
		) + base_odoo_forms_arguments()
	);
}

function delete_odoo_form ($data) {
	$delete_odoo_form = new DeleteOdooForm();
	$response = $delete_odoo_form->request($data);
	return $response;
}

function delete_odoo_form_schema () {
	return base_delete_request_schema("Odoo Form");
}

function delete_odoo_form_arguments () {
	return base_delete_arguments();
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/create-odoo-form", array(
		array(
			"methods" => "POST",
			"callback" => "create_odoo_form",
			"args" => create_odoo_form_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "create_odoo_form_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/get-odoo-forms", array(
		array(
			"methods" => "GET",
			"callback" => "get_odoo_forms",
			"args" => get_odoo_forms_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "get_odoo_forms_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/update-odoo-form", array(
		array(
			"methods" => "PUT",
			"callback" => "update_odoo_form",
			"args" => updated_odoo_form_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "updated_odoo_form_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/delete-odoo-form", array(
		array(
			"methods" => "DELETE",
			"callback" => "delete_odoo_form",
			"args" => delete_odoo_form_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "delete_odoo_form_schema"
	));
});

?>
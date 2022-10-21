<?php

class GetOdooFormMappings extends GetBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

	protected function get_columns () {
		global $table_prefix;

		$columns = [
			$table_prefix . "odoo_conn_form_mapping.id", 
			$table_prefix . "odoo_conn_form_mapping.odoo_form_id",
			$table_prefix . "odoo_conn_form.name as 'odoo_form_name'", 
			$table_prefix . "odoo_conn_form_mapping.cf7_field_name", 
			$table_prefix . "odoo_conn_form_mapping.odoo_field_name", 
			$table_prefix . "odoo_conn_form_mapping.constant_value"
		];
		
		return implode(", ", $columns);
	}

	protected function foreign_keys () {
		global $table_prefix;

		return [
			"odoo_form_id" => [
				"table_name" => $table_prefix . "odoo_conn_form",
				"column_name" => "id"
			]
		];
	}

}


class PostOdooFormMappings extends PostBaseSchema { 
	
	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

	protected function parse_data ($data) {
		// TODO: validate either constant value has been parsed or cf7_field_name
		return array (
			"odoo_form_id" => $data["odoo_form_id"],
			"cf7_field_name" => $data["cf7_field_name"],
			"odoo_field_name" => $data["odoo_field_name"],
			"constant_value" => $data["constant_value"]
		);
	}

	protected function insert_data_types () {
		return array ("%d", "%s", "%s", "%s");
	}

}


class PutOdooFormMappings extends PutBaseSchema {
	
	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

	protected function update_data ($data) {
		return array(
			"odoo_form_id" => $data["odoo_form_id"],
			"cf7_field_name" => $data["cf7_field_name"],
			"odoo_field_name" => $data["odoo_field_name"]
		);
	}
}


class DeleteOdooFromMappings extends DeleteBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

}

function base_odoo_form_mappings_schema ($properties) {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => "Odoo Form Mapping",
		"type" => "object",
		"properties" => $properties,
	);
}

function base_odoo_form_mappings_properties () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Form Mapping"),
		),
		"odoo_form_id" => array(
			"type" => "int",
			"description" => esc_html__("Foreign key for the Odoo Form that relates to the Odoo Form Mapping"),
		),
		"cf7_field_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the field on the Contact 7 Form"),
		),
		"odoo_field_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the field in Odoo that the value shall fill"),
		),
		"constant_value" => array(
			"type" => "string",
			"description" => esc_html__("If there is a default value that needs to be mapped when filling in a form"),
		),
	);
}

function base_odoo_form_mappings_arguments () {
	return array(
		"odoo_form_id" => array(
			"type" => "int",
			"description" => esc_html__("Foreign key for the connection that relates to the form"),
			"required" => true,
		),
		"cf7_field_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the field on the contact 7 form"),
			"required" => true,
		),
		"odoo_field_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the field in Odoo that the value shall fill"),
			"required" => true,
		),
		"constant_value" => array(
			"type" => "string",
			"description" => esc_html__("If there is a default value that needs to be mapped when filling in a form"),
			"required" => true,
		),
	);
}

function get_odoo_from_mappings ($data) {
	$get_odoo_form_mappings = new GetOdooFormMappings();
	$response = $get_odoo_form_mappings->request($data);
	return $response;
}

function get_odoo_form_mappings_schema () {
	return base_odoo_form_mappings_schema(
		array(
			"type" => "array",
			"items" => array(
				"type" => "object",
				base_odoo_form_mappings_properties() + 
				array(
					"odoo_form_name" => array(
						"type" => "string",
						"description" => esc_html__("The name of the Odoo Connection instance"),
					),
				),
			),
		),
	);
}

function get_odoo_form_mapping_arguments () {
	return base_get_request_arguments();	
}

function create_odoo_form_mapping ($data) {
	$post_odoo_form_mappings = new PostOdooFormMappings();
	$response = $post_odoo_form_mappings->request($data);
	return $response;
}

function create_odoo_form_mapping_schema () {
	return base_odoo_form_mappings_schema(base_odoo_form_mappings_properties());
}

function create_odoo_form_mapping_arguments () {
	return base_odoo_form_mappings_arguments();
}

function update_odoo_form_mapping ($data) {
	$id = $data["id"];
	$put_odoo_form_mappings = new PutOdooFormMappings($id);
	$response = $put_odoo_form_mappings->request($data);
	return $response;
}

function update_odoo_form_mapping_schema () {
	return base_odoo_form_mappings_schema(base_odoo_form_mappings_properties());
}

function update_odoo_form_mapping_arguments () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Form Mapping"),
			"required" => true,
		),
	) + base_odoo_form_mappings_arguments();
}

function delete_odoo_form_mapping ($data) {
	$delete_odoo_form_mapping = new DeleteOdooFromMappings();
	$response = $delete_odoo_form_mapping->request($data);
	return $response;
}

function delete_odoo_form_mapping_schema () {
	return base_delete_request_schema("Odoo Form Mapping");
}

function delete_odoo_form_mapping_arguments () {
	return base_delete_arguments();
}

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-form-mappings", array(
		array(
			"methods" => "GET",
			"callback" => "get_odoo_from_mappings",
			"args" => get_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "get_odoo_form_mappings_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/create-odoo-form-mapping", array(
		array(
			"methods" => "POST",
			"callback" => "create_odoo_form_mapping",
			"args" => create_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "create_odoo_form_mapping_schema"
	));

	register_rest_route ( "odoo-conn/v1", "/update-odoo-form-mapping", array(
		array(
			"methods" => "PUT",
			"callback" => "update_odoo_form_mapping",
			"args" => update_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "update_odoo_form_mapping_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/delete-odoo-form-mapping", array(
		array(
			"methods" => "DELETE",
			"callback" => "delete_odoo_form_mapping",
			"args" => delete_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "delete_odoo_form_mapping_schema",
	));
});


?>

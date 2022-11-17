<?php

namespace odoo_conn\admin\api\endpoints;


trait OdooConnFormMappingTableName {

	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

}


trait OdooConnFormMappingColumns {

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

}

class OdooConnGetOdooFormMappings extends OdooConnGetBaseSchema {

	use OdooConnFormMappingTableName;
	use OdooConnFormMappingColumns;

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


class OdooConnPostOdooFormMappings extends OdooConnPostBaseSchema { 
	
	use OdooConnFormMappingTableName;

	protected function parse_data ($data) {
		return array(
			"odoo_form_id" => $data["odoo_form_id"],
			"cf7_field_name" => $data["cf7_field_name"],
			"odoo_field_name" => $data["odoo_field_name"],
			"constant_value" => $data["constant_value"],
		);
	}

	protected function insert_data_types () {
		return array ("%d", "%s", "%s", "%s");
	}

}


class OdooConnPutOdooFormMappings extends OdooConnPutBaseSchema {
	
	use OdooConnFormMappingTableName;

	protected function update_data ($data) {
		$parsed_data = [];

		if ( isset( $data["cf7_field_name"] ) && !empty( $data["cf7_field_name"] ) ) {
			$parsed_data["cf7_field_name"] = $data["cf7_field_name"];
		}

		if ( isset( $data["constant_value"] ) && !empty( $data["constant_value"] ) ) {
			$parsed_data["constant_value"] = $data["constant_value"];
		}

		if ( isset( $parsed_data["constant_value"] ) && isset( $parsed_data["cf7_field_name"] ) ) {
			throw new \Exception("Can't pass both a constant value and a cf7 field name as arguments");
		}

		return $parsed_data + array(
			"odoo_form_id" => $data["odoo_form_id"],
			"odoo_field_name" => $data["odoo_field_name"],
		);
	}
}


class OdooConnDeleteOdooFormMappings extends OdooConnDeleteBaseSchema {

	use OdooConnFormMappingTableName;

}

function odoo_conn_base_odoo_form_mappings_schema ($properties) {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => "Odoo Form Mapping",
		"type" => "object",
		"properties" => $properties,
	);
}

function odoo_conn_base_odoo_form_mappings_properties () {
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

function odoo_conn_base_odoo_form_mappings_arguments () {
	return array(
		"odoo_form_id" => array(
			"type" => "int",
			"description" => esc_html__("Foreign key for the connection that relates to the form"),
			"required" => true,
		),
		"cf7_field_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the field on the contact 7 form"),
		),
		"odoo_field_name" => array(
			"type" => "string",
			"description" => esc_html__("The name of the field in Odoo that the value shall fill"),
			"required" => true,
		),
		"constant_value" => array(
			"type" => "string",
			"description" => esc_html__("If there is a default value that needs to be mapped when filling in a form"),
		),
	);
}

function odoo_conn_get_odoo_from_mappings ($data) {
	$get_odoo_form_mappings = new OdooConnGetOdooFormMappings();
	$response = $get_odoo_form_mappings->request($data);
	return $response;
}

function odoo_conn_get_odoo_form_mappings_schema () {
	return odoo_conn_base_odoo_form_mappings_schema(
		array(
			"type" => "array",
			"items" => array(
				"type" => "object",
				odoo_conn_base_odoo_form_mappings_properties() + 
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

function odoo_conn_get_odoo_form_mapping_arguments () {
	return odoo_conn_base_get_request_arguments();	
}

function odoo_conn_create_odoo_form_mapping ($data) {
	$post_odoo_form_mappings = new OdooConnPostOdooFormMappings();
	$response = $post_odoo_form_mappings->request($data);
	return $response;
}

function odoo_conn_create_odoo_form_mapping_schema () {
	return odoo_conn_base_odoo_form_mappings_schema(odoo_conn_base_odoo_form_mappings_properties());
}

function odoo_conn_create_odoo_form_mapping_arguments () {
	return odoo_conn_base_odoo_form_mappings_arguments();
}

function odoo_conn_update_odoo_form_mapping ($data) {
	$id = $data["id"];
	$put_odoo_form_mappings = new OdooConnPutOdooFormMappings($id);
	$response = $put_odoo_form_mappings->request($data);
	return $response;
}

function odoo_conn_update_odoo_form_mapping_schema () {
	return odoo_conn_base_odoo_form_mappings_schema(odoo_conn_base_odoo_form_mappings_properties());
}

function odoo_conn_update_odoo_form_mapping_arguments () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Form Mapping"),
			"required" => true,
		),
	) + odoo_conn_base_odoo_form_mappings_arguments();
}

function odoo_conn_delete_odoo_form_mapping ($data) {
	$delete_odoo_form_mapping = new OdooConnDeleteOdooFromMappings();
	$response = $delete_odoo_form_mapping->request($data);
	return $response;
}

function odoo_conn_delete_odoo_form_mapping_schema () {
	return odoo_conn_base_delete_request_schema("Odoo Form Mapping");
}

function odoo_conn_delete_odoo_form_mapping_arguments () {
	return odoo_conn_base_delete_arguments();
}

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-form-mappings", array(
		array(
			"methods" => "GET",
			"callback" => "odoo_conn_get_odoo_from_mappings",
			"args" => odoo_conn_get_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "odoo_conn_get_odoo_form_mappings_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/create-odoo-form-mapping", array(
		array(
			"methods" => "POST",
			"callback" => "odoo_conn_create_odoo_form_mapping",
			"args" => odoo_conn_create_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "odoo_conn_create_odoo_form_mapping_schema"
	));

	register_rest_route ( "odoo-conn/v1", "/update-odoo-form-mapping", array(
		array(
			"methods" => "PUT",
			"callback" => "odoo_conn_update_odoo_form_mapping",
			"args" => odoo_conn_update_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "odoo_conn_update_odoo_form_mapping_schema",
	));

	register_rest_route ( "odoo-conn/v1", "/delete-odoo-form-mapping", array(
		array(
			"methods" => "DELETE",
			"callback" => "odoo_conn_delete_odoo_form_mapping",
			"args" => odoo_conn_delete_odoo_form_mapping_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "odoo_conn_delete_odoo_form_mapping_schema",
	));
});


?>

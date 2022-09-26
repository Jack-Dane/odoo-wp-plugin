<?php

class GetOdooFormMappings extends GetBaseSchema {

	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

}


class PostOdooFormMappings extends PostBaseSchema { 
	
	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

	protected function parse_data ($data) {
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


function get_odoo_from_mappings ($data) {
	$get_odoo_form_mappings = new GetOdooFormMappings();
	$response = $get_odoo_form_mappings->request($data);
	return $response;
}

function create_odoo_form_mapping ($data) {
	$post_odoo_form_mappings = new PostOdooFormMappings();
	$response = $post_odoo_form_mappings->request($data);
	return $response;
}

function update_odoo_form_mapping ($data) {
	$id = $data["id"];
	$put_odoo_form_mappings = new PutOdooFormMappings($id);
	$response = $put_odoo_form_mappings->request($data);
	return $response;
}

function delete_odoo_form_mapping ($data) {
	$delete_odoo_form_mapping = new DeleteOdooFromMappings();
	$response = $delete_odoo_form_mapping->request($data);
	return $response;
}

add_action ( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-odoo-form-mappings", array(
		"methods" => "GET",
		"callback" => "get_odoo_from_mappings",
		"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route ( "odoo-conn/v1", "/create-odoo-form-mapping", array(
		"methods" => "POST",
		"callback" => "create_odoo_form_mapping",
		"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route ( "odoo-conn/v1", "/update-odoo-form-mapping", array(
		"methods" => "PUT",
		"callback" => "update_odoo_form_mapping",
		"permission_callback" => "is_authorised_to_request_data",
	));

	register_rest_route ( "odoo-conn/v1", "/delete-odoo-form-mapping", array(
		"methods" => "DELETE",
		"callback" => "delete_odoo_form_mapping",
		"permission_callback" => "is_authorised_to_request_data",
	));
});


?>

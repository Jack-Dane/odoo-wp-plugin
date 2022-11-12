<?php

namespace odoo_conn\admin\api\endpoints;


class OdooConnGetContact7Form extends GetExtendedSchema {

	public function __construct () {
		parent::__construct($where_condition="post_type='wpcf7_contact_form'");
	}

	protected function get_columns () {
		return "ID, post_title";
	}

	protected function get_table_name () {
		return "posts";
	}

}

function get_contact_7_forms ($data) {
	$get_contact_7_form = new OdooConnGetContact7Form();
	$response = $get_contact_7_form->request($data);
	return $response;
}

function get_contact_7_forms_schema () {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => "Contact 7 Form",
		"type" => "object",
		"properties" => array(
			"type" => "array",
			"items" => array(
				"type" => "object",
				"properties" => array(
					"ID" => array(
						"type" => "integer",
						"description" => esc_html__("Primary key for the Contact 7 Form"),
					),
					"odoo_connection_id" => array(
						"type" => "string",
						"description" => esc_html__("The Title of the Contact 7 Form"),
					),
				),
			),
		),
	);
}

function get_contact_7_forms_arguments () {
	return base_get_request_arguments();
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-contact-7-forms", array(
		array(
			"methods" => "GET",
			"callback" => "get_contact_7_forms",
			"args" => get_contact_7_forms_arguments(),
		),
		"permission_callback" => "is_authorised_to_request_data",
		"schema" => "get_contact_7_forms_schema",
	));
});

?>
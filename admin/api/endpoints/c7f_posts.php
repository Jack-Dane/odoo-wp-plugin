<?php

class GetContact7Form extends GetExtendedSchema {

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
	$get_contact_7_form = new GetContact7Form();
	$response = $get_contact_7_form->request($data);
	return $response;
}

add_action( "rest_api_init", function () {
	register_rest_route ( "odoo-conn/v1", "/get-contact-7-forms", array(
		"methods" => "GET",
		"callback" => "get_contact_7_forms",
		"permission_callback" => "is_authorised_to_request_data",
	));
});

?>
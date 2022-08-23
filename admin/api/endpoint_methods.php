<?php 

function create_odoo_connection ($data) {
	global $wpdb, $table_prefix;

	$wpdb->insert(
		$table_prefix . "odoo_conn_connection",
		array (
			"name" => $data["name"],
			"username" => $data["username"],
			"api_key" => $data["api_key"],
			"url" => $data["url"],
			"database_name" => $data["database_name"]
		)
	);
}

function get_odoo_connections ($data) {
	global $wpdb, $table_prefix;

	$results = $wpdb->get_results("SELECT * FROM " . $table_prefix . "odoo_conn_connection");
	return $results;
}

function create_odoo_form ($data) {
	global $wpdb, $table_prefix;

	$wpdb->insert(
		$table_prefix . "odoo_conn_form",
		array (
			"odoo_connection_id" => $data["odoo_connection_id"],
			"odoo_model" => $data["odoo_model"],
			"name" => $data["name"],
			"contact_7_id" => $data["contact_7_id"]
		),
		array (
			"%d",
			"%s",
			"%s",
			"%d"
		)
	);
}

function get_odoo_forms ($data) {
	global $wpdb, $table_prefix;

	$results = $wpdb->get_results("SELECT * FROM " . $table_prefix . "odoo_conn_form");
	return $results;
}

function create_odoo_form_mapping ($data) {
	global $wpdb, $table_prefix;

	$wpdb->insert(
		$table_prefix . "odoo_conn_form_mapping",
		array (
			"odoo_form_id" => $data["odoo_form_id"],
			"cf7_field_name" => $data["cf7_field_name"],
			"odoo_field_name" => $data["odoo_field_name"]
		),
		array (
			"%d",
			"%s",
			"%s"
		)
	);
}

function get_odoo_from_mappings ($data) {
	global $wpdb, $table_prefix;

	$results = $wpdb->get_results("SELECT * FROM " . $table_prefix . "odoo_conn_form_mapping");
	return $results;
}

?>
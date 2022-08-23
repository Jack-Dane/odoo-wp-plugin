<?php

function echo_headers ($headers) {
	echo "<tr>";
	foreach ($headers as $header) {
		echo "<th>" . $header . "</th>";
	}
	echo "</tr>";
}

function echo_row ($row, $column_names) {
	echo "<tr>";
	foreach ($column_names as $column_name) {
		echo "<td>" . $row[$column_name] . "</td>";
	}
	echo "</tr>";
}

function get_connection_data () {
	global $wpdb;

	$column_names = ["id", "name", "username", "api_key", "url", "database_name"];

	$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_connection", ARRAY_A );

	echo "<table>";
	echo echo_headers($column_names);
	foreach ($rows as $row) {
		echo_row($row, $column_names);
	}
	echo "</table>";
}

function get_form_data () {
	global $wpdb;

	$column_names = ["id", "odoo_connection_id", "name", "contact_7_id"];

	$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_form", ARRAY_A );

	echo "<table>";
	echo_headers($column_names);
	foreach ($rows as $row) {
		echo_row($row, $column_names);
	}
	echo "</table>";
}

function get_odoo_mappings () {
	global $wpdb;

	$column_names = ["id", "odoo_submit_id", "cf7_field_name", "odoo_field_name"];

	$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_form_mapping", ARRAY_A );

	echo "<table>";
	echo_headers($column_names);
	foreach ($rows as $row) {
		echo_row($row, $column_names);
	}
	echo "</table>";
}

?>
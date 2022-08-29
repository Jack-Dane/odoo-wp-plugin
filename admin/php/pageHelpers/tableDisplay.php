<?php

// register styles used
add_action('admin_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    wp_enqueue_style( "table-style", plugins_url("/odoo-conn/admin/php/pageHelpers/table_style.css") );
}

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

function get_page_buttons ($display_next) {
	$page = isset($_GET["p"]) ? htmlspecialchars($_GET["p"]) : 0;
	$wp_page = htmlspecialchars($_GET["page"]);
	$previous_page = $page - 1;
	$next_page = $page + 1;

	echo "<div class='table-buttons'>";
	if ($page > 0) {
		echo "<a id='previous-button' href='?p={$previous_page}&page={$wp_page}'>Previous</a>";
	}
	if ($display_next) {
		echo "<a id='next-button' href='?p={$next_page}&page={$wp_page}'>Next</a>";
	}
	echo "</div>";
}

function get_connection_data () {
	global $wpdb;

	$page = isset($_GET["p"]) ? htmlspecialchars($_GET["p"]) * 10 : 0;
	$column_names = ["id", "name", "username", "api_key", "url", "database_name"];

	$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_connection ORDER BY id LIMIT {$page}, 11", ARRAY_A );
	$next_page = count($rows) == 11;
	if ($next_page) {
		array_pop($rows);
	}

	echo "<table class='database-table'>";
	echo echo_headers($column_names);
	foreach ($rows as $row) {
		echo_row($row, $column_names);
	}
	echo "</table>";

	get_page_buttons($next_page);
}

function get_form_data () {
	global $wpdb;

	$page = isset($_GET["p"]) ? htmlspecialchars($_GET["p"]) * 10 : 0;
	$column_names = ["id", "odoo_connection_id", "name", "contact_7_id"];

	$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_form ORDER BY id LIMIT {$page}, 11", ARRAY_A );
	$next_page = count($rows) == 11;
	if ($next_page) {
		array_pop($rows);
	}

	echo "<table class='database-table'>";
	echo_headers($column_names);
	foreach ($rows as $row) {
		echo_row($row, $column_names);
	}
	echo "</table>";

	get_page_buttons($next_page);
}

function get_odoo_mappings () {
	global $wpdb;

	$page = isset($_GET["p"]) ? htmlspecialchars($_GET["p"]) * 10 : 0;
	$column_names = ["id", "odoo_form_id", "cf7_field_name", "odoo_field_name"];

	$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_form_mapping ORDER BY id LIMIT {$page}, 11", ARRAY_A );
	$next_page = count($rows) == 11;
	if ($next_page) {
		array_pop($rows);
	}

	echo "<table class='database-table'>";
	echo_headers($column_names);
	foreach ($rows as $row) {
		echo_row($row, $column_names);
	}
	echo "</table>";

	get_page_buttons($next_page);
}

?>
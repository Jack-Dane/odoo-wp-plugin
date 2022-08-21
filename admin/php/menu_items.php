<?php

include("pages/form_c7f_connection.php");
include("pages/connections.php");
include("pages/form_mapping.php");

function add_top_menu_page() {
	add_menu_page(
		"Odoo Submits",
		"Odoo Submits",
		"administrator",
		"odoo_connector_form_submit",
		"form_c7f_connection"
	);

	add_submenu_page(
		"odoo_connector_form_submit",
		"Odoo Connections",
		"Odoo Connections",
		"administrator",
		"odoo_connector_connections",
		"connections_page"
	);
	add_submenu_page(
		"odoo_connector_form_submit",
		"Odoo Forms",
		"Odoo Forms",
		"administrator",
		"odoo_connector_forms",
		"forms_page"
	);
}

?>
<?php

include("pages/odoo_form.php");
include("pages/odoo_connection.php");
include("pages/odoo_form_mapping.php");

function add_top_menu_page() {
	add_menu_page(
		"Odoo Froms",
		"Odoo Forms",
		"administrator",
		"odoo_form",
		"odoo_form_page"
	);

	add_submenu_page(
		"odoo_form",
		"Odoo Connections",
		"Odoo Connections",
		"administrator",
		"odoo_connection",
		"odoo_connection_page"
	);
	add_submenu_page(
		"odoo_form",
		"Odoo Form Mappings",
		"Odoo Form Mappings",
		"administrator",
		"odoo_form_mapping",
		"odoo_form_mapping_page"
	);
}

add_action("admin_menu", "add_top_menu_page");

?>
<?php

function activate_capabilities () {
	$role = get_role( "administrator" );
	$role->add_cap( "odoo_connector_edit", true );
}

add_action( "init", "activate_capabilities", 11 );

?>
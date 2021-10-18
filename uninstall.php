<?php

function odoo_conn_deactivation_function(){
	global $wpdb, $table_prefix;

	$tables_to_drop = array(
		"odoo_conn_field_mapping",
		"odoo_conn_submit",
		"odoo_conn_connection"
	);

	foreach($tables_to_drop as $table){
		$table_name = $table_prefix . $table;
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
	}
}

odoo_conn_deactivation_function();

?>
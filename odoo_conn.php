<?php
/*
Plugin Name: Contact 7 to Odoo connector
Plugin URI: https://www.jackdane.co.uk
Description: Connect your WordPress Contact 7 Forms to Odoo
Version: 0.0.1
*/

register_activation_hook(__FILE__,  "odoo_conn_activation_function");

register_deactivation_hook(__FILE__, "odoo_conn_deactivation_function");

function odoo_conn_activation_function(){
	require_once(ABSPATH . "wp-admin/includes/upgrade.php");

	create_odoo_connections_table();
	create_odoo_submit_table();
	create_odoo_submit_field_mapping();
}

function create_odoo_connections_table(){
	global $wpdb, $table_prefix;

	$table_name = $table_prefix . "odoo_conn_connection";
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
	CREATE TABLE `$table_name` (
	`id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(30) NOT NULL,
	`username` VARCHAR(100) NOT NULL,
	`api_key` VARCHAR(100) NOT NULL,
	`url` VARCHAR(300) NOT NULL,
	`database_name` VARCHAR(200) NOT NULL,
	PRIMARY KEY  (id)
	) $charset_collate;
	";

	dbDelta($sql);
}

function create_odoo_submit_table(){
	global $wpdb, $table_prefix;

	$table_name = $table_prefix . "odoo_conn_submit";
	$odoo_conn_table = $table_prefix . "odoo_conn_connection";
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
	CREATE TABLE `$table_name` (
	`id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
	`odoo_connection_id` MEDIUMINT(9) NOT NULL,
	`name` VARCHAR(30) NOT NULL,
	`contact_7_id` TINYINT(2) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (odoo_connection_id) REFERENCES $odoo_conn_table(id)
	) $charset_collate;
	";

	dbDelta($sql);
}

function create_odoo_submit_field_mapping(){
	global $wpdb, $table_prefix;

	$table_name = $table_prefix . "odoo_conn_field_mapping";
	$odoo_submit_table = $table_prefix . "odoo_conn_submit";

	$sql = "
	CREATE TABLE `$table_name` (
	`id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
	`odoo_submit_id` MEDIUMINT(9) NOT NULL,
	`odoo_model` VARCHAR(50) NOT NULL,
	`cf7_field_id` TINYINT(2) NOT NULL,
	`odoo_field_name` VARCHAR(100) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (odoo_submit_id) REFERENCES $odoo_submit_table(id)
	) $charset_collate;
	";

	dbDelta($sql);
}

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

?>
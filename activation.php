<?php

require_once("dependency_check.php");

function odoo_conn_activation_function () {
	require_once(ABSPATH . "wp-admin/includes/upgrade.php");

	create_odoo_connections_table();
	create_odoo_form_table();
	create_odoo_form_field_mapping();
}

function create_odoo_connections_table () {
	global $wpdb, $table_prefix;

	$table_name = $table_prefix . "odoo_conn_connection";
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
	CREATE TABLE `$table_name` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
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

function create_odoo_form_table () {
	global $wpdb, $table_prefix;

	$table_name = $table_prefix . "odoo_conn_form";
	$odoo_conn_table = $table_prefix . "odoo_conn_connection";
	// contact form 7 stores forms in the generic wordpress posts
	// to know if it is a contact 7 form "post": post_type = wpcf7_contact_form
	$contact_7_form_table = $table_prefix . "posts";
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
	CREATE TABLE `$table_name` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`odoo_connection_id` BIGINT(20) UNSIGNED NOT NULL,
	`odoo_model` VARCHAR(50) NOT NULL,
	`name` VARCHAR(30) NOT NULL,
	`contact_7_id` BIGINT(20) UNSIGNED NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (odoo_connection_id) REFERENCES $odoo_conn_table(id) ON DELETE CASCADE,
	FOREIGN KEY (contact_7_id) REFERENCES $contact_7_form_table(ID) ON DELETE CASCADE
	) $charset_collate;
	";

	dbDelta($sql);
}

function create_odoo_form_field_mapping () {
	global $wpdb, $table_prefix;

	$table_name = $table_prefix . "odoo_conn_form_mapping";
	$odoo_form_table = $table_prefix . "odoo_conn_form";

	$sql = "
	CREATE TABLE `$table_name` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`odoo_form_id` BIGINT(20) UNSIGNED NOT NULL,
	`cf7_field_name` VARCHAR(100),
	`odoo_field_name` VARCHAR(100) NOT NULL,
	`constant_value` VARCHAR(200),
	PRIMARY KEY(id),
	FOREIGN KEY (odoo_form_id) REFERENCES $odoo_form_table(id) ON DELETE CASCADE
	) $charset_collate;
	";

	dbDelta($sql);
}

?>
<?php 

function send_odoo_data ($wpcf) {

	$wpcf7 = WPCF7_ContactForm::get_current();

	$submission = WPCF7_Submission::get_instance();
    $contact_form_id = $contact_form->id;
    $forms = get_forms_from_database($contact_form_id);
    $posted_data = $submission->get_posted_data();

    foreach ($forms as $form) {
    	$odoo_model = $form->odoo_model;
    	$connection = get_connection_from_database($form->odoo_connection_id);
    	$field_mappings = get_field_mappings_from_database($form->id);

    	$odoo_field_data = array();
    	foreach ($field_mappings as $field_mapping) {
    		if ($posted_data[$field_mapping->cf7_field_name] != "") {
    			$cf7_field_value = $posted_data[$field_mapping->cf7_field_name];
    		} else {
    			$cf7_field_value = $field_mapping->constant_value;
    		}
    		$odoo_field_data[$field_mapping->odoo_field_name] = $cf7_field_value;
    	}

    	if (count($field_mappings) == 0) {
    		error_log("Not sending data as there isn't any form field mappings.");
    		return $wpcf;
    	}

    	send_form_data_to_odoo($connection, $odoo_model, $odoo_field_data);
    }

    return $wpcf;
}

function send_form_data_to_odoo ($connection, $odoo_model, $odoo_field_data) {
	$username = $connection->username;
	$api_key = $connection->api_key;
	$database = $connection->database_name;
	$url = $connection->url;
	$odoo_field_data = array($odoo_field_data);
	$odoo_connector = new OdooConnector($username, $api_key, $database, $url);
	$objectId = $odoo_connector->createObject($odoo_model, $odoo_field_data);
}

function get_field_mappings_from_database ($odoo_form_id) {
	global $wpdb;

	$form_mappings = $wpdb->get_results( 
		"SELECT * FROM {$wpdb->prefix}odoo_conn_form_mapping WHERE odoo_form_id = {$odoo_form_id}", OBJECT 
	);

	return $form_mappings;
}

function get_connection_from_database ($connection_id) {
	global $wpdb;

	$connections = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_connection WHERE id = {$connection_id}", OBJECT );

	return $connections[0];
}

function get_forms_from_database ($contact_form_id) {
	global $wpdb;

	$forms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}odoo_conn_form", OBJECT );

	return $forms;
}

add_action( "wpcf7_before_send_mail", "send_odoo_data");

?>
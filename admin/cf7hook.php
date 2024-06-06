<?php

namespace odoo_conn\admin\php\cf7hook;


use odoo_conn\admin\odoo_connector\OdooConnException;
use odoo_conn\admin\odoo_connector\OdooConnOdooConnector;
use odoo_conn\admin\odoo_connector\OdooConnXMLRPCStringField;
use odoo_conn\admin\odoo_connector\OdooConnXMLRPCStringX2ManyField;
use odoo_conn\admin\odoo_connector\OdooConnXMLRPCBaseX2ManyField;
use odoo_conn\encryption\OdooConnEncryptionFileHandler;
use odoo_conn\encryption\OdooConnEncryptionHandler;


class DatabaseHandler
{

    public function get_field_mappings_from_database($odoo_form_id)
    {
        global $wpdb;

        $form_mappings = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}odoo_conn_form_mapping WHERE odoo_form_id=%d",
                array($odoo_form_id)
            ), OBJECT
        );

        return $form_mappings;
    }

    public function get_connection_from_database($connection_id)
    {
        global $wpdb;

        $connections = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}odoo_conn_connection WHERE id=%d",
                array($connection_id)
            ), OBJECT
        );

        return $connections[0];
    }

    public function get_forms_from_database($contact_form_id)
    {
        global $wpdb;

        $forms = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}odoo_conn_form WHERE contact_7_id=%d",
                array($contact_form_id)
            ), OBJECT
        );

        return $forms;
    }

    public function insert_error($cf7_form_id, $error_message)
    {
        global $wpdb;

        $current_datetime = new \DateTime();

        $wpdb->insert(
            $wpdb->prefix . "odoo_conn_errors",
            array(
                "contact_7_id" => $cf7_form_id,
                "error_message" => $error_message,
                "time_occurred" => $current_datetime->format("Y-m-d H:i:s")
            ),
            array(
                "%d", "%s", "%s"
            )
        );
    }

}


class OdooConnContactForm7Hook
{

    public function __construct(
        $wpcf7_contact_form, $wpcf7_submission, $encrypter, $database_handler
    )
    {
        $this->wpcf7_contact_form = $wpcf7_contact_form;
        $this->wpcf7_submission = $wpcf7_submission;
        $this->contact_form_id = $this->wpcf7_contact_form->id;
        $this->encrypter = $encrypter;
        $this->database_handler = $database_handler;
    }

    public function send_odoo_data($wpcf)
    {
        $forms = $this->database_handler->get_forms_from_database($this->contact_form_id);
        $posted_data = $this->wpcf7_submission->get_posted_data();

        foreach ($forms as $form) {
            $odoo_model = $form->odoo_model;
            $connection = $this->database_handler->get_connection_from_database(
                $form->odoo_connection_id
            );
            $field_mappings = $this->database_handler->get_field_mappings_from_database(
                $form->id
            );

            if (count($field_mappings) == 0) {
                error_log("Not sending data as there isn't any form field mappings.");
                return $wpcf;
            }

            $odoo_field_data = [];
            foreach ($field_mappings as $field_mapping) {
                $cf7_field_value = $field_mapping->cf7_field_name ?
                    $posted_data[$field_mapping->cf7_field_name] : $field_mapping->constant_value;

                if ($field_mapping->x_2_many) {
                    if (is_array($cf7_field_value)) {
                        $cf7_field_value = new OdooConnXMLRPCBaseX2ManyField(
                            $field_mapping->odoo_field_name,
                            $cf7_field_value
                        );
                    } else {
                        $cf7_field_value = new OdooConnXMLRPCStringX2ManyField(
                            $field_mapping->odoo_field_name,
                            $cf7_field_value
                        );
                    }
                } else {
                    if (is_array($cf7_field_value)) {
                        // multiple choice input
                        // implode as multiple options can be selected at the same time
                        $cf7_field_value = implode(", ", $cf7_field_value);
                    }

                    $cf7_field_value = new OdooConnXMLRPCStringField(
                        $field_mapping->odoo_field_name,
                        $cf7_field_value
                    );
                }

                $odoo_field_data[] = $cf7_field_value;
            }

            $this->send_form_data_to_odoo($connection, $odoo_model, $odoo_field_data);
        }

        return $wpcf;
    }

    private function send_form_data_to_odoo($connection, $odoo_model, $odoo_field_data)
    {
        $username = $connection->username;
        $api_key = $this->encrypter->decrypt($connection->api_key);
        $database = $connection->database_name;
        $url = $connection->url;
        $odoo_connector = $this->create_odoo_connection(
            $username, $api_key, $database, $url
        );

        try {
            $odoo_connector->create_object($odoo_model, $odoo_field_data);
        } catch (OdooConnException $odoo_conn_exception) {
            $this->log_error($odoo_conn_exception);
        }

    }

    private function log_error($odoo_conn_exception)
    {
        $this->database_handler->insert_error(
            $this->contact_form_id, $odoo_conn_exception->getMessage()
        );
    }

    public function create_odoo_connection($username, $api_key, $database, $url)
    {
        return new OdooConnOdooConnector(
            $username, $api_key, $database, $url
        );
    }

}


function send_odoo_data($wpcf)
{
    $wpcf7_contact_form = \WPCF7_ContactForm::get_current();
    $wpcf7_submission = \WPCF7_Submission::get_instance();
    $encryption_file_handler = new OdooConnEncryptionFileHandler();
    $encryption_handler = new OdooConnEncryptionHandler($encryption_file_handler);
    $database_handler = new DatabaseHandler();
    $odoo_conn_contact_form_7_hook = new OdooConnContactForm7Hook(
        $wpcf7_contact_form, $wpcf7_submission, $encryption_handler, $database_handler
    );
    return $odoo_conn_contact_form_7_hook->send_odoo_data($wpcf);
}

add_action("wpcf7_before_send_mail", __NAMESPACE__ . "\\send_odoo_data");

?>
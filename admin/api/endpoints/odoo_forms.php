<?php

namespace odoo_conn\admin\api\endpoints;

use odoo_conn\admin\database_connection\OdooConnGetOdooForm;


function odoo_conn_base_odoo_forms_schema()
{
    return array(
        "\$schema" => "https://json-schema.org/draft/2020-12/schema",
        "title" => "Odoo Form",
        "type" => "object",
        "properties" => [
            "type" => "array",
            "items" => array(
                "type" => "object",
                "properties" => array(
                    "id" => array(
                        "type" => "integer",
                        "description" => esc_html__("Primary key for an Odoo Form"),
                    ),
                    "odoo_connection_id" => array(
                        "type" => "integer",
                        "description" => esc_html__("Foreign key for the connection that relates to the Odoo Form"),
                    ),
                    "odoo_model" => array(
                        "type" => "string",
                        "description" => esc_html__("The name of the Odoo model that will be created when the form is submitted"),
                    ),
                    "name" => array(
                        "type" => "string",
                        "description" => esc_html__("The name of the Odoo Form instance"),
                    ),
                    "contact_7_id" => array(
                        "type" => "integer",
                        "description" => esc_html__("Foreign key for the Contact 7 Form that is submitted"),
                    ),
                    "odoo_connection_name" => array(
                        "type" => "string",
                        "description" => esc_html__("The name of the connection name from the Connection object")
                    ),
                    "contact_7_title" => array(
                        "type" => "string",
                        "description" => esc_html__("Title of the Contact 7 Form that is submitted")
                    ),
                )
            ),
        ],
    );
}

function odoo_conn_get_odoo_forms($data)
{
    $get_odoo_forms = new OdooConnGetOdooForm();
    return $get_odoo_forms->request($data);
}

add_action("rest_api_init", function () {
    register_rest_route("odoo_conn/v1", "/get-odoo-forms", array(
        array(
            "methods" => "GET",
            "callback" => __NAMESPACE__ . "\\odoo_conn_get_odoo_forms",
            "args" => odoo_conn_base_get_request_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_base_odoo_forms_schema",
    ));
});

?>
<?php

namespace odoo_conn\admin\api\endpoints;


class OdooConnGetContact7Form extends OdooConnGetExtendedSchema
{

    public function __construct()
    {
        parent::__construct("wpcf7_contact_form");
    }

    protected function get_public_key()
    {
        return "ID";
    }

    protected function get_columns()
    {
        return "ID, post_title";
    }

    protected function get_table_name()
    {
        global $wpdb;

        return $wpdb->posts;
    }

    protected function where_query()
    {
        return "post_type=%s";
    }
}

function odoo_conn_get_contact_7_forms($data)
{
    $get_contact_7_form = new OdooConnGetContact7Form();
    $response = $get_contact_7_form->request($data);
    return $response;
}

function odoo_conn_get_contact_7_forms_schema()
{
    return array(
        "\$schema" => "https://json-schema.org/draft/2020-12/schema",
        "title" => "Contact 7 Form",
        "type" => "object",
        "properties" => array(
            "type" => "array",
            "items" => array(
                "type" => "object",
                "properties" => array(
                    "ID" => array(
                        "type" => "integer",
                        "description" => esc_html__("Primary key for the Contact 7 Form"),
                    ),
                    "odoo_connection_id" => array(
                        "type" => "string",
                        "description" => esc_html__("The Title of the Contact 7 Form"),
                    ),
                ),
            ),
        ),
    );
}

add_action("rest_api_init", function () {
    register_rest_route("odoo_conn/v1", "/get-contact-7-forms", array(
        array(
            "methods" => "GET",
            "callback" => __NAMESPACE__ . "\\odoo_conn_get_contact_7_forms",
            "args" => odoo_conn_base_get_request_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_get_contact_7_forms_schema",
    ));
});

?>
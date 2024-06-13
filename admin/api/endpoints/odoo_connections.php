<?php

namespace odoo_conn\admin\api\endpoints;

use odoo_conn\admin\database_connection\OdooConnGetOdooConnection;


function odoo_conn_base_odoo_connections_schema()
{
    return array(
        "\$schema" => "https://json-schema.org/draft/2020-12/schema",
        "title" => "Odoo Connection",
        "type" => "object",
        "properties" => array(
            "type" => "array",
            "items" => array(
                "type" => "object",
                "properties" => array(
                    "id" => array(
                        "type" => "integer",
                        "description" => esc_html__("Primary key for an Odoo Connection"),
                    ),
                    "name" => array(
                        "type" => "string",
                        "description" => esc_html__("The name of the Odoo Connection instance"),
                    ),
                    "url" => array(
                        "type" => "string",
                        "description" => esc_html__("The URL of the Odoo database"),
                    ),
                    "database_name" => array(
                        "type" => "string",
                        "description" => esc_html__("The name of the database to connect to"),
                    ),
                )
            )
        )
    );
}

function odoo_conn_get_odoo_connections($data)
{
    $get_odoo_connection = new OdooConnGetOdooConnection();
    return $get_odoo_connection->request($data);
}

add_action("rest_api_init", function () {
    register_rest_route("odoo_conn/v1", "/get-odoo-connections", array(
        array(
            "methods" => "GET",
            "callback" => __NAMESPACE__ . "\\odoo_conn_get_odoo_connections",
            "args" => odoo_conn_base_get_request_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_base_odoo_connections_schema",
    ));

});

?>

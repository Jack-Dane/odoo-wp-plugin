<?php

namespace odoo_conn\admin\api\endpoints;


trait OdooConnOdooErrorsTableName
{

    protected function get_table_name()
    {
        return "odoo_conn_errors";
    }

}


trait OdooConnOdooErrorsColumns
{

    protected function get_columns()
    {
        $columns = [
            "id",
            "contact_7_id",
            "time_occurred",
            "error_message",
        ];

        return implode(", ", $columns);
    }

}


class OdooConnGetOdooErrors extends OdooConnGetBaseSchema
{

    use OdooConnOdooErrorsTableName;
    use OdooConnOdooErrorsColumns;
}


class OdooConnDeleteOdooErrors extends OdooConnDeleteBaseSchema
{

    use OdooConnOdooErrorsTableName;
}


function odoo_conn_get_odoo_errors($data)
{
    $odoo_conn_get_odoo_errors = new OdooConnGetOdooErrors();
    return $odoo_conn_get_odoo_errors->request($data);
}


function odoo_conn_base_odoo_errors_schema_properties()
{
    return array(
        "id" => array(
            "type" => "integer",
            "description" => esc_html__("Primary key for an Odoo Error instance"),
        ),
        "contact_7_id" => array(
            "type" => "integer",
            "description" => esc_html__("The contact 7 form id where the error occurred"),
        ),
        "time_occurred" => array(
            "type" => "string",
            "description" => esc_html__("The Datetime at which the error occurred"),
        ),
        "error_message" => array(
            "type" => "string",
            "description" => esc_html__("The error message of the failed submission"),
        ),
    );
}


function odoo_conn_get_odoo_errors_schema()
{
    return odoo_conn_base_odoo_connections_schema(
        array(
            "type" => "array",
            "items" => array(
                "type" => "object",
                "properties" => odoo_conn_base_odoo_errors_schema_properties(),
            ),
        )
    );
}


function odoo_conn_delete_odoo_error($data) {
    $odoo_conn_delete_odoo_errors = new OdooConnDeleteOdooErrors();
    return $odoo_conn_delete_odoo_errors->request($data);
}


add_action("rest_api_init", function () {
    register_rest_route("odoo_conn/v1", "/get-odoo-errors", array(
        array(
            "methods" => "GET",
            "callback" => __NAMESPACE__ . "\\odoo_conn_get_odoo_errors",
            "args" => odoo_conn_base_get_request_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_get_odoo_connections_schema",
    ));
    register_rest_route("odoo_conn/v1", "/delete-odoo-error", array(
        array(
            "methods" => "DELETE",
            "callback" => __NAMESPACE__ . "\\odoo_conn_delete_odoo_error",
            "args" => odoo_conn_base_get_request_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_base_delete_request_schema",
    ));
});

<?php

namespace odoo_conn\admin\api\endpoints;


trait OdooConnOdooFormTableName
{

    protected function get_table_name()
    {
        global $table_prefix;

        return $table_prefix . "odoo_conn_form";
    }

}


trait OdooConnOdooFormColumns
{

    protected function get_columns()
    {
        global $wpdb, $table_prefix;

        $columns = [
            $table_prefix . "odoo_conn_form.id",
            $table_prefix . "odoo_conn_form.odoo_connection_id",
            $table_prefix . "odoo_conn_connection.name as 'odoo_connection_name'",
            $table_prefix . "odoo_conn_form.odoo_model",
            $table_prefix . "odoo_conn_form.name",
            $table_prefix . "odoo_conn_form.contact_7_id as 'contact_7_id'",
            $wpdb->posts . ".post_title as 'contact_7_title'"
        ];

        return implode(", ", $columns);
    }

}


class OdooConnGetOdooForm extends OdooConnGetBaseSchema
{

    use OdooConnOdooFormTableName;
    use OdooConnOdooFormColumns;

    protected function foreign_keys()
    {
        global $wpdb, $table_prefix;

        return [
            "odoo_connection_id" => [
                "table_name" => $table_prefix . "odoo_conn_connection",
                "column_name" => "id"
            ],
            "contact_7_id" => [
                "table_name" => $wpdb->posts,
                "column_name" => "ID"
            ]
        ];
    }

}


class OdooConnGetOdooFormSingle extends OdooConnGetExtendedSchema
{

    use OdooConnOdooFormTableName;

    public function request($data)
    {
        $connections = parent::request($data);
        return !$connections ? null : $connections[0];
    }

    protected function where_query()
    {
        return "id=%d";
    }
}


class OdooConnPostOdooForm extends OdooConnPostBaseSchema
{

    use OdooConnOdooFormTableName;

    protected function parse_data($data)
    {
        return array(
            "odoo_connection_id" => sanitize_text_field($data["odoo_connection_id"]),
            "odoo_model" => sanitize_text_field($data["odoo_model"]),
            "name" => sanitize_text_field($data["name"]),
            "contact_7_id" => sanitize_text_field($data["contact_7_id"])
        );
    }

    protected function insert_data_types()
    {
        return array("%d", "%s", "%s", "%d");
    }

}


class OdooConnPutOdooForm extends OdooConnPutBaseSchema
{

    use OdooConnOdooFormTableName;

    protected function update_data($data)
    {
        return array(
            "odoo_connection_id" => sanitize_text_field($data["odoo_connection_id"]),
            "name" => sanitize_text_field($data["name"]),
            "contact_7_id" => sanitize_text_field($data["contact_7_id"]),
            "odoo_model" => sanitize_text_field($data["odoo_model"]),
        );
    }

}


class OdooConnDeleteOdooForm extends OdooConnDeleteBaseSchema
{

    use OdooConnOdooFormTableName;
}

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
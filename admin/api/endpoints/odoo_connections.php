<?php

namespace odoo_conn\admin\api\endpoints;

use \odoo_conn\encryption\OdooConnEncryptionFileHandler;
use \odoo_conn\encryption\OdooConnEncryptionHandler;
use odoo_conn\odoo_connector\odoo_connector\OdooConnException;
use \odoo_conn\odoo_connector\odoo_connector\OdooConnOdooConnector;
use WP_Error;


trait OdooConnOdooConnectionTableName
{

    protected function get_table_name()
    {
        return "odoo_conn_connection";
    }

}


trait OdooConnOdooConnectionColumns
{

    protected function get_columns()
    {
        $columns = [
            "id",
            "name",
            "username",
            "url",
            "database_name"
        ];

        return implode(", ", $columns);
    }

}


class OdooConnGetOdooConnection extends OdooConnGetBaseSchema
{

    use OdooConnOdooConnectionTableName;
    use OdooConnOdooConnectionColumns;
}

class OdooConnGetOdooConnectionSingle extends OdooConnGetExtendedSchema
{

    use OdooConnOdooConnectionTableName;

    function __construct($id)
    {
        parent::__construct($id);
    }

    function request($data)
    {
        $connections = parent::request($data);
        return !$connections ? null : $connections[0];
    }

    protected function where_query()
    {
        return "id=%d";
    }
}


class OdooConnPostOdooConnection extends OdooConnPostBaseSchema
{

    use OdooConnOdooConnectionTableName;
    use OdooConnOdooConnectionColumns;

    public function __construct($encryption_handler)
    {
        $this->encryption_handler = $encryption_handler;
    }

    protected function parse_data($data)
    {
        $api_key = $data["api_key"];
        $encrypted_api_key = $this->encryption_handler->encrypt($api_key);

        return array(
            "name" => $data["name"],
            "username" => $data["username"],
            "api_key" => $encrypted_api_key,
            "url" => $data["url"],
            "database_name" => $data["database_name"],
        );
    }

    protected function insert_data_types()
    {
        return array("%s", "%s", "%s", "%s", "%s");
    }

}


class OdooConnPutOdooConnection extends OdooConnPutBaseSchema
{

    use OdooConnOdooConnectionTableName;
    use OdooConnOdooConnectionColumns;

    protected function update_data($data)
    {
        return array(
            "name" => $data["name"],
            "username" => $data["username"],
            "url" => $data["url"],
            "database_name" => $data["database_name"]
        );
    }

}


class OdooConnDeleteOdooConnection extends OdooConnDeleteBaseSchema
{

    use OdooConnOdooConnectionTableName;
}

function odoo_conn_base_odoo_connections_schema($properties)
{
    return array(
        "$schema" => "https://json-schema.org/draft/2020-12/schema",
        "title" => "Odoo Connection",
        "type" => "object",
        "properties" => $properties,
    );
}

function odoo_conn_base_odoo_connections_schema_properties()
{
    return array(
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
    );
}

function odoo_conn_base_odoo_connections_arguments()
{
    return array(
        "name" => array(
            "type" => "string",
            "description" => esc_html__("The name of the Odoo Connection instance"),
            "required" => true,
        ),
        "url" => array(
            "type" => "string",
            "description" => esc_html__("The URL of the Odoo database"),
            "required" => true,
        ),
        "database_name" => array(
            "type" => "string",
            "description" => esc_html__("The name of the database to connect to"),
            "required" => true,
        ),
    );
}

function odoo_conn_get_odoo_connections($data)
{
    $get_odoo_connection = new OdooConnGetOdooConnection();
    $response = $get_odoo_connection->request($data);
    return $response;
}

function odoo_conn_get_odoo_connections_schema()
{
    return odoo_conn_base_odoo_connections_schema(
        array(
            "type" => "array",
            "items" => array(
                "type" => "object",
                "properties" => odoo_conn_base_odoo_connections_schema_properties(),
            ),
        )
    );
    return $schema;
}

function odoo_conn_get_odoo_connections_arguments()
{
    return odoo_conn_base_get_request_arguments();
}

function odoo_conn_create_odoo_connection($data)
{
    $odoo_conn_file_hanlder = new OdooConnEncryptionFileHandler();
    $odoo_conn_encryption_handler = new OdooConnEncryptionHandler($odoo_conn_file_hanlder);
    $post_odoo_connection = new OdooConnPostOdooConnection($odoo_conn_encryption_handler);
    $response = $post_odoo_connection->request($data);
    return $response;
}

function odoo_conn_create_odoo_connection_schema()
{
    return odoo_conn_base_odoo_connections_schema(base_odoo_connections_schema_properties());
}

function odoo_conn_create_odoo_connection_arguments()
{
    return odoo_conn_base_odoo_connections_arguments() + array(
            "api_key" => array(
                "type" => "string",
                "description" => esc_html__("The API Key used to authenticate the Odoo Connection"),
                "required" => true,
            ),
        );
}

function odoo_conn_update_odoo_connection($data)
{
    $id = $data["id"];
    $put_odoo_connection = new OdooConnPutOdooConnection($id);
    $response = $put_odoo_connection->request($data);
    return $response;
}

function odoo_conn_update_odoo_connection_schema()
{
    return odoo_conn_base_odoo_connections_schema(odoo_conn_base_odoo_connections_schema_properties());
}

function odoo_conn_odoo_connection_id_argument()
{
    return array(
        "id" => array(
            "type" => "integer",
            "description" => esc_html__("Primary key for an Odoo Connection"),
            "required" => true,
        )
    );
}

function odoo_conn_test_odoo_connection($data)
{
    $id = $data["id"];
    $connection_getter = new OdooConnGetOdooConnectionSingle($id);

    $connection = $connection_getter->request($data);
    if (!$connection) {
        return new WP_Error(
            "no_connection",
            "No connection for that Id",
            array("status" => 404)
        );
    }

    $encryption_file_handler = new OdooConnEncryptionFileHandler();
    $encryption_handler = new OdooConnEncryptionHandler($encryption_file_handler);
    $decrypted_api_key = $encryption_handler->decrypt($connection->api_key);

    $odoo_connector = new OdooConnOdooConnector(
        $connection->username,
        $decrypted_api_key,
        $connection->database_name,
        $connection->url
    );

    $success = true;
    try {
        $odoo_connector->test_connection();
    } catch (OdooConnException $e) {
        return array(
            "success" => false,
            "error_string" => $e->getMessage(),
            "error_code" => $e->getCode()
        );
    }

    return array("success" => $success);
}

function odoo_conn_test_odoo_connection_schema_properties()
{
    return array(
        "success" => array(
            "type" => "boolean",
            "description" => esc_html__("If the connection test was successful"),
        ),
        "error_code" => array(
            "type" => "integer",
            "description" => esc_html__("Error Code when there is an error connecting to Odoo"),
        ),
        "error_string" => array(
            "type" => "string",
            "description" => esc_html__("Error String when there is an error connecting to Odoo"),
        )
    );
}


function odoo_conn_test_odoo_connection_schema()
{
    return odoo_conn_base_odoo_connections_schema(odoo_conn_test_odoo_connection_schema_properties());
}

function odoo_conn_update_odoo_connection_arguments()
{
    return odoo_conn_odoo_connection_id_argument() + odoo_conn_base_odoo_connections_arguments();
}

function odoo_conn_delete_odoo_connection($data)
{
    $delete_odoo_connection = new OdooConnDeleteOdooConnection();
    $response = $delete_odoo_connection->request($data);
    return $response;
}

function odoo_conn_delete_odoo_connection_schema()
{
    return odoo_conn_base_delete_request_schema("Odoo Connection");
}

function odoo_conn_delete_odoo_connection_arguments()
{
    return odoo_conn_base_delete_arguments();
}

add_action("rest_api_init", function () {
    register_rest_route("odoo_conn/v1", "/get-odoo-connections", array(
        array(
            "methods" => "GET",
            "callback" => __NAMESPACE__ . "\\odoo_conn_get_odoo_connections",
            "args" => odoo_conn_get_odoo_connections_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_get_odoo_connections_schema",
    ));

    register_rest_route("odoo_conn/v1", "/create-odoo-connection", array(
        array(
            "methods" => "POST",
            "callback" => __NAMESPACE__ . "\\odoo_conn_create_odoo_connection",
            "args" => odoo_conn_create_odoo_connection_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_create_odoo_connection_schema",
    ));

    register_rest_route("odoo_conn/v1", "/update-odoo-connection", array(
        array(
            "methods" => "PUT",
            "callback" => __NAMESPACE__ . "\\odoo_conn_update_odoo_connection",
            "args" => odoo_conn_update_odoo_connection_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_update_odoo_connection_schema",
    ));

    register_rest_route("odoo_conn/v1", "/delete-odoo-connection", array(
        array(
            "methods" => "DELETE",
            "callback" => __NAMESPACE__ . "\\odoo_conn_delete_odoo_connection",
            "args" => odoo_conn_delete_odoo_connection_arguments(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_delete_odoo_connection_schema",
    ));

    register_rest_route("odoo_conn/v1", "/get-odoo-connection", array(
        array(
            "methods" => "GET",
            "callback" => __NAMESPACE__ . "\\odoo_conn_test_odoo_connection",
            "args" => odoo_conn_odoo_connection_id_argument(),
            "permission_callback" => __NAMESPACE__ . "\\odoo_conn_is_authorised_to_request_data",
        ),
        "schema" => __NAMESPACE__ . "\\odoo_conn_test_odoo_connection_schema"
    ));
});

?>

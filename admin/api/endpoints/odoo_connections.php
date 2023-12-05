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
        global $table_prefix;

        return $table_prefix . "odoo_conn_connection";
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


class OdooConnTestOdooConnection extends OdooConnGetOdooConnectionSingle
{
    public function request($data)
    {
        $connection = parent::request($data);
        if (!$connection) {
            return new WP_Error(
                "no_connection",
                "No connection for that Id",
                array("status" => 404)
            );
        }

        return $connection;
    }

    public function test_connection($odoo_connector)
    {
        try {
            $success = $odoo_connector->test_connection();
        } catch (OdooConnException $e) {
            return array(
                "success" => false,
                "error_string" => $e->getMessage(),
                "error_code" => $e->getCode()
            );
        }
        return array(
            "success" => $success
        );
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
    $response = $get_odoo_connection->request($data);
    return $response;
}

function odoo_conn_test_odoo_connection($data)
{
    $id = $data["id"];
    $connection_tester = new OdooConnTestOdooConnection($id);
    $connection = $connection_tester->request($data);

    $encryption_file_handler = new OdooConnEncryptionFileHandler();
    $encryption_handler = new OdooConnEncryptionHandler($encryption_file_handler);
    $decrypted_api_key = $encryption_handler->decrypt($connection->api_key);

    $odoo_connector = new OdooConnOdooConnector(
        $connection->username,
        $decrypted_api_key,
        $connection->database_name,
        $connection->url
    );

    return $connection_tester->test_connection($odoo_connector);
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

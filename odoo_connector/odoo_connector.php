<?php

namespace odoo_conn\odoo_connector\odoo_connector;

use \PhpXmlRpc\Request;
use \PhpXmlRpc\Value;
use \PhpXmlRpc\Client;


class OdooConnException extends \Exception
{
}


abstract class OdooConnXMLRPCField
{

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public abstract function get_parsed_value();

}


class OdooConnXMLRPCStringField extends OdooConnXMLRPCField
{

    public function get_parsed_value()
    {
        return new Value($this->value, Value::$xmlrpcString);
    }

}


class OdooConnXMLRPCBaseX2ManyField extends OdooConnXMLRPCField
{

    public function get_parsed_value()
    {
        $parsed_id_values = [];
        foreach ($this->value as $id) {
            $parsed_id_values[] = new Value($id, Value::$xmlrpcInt);
        }

        return new Value(
            [
                new Value(
                    [
                        new Value(6, Value::$xmlrpcInt),
                        new Value(0, Value::$xmlrpcInt),
                        new Value(
                            $parsed_id_values,
                            Value::$xmlrpcArray
                        ),
                    ],
                    Value::$xmlrpcArray
                )
            ],
            Value::$xmlrpcArray
        );
    }
}


class OdooConnXMLRPCStringX2ManyField extends OdooConnXMLRPCBaseX2ManyField {

    public function get_parsed_value()
    {
        $this->value = explode(",", $this->value);
        return parent::get_parsed_value();
    }

}


class OdooConnOdooConnector
{

    function __construct(
        $username, $api_key, $database, $url
    )
    {
        $this->username = $username;
        $this->api_key = $api_key;
        $this->database = $database;
        $this->url = $url;
        $this->uid = null;
    }

    public function create_client($url)
    {
        return new Client($url);
    }

    public function create_request($methodName, $arguments)
    {
        return new Request($methodName, $arguments);
    }

    private function check_connection_ok()
    {
        if ($this->uid === false) {
            throw new OdooConnException(
                "Username or API Key is incorrect"
            );
        }
        return is_int($this->uid);
    }

    public function test_connection()
    {
        $this->set_user_id();
        return $this->check_connection_ok();
    }

    private function authenticate()
    {
        $common_client = $this->create_client($this->url . "/xmlrpc/2/common");
        $version_request = $this->create_request("version", []);
        $version_response = $common_client->send($version_request);

        $authentication_request = $this->create_request(
            "authenticate", [
                new Value($this->database, Value::$xmlrpcString),
                new Value($this->username, Value::$xmlrpcString),
                new Value($this->api_key, Value::$xmlrpcString),
                $version_response->value()
            ]
        );
        return $common_client->send($authentication_request);
    }

    private function set_user_id()
    {
        $authentication_response = $this->authenticate();

        if ($authentication_response->faultCode()) {
            $fault_string = $authentication_response->faultString();
            $database_error = preg_match(
                "/failed: FATAL:  database (?-s:.)+ does not exist/", $fault_string
            );

            if ($database_error) {
                $fault_string = "Database name '{$this->database}' does not exist in Odoo instance";
            }

            throw new OdooConnException(
                $fault_string,
                $authentication_response->faultCode()
            );
        }

        $this->uid = $authentication_response->value()->scalarval();
    }

    public function create_object($model, $field_values)
    {
        $this->set_user_id();
        $this->check_connection_ok();

        $model_client = $this->create_client($this->url . "/xmlrpc/2/object");
        $parsed_field_values = $this->parse_field_values($field_values);

        $response = $model_client->send(
            $this->create_request(
                "execute_kw",
                [
                    new Value($this->database, Value::$xmlrpcString),
                    new Value($this->uid, Value::$xmlrpcString),
                    new Value($this->api_key, Value::$xmlrpcString),
                    new Value($model, Value::$xmlrpcString),
                    new Value("create", Value::$xmlrpcString),
                    $parsed_field_values
                ]
            )
        );

        if ($response->faultCode()) {
            throw new OdooConnException(
                $response->faultString(), $response->faultCode()
            );
        }
    }

    private function parse_field_values($field_values)
    {
        $parsed_array = [];
        foreach ($field_values as $field_value) {
            $parsed_array[$field_value->key] = $field_value->get_parsed_value();
        }
        $field_values_xmlrpc = new Value($parsed_array, Value::$xmlrpcStruct);
        return new Value([$field_values_xmlrpc], Value::$xmlrpcArray);
    }

}

?>
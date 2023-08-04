<?php

namespace odoo_conn\odoo_connector\odoo_connector;

use \PhpXmlRpc\Request;
use \PhpXmlRpc\Value;
use \PhpXmlRpc\Client;


class OdooConnException extends \Exception {}


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

    public function create_value($value, $type = "string")
    {
        return new Value($value, $type);
    }

    public function test_connection() {
        $this->set_user_id();

        if ($this->uid === false) {
            throw new OdooConnException(
                "Username or API Key is incorrect"
            );
        }
        return is_int($this->uid);
    }

    private function authenticate() {
        $common_client = $this->create_client($this->url . "/xmlrpc/2/common");
        $version_request = $this->create_request("version", array());
        $version_response = $common_client->send($version_request);

        $authentication_request = $this->create_request(
            "authenticate", array(
                $this->create_value($this->database),
                $this->create_value($this->username),
                $this->create_value($this->api_key),
                $version_response->value()
            )
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
        $model_client = $this->create_client($this->url . "/xmlrpc/2/object");
        $parsed_field_values = $this->parse_field_values($field_values);

        $model_client->send(
            $this->create_request(
                "execute_kw",
                array(
                    $this->create_value($this->database),
                    $this->create_value($this->uid),
                    $this->create_value($this->api_key),
                    $this->create_value($model),
                    $this->create_value("create"),
                    $parsed_field_values
                )
            )
        );
    }

    private function parse_field_values($field_values)
    {
        $parsed_array = array();
        foreach ($field_values as $field_value_array) {
            foreach ($field_value_array as $field_key => $field_value) {
                $parsed_array[$field_key] = $this->create_value($field_value);
            }
        }
        $field_values_xmlrpc = $this->create_value($parsed_array, "struct");
        return $this->create_value(array($field_values_xmlrpc), "array");
    }

}

?>
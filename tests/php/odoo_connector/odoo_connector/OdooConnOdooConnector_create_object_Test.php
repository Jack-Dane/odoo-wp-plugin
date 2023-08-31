<?php

namespace odoo_conn\tests\odoo_connector\odoo_connector;

require_once(__DIR__ . "/../../../../odoo_connector/odoo_connector.php");
require_once(__DIR__ . "/OdooConnOdooConnectorTestBase.php");

use odoo_conn\odoo_connector\odoo_connector\OdooConnException;


class OdooConnOdooConnector_create_object_Test extends OdooConnOdooConnectorTestBase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->odoo_connector->shouldReceive("create_request")->with(
            "authenticate",
            array("database_value", "username_value", "api_key_value", "14.0")
        )->andReturn($this->authentication_request);
        $this->authentication_response = \Mockery::mock();
        $this->value_mock = \Mockery::mock();
        $this->value_mock->shouldReceive("scalarval")->andReturn(2);

        $this->model_request = \Mockery::mock();
        $this->odoo_connector->shouldReceive("create_request")->with(
            "execute_kw",
            array(
                "database_value", "2_value", "api_key_value", "res.partner_value", "create_value",
                array(
                    array(
                        "name" => "Jack_value",
                        "email" => "test@test.com_value"
                    )
                )
            )
        )->andReturn($this->model_request);

        $this->common_client->shouldReceive("send")->with($this->authentication_request)->once()->andReturn(
            $this->authentication_response
        );
    }

    public function test_ok()
    {
        $this->authentication_response->shouldReceive("value")->andReturn($this->value_mock);
        $this->authentication_response->shouldReceive("faultCode")->andReturn(0);

        $models_client = \Mockery::mock();
        $this->odoo_connector->shouldReceive("create_client")->with(
            "test_url/xmlrpc/2/object"
        )->once()->andReturn(
            $models_client
        );
        $response = \Mockery::mock();
        $models_client->shouldReceive("send")->with($this->model_request)->once()->andReturn(
            $response
        );
        $response->shouldReceive("faultCode")->andReturn(0);

        $this->odoo_connector->create_object(
            "res.partner",
            array(
                array(
                    "name" => "Jack",
                    "email" => "test@test.com"
                )
            )
        );

        $this->assertEquals(2, $this->odoo_connector->uid);
    }

    public function test_bad_send_response()
    {
        $this->authentication_response->shouldReceive("value")->andReturn($this->value_mock);
        $this->authentication_response->shouldReceive("faultCode")->andReturn(0);

        $models_client = \Mockery::mock();
        $this->odoo_connector->shouldReceive("create_client")->with(
            "test_url/xmlrpc/2/object"
        )->once()->andReturn(
            $models_client
        );
        $response = \Mockery::mock();
        $models_client->shouldReceive("send")->with($this->model_request)->once()->andReturn(
            $response
        );
        $response->shouldReceive("faultCode")->andReturn(2);
        $response->shouldReceive("faultString")->andReturn("No such model res.partner");

        $raised_exception = false;
        try {
            $this->odoo_connector->create_object(
                "res.partner",
                array(
                    array(
                        "name" => "Jack",
                        "email" => "test@test.com"
                    )
                )
            );
        } catch (OdooConnException $exception) {
            $this->assertEquals(
                2, $exception->getCode()
            );
            $this->assertEquals(
                "No such model res.partner", $exception->getMessage()
            );
            $raised_exception = true;
        }
        $this->assertTrue($raised_exception);
    }

    public function test_failed_to_authenticate()
    {
        $this->authentication_response->shouldReceive("faultCode")->andReturn(1);
        $this->authentication_response->shouldReceive("faultString")->andReturn("Failed to authenticate");
        $this->odoo_connector->shouldReceive("create_client")->with("test_url/xmlrpc/2/object")->never();

        $raised_exception = false;
        try {
            $this->odoo_connector->create_object(
                "res.partner",
                array(
                    array(
                        "name" => "Jack",
                        "email" => "test@test.com"
                    )
                )
            );
        } catch (OdooConnException $exception) {
            $this->assertEquals(
                1, $exception->getCode()
            );
            $this->assertEquals(
                "Failed to authenticate", $exception->getMessage()
            );
            $raised_exception = true;
        }
        $this->assertTrue($raised_exception);
    }

    public function test_failed_to_find_database()
    {
        $this->authentication_response->shouldReceive("faultCode")->andReturn(1);
        $this->authentication_response->shouldReceive("faultString")->andReturn(
            "foo failed: FATAL:  database Test2 does not exist bar"
        );
        $this->odoo_connector->shouldReceive("create_client")->with("test_url/xmlrpc/2/object")->never();

        $raised_exception = false;
        try {
            $this->odoo_connector->create_object(
                "res.partner",
                array(
                    array(
                        "name" => "Jack",
                        "email" => "test@test.com"
                    )
                )
            );
        } catch (OdooConnException $exception) {
            $this->assertEquals(
                1, $exception->getCode()
            );
            $this->assertEquals(
                "Database name 'database' does not exist in Odoo instance", $exception->getMessage()
            );
            $raised_exception = true;
        }
        $this->assertTrue($raised_exception);
    }

}

?>
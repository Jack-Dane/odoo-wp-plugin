<?php

namespace php\admin\odoo_connector\odoo_connector;

require_once __DIR__ . "/../../../../../admin/odoo_connector/odoo_connector.php";
require_once __DIR__ . "/OdooConnOdooConnectorTestBase.php";

use odoo_conn\admin\odoo_connector\OdooConnException;


class OdooConnOdooConnector_test_connection_Test extends OdooConnOdooConnectorTestBase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->odoo_connector->shouldReceive("create_request")->with(
            "authenticate",
            array("database_value", "username_value", "api_key_value", "14.0")
        )->andReturn($this->authentication_request);

        $this->authentication_response = \Mockery::mock();
        $this->authentication_response->shouldReceive("faultCode")->andReturn(0);

        $this->value_mock = \Mockery::mock();
        $this->authentication_response->shouldReceive("value")->andReturn($this->value_mock);
        $this->common_client->shouldReceive("send")->with($this->authentication_request)->once()->andReturn(
            $this->authentication_response
        );
    }

    public function test_ok()
    {
        $this->value_mock->shouldReceive("scalarval")->andReturn(2);
        $this->authentication_response->shouldReceive("faultCode")->andReturn(0);

        $result = $this->odoo_connector->test_connection();

        $this->assertTrue($result);
    }

    function test_failed_auth()
    {
        $this->value_mock->shouldReceive("scalarval")->andReturn(false);

        $raised_exception = false;
        try {
            $this->odoo_connector->test_connection();
        } catch (OdooConnException $exception) {
            $this->assertEquals("Username or API Key is incorrect", $exception->getMessage());
            $raised_exception = true;
        }

        $this->assertTrue($raised_exception);
    }

    function test_unexpected_uid()
    {
        $this->value_mock->shouldReceive("scalarval")->andReturn("bar");

        $result = $this->odoo_connector->test_connection();

        $this->assertFalse($result);
    }

}

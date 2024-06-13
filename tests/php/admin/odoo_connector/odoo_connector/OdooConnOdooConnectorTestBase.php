<?php

namespace php\admin\odoo_connector\odoo_connector;

require_once __DIR__ . "/OdooConnOdooConnectorTestBase.php";

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use PhpXmlRpc\Value;


class OdooConnOdooConnectorTestBase extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function setUp(): void
    {
        $this->odoo_connector = Mockery::mock(
            "\odoo_conn\admin\odoo_connector\OdooConnOdooConnector[create_client, create_request, create_value]",
            ["username", "api_key", "database", "test_url"]
        )->makePartial();

        $this->odoo_connector->shouldReceive("create_value")
            ->andReturnUsing(function ($value, $type = "string") {
                if ($type == "string") {
                    return $value . "_value";
                }
                return $value;
            });

        $this->version_request = Mockery::mock();
        $this->odoo_connector->shouldReceive("create_request")->with(
            "version", []
        )->andReturn($this->version_request);
        $this->version_response = Mockery::mock();
        $this->version_response->shouldReceive("value")->with()->andReturn("14.0");

        $this->authentication_request = Mockery::mock();
        $this->odoo_connector->shouldReceive("create_request")->with(
            "authenticate",
            [
                new Value("database", Value::$xmlrpcString),
                new Value("username", Value::$xmlrpcString),
                new Value("api_key", Value::$xmlrpcString),
                "14.0"
            ]
        )->andReturn($this->authentication_request);

        $this->common_client = Mockery::mock();
        $this->odoo_connector->shouldReceive("create_client")->with(
            "test_url/xmlrpc/2/common"
        )->once()->andReturn(
            $this->common_client
        );
        $this->common_client->shouldReceive("send")->with($this->version_request)->once()->andReturn(
            $this->version_response
        );
        $this->odoo_connector->shouldReceive("create_request")->with(
            "authenticate",
            [
                new Value("database", Value::$xmlrpcString),
                new Value("username", Value::$xmlrpcString),
                new Value("api_key", Value::$xmlrpcString),
                "14.0"
            ]
        )->andReturn($this->authentication_request);
    }

}


<?php

namespace odoo_conn\tests\odoo_connector\odoo_connector\OdooConnOdooConnector_Test;

require_once(__DIR__ . "/../../../../odoo_connector/odoo_connector.php");

use \PHPUnit\Framework\TestCase;
use \odoo_conn\odoo_connector\odoo_connector\OdooConnOdooConnector;
use \PhpXmlRpc\Request;
use \PhpXmlRpc\Value;


class OdooConnOdooConnector_Test extends TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function test_ok()
    {
        $odoo_connector = \Mockery::mock(
            "\odoo_conn\odoo_connector\odoo_connector\OdooConnOdooConnector[create_client, create_request, create_value]",
            array("username", "api_key", "database", "test_url")
        )->makePartial();

        $odoo_connector->shouldReceive("create_value")
            ->andReturnUsing(function ($value, $type = "string") {
                if ($type == "string") {
                    return $value . "_value";
                }
                return $value;
            });

        $version_request = \Mockery::mock();
        $odoo_connector->shouldReceive("create_request")->with(
            "version", array()
        )->andReturn($version_request);
        $version_response = \Mockery::mock();
        $version_response->shouldReceive("value")->with()->andReturn("14.0");

        $authentication_request = \Mockery::mock();
        $odoo_connector->shouldReceive("create_request")->with(
            "authenticate",
            array("database_value", "username_value", "api_key_value", "14.0")
        )->andReturn($authentication_request);
        $authentication_response = \Mockery::mock();
        $value_mock = \Mockery::mock();
        $value_mock->shouldReceive("scalarval")->andReturn(2);
        $authentication_response->shouldReceive("value")->andReturn($value_mock);

        $model_request = \Mockery::mock();
        $odoo_connector->shouldReceive("create_request")->with(
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
        )->andReturn($model_request);

        $common_client = \Mockery::mock();
        $odoo_connector->shouldReceive("create_client")->with(
            "test_url/xmlrpc/2/common"
        )->once()->andReturn(
            $common_client
        );
        $common_client->shouldReceive("send")->with($version_request)->once()->andReturn(
            $version_response
        );
        $common_client->shouldReceive("send")->with($authentication_request)->once()->andReturn(
            $authentication_response
        );

        $models_client = \Mockery::mock();
        $odoo_connector->shouldReceive("create_client")->with(
            "test_url/xmlrpc/2/object"
        )->once()->andReturn(
            $models_client
        );
        $models_client->shouldReceive("send")->with($model_request)->once();

        $odoo_connector->create_object(
            "res.partner",
            array(
                array(
                    "name" => "Jack",
                    "email" => "test@test.com"
                )
            )
        );

        $this->assertEquals(2, $odoo_connector->uid);
    }

}

?>
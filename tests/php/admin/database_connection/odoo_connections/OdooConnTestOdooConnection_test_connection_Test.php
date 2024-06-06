<?php

namespace php\admin\database_connection\odoo_connections;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnTestOdooConnection;
use odoo_conn\odoo_connector\odoo_connector\OdooConnException;
use PHPUnit\Framework\TestCase;

class OdooConnTestOdooConnection_test_connection_Test extends TestCase
{
    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
		require_once __DIR__ . '/../../../../../odoo_connector/odoo_connector.php';

        $this->odoo_connector = \Mockery::mock();
        $this->odoo_conn_test_odoo_connection = new OdooConnTestOdooConnection(2);
    }

    public function test_success()
    {
        $this->odoo_connector->shouldReceive('test_connection')->with()->andReturn(true);

        $response = $this->odoo_conn_test_odoo_connection->test_connection($this->odoo_connector);

        $this->assertEquals(array('success' => true), $response);
    }

    public function test_failure()
    {
        $this->odoo_connector->shouldReceive('test_connection')->with()->andReturn(false);

        $response = $this->odoo_conn_test_odoo_connection->test_connection($this->odoo_connector);

        $this->assertEquals(array('success' => false), $response);
    }

    public function test_exception()
    {
        $this->odoo_connector->shouldReceive('test_connection')->with()->andThrow(
            new OdooConnException(
                'Failed to authenticate', 2
            )
        );

        $response = $this->odoo_conn_test_odoo_connection->test_connection($this->odoo_connector);

        $this->assertEquals(
            array(
                'success' => false,
                'error_string' => 'Failed to authenticate',
                'error_code' => 2
            ),
            $response
        );
    }

}
<?php

namespace php\admin\api\endpoints\odoo_connections;

require_once(__DIR__ . "/../common.php");
require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_connections.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\api\endpoints\OdooConnTestOdooConnection;
use PHPUnit\Framework\TestCase;

class OdooConnTestOdooConnection_request_Test extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function setUp(): void
    {
        $this->odoo_connection_tester = new OdooConnTestOdooConnection(2);
        $this->request_data = array(
            "id" => 2
        );
    }

    public function test_ok()
    {
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("prepare")->with("SELECT * FROM wp_odoo_conn_connection WHERE id=%d ORDER BY wp_odoo_conn_connection.id DESC", [2])
            ->once()->andReturn("SELECT * FROM wp_odoo_conn_connection WHERE id=2 ORDER BY wp_odoo_conn_connection.id DESC");
        $wpdb->shouldReceive("get_results")->with("SELECT * FROM wp_odoo_conn_connection WHERE id=2 ORDER BY wp_odoo_conn_connection.id DESC", "OBJECT")
            ->once()->andReturn(
                array(array("id" => 2, "name" => "Odoo Connection", "username" => "jackd98", "url" => "localhost:8069", "database_name" => "odoo_db", "api_key" => "abc"))
            );
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $connection = $this->odoo_connection_tester->request($this->request_data);

        $this->assertEquals(
            array("id" => 2, "name" => "Odoo Connection", "username" => "jackd98", "url" => "localhost:8069", "database_name" => "odoo_db", "api_key" => "abc"),
            $connection
        );
    }

    public function test_non_existing_id()
    {
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("prepare")->with("SELECT * FROM wp_odoo_conn_connection WHERE id=%d ORDER BY wp_odoo_conn_connection.id DESC", [2])
            ->once()->andReturn("SELECT * FROM wp_odoo_conn_connection WHERE id=2 ORDER BY wp_odoo_conn_connection.id DESC");
        $wpdb->shouldReceive("get_results")->with("SELECT * FROM wp_odoo_conn_connection WHERE id=2 ORDER BY wp_odoo_conn_connection.id DESC", "OBJECT")
            ->once()->andReturn(array());
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $connection = $this->odoo_connection_tester->request($this->request_data);

        $this->assertEquals("no_connection", $connection->error_id);
        $this->assertEquals("No connection for that Id", $connection->error_message);
        $this->assertEquals(array("status" => 404), $connection->error_status);
    }

}
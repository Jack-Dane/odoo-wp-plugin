<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_connections\OdooConnGetOdooConnection;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_connections.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooConnection;

class OdooConnGetOdooConnection_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->shouldReceive("prepare")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection ORDER BY wp_odoo_conn_connection.id DESC", [])
			->once()->andReturn("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection ORDER BY wp_odoo_conn_connection.id DESC");
		$wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection ORDER BY wp_odoo_conn_connection.id DESC")
			->once()->andReturn(
				array(array("id"=>3, "name"=>"Odoo Connection", "username"=>"jackd98", "url"=>"localhost:8069", "database_name"=>"odoo_db"))
			);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odoo_conn_get_odoo_connection = new OdooConnGetOdooConnection();
		$results = $odoo_conn_get_odoo_connection->request(array());

		$this->assertEquals(
			array(array("id"=>3, "name"=>"Odoo Connection", "username"=>"jackd98", "url"=>"localhost:8069", "database_name"=>"odoo_db")
		), $results);
	}

}

?>
<?php

namespace odoo_conn\tests\admin\api\endpoints\cf7_posts\OdooConnGetOdooConnection;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_connections.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooConnection;

class OdooConnGetOdooConnection_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->shouldReceive("prepare")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection", [])
			->once()->andReturn("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection");
		$wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection")
			->once()->andReturn(
				array(array("id"=>3, "name"=>"Odoo Connection", "username"=>"jackd98", "url"=>"localhost:8069", "database_name"=>"odoo_db"))
			);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odooConnGetOdooConnection = new OdooConnGetOdooConnection();
		$results = $odooConnGetOdooConnection->request(array());

		$this->assertEquals(
			array(array("id"=>3, "name"=>"Odoo Connection", "username"=>"jackd98", "url"=>"localhost:8069", "database_name"=>"odoo_db")
		), $results);
	}

}

?>
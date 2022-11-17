<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_connections\OdooConnPutOdooConnection;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_connections.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnPutOdooConnection;

class OdooConnPutOdooConnection_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$data = array(
			"name"=>"name",
			"username"=>"username",
			"url"=>"url",
			"database_name"=>"database_name"
		);
		$results = array(
			array("id"=>3) + $data
		);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->insert_id = 3;
		$wpdb->shouldReceive("update")->with("wp_odoo_conn_connection", $data, array("id" => 3))->once();
		$wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3")
			->once()->andReturn($results);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odoo_conn_put_odoo_connection = new OdooConnPutOdooConnection(3);
		$response = $odoo_conn_put_odoo_connection->request($data);

		$this->assertEquals($results, $response);
	}

	public function test_cant_update_api_key () {
		$data = array(
			"name"=>"name",
			"username"=>"username",
			"url"=>"url",
			"database_name"=>"database_name"
		);
		$api_key_data = $data + array("api_key"=>"abc");
		$results = array(
			array("id"=>3) + $data
		);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->insert_id = 3;
		$wpdb->shouldReceive("update")->with("wp_odoo_conn_connection", $data, array("id" => 3))->once();
		$wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3")
			->once()->andReturn($results);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odoo_conn_put_odoo_connection = new OdooConnPutOdooConnection(3);
		$response = $odoo_conn_put_odoo_connection->request($api_key_data);

		$this->assertEquals($results, $response);
	}

}

?>
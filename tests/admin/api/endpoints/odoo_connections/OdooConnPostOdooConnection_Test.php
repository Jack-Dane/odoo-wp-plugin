<?php

namespace odoo_conn\tests\admin\api\endpoints\cf7_posts\OdooConnPostOdooConnection;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/c7f_posts.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooConnection;

class OdooConnPostOdooConnection_Test extends TestCase {

	use \phpmock\phpunit\PHPMock;
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$data = array(
			"name"=>"name",
			"username"=>"username",
			"api_key"=>"api_key",
			"url"=>"url",
			"database_name"=>"database_name"
		);
		$results = array(
			array("id"=>3, "name"=>"Odoo Connection", "username"=>"jackd98", "url"=>"localhost:8069", "database_name"=>"odoo_db")
		);
		$odoo_conn_encrypt_data = $this->getFunctionMock("odoo_conn\\admin\\api\\endpoints", "odoo_conn_encrypt_data");
		$odoo_conn_encrypt_data->expects($this->once())->willReturn("api_key");
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->insert_id = 3;
		$wpdb->shouldReceive("insert")->with("wp_odoo_conn_connection", $data, array("%s", "%s", "%s", "%s", "%s"))->once();
		$wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3")
			->once()->andReturn($results);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odooConnGetOdooConnection = new OdooConnPostOdooConnection();
		$response = $odooConnGetOdooConnection->request($data);

		$this->assertEquals($results, $response);
	}

}

?>
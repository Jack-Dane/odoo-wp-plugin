<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_connections\OdooConnPostOdooConnection;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/c7f_posts.php");
require_once("encryption.php");

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
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->insert_id = 3;
		$encrypted_data = array(
			"name"=>"name",
			"username"=>"username",
			"api_key"=>"encrypted_api_key",
			"url"=>"url",
			"database_name"=>"database_name"
		);
		$wpdb->shouldReceive("insert")->with("wp_odoo_conn_connection", $encrypted_data, array("%s", "%s", "%s", "%s", "%s"))->once();
		$wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3")
			->once()->andReturn($results);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";
		$odoo_conn_file_handler_mock = $this->createMock(\odoo_conn\encryption\OdooConnEncryptionHandler::class);
		$odoo_conn_file_handler_mock->expects($this->once())->method("encrypt")->with($this->equalTo("api_key"))->willReturn("encrypted_api_key");

		$odoo_conn_post_odoo_connection = new OdooConnPostOdooConnection($odoo_conn_file_handler_mock);
		$response = $odoo_conn_post_odoo_connection->request($data);

		$this->assertEquals($results, $response);
	}

}

?>
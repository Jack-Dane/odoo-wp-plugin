<?php 

use \PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

class OdooConnection_Test extends TestCase {

	public function setUp (): void {
		$this->client = new Client();
	}

	public function test_get_odoo_connections () {
		try {
			$response = $this->client->request(
				"GET", "http://localhost:8000/wp-json/odoo_conn/v1/get-odoo-connections"
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
	}

	public function test_create_odoo_connection () {
		try {
			$response = $this->client->request(
				"POST", "http://localhost:8000/wp-json/odoo_conn/v1/create-odoo-connection",
				array(
					"form_params" => array(
						"name" => "test_name",
						"username" => "test_username",
						"api_key" => "test_api_key",
						"url" => "test_url",
						"database_name" => "test_database_name"
					)
				)
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
	}

	public function test_update_odoo_connection () {
		try {
			$response = $this->client->request(
				"PUT", "http://localhost:8000/wp-json/odoo_conn/v1/update-odoo-connection",
				array(
					"form_params" => array(
						"id" => 1,
						"name" => "test_name",
						"username" => "test_username",
						"url" => "test_url",
						"database_name" => "test_database_name"
					)
				)
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
	}

	public function test_delete_odoo_connection () {
		try {
			$response = $this->client->request(
				"DELETE", "http://localhost:8000/wp-json/odoo_conn/v1/delete-odoo-connection",
				array(
					"form_params" => array(
						"id" => 1,
					)
				)
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
	}

}

?>
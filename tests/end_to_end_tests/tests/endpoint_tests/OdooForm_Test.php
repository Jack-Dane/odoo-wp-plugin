<?php

use \PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

class OdooForm_Test extends TestCase {

	public function setUp (): void {
		$this->client = new Client();
	}

	public function test_get_odoo_forms () {
		$failure = false;
		try {
			$response = $this->client->request(
				"GET", "http://localhost:8000/?rest_route=/odoo_conn/v1/get-odoo-forms"
			);
		} catch (ClientException $e) {
			$failure = true;
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
		$this->assertTrue($failure);
	}

	public function test_create_odoo_form () {
		$failure = false;
		try {
			$response = $this->client->request(
				"POST", "http://localhost:8000/?rest_route=/odoo_conn/v1/create-odoo-form",
				array(
					"form_params" => array(
						"odoo_connection_id" => 1,
						"odoo_model" => "res.partner",
						"name" => "Test Form",
						"contact_7_id" => 1,
					)
				)
			);
		} catch (ClientException $e) {
			$failure = true;
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
		$this->assertTrue($failure);
	}

	public function test_update_odoo_form () {
		$failure = false;
		try {
			$response = $this->client->request(
				"PUT", "http://localhost:8000/?rest_route=/odoo_conn/v1/update-odoo-form",
				array(
					"form_params" => array(
						"id" => 1,
						"odoo_connection_id" => 1,
						"odoo_model" => "res.partner",
						"name" => "Test Form",
						"contact_7_id" => 1,
					)
				)
			);
		} catch (ClientException $e) {
			$failure = true;
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
		$this->assertTrue($failure);
	}

	public function test_delete_odoo_form () {
		$failure = false;
		try {
			$response = $this->client->request(
				"DELETE", "http://localhost:8000/?rest_route=/odoo_conn/v1/delete-odoo-form",
				array(
					"form_params" => array(
						"id" => 1,
					)
				)
			);
		} catch (ClientException $e) {
			$failure = true;
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
		$this->assertTrue($failure);
	}

}

?>

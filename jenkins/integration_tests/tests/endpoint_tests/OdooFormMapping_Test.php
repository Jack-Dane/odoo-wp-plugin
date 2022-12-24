<?php 

use \PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

class OdooFormMapping_Test extends TestCase {

	public function setUp (): void {
		$this->client = new Client();
	}

	public function test_get_odoo_form_mapping () {
		$failure = false;
		try {
			$response = $this->client->request(
				"GET", "http://localhost:8000/?rest_route=/odoo_conn/v1/get-odoo-form-mappings"
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
			$failure = true;
		}
		$this->assertTrue($failure);
	}

	public function test_create_odoo_form_mapping () {
		$failure = false;
		try {
			$response = $this->client->request(
				"POST", "http://localhost:8000/?rest_route=/odoo_conn/v1/create-odoo-form-mapping",
				array(
					"form_params" => array(
						"odoo_form_id" => 1,
						"cf7_field_name" => "your-name",
						"odoo_field_name" => "name",
						"constant_value" => "",
					)
				)
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
			$failure = true;
		}
		$this->assertTrue($failure);
	}

	public function test_update_odoo_form_mapping () {
		$failure = false;
		try {
			$response = $this->client->request(
				"PUT", "http://localhost:8000/?rest_route=/odoo_conn/v1/update-odoo-form-mapping",
				array(
					"form_params" => array(
						"id" => 1,
						"odoo_form_id" => 1,
						"cf7_field_name" => "your-name",
						"odoo_field_name" => "name",
						"constant_value" => "",
					)
				)
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
			$failure = true;
		}
		$this->assertTrue($failure);
	}

	public function test_delete_odoo_form_mapping () {
		$failure = false;
		try {
			$response = $this->client->request(
				"DELETE", "http://localhost:8000/?rest_route=/odoo_conn/v1/delete-odoo-form-mapping",
				array(
					"form_params" => array(
						"id" => 1,
					)
				)
			);
		} catch (ClientException $e) {
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
			$failure = true;
		}
		$this->assertTrue($failure);
	}

}

?>
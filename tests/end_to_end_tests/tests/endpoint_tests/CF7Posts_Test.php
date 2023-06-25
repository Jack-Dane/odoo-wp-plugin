<?php 

use \PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

class CF7Posts_Test extends TestCase {

	public function setUp (): void {
		$this->client = new Client();
	}

	public function test_get_contact_forms () {
		$failure = false;
		try {
			$response = $this->client->request(
				"GET", "http://localhost:8000/?rest_route=/odoo_conn/v1/get-contact-7-forms"
			);
		} catch (ClientException $e) {
			$failure = true;
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
		$this->assertTrue($failure);
	}
}

?>
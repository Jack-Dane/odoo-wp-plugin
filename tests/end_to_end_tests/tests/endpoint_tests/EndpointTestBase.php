<?php

namespace end_to_end_tests\tests\endpoint_tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


abstract class EndpointTestBase extends Testcase {

	public function setUp(): void
	{
		$this->client = new Client();
	}

	protected abstract function endpoint();

	protected function make_request()
	{
		$failure = false;
		try {
			$this->client->request(
				'GET', $this->endpoint()
			);
		} catch (ClientException $e) {
			$failure = true;
			$this->assertEquals(401, $e->getResponse()->getStatusCode());
		}
		$this->assertTrue($failure);
	}

}


?>
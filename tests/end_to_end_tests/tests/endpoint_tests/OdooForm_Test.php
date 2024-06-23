<?php

namespace end_to_end_tests\tests\endpoint_tests;

require_once __DIR__ . '/EndpointTestBase.php';

class OdooForm_Test extends EndpointTestBase
{

	protected function endpoint()
	{
		return 'http://localhost:8000/?rest_route=/odoo_conn/v1/get-odoo-forms';
	}

	public function test()
	{
		$this->make_request();
	}

}

?>

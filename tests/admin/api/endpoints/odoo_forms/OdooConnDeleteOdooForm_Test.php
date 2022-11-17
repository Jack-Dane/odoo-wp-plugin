<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_forms\OdooConnDeleteOdooForm;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_forms.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooForm;

class OdooConnDeleteOdooForm_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$data = array("id" => 5);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->shouldReceive("delete")->with("wp_odoo_conn_form", $data, array("%d"))->once();
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odoo_conn_delete_odoo_form = new OdooConnDeleteOdooForm();
		$results = $odoo_conn_delete_odoo_form->request($data);

		$this->assertEquals(
			array("DELETE" => 5, "table" => "wp_odoo_conn_form"), $results
		);
	}

}

?>
<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnDeleteOdooFormMappings;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_connections.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooFormMappings;

class OdooConnDeleteOdooFormMappings_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$data = array("id" => 5);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->shouldReceive("delete")->with("wp_odoo_conn_form_mapping", $data, array("%d"))->once();
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odoo_conn_delete_odoo_from_mappings = new OdooConnDeleteOdooFormMappings();
		$results = $odoo_conn_delete_odoo_from_mappings->request($data);

		$this->assertEquals(
			array("DELETE" => 5, "table" => "wp_odoo_conn_form_mapping"), $results
		);
	}

}

?>
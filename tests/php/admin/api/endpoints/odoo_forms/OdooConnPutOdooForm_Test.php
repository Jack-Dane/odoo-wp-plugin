<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_forms\OdooConnPutOdooForm;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_forms.php");

use \PHPUnit\Framework\TestCase;
use function odoo_conn\admin\api\endpoints\odoo_conn_update_odoo_form;

class OdooConnPutOdooForm_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$data = array(
			"odoo_connection_id"=>1,
			"name"=>"form name",
			"contact_7_id"=>1,
			"odoo_model"=>"res.partner"
		);
		$results = array(array("id"=>3) + $data);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->insert_id = 3;
		$wpdb->shouldReceive("update")->with("wp_odoo_conn_form", $data, array("id" => 3))->once();
		$wpdb->shouldReceive("prepare")->with("SELECT * FROM wp_odoo_conn_form WHERE id=%d", array(3))
			->once()->andReturn("SELECT * FROM wp_odoo_conn_form WHERE id=3");
		$wpdb->shouldReceive("get_results")->with("SELECT * FROM wp_odoo_conn_form WHERE id=3")
			->once()->andReturn($results);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$response = odoo_conn_update_odoo_form($data + array("id" => 3));

		$this->assertEquals($results, $response);
	}

}

?>
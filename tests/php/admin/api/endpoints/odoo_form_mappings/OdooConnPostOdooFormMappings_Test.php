<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnPostOdooFormMappings;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_form_mappings.php");

use \PHPUnit\Framework\TestCase;
use function odoo_conn\admin\api\endpoints\odoo_conn_create_odoo_form_mapping;

class OdooConnPostOdooFormMappings_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$data = array(
			"odoo_form_id"=>1,
			"cf7_field_name"=>"your-name",
			"odoo_field_name"=>"name",
			"constant_value"=>""
		);
		$results = array(
			array("id"=>3, "odoo_form_id"=>1, "cf7_field_name"=>"your-name", "odoo_field_name"=>"name", "constant_value"=>null)
		);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->insert_id = 3;
		$wpdb->shouldReceive("insert")->with("wp_odoo_conn_form_mapping", $data, array("%d", "%s", "%s", "%s"))->once();
		$wpdb->shouldReceive("prepare")->with("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", array(3))->once()
			->andReturn("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3");
		$wpdb->shouldReceive("get_results")->with("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3")
			->once()->andReturn($results);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$response = odoo_conn_create_odoo_form_mapping($data);

		$this->assertEquals($results, $response);
	}

}

?>
<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnPutOdooFormMappings;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_connections.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnPutOdooFormMappings;

class OdooConnPutOdooFormMappings_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function setUp (): void {
		$this->wpdb = \Mockery::mock("WPDB");
		$this->wpdb->insert_id = 3;
		$GLOBALS["wpdb"] = $this->wpdb;
		$GLOBALS["table_prefix"] = "wp_";
		$this->odooConnPutOdooFormMappings = new OdooConnPutOdooFormMappings(3);
	}

	public function test_constant_value () {
		$data = array(
			"constant_value"=>"constant name",
			"odoo_form_id"=>1,
			"odoo_field_name"=>"name"
		);
		$results = array(array("id"=>3) + $data);
		$this->wpdb->shouldReceive("update")->with("wp_odoo_conn_form_mapping", $data, array("id" => 3))->once();
		$this->wpdb->shouldReceive("get_results")->with(
			"SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
		)->once()->andReturn($results);

		$response = $this->odooConnPutOdooFormMappings->request($data);

		$this->assertEquals($results, $response);
	}

	public function test_field_value () {
		$data = array(
			"cf7_field_name"=>"your-name",
			"odoo_form_id"=>1,
			"odoo_field_name"=>"name"
		);
		$results = array(array("id"=>3) + $data);
		$this->wpdb->shouldReceive("update")->with("wp_odoo_conn_form_mapping", $data, array("id" => 3))->once();
		$this->wpdb->shouldReceive("get_results")->with(
			"SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
		)->once()->andReturn($results);

		$response = $this->odooConnPutOdooFormMappings->request($data);

		$this->assertEquals($results, $response);
	}

	public function test_both_constant_value_field_value () {
		$data = array(
			"constant_value"=>"constant name",
			"cf7_field_name"=>"your-name",
			"odoo_form_id"=>1,
			"odoo_field_name"=>"name",
		);
		$this->wpdb->shouldReceive("update")->never();
		$this->wpdb->shouldReceive("get_results")->never();
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("Can't pass both a constant value and a cf7 field name as arguments");

		$response = $this->odooConnPutOdooFormMappings->request($data);
	}

}

?>
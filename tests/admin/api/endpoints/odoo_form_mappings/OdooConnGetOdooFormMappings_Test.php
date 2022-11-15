<?php

namespace odoo_conn\tests\admin\api\endpoints\cf7_posts\OdooConnGetOdooFormMappings;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_form_mappings.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooFormMappings;

class OdooConnGetOdooFormMappings_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$query_response = array(
			array(
				"id"=>3, "odoo_form_id"=>2, "odoo_form_name"=>"form name", "cf7_field_name"=>"your-name", "odoo_field_name"=>"name",
				"constant_value" => null
			)
		);
		$wpdb = \Mockery::mock("WPDB");
		$wpdb->shouldReceive("prepare")->with("SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id", [])
			->once()->andReturn("SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id");
		$wpdb->shouldReceive("get_results")->with("SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id")
			->once()->andReturn($query_response);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$odoo_conn_get_odoo_form_mappings = new OdooConnGetOdooFormMappings();	
		$result = $odoo_conn_get_odoo_form_mappings->request(array());

		$this->assertEquals($query_response, $result);
	}

}

?>
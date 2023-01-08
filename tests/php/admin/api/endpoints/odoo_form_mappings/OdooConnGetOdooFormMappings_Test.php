<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnGetOdooFormMappings;

require_once(__DIR__ . "/../common.php");
require_once("admin/api/schema.php");
require_once("admin/api/endpoints/odoo_form_mappings.php");

use \PHPUnit\Framework\TestCase;
use function odoo_conn\admin\api\endpoints\odoo_conn_get_odoo_from_mappings;

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
		$wpdb->shouldReceive("prepare")->with("SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC", [])
			->once()->andReturn("SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC");
		$wpdb->shouldReceive("get_results")->with("SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC")
			->once()->andReturn($query_response);
		$GLOBALS["wpdb"] = $wpdb;
		$GLOBALS["table_prefix"] = "wp_";

		$response = odoo_conn_get_odoo_from_mappings(array());

		$this->assertEquals($query_response, $response);
	}

}

?>
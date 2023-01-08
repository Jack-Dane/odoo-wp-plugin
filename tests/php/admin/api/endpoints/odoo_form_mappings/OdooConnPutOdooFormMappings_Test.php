<?php

namespace {

	class WP_Error {

		public function __construct ($error_id, $error_message, $error_status) {
			$this->error_id = $error_id;
			$this->error_message = $error_message;
			$this->error_status = $error_status;
		}

	}

}

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnPutOdooFormMappings {

	require_once(__DIR__ . "/../common.php");
	require_once("admin/api/schema.php");
	require_once("admin/api/endpoints/odoo_form_mappings.php");

	use \PHPUnit\Framework\TestCase;
	use function odoo_conn\admin\api\endpoints\odoo_conn_update_odoo_form_mapping;

	class OdooConnPutOdooFormMappings_Test extends TestCase {

		use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

		public function setUp (): void {
			$this->wpdb = \Mockery::mock("WPDB");
			$this->wpdb->insert_id = 3;
			$GLOBALS["wpdb"] = $this->wpdb;
			$GLOBALS["table_prefix"] = "wp_";
		}

		public function test_constant_value () {
			$data = array(
				"constant_value"=>"constant name",
				"odoo_form_id"=>1,
				"odoo_field_name"=>"name"
			);
			$results = array(array("id"=>3) + $data);
			$this->wpdb->shouldReceive("update")->with("wp_odoo_conn_form_mapping", $data, array("id" => 3))->once();
			$this->wpdb->shouldReceive("prepare")->with("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", array(3))
				->once()->andReturn("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3");
			$this->wpdb->shouldReceive("get_results")->with(
				"SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
			)->once()->andReturn($results);

			$response = odoo_conn_update_odoo_form_mapping($data + array("id" => 3));

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
			$this->wpdb->shouldReceive("prepare")->with("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", array(3))
				->once()->andReturn("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3");
			$this->wpdb->shouldReceive("get_results")->with(
				"SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
			)->once()->andReturn($results);

			$response = odoo_conn_update_odoo_form_mapping($data + array("id" => 3));

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
			$this->wpdb->shouldReceive("prepare")->never();
			$this->wpdb->shouldReceive("get_results")->never();

			$response = odoo_conn_update_odoo_form_mapping($data + array("id" => 3));

			$this->assertInstanceOf(\WP_Error::class, $response);
			$this->assertEquals("field_name_constant_value_failed", $response->error_id);
			$this->assertEquals(
				"Both cf7 field name and constant value passed, only one is expected", $response->error_message
			);
			$this->assertEquals(array("status" => 400), $response->error_status);
		}

	}

}

?>
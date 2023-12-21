<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnGetOdooFormMappings;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooFormMappings;

class OdooConnGetOdooFormMappings_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    public function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_form_mappings.php");
    }

    public function test_ok()
    {
        $query_response = array(
            array(
                "id" => 3, "odoo_form_id" => 2, "odoo_form_name" => "form name", "cf7_field_name" => "your-name", "odoo_field_name" => "name",
                "constant_value" => null
            )
        );
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("prepare")->with(
            "SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC", []
        )->once()->andReturn(
            "SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC"
        );
        $wpdb->shouldReceive("get_results")->with(
            "SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC", "OBJECT"
        )->once()->andReturn(
            $query_response
        );
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $odoo_conn_get_odoo_form_mappings = new OdooConnGetOdooFormMappings();
        $response = $odoo_conn_get_odoo_form_mappings->request([]);

        $this->assertEquals($query_response, $response);
    }

}

?>
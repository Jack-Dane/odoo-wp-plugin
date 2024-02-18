<?php

namespace php\admin\api\endpoints\odoo_form_mappings;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooFormMappings;
use odoo_conn\admin\api\endpoints\FieldNameConstantValueException;
use Brain\Monkey\Functions;

class OdooConnPostOdooFormMappings_Test extends \TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_form_mappings.php");
    }

    public function test_odoo_field_value()
    {
        $data = [
            "odoo_form_id" => 1,
            "cf7_field_name" => "your-name",
            "odoo_field_name" => "name",
            "constant_value" => "",
            "x_2_many" => ""
        ];
        $san_data = [
            "odoo_form_id" => 1,
            "cf7_field_name" => "san_your-name",
            "odoo_field_name" => "san_name",
            "constant_value" => "",
            "x_2_many" => ""
        ];
        $results = [
            [
                "id" => 3, "odoo_form_id" => 1, "cf7_field_name" => "your-name", "odoo_field_name" =>
                "name", "constant_value" => null, "x_2_many" => 0
            ]
        ];
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive("insert")->with(
            "wp_odoo_conn_form_mapping", $san_data, ["%d", "%s", "%s", "%s", "%d"]
        )->once();
        $wpdb->shouldReceive("prepare")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", [3]
        )->once()->andReturn(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        );
        $wpdb->shouldReceive("get_results")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        )->once()->andReturn($results);
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";
        Functions\expect("sanitize_text_field")
            ->times(5)
            ->andReturnValues(["", 1, "san_your-name", "san_name", "", ""]);

        $odoo_conn_post_odoo_form_mappings = new OdooConnPostOdooFormMappings();
        $response = $odoo_conn_post_odoo_form_mappings->request($data);

        $this->assertEquals($results, $response);
    }

    public function test_constant_value()
    {
        $data = [
            "odoo_form_id" => 1,
            "cf7_field_name" => "",
            "odoo_field_name" => "name",
            "constant_value" => "jack",
            "x_2_many" => "on"
        ];
        $san_data = [
            "odoo_form_id" => 1,
            "cf7_field_name" => "",
            "odoo_field_name" => "san_name",
            "constant_value" => "san_jack",
            "x_2_many" => "on"
        ];
        $results = [
            [
                "id" => 3, "odoo_form_id" => 1, "cf7_field_name" => "", "constant_value" => "jack",
                "odoo_field_name" => "name", "x_2_many" => 1
            ]
        ];
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive("insert")->with(
            "wp_odoo_conn_form_mapping", $san_data, ["%d", "%s", "%s", "%s", "%d"]
        )->once();
        $wpdb->shouldReceive("prepare")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", [3]
        )->once()->andReturn(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        );
        $wpdb->shouldReceive(
            "get_results"
        )->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        )->once()->andReturn($results);
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";
        Functions\expect("sanitize_text_field")
            ->times(5)
            ->andReturnValues(["on", 1, "", "san_name", "san_jack"]);

        $odoo_conn_post_odoo_form_mappings = new OdooConnPostOdooFormMappings();
        $response = $odoo_conn_post_odoo_form_mappings->request($data);

        $this->assertEquals($results, $response);
    }

    public function test_constant_value_and_odoo_field_value()
    {
        $this->expectException(FieldNameConstantValueException::class);
        $this->expectExceptionMessage("Can't pass both a constant value and a cf7 field name as arguments");

        $data = [
            "odoo_form_id" => 1,
            "cf7_field_name" => "your-name",
            "odoo_field_name" => "name",
            "constant_value" => "jack",
            "x_2_many" => ""
        ];
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive("insert")->never();
        $wpdb->shouldReceive("prepare")->never();
        $wpdb->shouldReceive("get_results")->never();
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $odoo_conn_post_odoo_form_mappings = new OdooConnPostOdooFormMappings();
        $odoo_conn_post_odoo_form_mappings->request($data);
    }

}

?>
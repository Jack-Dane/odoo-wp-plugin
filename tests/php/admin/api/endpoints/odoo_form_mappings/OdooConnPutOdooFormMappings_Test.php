<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnPutOdooFormMappings;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnPutOdooFormMappings;
use odoo_conn\admin\api\endpoints\FieldNameConstantValueException;

class OdooConnPutOdooFormMappings_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    public function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_form_mappings.php");

        $this->wpdb = \Mockery::mock("WPDB");
        $this->wpdb->insert_id = 3;
        $GLOBALS["wpdb"] = $this->wpdb;
        $GLOBALS["table_prefix"] = "wp_";
    }

    public function test_constant_value()
    {
        $data = [
            "constant_value" => "constant name",
            "odoo_form_id" => 1,
            "odoo_field_name" => "name",
            "cf7_field_name" => "",
        ];
        $update_data = ["x_2_many" => false] + $data;
        $results = array(array("id" => 3) + $data);
        $this->wpdb->shouldReceive("update")->with(
            "wp_odoo_conn_form_mapping", $update_data, array("id" => 3)
        )->once();
        $this->wpdb->shouldReceive("prepare")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", array(3)
        )->once()->andReturn("SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3");
        $this->wpdb->shouldReceive("get_results")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        )->once()->andReturn($results);

        $odoo_conn_put_form_mappings = new OdooConnPutOdooFormMappings(3);
        $response = $odoo_conn_put_form_mappings->request($data + array("id" => 3));

        $this->assertEquals($results, $response);
    }

    public function test_field_value()
    {
        $data = array(
            "cf7_field_name" => "your-name",
            "odoo_form_id" => 1,
            "odoo_field_name" => "name",
            "constant_value" => "",
            "x_2_many" => true
        );
        $sent_data = ["x_2_many" => "on"] + $data;
        $results = array(array("id" => 3) + $data);
        $this->wpdb->shouldReceive("update")->with(
            "wp_odoo_conn_form_mapping", $data, array("id" => 3)
        )->once();
        $this->wpdb->shouldReceive("prepare")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=%d", array(3)
        )->once()->andReturn(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        );
        $this->wpdb->shouldReceive("get_results")->with(
            "SELECT * FROM wp_odoo_conn_form_mapping WHERE id=3"
        )->once()->andReturn($results);

        $odoo_conn_put_form_mappings = new OdooConnPutOdooFormMappings(3);
        $response = $odoo_conn_put_form_mappings->request($sent_data + array("id" => 3));

        $this->assertEquals($results, $response);
    }

    public function test_both_constant_value_field_value()
    {
        $this->expectException(FieldNameConstantValueException::class);
        $this->expectExceptionMessage("Can't pass both a constant value and a cf7 field name as arguments");

        $data = array(
            "constant_value" => "constant name",
            "cf7_field_name" => "your-name",
            "odoo_form_id" => 1,
            "odoo_field_name" => "name",
        );
        $this->wpdb->shouldReceive("update")->never();
        $this->wpdb->shouldReceive("prepare")->never();
        $this->wpdb->shouldReceive("get_results")->never();

        $odoo_conn_put_form_mappings = new OdooConnPutOdooFormMappings(3);
        $odoo_conn_put_form_mappings->request($data + array("id" => 3));
    }

}

?>
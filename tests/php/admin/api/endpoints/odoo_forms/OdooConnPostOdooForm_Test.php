<?php

namespace php\admin\api\endpoints\odoo_forms;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooForm;
use Brain\Monkey\Functions;

class OdooConnPostOdooForm_Test extends \TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_forms.php");
    }

    public function test_ok()
    {
        $data = [
            "odoo_connection_id" => 1,
            "odoo_model" => "res.partner",
            "name" => "name",
            "contact_7_id" => 3
        ];
        $san_data = [
            "odoo_connection_id" => 1,
            "odoo_model" => "san_res.partner",
            "name" => "san_name",
            "contact_7_id" => 3
        ];
        $results = [["id" => 3] + $data];
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive("insert")->with(
            "wp_odoo_conn_form", $san_data, ["%d", "%s", "%s", "%d"]
        )->once();
        $wpdb->shouldReceive("prepare")->with(
            "SELECT * FROM wp_odoo_conn_form WHERE id=%d", [3]
        )->once()->andReturn(
            "SELECT * FROM wp_odoo_conn_form WHERE id=3"
        );
        $wpdb->shouldReceive("get_results")->with(
            "SELECT * FROM wp_odoo_conn_form WHERE id=3"
        )->once()->andReturn($results);
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";
        Functions\expect("sanitize_text_field")
            ->times(4)
            ->andReturnValues([1, "san_res.partner", "san_name", 3]);

        $odoo_conn_post_form = new OdooConnPostOdooForm();
        $response = $odoo_conn_post_form->request($data);

        $this->assertEquals($results, $response);
    }

}

?>
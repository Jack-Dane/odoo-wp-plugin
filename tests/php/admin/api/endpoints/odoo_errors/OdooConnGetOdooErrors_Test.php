<?php

namespace tests\php\admin\api\endpoints\odoo_errors\OdooConnGetOdooErrors;

require_once(__DIR__ . "/../common.php");
require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_errors.php");

use \PHPUnit\Framework\TestCase;
use function odoo_conn\admin\api\endpoints\odoo_conn_get_odoo_errors;

class OdooConnGetOdooErrors_Test extends TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function test_ok()
    {
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("prepare")->with("SELECT id, contact_7_id, time_occurred, error_message FROM wp_odoo_conn_errors ORDER BY wp_odoo_conn_errors.id DESC", [])
            ->once()->andReturn("SELECT id, contact_7_id, time_occurred, error_message FROM wp_odoo_conn_errors ORDER BY wp_odoo_conn_errors.id DESC");
        $wpdb->shouldReceive("get_results")->with("SELECT id, contact_7_id, time_occurred, error_message FROM wp_odoo_conn_errors ORDER BY wp_odoo_conn_errors.id DESC")
            ->once()->andReturn(
                array(array("id" => 3, "contact_7_id" => 2, "time_occurred" => "2023-08-23 00:00:00", "error_message" => "boom!"))
            );
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $results = odoo_conn_get_odoo_errors(array());

        $this->assertEquals(
            array(array("id" => 3, "contact_7_id" => 2, "time_occurred" => "2023-08-23 00:00:00", "error_message" => "boom!")
            ), $results);
    }

}



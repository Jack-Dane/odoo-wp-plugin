<?php

namespace tests\php\admin\api\endpoints\odoo_errors\OdooConnGetOdooErrors;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooErrors;

class OdooConnGetOdooErrors_Test extends \TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_errors.php");
    }

    public function test_ok()
    {
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->posts = "wp_posts";
        $wpdb->shouldReceive("prepare")->with(
            "SELECT wp_odoo_conn_errors.id, wp_odoo_conn_errors.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title', wp_odoo_conn_errors.time_occurred, wp_odoo_conn_errors.error_message"
            . " FROM wp_odoo_conn_errors JOIN wp_posts ON wp_odoo_conn_errors.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_errors.id DESC", []
        )->once()->andReturn(
            "SELECT wp_odoo_conn_errors.id, wp_odoo_conn_errors.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title', wp_odoo_conn_errors.time_occurred, wp_odoo_conn_errors.error_message"
            . " FROM wp_odoo_conn_errors JOIN wp_posts ON wp_odoo_conn_errors.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_errors.id DESC"
        );
        $wpdb->shouldReceive("get_results")->with(
            "SELECT wp_odoo_conn_errors.id, wp_odoo_conn_errors.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title', wp_odoo_conn_errors.time_occurred, wp_odoo_conn_errors.error_message"
            . " FROM wp_odoo_conn_errors JOIN wp_posts ON wp_odoo_conn_errors.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_errors.id DESC", "OBJECT"
        )->once()->andReturn(
            array(array("id" => 3, "contact_7_id" => 2, "time_occurred" => "2023-08-23 00:00:00", "error_message" => "boom!"))
        );
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $odoo_conn_get_odoo_errors = new OdooConnGetOdooErrors();
        $results = $odoo_conn_get_odoo_errors->request([]);

        $this->assertEquals(
            array(array("id" => 3, "contact_7_id" => 2, "time_occurred" => "2023-08-23 00:00:00", "error_message" => "boom!")),
            $results
        );
    }

}



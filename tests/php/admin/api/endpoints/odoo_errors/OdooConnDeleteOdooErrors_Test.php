<?php

namespace tests\php\admin\api\endpoints\odoo_errors\OdooConnDeleteOdooErrors;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooErrors;

class OdooConnDeleteOdooErrors_Test extends \TestClassBrainMonkey
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
        $data = array("id" => 5);
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("delete")->with("wp_odoo_conn_errors", $data, array("%d"))->once();
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $odoo_conn_delete_errors = new OdooConnDeleteOdooErrors();
        $results = $odoo_conn_delete_errors->request($data);

        $this->assertEquals(
            array("DELETE" => 5, "table" => "wp_odoo_conn_errors"), $results
        );
    }

}



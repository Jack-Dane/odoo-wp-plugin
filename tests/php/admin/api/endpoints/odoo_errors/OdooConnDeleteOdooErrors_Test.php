<?php

namespace tests\php\admin\api\endpoints\odoo_errors\OdooConnDeleteOdooErrors;

require_once(__DIR__ . "/../common.php");
require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_errors.php");

use \PHPUnit\Framework\TestCase;
use function odoo_conn\admin\api\endpoints\odoo_conn_delete_odoo_error;

class OdooConnDeleteOdooErrors_Test extends TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function test_ok()
    {
        $data = array("id" => 5);
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("delete")->with("wp_odoo_conn_errors", $data, array("%d"))->once();
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $results = odoo_conn_delete_odoo_error($data);

        $this->assertEquals(
            array("DELETE" => 5, "table" => "wp_odoo_conn_errors"), $results
        );
    }

}



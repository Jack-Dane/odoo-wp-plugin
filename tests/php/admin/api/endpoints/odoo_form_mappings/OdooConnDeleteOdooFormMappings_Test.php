<?php

namespace odoo_conn\tests\admin\api\endpoints\odoo_form_mappings\OdooConnDeleteOdooFormMappings;

require_once(__DIR__ . "/../common.php");
require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_form_mappings.php");

use \PHPUnit\Framework\TestCase;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooFormMappings;

class OdooConnDeleteOdooFormMappings_Test extends TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function test_ok()
    {
        $data = array("id" => 5);
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->shouldReceive("delete")->with("wp_odoo_conn_form_mapping", $data, array("%d"))->once();
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $odoo_conn_delete_odoo_form_mappings = new OdooConnDeleteOdooFormMappings();
        $response = $odoo_conn_delete_odoo_form_mappings->request($data);

        $this->assertEquals(
            array("DELETE" => 5, "table" => "wp_odoo_conn_form_mapping"), $response
        );
    }

}

?>
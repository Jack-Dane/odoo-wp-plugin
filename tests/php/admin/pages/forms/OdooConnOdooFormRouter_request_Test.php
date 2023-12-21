<?php

namespace php\admin\pages\forms;

require_once(__DIR__ . "/../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\pages\forms\OdooConnOdooFormRouter;
use Brain\Monkey\Functions;


class OdooConnOdooFormRouter_request_Test extends \TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        \Mockery::mock("WP_List_Table");

        require_once(__DIR__ . "/../../../../../admin/api/main.php");
        require_once(__DIR__ . "/../../../../../admin/table_display.php");
        require_once(__DIR__ . "/../../../../../admin/pages/page_router.php");
        require_once(__DIR__ . "/../../../../../admin/pages/forms/odoo_form.php");

        $this->odoo_conn_page_router = \Mockery::mock(
            OdooConnOdooFormRouter::class, ["menu-slug"]
        )->makePartial();
        $this->odoo_conn_page_router->shouldAllowMockingProtectedMethods();
    }

    function test_request_delete()
    {
        $GLOBALS["_REQUEST"] = ["id" => 3, "page_action" => "delete"];
        $this->odoo_conn_page_router->shouldReceive("delete")->with(3)->once();
        $this->odoo_conn_page_router->shouldReceive("display_table")->once();
        Functions\expect("check_admin_referer")->once();

        $this->odoo_conn_page_router->request();
    }

    function test_request_new()
    {
        $GLOBALS["_REQUEST"] = ["page_action" => "new"];
        $this->odoo_conn_page_router->shouldReceive("display_input_form")->once();
        $this->odoo_conn_page_router->shouldReceive("display_table")->never();
        Functions\expect("wp_enqueue_style")->once();
        Functions\expect("plugins_url")->once();

        $this->odoo_conn_page_router->request();
    }

    function test_request_edit()
    {
        $GLOBALS["_REQUEST"] = ["page_action" => "edit", "id" => 3];
        $this->odoo_conn_page_router->shouldReceive("display_edit_form")->with(3)->once();
        $this->odoo_conn_page_router->shouldReceive("display_table")->never();
        Functions\expect("wp_enqueue_style")->once();
        Functions\expect("plugins_url")->once();

        $this->odoo_conn_page_router->request();
    }

    function test_request_other()
    {
        $GLOBALS["_REQUEST"] = ["id" => 3, "page_action" => "other"];
        $this->odoo_conn_page_router->shouldReceive("delete")->never();
        $this->odoo_conn_page_router->shouldReceive("display_input_form")->never();
        $this->odoo_conn_page_router->shouldReceive("display_table")->once();

        $this->odoo_conn_page_router->request();
    }

    function test_no_page_action()
    {
        $GLOBALS["_REQUEST"] = [];
        $this->odoo_conn_page_router->shouldReceive("delete")->never();
        $this->odoo_conn_page_router->shouldReceive("display_input_form")->never();
        $this->odoo_conn_page_router->shouldReceive("display_table")->once();

        $this->odoo_conn_page_router->request();
    }

}

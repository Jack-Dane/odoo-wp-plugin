<?php

namespace php\admin\api\endpoints\cf7_posts;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use function \odoo_conn\admin\api\endpoints\odoo_conn_get_contact_7_forms;


class OdooConnGetContact7Form_Test extends \TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/c7f_posts.php");
    }

    public function test_ok()
    {
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->posts = "wp_posts";
        $wpdb->shouldReceive("prepare")->with(
            "SELECT ID, post_title FROM wp_posts WHERE post_type=%s ORDER BY wp_posts.ID DESC",
            ["wpcf7_contact_form"]
        )->once()->andReturn(
            "SELECT ID, post_title FROM wp_posts WHERE post_type='wpcf7_contact_form' ORDER BY wp_posts.ID DESC"
        );
        $wpdb->shouldReceive("get_results")->with(
            "SELECT ID, post_title FROM wp_posts WHERE post_type='wpcf7_contact_form' ORDER BY wp_posts.ID DESC",
            "OBJECT"
        )->once()->andReturn(
            array(array("ID" => 4, "post_title" => "Title"))
        );
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";

        $response = odoo_conn_get_contact_7_forms(array());

        $this->assertEquals(array(array("ID" => 4, "post_title" => "Title")), $response);
    }
}

?>
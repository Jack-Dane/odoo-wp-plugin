<?php

namespace php\admin\database_connection\odoo_errors;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnGetOdooErrors;
use PHPUnit\Framework\TestCase;

class OdooConnGetOdooErrors_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->posts = 'wp_posts';
        $wpdb->shouldReceive('prepare')->with(
            "SELECT wp_odoo_conn_errors.id, wp_odoo_conn_errors.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title', wp_odoo_conn_errors.time_occurred, wp_odoo_conn_errors.error_message"
            . " FROM wp_odoo_conn_errors JOIN wp_posts ON wp_odoo_conn_errors.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_errors.id DESC", []
        )->once()->andReturn(
            "SELECT wp_odoo_conn_errors.id, wp_odoo_conn_errors.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title', wp_odoo_conn_errors.time_occurred, wp_odoo_conn_errors.error_message"
            . " FROM wp_odoo_conn_errors JOIN wp_posts ON wp_odoo_conn_errors.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_errors.id DESC"
        );
        $wpdb->shouldReceive('get_results')->with(
            "SELECT wp_odoo_conn_errors.id, wp_odoo_conn_errors.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title', wp_odoo_conn_errors.time_occurred, wp_odoo_conn_errors.error_message"
            . " FROM wp_odoo_conn_errors JOIN wp_posts ON wp_odoo_conn_errors.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_errors.id DESC", 'OBJECT'
        )->once()->andReturn(
            [[
				'id' => 3,
				'contact_7_id' => 2,
				'time_occurred' => '2023-08-23 00:00:00',
				'error_message' => 'boom!'
			]]
        );
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

        $odoo_conn_get_odoo_errors = new OdooConnGetOdooErrors();
        $results = $odoo_conn_get_odoo_errors->request([]);

        $this->assertEquals(
            [[
				'id' => 3,
				'contact_7_id' => 2,
				'time_occurred' => '2023-08-23 00:00:00',
				'error_message' => 'boom!'
			]],
            $results
        );
    }

}



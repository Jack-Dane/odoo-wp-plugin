<?php

namespace php\admin\database_connection\odoo_forms;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnGetOdooForm;
use PHPUnit\Framework\TestCase;

class OdooConnGetOdooForm_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $query_response = [[
			'id' => 3,
			'odoo_form_id' => 2,
			'odoo_form_name' => 'form name',
			'cf7_field_name' => 'your-name',
			'odoo_field_name' => 'name',
			'constant_value' => null
		]];
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->posts = 'wp_posts';
        $wpdb->shouldReceive('prepare')->with(
            "SELECT wp_odoo_conn_form.id, wp_odoo_conn_form.odoo_connection_id, wp_odoo_conn_connection.name as 'odoo_connection_name', wp_odoo_conn_form.odoo_model, wp_odoo_conn_form.name, wp_odoo_conn_form.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title' FROM wp_odoo_conn_form JOIN wp_odoo_conn_connection ON wp_odoo_conn_form.odoo_connection_id=wp_odoo_conn_connection.id JOIN wp_posts ON wp_odoo_conn_form.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_form.id DESC", []
        )->once()->andReturn(
            "SELECT wp_odoo_conn_form.id, wp_odoo_conn_form.odoo_connection_id, wp_odoo_conn_connection.name as 'odoo_connection_name', wp_odoo_conn_form.odoo_model, wp_odoo_conn_form.name, wp_odoo_conn_form.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title' FROM wp_odoo_conn_form JOIN wp_odoo_conn_connection ON wp_odoo_conn_form.odoo_connection_id=wp_odoo_conn_connection.id JOIN wp_posts ON wp_odoo_conn_form.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_form.id DESC"
        );
        $wpdb->shouldReceive('get_results')->with(
            "SELECT wp_odoo_conn_form.id, wp_odoo_conn_form.odoo_connection_id, wp_odoo_conn_connection.name as 'odoo_connection_name', wp_odoo_conn_form.odoo_model, wp_odoo_conn_form.name, wp_odoo_conn_form.contact_7_id as 'contact_7_id', wp_posts.post_title as 'contact_7_title' FROM wp_odoo_conn_form JOIN wp_odoo_conn_connection ON wp_odoo_conn_form.odoo_connection_id=wp_odoo_conn_connection.id JOIN wp_posts ON wp_odoo_conn_form.contact_7_id=wp_posts.ID ORDER BY wp_odoo_conn_form.id DESC", 'OBJECT'
        )->once()->andReturn($query_response);
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

        $odoo_conn_get_form = new OdooConnGetOdooForm();
        $response = $odoo_conn_get_form->request([]);

        $this->assertEquals($query_response, $response);
    }

}

?>
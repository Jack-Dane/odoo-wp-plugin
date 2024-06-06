<?php

namespace php\admin\database_connection\odoo_form_mappings;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnGetOdooFormMappings;
use PHPUnit\Framework\TestCase;

class OdooConnGetOdooFormMappings_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    public function setUp(): void
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
			'constant_value' => null,
			'x_2_many' => false
		]];
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->shouldReceive('prepare')->with(
            "SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value, wp_odoo_conn_form_mapping.x_2_many FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC", []
        )->once()->andReturn(
            "SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value, wp_odoo_conn_form_mapping.x_2_many FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC"
        );
        $wpdb->shouldReceive('get_results')->with(
            "SELECT wp_odoo_conn_form_mapping.id, wp_odoo_conn_form_mapping.odoo_form_id, wp_odoo_conn_form.name as 'odoo_form_name', wp_odoo_conn_form_mapping.cf7_field_name, wp_odoo_conn_form_mapping.odoo_field_name, wp_odoo_conn_form_mapping.constant_value, wp_odoo_conn_form_mapping.x_2_many FROM wp_odoo_conn_form_mapping JOIN wp_odoo_conn_form ON wp_odoo_conn_form_mapping.odoo_form_id=wp_odoo_conn_form.id ORDER BY wp_odoo_conn_form_mapping.id DESC", 'OBJECT'
        )->once()->andReturn(
            $query_response
        );
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

        $odoo_conn_get_odoo_form_mappings = new OdooConnGetOdooFormMappings();
        $response = $odoo_conn_get_odoo_form_mappings->request([]);

        $this->assertEquals($query_response, $response);
    }

}

?>
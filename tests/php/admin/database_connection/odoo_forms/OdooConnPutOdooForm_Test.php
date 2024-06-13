<?php

namespace php\admin\database_connection\odoo_forms;

use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnPutOdooForm;
use PHPUnit\Framework\TestCase;

class OdooConnPutOdooForm_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $data = [
            'odoo_connection_id' => 1,
            'name' => 'form name',
            'contact_7_id' => 1,
            'odoo_model' => 'res.partner'
        ];
        $san_data = [
            'odoo_connection_id' => 1,
            'name' => 'san form name',
            'contact_7_id' => 1,
            'odoo_model' => 'san_res.partner'
        ];
        $results = [['id' => 3] + $data];
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive('update')->with(
            'wp_odoo_conn_form', $san_data, ['id' => 3]
        )->once();
        $wpdb->shouldReceive('prepare')->with(
            'SELECT * FROM wp_odoo_conn_form WHERE id=%d', [3]
        )->once()->andReturn(
            'SELECT * FROM wp_odoo_conn_form WHERE id=3'
        );
        $wpdb->shouldReceive('get_results')->with(
            'SELECT * FROM wp_odoo_conn_form WHERE id=3'
        )->once()->andReturn($results);
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';
        Functions\expect('sanitize_text_field')
            ->times(4)
            ->andReturnValues([1, 'san form name', 1, 'san_res.partner']);

        $odoo_conn_put_odoo_form = new OdooConnPutOdooForm(3);
        $response = $odoo_conn_put_odoo_form->request($data + ['id' => 3]);

        $this->assertEquals($results, $response);
    }

}

?>
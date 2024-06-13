<?php

namespace php\admin\database_connection\odoo_form_mappings;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnDeleteOdooFormMappings;
use PHPUnit\Framework\TestCase;

class OdooConnDeleteOdooFormMappings_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $data = ['id' => 5];
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->shouldReceive('delete')->with('wp_odoo_conn_form_mapping', $data, ['%d'])->once();
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

        $odoo_conn_delete_odoo_form_mappings = new OdooConnDeleteOdooFormMappings();
        $response = $odoo_conn_delete_odoo_form_mappings->request($data);

        $this->assertEquals(
            ['DELETE' => 5, 'table' => 'wp_odoo_conn_form_mapping'], $response
        );
    }

}

?>
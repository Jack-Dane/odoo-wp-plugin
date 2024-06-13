<?php

namespace php\admin\database_connection\odoo_connections;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnDeleteOdooConnection;
use PHPUnit\Framework\TestCase;

class OdooConnDeleteOdooConnection_Test extends TestCase
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
        $wpdb->shouldReceive('delete')->with('wp_odoo_conn_connection', $data, ['%d'])->once();
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

        $odoo_conn_delete = new OdooConnDeleteOdooConnection();
        $results = $odoo_conn_delete->request($data);

        $this->assertEquals(
            ['DELETE' => 5, 'table' => 'wp_odoo_conn_connection'], $results
        );
    }

}

?>
<?php

namespace php\admin\database_connection\odoo_connections;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnGetOdooConnection;
use PHPUnit\Framework\TestCase;


class OdooConnGetOdooConnection_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->shouldReceive(
            'prepare'
        )->with(
            'SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection ORDER BY wp_odoo_conn_connection.id DESC',
			[]
        )->once()->andReturn(
            'SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection ORDER BY wp_odoo_conn_connection.id DESC'
        );
        $wpdb->shouldReceive(
            'get_results'
        )->with(
            'SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection ORDER BY wp_odoo_conn_connection.id DESC',
			'OBJECT'
        )->once()->andReturn(
            [[
				'id' => 3,
				'name' => 'Odoo Connection',
				'username' => 'jackd98',
				'url' => 'localhost:8069',
				'database_name' => 'odoo_db'
			]]
        );
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

        $get_odoo_connection = new OdooConnGetOdooConnection();
        $results = $get_odoo_connection->request([]);

        $this->assertEquals(
            array(
                array(
                    'id' => 3,
                    'name' => 'Odoo Connection',
                    'username' => 'jackd98',
                    'url' => 'localhost:8069',
                    'database_name' => 'odoo_db'
                )
            ),
            $results
        );
    }

}

?>
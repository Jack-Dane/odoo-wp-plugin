<?php

namespace php\admin\database_connection\odoo_connections;

use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnPutOdooConnection;
use PHPUnit\Framework\TestCase;

class OdooConnPutOdooConnection_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $data = [
            'name' => 'name',
            'username' => 'username',
            'url' => 'url',
            'database_name' => 'database_name'
        ];
        $san_data = [
            'name' => 'san_name',
            'username' => 'san_username',
            'url' => 'san_url',
            'database_name' => 'san_database_name'
        ];
        $results = [
            ['id' => 3] + $data
        ];
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive('update')->with('wp_odoo_conn_connection', $san_data, ['id' => 3])->once();
        $wpdb->shouldReceive('prepare')->with('SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=%d', [3])
            ->once()->andReturn('SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3');
        $wpdb->shouldReceive('get_results')->with('SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3')
            ->once()->andReturn($results);
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';
        Functions\expect('sanitize_text_field')
            ->times(3)
            ->andReturnValues(['san_name', 'san_username', 'san_database_name']);
        Functions\expect('sanitize_url')->once()->andReturn('san_url');

        $odoo_conn_put_connection = new OdooConnPutOdooConnection(3);
        $response = $odoo_conn_put_connection->request($data + ['id' => 3]);

        $this->assertEquals($results, $response);
    }

    public function test_cant_update_api_key()
    {
        $update_data = [
            'name' => 'san_name',
            'username' => 'san_username',
            'url' => 'san_url',
            'database_name' => 'san_database_name',
        ];
        $results = [
            ['id' => 3] + $update_data
        ];
        $send_data = ['api_key' => 'abc'] + $update_data;
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->insert_id = 3;
        $wpdb->shouldReceive('update')->with('wp_odoo_conn_connection', $update_data, ['id' => 3])->once();
        $wpdb->shouldReceive('prepare')->with('SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=%d', [3])
            ->once()->andReturn('SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3');
        $wpdb->shouldReceive('get_results')->with('SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3')
            ->once()->andReturn($results);
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';
        Functions\expect('sanitize_text_field')
            ->times(3)
            ->andReturnValues(['san_name', 'san_username', 'san_database_name']);
        Functions\expect('sanitize_url')->once()->andReturn('san_url');

        $odoo_conn_put_connection = new OdooConnPutOdooConnection(3);
        $response = $odoo_conn_put_connection->request($send_data + ['id' => 3]);

        $this->assertEquals($results, $response);
    }

}

?>
<?php

namespace php\admin\api\endpoints\odoo_connections;

require_once(__DIR__ . "/../../../../TestClassBrainMonkey.php");

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooConnection;
use Brain\Monkey\Functions;

class OdooConnPostOdooConnection_Test extends \TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . "/../../../../../../admin/api/schema.php");
        require_once(__DIR__ . "/../../../../../../admin/api/endpoints/odoo_connections.php");
        require_once(__DIR__ . "/../../../../../../encryption.php");
    }

    public function test_ok()
    {
        $data = [
            "name" => "name",
            "username" => "username",
            "api_key" => "api_key",
            "url" => "url",
            "database_name" => "database_name"
        ];
        $results = [
            [
                "id" => 3,
                "name" => "Odoo Connection",
                "username" => "jackd98",
                "url" => "localhost:8069",
                "database_name" => "odoo_db"
            ]
        ];
        $wpdb = \Mockery::mock("WPDB");
        $wpdb->insert_id = 3;
        $encrypted_data = [
            "name" => "san_name",
            "username" => "san_username",
            "api_key" => "encrypted_api_key",
            "url" => "san_url",
            "database_name" => "san_database_name"
        ];
        $wpdb->shouldReceive("insert")->with("wp_odoo_conn_connection", $encrypted_data, ["%s", "%s", "%s", "%s", "%s"])->once();
        $wpdb->shouldReceive("prepare")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=%d", [3])
            ->once()->andReturn("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3");
        $wpdb->shouldReceive("get_results")->with("SELECT id, name, username, url, database_name FROM wp_odoo_conn_connection WHERE id=3")
            ->once()->andReturn($results);
        $GLOBALS["wpdb"] = $wpdb;
        $GLOBALS["table_prefix"] = "wp_";
        $odoo_conn_file_handler_mock = $this->createMock(\odoo_conn\encryption\OdooConnEncryptionHandler::class);
        $odoo_conn_file_handler_mock->expects($this->once())->method("encrypt")->with($this->equalTo("san_api_key"))->willReturn("encrypted_api_key");
        Functions\expect("sanitize_text_field")
            ->times(4)
            ->andReturnValues(["san_api_key", "san_name", "san_username", "san_database_name"]);
        Functions\expect("sanitize_url")->once()->andReturn("san_url");

        // easier to call class directly to mock the encryption file handler
        $odoo_conn_post_odoo_connection = new OdooConnPostOdooConnection($odoo_conn_file_handler_mock);
        $response = $odoo_conn_post_odoo_connection->request($data);

        $this->assertEquals($results, $response);
    }

}

?>
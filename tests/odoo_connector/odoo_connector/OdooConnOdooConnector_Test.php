<?php 

namespace odoo_conn\tests\odoo_connector\odoo_connector\OdooConnOdooConnector_Test;

require_once("odoo_connector/odoo_connector.php");

use \PHPUnit\Framework\TestCase;
use \odoo_conn\odoo_connector\odoo_connector\OdooConnOdooConnector;

class OdooConnOdooConnector_Test extends TestCase {

	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_ok () {
		$common_mock = \Mockery::mock("Ripcord_Client");
		$common_mock->shouldReceive("version")->with()->once()->andReturn("1.0");
		$common_mock->shouldReceive("authenticate")->with("database", "username", "api_key", "1.0")->once()->andReturn(2);
		$models_mock = \Mockery::mock("Models");
		$models_mock->shouldReceive("execute_kw")->with("database", 2, "api_key", "res.partner", "create", array("name" => "Jack"))->once();
		$ripcord_mock = \Mockery::mock("alias:ripcord");
		$ripcord_mock->shouldReceive("client")->with("url/xmlrpc/2/common")->once()->andReturn($common_mock);
		$ripcord_mock->shouldReceive("client")->with("url/xmlrpc/2/object")->once()->andReturn($models_mock);

		$odoo_connector = new OdooConnOdooConnector("username", "api_key", "database", "url", $ripcord_mock);
		$odoo_connector->create_object("res.partner", array("name" => "Jack"));
	} 

}

?>
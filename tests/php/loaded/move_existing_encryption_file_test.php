<?php

namespace php\loaded;
require_once __DIR__ . "/../TestClassBrainMonkey.php";

use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use TestClassBrainMonkey;

class move_existing_encryption_file_test extends TestClassBrainMonkey
{

	use MockeryPHPUnitIntegration;

	function setUp(): void
	{
		parent::setUp();

		if (!defined("ABSPATH")) {
			define("ABSPATH", "vfs://root/");
		}
		Functions\when("plugin_dir_path")->justReturn("vfs://root/plugins/odoo-wp-plugin/");

		require_once __DIR__ . "/../../../loaded.php";

		$this->root = vfsStream::setup("root", 0777);
		vfsStream::newDirectory("/plugins/odoo-wp-plugin/")->at($this->root);
	}

	function test_key_file_moved()
	{
		vfsStream::newFile("odoo_conn.key")->at($this->root)->setContent("key");

		move_existing_encryption_file();

		$this->assertTrue(file_exists("vfs://root/plugins/odoo-wp-plugin/odoo_conn.key"));
		$this->assertFalse(file_exists("vfs://root/odoo_conn.key"));
		$this->assertEquals(
			"key", $this->root->getChild("plugins/odoo-wp-plugin/odoo_conn.key")->getContent()
		);
	}

	function test_old_key_file_does_not_exist()
	{
		vfsStream::newFile("plugins/odoo-wp-plugin/odoo_conn.key")->at($this->root)->setContent("key");

		move_existing_encryption_file();

		$this->assertTrue(file_exists("vfs://root/plugins/odoo-wp-plugin/odoo_conn.key"));
		$this->assertFalse(file_exists("vfs://root/odoo_conn.key"));
		$this->assertEquals(
			"key", $this->root->getChild("plugins/odoo-wp-plugin/odoo_conn.key")->getContent()
		);
	}

}

?>

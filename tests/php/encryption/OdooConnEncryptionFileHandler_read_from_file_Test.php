<?php

namespace odoo_conn\tests\OdooConnEncryptionFileHandler_Test;

use odoo_conn\encryption\OdooConnEncryptionFileHandler;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;

require_once(__DIR__ . "/../../../encryption.php");

class OdooConnEncryptionFileHandler_read_from_file_Test extends TestCase
{

    public function setUp(): void
    {
		if (!defined("ABSPATH")) {
			define("ABSPATH", "vfs://root/");
		}
		Functions\when("plugin_dir_path")->justReturn("vfs://root/plugins/odoo-wp-plugin/");

        $this->root = vfsStream::setup("root", 0777);
        $this->file_handler = new OdooConnEncryptionFileHandler();
    }

    public function test_existing_file()
    {
        vfsStream::newFile("plugins/odoo-wp-plugin/odoo_conn.key")->at($this->root)->setContent("abc");

        $key = $this->file_handler->read_from_file();

        $this->assertEquals("abc", $key);
    }

    public function test_non_existing_file()
    {
        $this->assertFalse($this->root->hasChild("plugins/odoo-wp-plugin/odoo_conn.key"));

        $key = $this->file_handler->read_from_file();

        $this->assertNull($key);
    }

}

?>
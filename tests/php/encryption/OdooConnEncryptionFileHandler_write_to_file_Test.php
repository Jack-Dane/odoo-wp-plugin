<?php

use odoo_conn\encryption\OdooConnEncryptionFileHandler;
use org\bovigo\vfs\vfsStream;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;

require_once(__DIR__ . "/../../../encryption.php");

class OdooConnEncryptionFileHandler_write_to_file_Test extends TestCase
{

    use PHPMock;

    public function setUp(): void
    {
		if (!defined("ABSPATH")) {
			define("ABSPATH", "vfs://root/");
		}
		Functions\when("plugin_dir_path")->justReturn("vfs://root/plugins/odoo-wp-plugin/");

        $this->root = vfsStream::setup("root", 0777);
		vfsStream::newDirectory("/plugins/odoo-wp-plugin/")->at($this->root);
        $this->file_handler = new OdooConnEncryptionFileHandler();
		$this->flock = $this->getFunctionMock("odoo_conn\\encryption", "flock");
        $this->sleep = $this->getFunctionMock("odoo_conn\\encryption", "sleep");
    }

    public function test_ok()
    {
        vfsStream::newFile("plugins/odoo-wp-plugin/odoo_conn.key")->at($this->root)->setContent("def");
        $this->flock->expects($this->exactly(1))->willReturnCallback(
            function ($encryption_file, $lock_type, &$would_block_lock = false) {
                $would_block_lock = false;
                return true;
            }
        );

        $this->file_handler->write_to_file("abc");

        $this->assertEquals("abc", $this->root->getChild("plugins/odoo-wp-plugin/odoo_conn.key")->getContent());
    }

    public function test_timeout()
    {
        $this->expectException(\Exception::class);
        $this->sleep->expects($this->exactly(10));
        $this->flock->expects($this->exactly(11))->willReturnCallback(
            function ($encryption_file, $lock_type, &$would_block_lock = false) {
                $would_block_lock = true;
                return false;
            }
        );

        $this->file_handler->write_to_file("abc");

        $this->expectErrorMessage("Timed out waiting to write to the key file");
    }

}

?>
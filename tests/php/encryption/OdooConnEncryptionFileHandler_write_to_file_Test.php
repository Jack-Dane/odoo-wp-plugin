<?php

namespace odoo_conn\tests\OdooConnEncryptionFileHandler_Test;

use \org\bovigo\vfs\vfsStream;
use \PHPUnit\Framework\TestCase;

define("ABSPATH", "vfs://root/");

require_once(__DIR__ . "/../../../encryption.php");

class OdooConnEncryptionFileHandler_write_to_file_Test extends TestCase {

	use \phpmock\phpunit\PHPMock;

	public function setUp (): void {
		$this->root = vfsStream::setup("root", 0777);
		$this->flock = $this->getFunctionMock("odoo_conn\\encryption", "flock");
		$this->file_handler = new \odoo_conn\encryption\OdooConnEncryptionFileHandler();
		$this->sleep = $this->getFunctionMock("odoo_conn\\encryption", "sleep");
	}

	public function test_ok () {
		vfsStream::newFile("odoo_conn.key")->at($this->root)->setContent("def");
		$this->flock->expects($this->exactly(1))->willReturnCallback(
			function ($encryption_file, $lock_type, &$would_block_lock=false) {
				$would_block_lock = false;
				return true;
			}
		);

		$key = $this->file_handler->write_to_file("abc");

		$this->assertEquals("abc", $this->root->getChild("odoo_conn.key")->getContent());
	}

	public function test_timeout () {
		$this->expectException(\Exception::class);
		$this->sleep->expects($this->exactly(10));
		$this->flock->expects($this->exactly(11))->willReturnCallback(
			function ($encryption_file, $lock_type, &$would_block_lock=false) {
				$would_block_lock = true;
				return false;
			}
		);

		$key = $this->file_handler->write_to_file("abc");

		$this->expectErrorMessage("Timed out waiting to write to the key file");
	}

}

?>
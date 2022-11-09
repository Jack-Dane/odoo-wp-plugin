<?php 

namespace odoo_conn\tests\odoo_conn_generate_encryption_key_Test;

use \org\bovigo\vfs\vfsStream;
use \PHPUnit\Framework\TestCase;

define("ABSPATH", "vfs://root/");

require_once("encryption.php");

class odoo_conn_generate_encryption_key_Test extends TestCase {

	use \phpmock\phpunit\PHPMock;

	protected function setUp(): void {
		$this->root = vfsStream::setup("root", 0777);
        $this->sodium_crypto_secretbox_keygen = $this->getFunctionMock("odoo_conn\\encryption", "sodium_crypto_secretbox_keygen");
		$this->flock = $this->getFunctionMock("odoo_conn\\encryption", "flock");
    }

	public function test_ok () {
		$this->flock->expects($this->exactly(2))->willReturnCallback(
			function ($encryption_file, $lock_type, &$would_block_lock=false) {
				$would_block_lock = false;
				return true;
			}
		);
		$this->sodium_crypto_secretbox_keygen->expects($this->once())
			->willReturnCallback(
				function () {
					return "abc";
				}
		);
		
		$key = \odoo_conn\encryption\odoo_conn_generate_encryption_key();

		$this->assertTrue( $this->root->hasChild( "odoo_conn.key" ) );
		$this->assertEquals( "abc", $this->root->getChild( "odoo_conn.key" )->getContent() );
		$this->assertEquals("abc", $key);
	}

	public function test_time_out () {
		$sleep = $this->getFunctionMock("odoo_conn\\encryption", "sleep");
		$sleep->expects($this->exactly(10));
		$this->flock->expects($this->exactly(11))->willReturnCallback(
			function ($encryption_file, $lock_type, &$would_block_lock=false) {
				$would_block_lock = true;
				return false;
			}
		);
		$this->sodium_crypto_secretbox_keygen->expects($this->never());
		$this->expectException(\Exception::class);

		$key = \odoo_conn\encryption\odoo_conn_generate_encryption_key();

		$this->expectErrorMessage("Timed out waiting to write to the key file");
	}

	public function test_failure_to_encrypt () {
		$this->flock->expects($this->exactly(2))->willReturnCallback(
			function ($encryption_file, $lock_type, &$would_block_lock=false) {
				$would_block_lock = false;
				return true;
			}
		);
		$this->sodium_crypto_secretbox_keygen->expects($this->once())
			->willReturnCallback(
				function () {
					throw new \Exception("boom!");
				}
		);

		$key = \odoo_conn\encryption\odoo_conn_generate_encryption_key();

		$this->assertFalse($key);
	}

}

?>
<?php

namespace odoo_conn\tests\get_encryption_key_Test; 

use \org\bovigo\vfs\vfsStream;
use \PHPUnit\Framework\TestCase;

define("ABSPATH", "vfs://root/");

require_once("encryption.php");

class get_encryption_key_Test extends TestCase {

	use \phpmock\phpunit\PHPMock;

	public function setUp (): void {
		$this->root = vfsStream::setup( "root", 0777 );
		$this->sodium_crypto_secretbox_keygen = $this->getFunctionMock("odoo_conn\\encryption", "sodium_crypto_secretbox_keygen");
		$this->flock = $this->getFunctionMock("odoo_conn\\encryption", "flock");
	}

	public function test_existing_encryption_file () {
		vfsStream::newFile( "odoo_conn.key" )->at( $this->root )->setContent( "abc" );
		$this->flock->expects($this->never());

		$key = \odoo_conn\encryption\get_encryption_key();

		$this->assertEquals("abc", $key);
	}

	public function test_non_existing_encryption_file () {
		$this->assertFalse( $this->root->hasChild( "odoo_conn.key" ) );
		$this->flock->expects($this->exactly(2))
			->willReturnCallback(
				function ($encryption_file, $lock_type, &$would_block_lock=false) {
					return true;
				}
		);
		$this->sodium_crypto_secretbox_keygen->expects($this->once())
			->willReturnCallback(
				function () {
					return "abc";
				}
		);

		$key = \odoo_conn\encryption\get_encryption_key();

		$this->assertTrue( $this->root->hasChild( "odoo_conn.key" ) );
		$this->assertEquals( "abc", $this->root->getChild( "odoo_conn.key" )->getContent() );
		$this->assertEquals( "abc", $key );
	}

}

?>
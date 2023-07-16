<?php

namespace odoo_conn\tests\OdooConnEncryptionHandler_Test;

use \PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../../../encryption.php");

class OdooConnEncryptionHandler_get_encryption_key_Test extends TestCase
{

    use \phpmock\phpunit\PHPMock;

    public function setUp(): void
    {
        $this->sodium_crypto_secretbox_keygen = $this->getFunctionMock(
            "odoo_conn\\encryption", "sodium_crypto_secretbox_keygen"
        );
        $this->file_handler_mock = $this->createMock(
            \odoo_conn\encryption\OdooConnEncryptionFileHandler::class
        );
        $this->encryption_handler = new \odoo_conn\encryption\OdooConnEncryptionHandler(
            $this->file_handler_mock
        );
    }

    public function test_no_encryption_file()
    {
        $this->file_handler_mock->expects($this->once())
            ->method("read_from_file")
            ->willReturn(null);
        $this->file_handler_mock->expects($this->once())
            ->method("write_to_file")
            ->with("abc");
        $this->sodium_crypto_secretbox_keygen->expects($this->once())
            ->willReturn("abc");

        $key = $this->encryption_handler->get_encryption_key();

        $this->assertEquals("abc", $key);
    }

    public function test_encryption_file()
    {
        $this->file_handler_mock->expects($this->once())
            ->method("read_from_file")
            ->willReturn("abc");
        $this->sodium_crypto_secretbox_keygen->expects($this->never());

        $key = $this->encryption_handler->get_encryption_key();

        $this->assertEquals("abc", $key);
    }

}

?>
<?php

use \PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

class OdooErrors_Test extends TestCase
{

    public function setUp(): void
    {
        $this->client = new Client();
    }

    public function test_get_errors()
    {
        $failure = false;
        try {
            $this->client->request(
                "GET", "http://localhost:8000/?rest_route=/odoo_conn/v1/get-odoo-errors"
            );
        } catch (ClientException $e) {
            $failure = true;
            $this->assertEquals(401, $e->getResponse()->getStatusCode());
        }
        $this->assertTrue($failure);
    }

    public function test_delete_error() {
        $failure = false;
        try {
            $this->client->request(
                "DELETE", "http://localhost:8000/?rest_route=/odoo_conn/v1/delete-odoo-error"
            );
        } catch (ClientException $e) {
            $failure = true;
            $this->assertEquals(401, $e->getResponse()->getStatusCode());
        }
        $this->assertTrue($failure);
    }

}


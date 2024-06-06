<?php

namespace php\admin;

require_once __DIR__ . '/../TestClassBrainMonkey.php';

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\php\cf7hook\OdooConnContactForm7Hook;
use odoo_conn\admin\odoo_connector\OdooConnException;
use odoo_conn\admin\odoo_connector\OdooConnXMLRPCStringField;
use odoo_conn\admin\odoo_connector\OdooConnXMLRPCBaseX2ManyField;
use odoo_conn\admin\odoo_connector\OdooConnXMLRPCStringX2ManyField;
use phpmock\phpunit\PHPMock;
use TestClassBrainMonkey;

class FormMock
{

    function __construct($id, $odoo_connection_id, $odoo_model)
    {
        $this->id = $id;
        $this->odoo_connection_id = $odoo_connection_id;
        $this->odoo_model = $odoo_model;
    }

}

class ConnectionMock
{

    function __construct($username, $api_key, $database_name, $url)
    {
        $this->username = $username;
        $this->api_key = $api_key;
        $this->database_name = $database_name;
        $this->url = $url;
    }

}

class FieldMappingMock
{

    function __construct($cf7_field_name, $constant_value, $odoo_field_name, $x_2_many)
    {
        $this->cf7_field_name = $cf7_field_name;
        $this->constant_value = $constant_value;
        $this->odoo_field_name = $odoo_field_name;
        $this->x_2_many = $x_2_many;
    }

}


class OdooConnContactForm7Hook_Test extends TestClassBrainMonkey
{

    use MockeryPHPUnitIntegration;
    use PHPMock;

    public function setUp(): void
    {
        parent::setUp();

        require_once(__DIR__ . '/../../../admin/cf7hook.php');
        require_once(__DIR__ . '/../../../admin/odoo_connector/odoo_connector.php');

        $this->odoo_connector = \Mockery::mock('OdooConnector');
        $this->wpcf7_contact_form = \Mockery::mock('WPCF7_ContactForm_current');
        $this->wpcf7_contact_form->id = 1;
        $this->wpcf7_submission = \Mockery::mock('WPCF7_Submission_instance');
        $this->encryption_handler = \Mockery::mock('EncryptionHandler');
        $this->encryption_handler->shouldReceive('decrypt')->with('api_key')
            ->andReturn('decrypted_api_key');
        $this->database_handler = \Mockery::mock('DatabaseHandler');
        $this->XMLRPC_string_field = \Mockery::mock('OdooConnXMLRPCStringField');
        $this->XMLRPC_Base_X_2_Many_Field = \Mockery::mock('OdooConnXMLRPCBaseX2ManyField');
        $this->XMLRPC_String_X_2_Many_Field = \Mockery::mock('OdooConnXMLRPCStringX2ManyField');
        $this->odoo_conn_contact_form_7_hook = \Mockery::mock(OdooConnContactForm7Hook::class,
            array(
                $this->wpcf7_contact_form, $this->wpcf7_submission, $this->encryption_handler,
                $this->database_handler
            ))->makePartial();
    }

    public function test_ok()
    {
        $this->database_handler->shouldReceive('get_forms_from_database')->with(1)
            ->andReturn([new FormMock(3, 4, 'res.partner')]);
        $this->database_handler->shouldReceive('get_connection_from_database')->with(4)
            ->andReturn(new ConnectionMock('username', 'api_key', 'odoo', 'http://127.0.0.1'));
        $this->database_handler->shouldReceive('get_field_mappings_from_database')->with(3)
            ->andReturn(
                [
                    new FieldMappingMock('your-name', '', 'name', 0),
                    new FieldMappingMock('your-email', '', 'email', 0),
                    new FieldMappingMock('', 'webform', 'source', 0),
                    new FieldMappingMock('multi', '', 'multiple', 0),
                    new FieldMappingMock('your-tags', '', 'category_id', 1),
                    new FieldMappingMock('test-data', '', 'test_id', 1)
                ]
            );
        $this->database_handler->expects('insert_error')->never();
        $this->odoo_conn_contact_form_7_hook->shouldReceive('create_odoo_connection')
            ->with('username', 'decrypted_api_key', 'odoo', 'http://127.0.0.1')
            ->andReturn($this->odoo_connector);
        $this->odoo_connector->expects('create_object')->with(
            'res.partner', [
                new OdooConnXMLRPCStringField('name', 'jack'),
                new OdooConnXMLRPCStringField('email', 'email@email.com'),
                new OdooConnXMLRPCStringField('source', 'webform'),
                new OdooConnXMLRPCStringField('multiple', 'option1, option2'),
                new OdooConnXMLRPCBaseX2ManyField('category_id', [1, 2, 3]),
                new OdooConnXMLRPCStringX2ManyField('test_id', '4,5,6')
            ]
        );
        $this->wpcf7_submission->shouldReceive('get_posted_data')->with()->andReturn(
            [
                'your-name' => 'jack',
                'your-email' => 'email@email.com',
                'multi' => [
                    'option1',
                    'option2'
                ],
                'your-tags' => [
                    1, 2, 3
                ],
                'test-data' => '4,5,6'
            ]
        );
        $this->odoo_connector->shouldReceive('create_object');

        $response = $this->odoo_conn_contact_form_7_hook->send_odoo_data('wpcf');

        $this->assertEquals('wpcf', $response);
    }

    public function test_no_fields_to_send()
    {
        $this->database_handler->shouldReceive('get_forms_from_database')->with(1)
            ->andReturn([new FormMock(3, 4, 'res.partner')]);
        $this->database_handler->shouldReceive('get_connection_from_database')->with(4)
            ->andReturn(new ConnectionMock('username', 'api_key', 'odoo', 'http://127.0.0.1'));
        $this->database_handler->shouldReceive('get_field_mappings_from_database')->with(3)
            ->andReturn([]);
        $this->database_handler->expects('insert_error')->never();
        $this->odoo_conn_contact_form_7_hook->shouldReceive('create_odoo_connection')
            ->never();
        $this->odoo_connector->shouldReceive('create_object')->never();
        $this->wpcf7_submission->shouldReceive('get_posted_data')->once();
        $this->getFunctionMock('\\odoo_conn\\admin\\php\\cf7hook', 'error_log')
            ->expects($this->once())
            ->with('Not sending data as there isn\'t any form field mappings.');

        $response = $this->odoo_conn_contact_form_7_hook->send_odoo_data('wpcf');

        $this->assertEquals('wpcf', $response);
    }

    public function test_error_reported()
    {
        $this->database_handler->shouldReceive('get_forms_from_database')->with(1)
            ->andReturn([new FormMock(3, 4, 'res.partner')]);
        $this->database_handler->shouldReceive('get_connection_from_database')->with(4)
            ->andReturn(new ConnectionMock('username', 'api_key', 'odoo', 'http://127.0.0.1'));
        $this->database_handler->shouldReceive('get_field_mappings_from_database')->with(3)
            ->andReturn(
                [
                    new FieldMappingMock('your-name', '', 'name', 0),
                    new FieldMappingMock('your-email', '', 'email', 0),
                    new FieldMappingMock('', 'webform', 'source', 0),
                    new FieldMappingMock('multi', '', 'multiple', 0)
                ]
            );
        $this->database_handler->shouldReceive('insert_error')
            ->with(1, 'New error to be logged');
        $this->odoo_conn_contact_form_7_hook->shouldReceive('create_odoo_connection')
            ->with('username', 'decrypted_api_key', 'odoo', 'http://127.0.0.1')
            ->andReturn($this->odoo_connector);
        $this->odoo_connector->expects('create_object')
            ->with('res.partner', [
                new OdooConnXMLRPCStringField('name', 'jack'),
                new OdooConnXMLRPCStringField('email', 'email@email.com'),
                new OdooConnXMLRPCStringField('source', 'webform'),
                new OdooConnXMLRPCStringField('multiple', 'option1, option2')
            ])->andThrow(new OdooConnException(
                    'New error to be logged', 5
                )
            );
        $this->wpcf7_submission->shouldReceive('get_posted_data')->with()->andReturn(
            array(
                'your-name' => 'jack',
                'your-email' => 'email@email.com',
                'multi' => array(
                    'option1',
                    'option2'
                )
            )
        );

        $response = $this->odoo_conn_contact_form_7_hook->send_odoo_data('wpcf');

        $this->assertEquals('wpcf', $response);
    }

}

?>
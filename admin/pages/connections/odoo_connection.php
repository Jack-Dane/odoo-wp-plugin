<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_connections.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooConnection;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooConnection;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooConnection;
use odoo_conn\encryption\OdooConnEncryptionFileHandler;
use odoo_conn\encryption\OdooConnEncryptionHandler;


class OdooConnOdooConnectionListTable extends OdooConnCustomTableDisplay
{

    function get_columns()
    {
        return array(
            "cb" => '<input type="checkbox" />',
            "name" => "Name",
            "username" => "Username",
            "url" => "URL",
            "database_name" => "Database Name"
        );
    }

}


class OdooConnOdooConnectionRouter extends OdooConnPageRouterCreate {

    public function __construct($menu_slug)
    {
        parent::__construct($menu_slug);
    }

    protected function display_input_form()
    {
        include("odoo_connection_input_form.php");
    }

    protected function create_new_record()
    {
        $odoo_conn_file_handler = new OdooConnEncryptionFileHandler();
        $odoo_conn_encryption_handler = new OdooConnEncryptionHandler($odoo_conn_file_handler);
        $post_odoo_connection = new OdooConnPostOdooConnection($odoo_conn_encryption_handler);
        $post_odoo_connection->request($_REQUEST);
    }

    protected function create_table_display()
    {
        $odoo_connection_get_backend = new OdooConnGetOdooConnection(ARRAY_A);
        $odoo_connection_delete_backend = new OdooConnDeleteOdooConnection();

        return new OdooConnOdooConnectionListTable(
            $odoo_connection_get_backend, $odoo_connection_delete_backend
        );
    }
}


function odoo_conn_odoo_connection_page()
{
    $router = new OdooConnOdooConnectionRouter("odoo-connection");
    $router->request();
}

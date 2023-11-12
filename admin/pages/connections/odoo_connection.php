<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_connections.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooConnectionSingle;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooConnection;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooConnection;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooConnection;
use odoo_conn\admin\api\endpoints\OdooConnPutOdooConnection;
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


class OdooConnOdooConnectionRouter extends OdooConnPageRouterCreate
{

    private OdooConnGetOdooConnection $get_backend;
    private OdooConnDeleteOdooConnection $delete_backend;

    public function __construct($menu_slug)
    {
        $this->get_backend = new OdooConnGetOdooConnection(ARRAY_A);
        $this->delete_backend = new OdooConnDeleteOdooConnection();

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
        return new OdooConnOdooConnectionListTable(
            $this->get_backend, $this->delete_backend
        );
    }

    protected function display_edit_form($id)
    {
        $odoo_conn_get = new OdooConnGetOdooConnectionSingle($id);
        $odoo_conn_data = $odoo_conn_get->request([]);

        include("odoo_connection_input_form.php");
    }

    protected function update_record()
    {
        $id = $_REQUEST["id"];
        $odoo_conn_put = new OdooConnPutOdooConnection($id);
        $odoo_conn_put->request($_REQUEST);
    }

    protected function delete($id)
    {
        $this->delete_backend->request(["id" => $id]);
    }
}


function odoo_conn_odoo_connection_page()
{
    $router = new OdooConnOdooConnectionRouter("odoo-connection");
    $router->request();
}

<?php

namespace odoo_conn\admin\pages\connections;

use odoo_conn\admin\database_connection\OdooConnGetOdooConnectionSingle;
use odoo_conn\admin\database_connection\OdooConnGetOdooConnection;
use odoo_conn\admin\database_connection\OdooConnDeleteOdooConnection;
use odoo_conn\admin\database_connection\OdooConnPostOdooConnection;
use odoo_conn\admin\database_connection\OdooConnPutOdooConnection;
use odoo_conn\encryption\OdooConnEncryptionFileHandler;
use odoo_conn\encryption\OdooConnEncryptionHandler;
use odoo_conn\admin\pages\OdooConnPageRouterCreate;
use odoo_conn\admin\table_display\OdooConnCustomTableEditableDisplay;

use function odoo_conn\admin\database_connection\odoo_conn_test_odoo_connection;


class OdooConnOdooConnectionListTableEditable extends OdooConnCustomTableEditableDisplay
{

    protected function row_action_buttons($item)
    {
        $base_url = wp_nonce_url(get_admin_url(null, "admin.php"));
        $test_url = add_query_arg([
            "page" => $_REQUEST["page"],
            "id" => $item["id"],
            "page_action" => "test_connection"
        ], $base_url);

        return array_merge(
            parent::row_action_buttons($item),
            [
                "test" => "<a href='$test_url'>Test Connection</a>"
            ]
        );
    }

    public function column_name($item)
    {
        return $item["name"] . " " . $this->row_actions($this->row_action_buttons($item));
    }

    public function get_columns()
    {
        return [
            "cb" => '<input type="checkbox" />',
            "name" => "Name",
            "username" => "Username",
            "url" => "URL",
            "database_name" => "Database Name"
        ];
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

    protected function handle_route($action)
    {
        parent::handle_route($action);

        if ($action == "test_connection") {
            $this->test_connection($_REQUEST["id"]);
        }
    }

    private function test_connection($id)
    {
		$test_result = odoo_conn_test_odoo_connection(
			["id" => $id]
		);
		$this->display_test_connection_result($test_result);
    }

	protected function display_test_connection_result($test_result)
	{
		// $test_result variable is used in the test_connection_result.php file.
		wp_enqueue_style("test-connection-result", plugins_url("test_connection_result.css", __FILE__));
		require_once "test_connection_result.php";
	}

    protected function display_input_form()
    {
        require_once "odoo_connection_input_form.php";
    }

    protected function create_new_record()
    {
        $odoo_conn_file_handler = new OdooConnEncryptionFileHandler();
        $odoo_conn_encryption_handler = new OdooConnEncryptionHandler($odoo_conn_file_handler);
        $post_odoo_connection = new OdooConnPostOdooConnection($odoo_conn_encryption_handler);
        $post_odoo_connection->request($_REQUEST);
    }

    protected function get_table_display()
    {
        return new OdooConnOdooConnectionListTableEditable(
            $this->get_backend, $this->delete_backend
        );
    }

    protected function display_edit_form($id)
    {
        $odoo_conn_get = new OdooConnGetOdooConnectionSingle($id);
        $odoo_conn_data = $odoo_conn_get->request([]);

        require_once "odoo_connection_input_form.php";
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

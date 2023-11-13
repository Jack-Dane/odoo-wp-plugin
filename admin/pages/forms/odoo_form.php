<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_forms.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooForm;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooForm;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooForm;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooFormSingle;
use odoo_conn\admin\api\endpoints\OdooConnPutOdooForm;


class OdooConnOdooFormListTableEditable extends OdooConnCustomTableEditableDisplay
{

    public function column_name($item)
    {
        return $item["name"] . " " . $this->row_actions($this->row_action_buttons($item));
    }

    public function get_columns()
    {
        return array(
            "cb" => "<input type='checkbox' />",
            "name" => "Name",
            "odoo_connection_name" => "Odoo Connection",
            "odoo_model" => "Odoo Model",
            "contact_7_title" => "Contact 7 Form"
        );
    }

}


class OdooConnOdooFormRouter extends OdooConnPageRouterCreate
{

    private OdooConnGetOdooForm $get_backend;
    private OdooConnDeleteOdooForm $delete_backend;

    public function __construct($menu_slug)
    {
        $this->get_backend = new OdooConnGetOdooForm(ARRAY_A);
        $this->delete_backend = new OdooConnDeleteOdooForm();

        parent::__construct($menu_slug);
    }

    private function load_form_scripts()
    {
        wp_register_script(
            "odoo-form", plugins_url("odoo_form.js", __FILE__), array("jquery"), "1.0.0", true
        );
        wp_enqueue_script("odoo-form");
    }

    protected function display_input_form()
    {
        $this->load_form_scripts();
        include("odoo_form_input_form.php");
    }

    protected function create_new_record()
    {
        $post_odoo_connection = new OdooConnPostOdooForm();
        $post_odoo_connection->request($_REQUEST);
    }

    protected function create_table_display()
    {
        return new OdooConnOdooFormListTableEditable(
            $this->get_backend, $this->delete_backend
        );
    }

    protected function display_edit_form($id)
    {
        $this->load_form_scripts();

        $odoo_conn_get = new OdooConnGetOdooFormSingle($id);
        $odoo_conn_data = $odoo_conn_get->request([]);
        
        include("odoo_form_input_form.php");
    }

    protected function update_record()
    {
        $id = $_REQUEST["id"];
        $odoo_conn_put = new OdooConnPutOdooForm($id);
        $odoo_conn_put->request($_REQUEST);
    }

    protected function delete($id)
    {
        $this->delete_backend->request(["id" => $id]);
    }
}


function odoo_conn_odoo_form_page()
{
    $odoo_conn_odoo_form_router = new OdooConnOdooFormRouter("odoo-form");
    $odoo_conn_odoo_form_router->request();
}

?>
<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_form_mappings.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooFormMappings;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooFormMappings;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooFormMappings;
use odoo_conn\admin\api\endpoints\OdooConnGetOdooFormMappingSingle;
use odoo_conn\admin\api\endpoints\OdooConnPutOdooFormMappings;


class OdooConnOdooFormMappingListTable extends OdooConnCustomTableDisplay
{

    public function column_odoo_form_name($item)
    {
        return $item["odoo_form_name"] . " " . $this->row_actions($this->row_action_buttons($item));
    }

    public function get_columns()
    {
        return array(
            "cb" => "<input type='checkbox' />",
            "odoo_form_name" => "Odoo Form",
            "cf7_field_name" => "CF7 Field Name",
            "odoo_field_name" => "Odoo Field Name",
            "constant_value" => "Constant Value"
        );
    }

}


class OdooConnOdooFormMappingRouter extends OdooConnPageRouterCreate
{

    private OdooConnGetOdooFormMappings $get_backend;
    private OdooConnDeleteOdooFormMappings $delete_backend;

    public function __construct($menu_slug)
    {
        $this->get_backend = new OdooConnGetOdooFormMappings(ARRAY_A);
        $this->delete_backend = new OdooConnDeleteOdooFormMappings();

        parent::__construct($menu_slug);
    }

    protected function create_table_display()
    {
        return new OdooConnOdooFormMappingListTable(
            $this->get_backend, $this->delete_backend
        );
    }

    protected function create_new_record()
    {
        if (isset($_REQUEST["value_type"])) {
            $_REQUEST["cf7_field_name"] = "";
        } else {
            $_REQUEST["constant_value"] = "";
        }

        $odoo_form_mapping = new OdooConnPostOdooFormMappings();
        $odoo_form_mapping->request($_REQUEST);
    }

    protected function display_input_form()
    {
        include("odoo_form_mapping_input_form.php");
    }

    protected function display_edit_form($id)
    {
        $odoo_conn_single = new OdooConnGetOdooFormMappingSingle($id);
        $odoo_conn_data = $odoo_conn_single->request([]);

        include("odoo_form_mapping_input_form.php");
    }

    protected function update_record()
    {
        $id = $_REQUEST["id"];
        $odoo_conn_update = new OdooConnPutOdooFormMappings($id);
        $odoo_conn_update->request($_REQUEST);
    }

    protected function delete($id)
    {
        $this->delete_backend->request(["id" => $id]);
    }
}


function odoo_conn_odoo_form_mapping_page()
{
    wp_register_script(
        "odoo-form-mapping", plugins_url("odoo_form_mapping.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_enqueue_script("odoo-form-mapping");

    $form_mapping_router = new OdooConnOdooFormMappingRouter("odoo-form-mapping");
    $form_mapping_router->request();
}

?>
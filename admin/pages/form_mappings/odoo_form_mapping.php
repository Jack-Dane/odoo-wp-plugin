<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_form_mappings.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooFormMappings;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooFormMappings;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooFormMappings;


class OdooConnOdooFormMappingListTable extends OdooConnCustomTableDisplay
{

    function get_columns()
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


class OdooConnOdooFormMappingRouter extends OdooConnPageRouterCreate {

    protected function create_table_display()
    {
        $odoo_form_mapping_get_backend = new OdooConnGetOdooFormMappings(ARRAY_A);
        $odoo_form_mapping_delete_backend = new OdooConnDeleteOdooFormMappings();
        return new OdooConnOdooFormMappingListTable(
            $odoo_form_mapping_get_backend, $odoo_form_mapping_delete_backend
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
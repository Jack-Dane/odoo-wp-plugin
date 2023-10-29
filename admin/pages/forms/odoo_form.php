<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_forms.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooForm;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooForm;
use odoo_conn\admin\api\endpoints\OdooConnPostOdooForm;


class OdooConnOdooFormListTable extends OdooConnCustomTableDisplay
{

    function get_columns()
    {
        return array(
            "cb" => "<input type='checkbox' />",
            "odoo_connection_name" => "Odoo Connection",
            "odoo_model" => "Odoo Model",
            "name" => "Name",
            "contact_7_title" => "Contact 7 Form"
        );
    }

}


class OdooConnOdooFormRouter extends OdooConnPageRouterCreate
{

    public function __construct($menu_slug)
    {
        parent::__construct($menu_slug);
    }

    protected function display_input_form()
    {
        include("odoo_form_input_form.php");
    }

    protected function create_new_record()
    {
        $post_odoo_connection = new OdooConnPostOdooForm();
        $post_odoo_connection->request($_REQUEST);
    }

    protected function create_table_display()
    {
        $odoo_form_get_backend = new OdooConnGetOdooForm(ARRAY_A);
        $odoo_form_delete_backend = new OdooConnDeleteOdooForm();
        return new OdooConnOdooFormListTable(
            $odoo_form_get_backend, $odoo_form_delete_backend
        );
    }
}


function odoo_conn_odoo_form_page()
{
    wp_register_script(
        "odoo-form", plugins_url("odoo_form.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_localize_script("table-display", "wpApiSettings", array(
        "root" => esc_url_raw(rest_url()), "nonce" => wp_create_nonce("wp_rest")
    ));
    wp_enqueue_script("odoo-form");

    $odoo_conn_odoo_form_router = new OdooConnOdooFormRouter("odoo-form");
    $odoo_conn_odoo_form_router->request();
}

?>
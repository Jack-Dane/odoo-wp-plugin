<?php

require_once(__DIR__ . "/../../api/endpoints/odoo_errors.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooErrors;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooErrors;


class OdooConnOdooErrorsListTable extends OdooConnCustomTableDisplay
{

    function get_columns()
    {
        return array(
            "cb" => "<input type='checkbox' />",
            "contact_7_title" => "Contact 7 Form",
            "time_occurred" => "Time of Error",
            "error_message" => "Error Message"
        );
    }

}


class OdooConnOdooErrorRouter extends OdooConnPageRouter {

    protected function create_table_display()
    {
        $odoo_errors_get_backend = new OdooConnGetOdooErrors(ARRAY_A);
        $odoo_errors_delete_backend = new OdooConnDeleteOdooErrors();
        return new OdooConnOdooErrorsListTable($odoo_errors_get_backend, $odoo_errors_delete_backend);
    }

    protected function delete($id)
    {
        // TODO: Implement delete() method.
    }
}


function odoo_conn_odoo_errors_page()
{
    $odoo_conn_errors_router = new OdooConnOdooErrorRouter();
    $odoo_conn_errors_router->request();
}

?>

<?php

require_once(__DIR__ . "/../api/endpoints/odoo_errors.php");

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


function odoo_conn_odoo_errors_page()
{
    wp_register_script(
        "odoo-errors", plugins_url("odoo_errors.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_localize_script("table-display", "wpApiSettings", array(
        "root" => esc_url_raw(rest_url()), "nonce" => wp_create_nonce("wp_rest")
    ));
    wp_enqueue_script("odoo-errors");

    ?>
    <div class="wrap">
        <h1>Odoo Connections</h1>
    </div>
    <?php

    echo "<div class='wrap'>";
    $odoo_errors_get_backend = new OdooConnGetOdooErrors(ARRAY_A);
    $odoo_errors_delete_backend = new OdooConnDeleteOdooErrors();
    $table_display = new OdooConnOdooErrorsListTable($odoo_errors_get_backend, $odoo_errors_delete_backend);
    $table_display->check_bulk_action();

    echo "<form method='post'>";
    $table_display->prepare_items();
    $table_display->display();
    echo "</form></div>";
}

?>

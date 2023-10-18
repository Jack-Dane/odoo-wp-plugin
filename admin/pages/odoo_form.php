<?php

require_once(__DIR__ . "/../api/endpoints/odoo_forms.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooForm;

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


function odoo_conn_odoo_form_page()
{
    wp_register_script(
        "odoo-form", plugins_url("odoo_form.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_localize_script("table-display", "wpApiSettings", array(
        "root" => esc_url_raw(rest_url()), "nonce" => wp_create_nonce("wp_rest")
    ));
    wp_enqueue_script("odoo-form");
    ?>
    <div class="wrap">
        <h1>Odoo Forms</h1>

        <a href="#" id="create-data" class="create-database-record button-primary" value="Create a new Form">Create a
            new Form</a>
        <form method="POST" onsubmit="return submitOdooForm();" id="form-data" class="submit-database"
              style="display: none;">
            <label for="odoo_connection_id">Odoo Connection</label>
            <select id="odoo_connection_id" name="odoo_connection_id"/><br/>
            <input type="text" name="odoo_model" id="odoo_model" placeholder="Odoo Model"/><br/>
            <input type="text" name="name" id="name" placeholder="Name"/><br/>
            <label for="contact_7_id">Contact 7 Form</label>
            <select name="contact_7_id" id="contact_7_id"/><br/>
            <input type="Submit" name="submit" class="button-primary"/>
        </form>

    </div>
    <?php

    echo "<div class='wrap'>";
    $odoo_connection = new OdooConnGetOdooForm(ARRAY_A);
    $table_display = new OdooConnOdooFormListTable($odoo_connection);

    echo "<form method='post'>";
    $table_display->prepare_items();
    $table_display->display();
    echo "</div>";
}

?>
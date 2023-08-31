<?php

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
        <h1>Odoo Submit Errors</h1>
        <table class="database-table"></table>
        <div id="pageination-display"></div>
    </div>
    <?php
}

?>

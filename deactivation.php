<?php

function odoo_conn_deactivation_function()
{
    global $wpdb, $table_prefix;

    $tables_to_drop = array(
        "odoo_conn_errors",
        "odoo_conn_form_mapping",
        "odoo_conn_form",
        "odoo_conn_connection"
    );

    foreach ($tables_to_drop as $table) {
        $table_name = $table_prefix . $table;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }

    update_option("odoo_conn_db_version", 0);
}

?>
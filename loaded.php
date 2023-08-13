<?php

function odoo_conn_update_db_check()
{
    require_once(ABSPATH . "wp-admin/includes/upgrade.php");
    $odoo_conn_db_version = get_option("odoo_conn_db_version", 1);

    if ($odoo_conn_db_version <= 1) {
        odoo_conn_create_odoo_errors();
        update_option("odoo_conn_db_version", 2);
    }
}

function odoo_conn_create_odoo_errors()
{
    global $wpdb, $table_prefix;

    $table_name = $table_prefix . "odoo_conn_errors";
    $odoo_form_table = $table_prefix . "odoo_conn_form";
    $odoo_conn_table = $table_prefix . "odoo_conn_connection";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
    CREATE TABLE `$table_name` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
        `odoo_form_id` BIGINT(20) UNSIGNED NOT NULL, 
        `odoo_connection_id` BIGINT(20) UNSIGNED NOT NULL, 
        `error_message` VARCHAR(2000) NOT NULL, 
        PRIMARY KEY(id), 
        CONSTRAINT fk_odoo_conn_errors_odoo_form FOREIGN KEY (odoo_form_id) REFERENCES $odoo_form_table(id) ON DELETE CASCADE, 
        CONSTRAINT fk_odoo_conn_errors_odoo_connection FOREIGN KEY (odoo_connection_id) REFERENCES $odoo_conn_table(id) ON DELETE CASCADE
    ) $charset_collate;
    ";

    dbDelta($sql);
}

add_action("plugins_loaded", "odoo_conn_update_db_check");

?>

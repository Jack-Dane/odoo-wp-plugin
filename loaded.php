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
    $contact_7_form_table = $table_prefix . "posts";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
    CREATE TABLE `$table_name` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
        `contact_7_id` BIGINT(20) UNSIGNED NOT NULL, 
        `time_occurred` DATETIME NOT NULL,
        `error_message` VARCHAR(2000) NOT NULL, 
        PRIMARY KEY(id), 
        FOREIGN KEY (contact_7_id) REFERENCES $contact_7_form_table(ID) ON DELETE CASCADE
    ) $charset_collate;
    ";

    dbDelta($sql);
}

add_action("plugins_loaded", "odoo_conn_update_db_check");

?>

<?php

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

global $wpdb, $table_prefix;

$tables_to_drop = [
    "odoo_conn_errors",
    "odoo_conn_form_mapping",
    "odoo_conn_form",
    "odoo_conn_connection"
];

foreach ($tables_to_drop as $table) {
    $table_name = $table_prefix . $table;
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

delete_option("odoo_conn_db_version");

?>
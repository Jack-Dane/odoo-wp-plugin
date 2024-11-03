<?php

function move_existing_encryption_file(): void
{
	// Original encryption file path that isn't compatible with WordPress SaaS.
	// The directory is owned by root in WordPress SaaS.
	$old_encryption_key_path = ABSPATH . "odoo_conn.key";
	$new_encryption_key_path = plugin_dir_path(__FILE__) . "odoo_conn.key";

	if (file_exists($old_encryption_key_path)) {
		rename($old_encryption_key_path, $new_encryption_key_path);
	}
}

function odoo_conn_update_db_check()
{
    require_once(ABSPATH . "wp-admin/includes/upgrade.php");
    $odoo_conn_db_version = get_option("odoo_conn_db_version", 1);

    if ($odoo_conn_db_version <= 1) {
        odoo_conn_create_odoo_errors();
        update_option("odoo_conn_db_version", 2);
    }

    if ($odoo_conn_db_version <= 2) {
        odoo_conn_add_multi_table_column();
        update_option("odoo_conn_db_version", 3);
    }
}

function odoo_conn_create_odoo_errors()
{
    global $wpdb, $table_prefix;

    $table_name = $table_prefix . "odoo_conn_errors";
    $contact_7_form_table = $wpdb->posts;
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

function odoo_conn_add_multi_table_column() {
    global $wpdb, $table_prefix;

    $table_name = $table_prefix . "odoo_conn_form_mapping";

    $sql = "ALTER TABLE `$table_name` ADD COLUMN `x_2_many` TINYINT(1) NOT NULL;";

    $wpdb->query($sql);
}

add_action("plugins_loaded", "move_existing_encryption_file");
add_action("plugins_loaded", "odoo_conn_update_db_check");

?>

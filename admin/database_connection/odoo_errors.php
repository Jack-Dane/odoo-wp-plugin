<?php

namespace odoo_conn\admin\database_connection;


trait OdooConnOdooErrorsTableName
{

    protected function get_table_name()
    {
        global $table_prefix;

        return $table_prefix . "odoo_conn_errors";
    }

}


trait OdooConnOdooErrorsColumns
{

    protected function get_columns()
    {
        global $wpdb, $table_prefix;

        $columns = [
            $table_prefix . "odoo_conn_errors.id",
            $table_prefix . "odoo_conn_errors.contact_7_id as 'contact_7_id'",
            $wpdb->posts . ".post_title as 'contact_7_title'",
            $table_prefix . "odoo_conn_errors.time_occurred",
            $table_prefix . "odoo_conn_errors.error_message",
        ];

        return implode(", ", $columns);
    }

}


class OdooConnGetOdooErrors extends OdooConnGetBaseDatabaseConnection
{

    use OdooConnOdooErrorsTableName;
    use OdooConnOdooErrorsColumns;

    protected function foreign_keys()
    {
        global $wpdb;

        return [
            "contact_7_id" => [
                "table_name" => $wpdb->posts,
                "column_name" => "ID"
            ]
        ];
    }

}


class OdooConnDeleteOdooErrors extends OdooConnDeleteBaseDatabaseConnection
{

    use OdooConnOdooErrorsTableName;
}

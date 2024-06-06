<?php

namespace odoo_conn\admin\database_connection;


trait OdooConnOdooFormTableName
{

	protected function get_table_name()
	{
		global $table_prefix;

		return $table_prefix . "odoo_conn_form";
	}

}


trait OdooConnOdooFormColumns
{

	protected function get_columns()
	{
		global $wpdb, $table_prefix;

		$columns = [
			$table_prefix . "odoo_conn_form.id",
			$table_prefix . "odoo_conn_form.odoo_connection_id",
			$table_prefix . "odoo_conn_connection.name as 'odoo_connection_name'",
			$table_prefix . "odoo_conn_form.odoo_model",
			$table_prefix . "odoo_conn_form.name",
			$table_prefix . "odoo_conn_form.contact_7_id as 'contact_7_id'",
			$wpdb->posts . ".post_title as 'contact_7_title'"
		];

		return implode(", ", $columns);
	}

}


class OdooConnGetOdooForm extends OdooConnGetBaseDatabaseConnection
{

	use OdooConnOdooFormTableName;
	use OdooConnOdooFormColumns;

	protected function foreign_keys()
	{
		global $wpdb, $table_prefix;

		return [
			"odoo_connection_id" => [
				"table_name" => $table_prefix . "odoo_conn_connection",
				"column_name" => "id"
			],
			"contact_7_id" => [
				"table_name" => $wpdb->posts,
				"column_name" => "ID"
			]
		];
	}

}


class OdooConnGetOdooFormSingle extends OdooConnGetExtendedDatabaseConnection
{

	use OdooConnOdooFormTableName;

	public function request($data)
	{
		$connections = parent::request($data);
		return !$connections ? null : $connections[0];
	}

	protected function where_query()
	{
		return "id=%d";
	}
}


class OdooConnPostOdooForm extends OdooConnPostBaseDatabaseConnection
{

	use OdooConnOdooFormTableName;

	protected function parse_data($data)
	{
		return array(
			"odoo_connection_id" => sanitize_text_field($data["odoo_connection_id"]),
			"odoo_model" => sanitize_text_field($data["odoo_model"]),
			"name" => sanitize_text_field($data["name"]),
			"contact_7_id" => sanitize_text_field($data["contact_7_id"])
		);
	}

	protected function insert_data_types()
	{
		return array("%d", "%s", "%s", "%d");
	}

}


class OdooConnPutOdooForm extends OdooConnPutBaseDatabaseConnection
{

	use OdooConnOdooFormTableName;

	protected function update_data($data)
	{
		return array(
			"odoo_connection_id" => sanitize_text_field($data["odoo_connection_id"]),
			"name" => sanitize_text_field($data["name"]),
			"contact_7_id" => sanitize_text_field($data["contact_7_id"]),
			"odoo_model" => sanitize_text_field($data["odoo_model"]),
		);
	}

}


class OdooConnDeleteOdooForm extends OdooConnDeleteBaseDatabaseConnection
{

	use OdooConnOdooFormTableName;
}

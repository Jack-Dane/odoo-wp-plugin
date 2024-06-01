<?php

namespace odoo_conn\admin\database_connection;


class OdooConnGetContact7Form extends OdooConnGetExtendedSchema
{

	public function __construct()
	{
		parent::__construct("wpcf7_contact_form");
	}

	protected function get_public_key()
	{
		return "ID";
	}

	protected function get_columns()
	{
		return "ID, post_title";
	}

	protected function get_table_name()
	{
		global $wpdb;

		return $wpdb->posts;
	}

	protected function where_query()
	{
		return "post_type=%s";
	}
}

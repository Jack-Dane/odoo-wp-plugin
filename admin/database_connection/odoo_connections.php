<?php

namespace odoo_conn\admin\database_connection;

use odoo_conn\encryption\OdooConnEncryptionFileHandler;
use odoo_conn\encryption\OdooConnEncryptionHandler;
use odoo_conn\admin\odoo_connector\OdooConnException;
use odoo_conn\admin\odoo_connector\OdooConnOdooConnector;
use WP_Error;


class OdooConnDeleteOdooConnection extends OdooConnDeleteBaseDatabaseConnection
{

	use OdooConnOdooConnectionTableName;
}

trait OdooConnOdooConnectionTableName
{

	protected function get_table_name()
	{
		global $table_prefix;

		return $table_prefix . "odoo_conn_connection";
	}

}


trait OdooConnOdooConnectionColumns
{

	protected function get_columns()
	{
		$columns = [
			"id",
			"name",
			"username",
			"url",
			"database_name"
		];

		return implode(", ", $columns);
	}

}


class OdooConnGetOdooConnection extends OdooConnGetBaseDatabaseConnection
{

	use OdooConnOdooConnectionTableName;
	use OdooConnOdooConnectionColumns;
}

class OdooConnGetOdooConnectionSingle extends OdooConnGetExtendedDatabaseConnection
{

	use OdooConnOdooConnectionTableName;

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


class OdooConnTestOdooConnection extends OdooConnGetOdooConnectionSingle
{
	public function request($data)
	{
		$connection = parent::request($data);
		if (!$connection) {
			return new WP_Error(
				"no_connection",
				"No connection for that Id",
				array("status" => 404)
			);
		}

		return $connection;
	}

	public function test_connection($odoo_connector)
	{
		try {
			$success = $odoo_connector->test_connection();
		} catch (OdooConnException $e) {
			return array(
				"success" => false,
				"error_string" => $e->getMessage(),
				"error_code" => $e->getCode()
			);
		}
		return array(
			"success" => $success
		);
	}
}


class OdooConnPostOdooConnection extends OdooConnPostBaseDatabaseConnection
{

	use OdooConnOdooConnectionTableName;
	use OdooConnOdooConnectionColumns;

	public function __construct($encryption_handler)
	{
		$this->encryption_handler = $encryption_handler;
	}

	protected function parse_data($data)
	{
		$api_key = sanitize_text_field($data["api_key"]);
		$encrypted_api_key = $this->encryption_handler->encrypt($api_key);

		return array(
			"name" => sanitize_text_field($data["name"]),
			"username" => sanitize_text_field($data["username"]),
			"api_key" => $encrypted_api_key,
			"url" => sanitize_url($data["url"]),
			"database_name" => sanitize_text_field($data["database_name"]),
		);
	}

	protected function insert_data_types()
	{
		return array("%s", "%s", "%s", "%s", "%s");
	}

}


class OdooConnPutOdooConnection extends OdooConnPutBaseDatabaseConnection
{

	use OdooConnOdooConnectionTableName;
	use OdooConnOdooConnectionColumns;

	protected function update_data($data)
	{
		return array(
			"name" => sanitize_text_field($data["name"]),
			"username" => sanitize_text_field($data["username"]),
			"url" => sanitize_url($data["url"]),
			"database_name" => sanitize_text_field($data["database_name"])
		);
	}

}

function odoo_conn_test_odoo_connection($data)
{
	$id = $data["id"];
	$connection_tester = new OdooConnTestOdooConnection($id);
	$connection = $connection_tester->request($data);

	$encryption_file_handler = new OdooConnEncryptionFileHandler();
	$encryption_handler = new OdooConnEncryptionHandler($encryption_file_handler);
	$decrypted_api_key = $encryption_handler->decrypt($connection->api_key);

	$odoo_connector = new OdooConnOdooConnector(
		$connection->username,
		$decrypted_api_key,
		$connection->database_name,
		$connection->url
	);

	return $connection_tester->test_connection($odoo_connector);
}

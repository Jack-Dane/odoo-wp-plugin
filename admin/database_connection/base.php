<?php

namespace odoo_conn\admin\database_connection;


abstract class OdooConnBaseDatabaseConnection
{

	abstract public function request($data);

	abstract protected function get_table_name();

}


abstract class OdooConnPostPutBaseDatabaseConnection extends OdooConnBaseDatabaseConnection
{

	protected function get_columns()
	{
		return "*";
	}

	protected function get_last_edited_record($table_name, $id)
	{
		global $wpdb;

		$query = "SELECT {$this->get_columns()} FROM {$table_name} WHERE id=%d";
		$inserted_row = $wpdb->get_results($wpdb->prepare($query, array($id)));
		return $inserted_row;
	}

}


abstract class OdooConnPostBaseDatabaseConnection extends OdooConnPostPutBaseDatabaseConnection
{

	abstract protected function parse_data($data);

	abstract protected function insert_data_types();

	public function request($data)
	{
		global $wpdb;

		$table_name = $this->get_table_name();
		$wpdb->insert(
			$table_name,
			$this->parse_data($data),
			$this->insert_data_types()
		);

		// return the row that was created
		return $this->get_last_edited_record($table_name, $wpdb->insert_id);
	}

}


abstract class OdooConnPutBaseDatabaseConnection extends OdooConnPostPutBaseDatabaseConnection
{

	public function __construct($id)
	{
		$this->id = $id;
	}

	abstract protected function update_data($data);

	public function request($data)
	{
		global $wpdb;

		$table_name = $this->get_table_name();
		$wpdb->update(
			$table_name,
			$this->update_data($data),
			array("id" => $this->id)
		);

		return $this->get_last_edited_record($table_name, $this->id);
	}

}


abstract class OdooConnGetBaseDatabaseConnection extends OdooConnBaseDatabaseConnection
{

	protected string $output_type = OBJECT;

	public function __construct($output = OBJECT)
	{
		$this->output_type = $output;
	}

	protected function get_public_key()
	{
		return "id";
	}

	protected function foreign_keys()
	{
		return [];
	}

	private function join_query($query)
	{
		foreach ($this->foreign_keys() as $foreign_key_column => $foreign_key_data) {
			$query .= " JOIN " . $foreign_key_data["table_name"];
			$query .= " ON " . $this->get_table_name() . "." . $foreign_key_column;
			$query .= "=" . $foreign_key_data["table_name"] . "." . $foreign_key_data["column_name"];
		}

		return $query;
	}

	protected function get_columns()
	{
		return "*";
	}

	protected function prepare_query($query, $data, $argument_array)
	{
		global $wpdb;

		// show newly created records first
		$query .= " ORDER BY " . $this->get_table_name() . "."
			. $this->get_public_key() . " DESC";

		if (isset($data["limit"])) {
			$query .= " LIMIT %d";
			array_push($argument_array, $data["limit"]);

			// offset can only be used when a limit has already been added
			if (isset($data["offset"])) {
				$query .= " OFFSET %d";
				array_push($argument_array, $data["offset"]);
			}
		}

		return $wpdb->prepare($query, $argument_array);
	}

	public function request($data)
	{
		global $wpdb;

		$query = "SELECT {$this->get_columns()} FROM {$this->get_table_name()}";
		$argument_array = array();

		$joined_query = $this->join_query($query);
		$safe_query = $this->prepare_query($joined_query, $data, $argument_array);

		return $wpdb->get_results($safe_query, $this->output_type);
	}

	public function count_records()
	{
		global $wpdb;

		$query = "SELECT COUNT(*) as 'count' FROM {$this->get_table_name()}";

		return $wpdb->get_results($query)[0]->count;
	}

}


abstract class OdooConnGetExtendedDatabaseConnection extends OdooConnGetBaseDatabaseConnection
{

	public function __construct($where_value)
	{
		$this->where_value = $where_value;
	}

	protected abstract function where_query();

	protected function prepare_query($query, $data, $argument_array)
	{
		if ($this->where_value) {
			$query .= " WHERE {$this->where_query()}";
			array_push($argument_array, $this->where_value);
		}

		return parent::prepare_query($query, $data, $argument_array);
	}

}


abstract class OdooConnDeleteBaseDatabaseConnection extends OdooConnBaseDatabaseConnection
{

	public function request($data)
	{
		global $wpdb;

		$id = $data["id"];
		$wpdb->delete(
			$this->get_table_name(), array("id" => $id), array("%d")
		);

		return [
			"DELETE" => $id,
			"table" => $this->get_table_name(),
		];
	}

}

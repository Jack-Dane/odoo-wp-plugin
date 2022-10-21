<?php

abstract class BaseSchema {

	abstract public function request ($data);

	abstract protected function get_table_name ();

}


abstract class PostPutBaseSchema extends BaseSchema {

	protected function get_last_edited_record ($table_name, $id) {
		global $wpdb;

		$query = "SELECT * FROM {$table_name} WHERE id={$id}";
		$inserted_row = $wpdb->get_results($query);
		return $inserted_row;	
	}

}


abstract class PostBaseSchema extends PostPutBaseSchema {

	abstract protected function parse_data ($data);

	abstract protected function insert_data_types ();

	public function request ($data) {
		global $wpdb, $table_prefix;

		$table_name = $table_prefix . $this->get_table_name();
		$wpdb->insert(
			$table_name,
			$this->parse_data($data),
			$this->insert_data_types()
		);

		// return the row that was created
		return $this->get_last_edited_record($table_name, $wpdb->insert_id);
	}

}


abstract class PutBaseSchema extends PostPutBaseSchema {

	public function __construct ($id) {
		$this->id = $id;
	}

	abstract protected function update_data ($data);

	public function request ($data) {
		global $wpdb, $table_prefix;

		$table_name = $table_prefix . $this->get_table_name();
		$wpdb->update(
			$table_name,
			$this->update_data($data),
			array ("id" => $this->id)
		);

		return $this->get_last_edited_record($table_name, $this->id);
	}

}


abstract class GetBaseSchema extends BaseSchema {

	protected function foreign_keys () {
		return [];
	}

	private function join_query ($query) {
		global $wpdb, $table_prefix;

		foreach ($this->foreign_keys() as $foreign_key_column => $foreign_key_data) { 
			$query .= " JOIN " . $foreign_key_data["table_name"];
			$query .= " ON " . $table_prefix . $this->get_table_name() . "." . $foreign_key_column;
			$query .= "=" . $foreign_key_data["table_name"] . "." . $foreign_key_data["column_name"];
		}

		return $query;
	}

	protected function get_columns () {
		return "*";
	}

	protected function prepare_query ($query, $data, $argument_array) {
		global $wpdb;
		
		if (isset($data["limit"])) {
			$query .= " LIMIT %d";
			array_push($argument_array, $data["limit"]);

			// offset can only be used when a limit has already been added
			if (isset($data["offset"])) {
				$query .= " OFFSET %d";
				array_push($argument_array, $data["offset"]);
			}
		}

		$safe_query = $wpdb->prepare($query, $argument_array);
		return $safe_query;
	}

	public function request ($data) {
		global $wpdb, $table_prefix;

		$table_name = $table_prefix . $this->get_table_name();
		$query = "SELECT {$this->get_columns()} FROM {$table_name}";
		$argument_array = array();

		$joined_query = $this->join_query($query);
		$safe_query = $this->prepare_query($joined_query, $data, $argument_array);

		$results = $wpdb->get_results($safe_query);
		return $results;
	}

}


abstract class GetExtendedSchema extends GetBaseSchema {

	public function __construct ($where_condition) {
		$this->where_condition = $where_condition;
	}

	protected function prepare_query ($query, $data, $argument_array) {
		if ($this->where_condition) {
			$query .= " WHERE " . $this->where_condition;
		}

		return parent::prepare_query($query, $data, $argument_array);
	}

}


abstract class DeleteBaseSchema extends BaseSchema {

	public function request ($data) {
		global $wpdb, $table_prefix;

		$id = $data["id"];
		$wpdb->delete(
			$table_prefix . $this->get_table_name(), array("id" => $id), array("%d")
		);

		return [
			"DELETE" => $id,
			"table" => $this->get_table_name(),
		];
	}

}

function base_get_request_arguments () {
	return array(
		"limit" => array(
			"type" => "integer",
			"description" => esc_html__("The total number of Odoo forms returned in the API")
		),
		"offset" => array(
			"type" => "integer",
			"description" => esc_html__("The offset based on the primary key")
		),
	);
}

function base_delete_request_schema ($title) {
	return array(
		"$schema" => "https://json-schema.org/draft/2020-12/schema",
		"title" => $title,
		"type" => 'object',
		"properties" => array(
			"DELETE" => array(
				"type" => "integer",
				"description" => esc_html__("Primary key for the Odoo Connection that was deleted"),
			),
			"table" => array(
				"type" => "string",
				"description" => esc_html__("Table that the row was deleted from"),
			),
		),
	);
}

function base_delete_arguments () {
	return array(
		"id" => array(
			"type" => "integer",
			"description" => esc_html__("Primary key for an Odoo Connection"),
			"required" => true,
		),
	);
}

?>
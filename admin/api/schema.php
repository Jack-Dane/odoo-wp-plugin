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

		$safe_query = $this->prepare_query($query, $data, $argument_array);

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

?>
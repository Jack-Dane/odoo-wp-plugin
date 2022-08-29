<?php

abstract class BaseSchema {

	abstract public function request ($data);

	abstract protected function get_table_name ();

}

abstract class PostBaseSchema extends BaseSchema {

	abstract protected function parse_data ($data);

	abstract protected function insert_data_types ();

	public function request ($data) {
		global $wpdb, $table_prefix;

		$table_name = $table_prefix . $this->get_table_name();
		$wpdb->insert (
			$table_name,
			$this->parse_data($data),
			$this->insert_data_types()
		);

		// return the row that was created
		$id = $wpdb->insert_id;
		$query = "SELECT * FROM {$table_name} WHERE id={$id}";
		$inserted_row = $wpdb->get_results($query);
		return $inserted_row;
	}

}

abstract class GetBaseSchema extends BaseSchema {

	protected function prepare_query ($query, $data, $argument_array) {
		global $wpdb;
		
		if (isset($data["limit"])) {
			$query .= " LIMIT %d";
			array_push($argument_array, $data["limit"]);
		}

		$safe_query = $wpdb->prepare($query, $argument_array);
		return $safe_query;
	}

	public function request ($data) {
		global $wpdb, $table_prefix;

		$table_name = $table_prefix . $this->get_table_name();
		$query = "SELECT * FROM {$table_name}";
		$argument_array = array();

		$safe_query = $this->prepare_query($query, $data, $argument_array);

		$results = $wpdb->get_results($safe_query);
		return $results;
	}

}

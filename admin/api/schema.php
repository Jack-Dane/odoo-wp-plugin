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

	protected function common_request_arguments ($query, $data) {
		
		if (isset($data["limit"])) {
			$query .= " LIMIT {$data["limit"]}";
		}

		return $query;

	}

	public function request ($data) {
		global $wpdb, $table_prefix;

		$table_name = $table_prefix . $this->get_table_name();
		$query = "SELECT * FROM {$table_name}";

		$query = $this->common_request_arguments($query, $data);

		$results = $wpdb->get_results($query);
		return $results;
	}
}

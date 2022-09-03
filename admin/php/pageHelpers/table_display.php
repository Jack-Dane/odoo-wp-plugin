<?php

// register styles & scripts used
add_action( "admin_enqueue_scripts", "callback_for_setting_up_scripts" );
function callback_for_setting_up_scripts() {
    wp_enqueue_style( "table-style", plugins_url("/odoo-conn/admin/php/pageHelpers/table_style.css") );

    wp_register_script( "table-editor", plugins_url("/odoo-conn/admin/php/pageHelpers/table_editor.js"), array("jquery"), "1.0.0", true );
    wp_enqueue_script( "table-editor" );

    wp_register_script( 
    	"form-creator", plugins_url("/odoo-conn/admin/php/pageHelpers/form_creator_show.js"), array("jquery"), "1.0.0", true 
    );
    wp_enqueue_script( "form-creator" );
}


abstract class TableData {

	abstract protected function get_column_names ();

	abstract protected function get_table_name ();

	abstract protected function get_update_endpoint ();

	public function echo_table_data () {
		global $wpdb;

		$page = isset($_GET["p"]) ? htmlspecialchars($_GET["p"]) * 10 : 0;
		$column_names = $this->get_column_names();

		$table_name = $wpdb->prefix . $this->get_table_name();
		$rows = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY id LIMIT {$page}, 11", ARRAY_A );
		$next_page = count($rows) == 11;
		if ($next_page) {
			array_pop($rows);
		}

		echo "<table class='database-table'>";
		$this->echo_headers($column_names);
		foreach ($rows as $index => $row) {
			$this->echo_row($row, $index, $column_names);
		}
		echo "</table>";

		$this->get_page_buttons($next_page);
	}

	private function echo_headers ($headers) {
		echo "<tr>";
		echo "<th>Edit</th>";
		foreach ($headers as $header) {
			echo "<th>" . $header . "</th>";
		}
		echo "</tr>";
	}

	private function echo_row ($row, $index, $column_names) {
		$endpoint = $this->get_update_endpoint();
		echo "<tr>";
		echo "<td><a href='#' id='table-row-{$index}' data-endpoint='{$endpoint}' class='table-row'>Edit</a></td>";
		foreach ($column_names as $column_name) {
			echo "<td><span class='table-row-{$index}' data-table-field='{$column_name}'>" . $row[$column_name] . "</span></td>";
		}
		echo "</tr>";
	}

	private function get_page_buttons ($display_next) {
		$page = isset($_GET["p"]) ? htmlspecialchars($_GET["p"]) : 0;
		$wp_page = htmlspecialchars($_GET["page"]);
		$previous_page = $page - 1;
		$next_page = $page + 1;

		echo "<div class='table-buttons'>";
		if ($page > 0) {
			echo "<a id='previous-button' href='?p={$previous_page}&page={$wp_page}'>Previous</a>";
		}
		if ($display_next) {
			echo "<a id='next-button' href='?p={$next_page}&page={$wp_page}'>Next</a>";
		}
		echo "</div>";
	}
}


class ConnectionTableData extends TableData {

	protected function get_update_endpoint () {
		return "update-odoo-connection";
	}

	protected function get_column_names () {
		return ["id", "name", "username", "api_key", "url", "database_name"];
	}

	protected function get_table_name () {
		return "odoo_conn_connection";
	}

}


class FormTableData extends TableData {

	protected function get_update_endpoint () {
		return "update-odoo-form";
	}

	protected function get_column_names () {
		return ["id", "odoo_connection_id", "name", "contact_7_id"];
	}

	protected function get_table_name () {
		return "odoo_conn_form";
	}

}


class FormMappingTableData extends TableData {

	protected function get_update_endpoint () {
		return "update-odoo-form-mapping";
	}

	protected function get_column_names () {
		return ["id", "odoo_form_id", "cf7_field_name", "odoo_field_name"];
	}

	protected function get_table_name () {
		return "odoo_conn_form_mapping";
	}

}

?>
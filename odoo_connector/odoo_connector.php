<?php

namespace odoo_conn\odoo_connector\odoo_connector;

class OdooConnOdooConnector {

	function __construct ($username, $api_key, $database, $url, $ripcord) {
		$this->username = $username;
		$this->api_key = $api_key;
		$this->database = $database;
		$this->url = $url;
		$this->ripcord = $ripcord;
		$this->uid = null;
	}

	private function get_user_id () {
		$common = $this->ripcord::client($this->url . "/xmlrpc/2/common");
		$version = $common->version();
		$this->uid = $common->authenticate($this->database, $this->username, $this->api_key, $version);
	}

	public function create_object ($model, $field_values) {
		$this->get_user_id();

		$models = $this->ripcord::client($this->url . "/xmlrpc/2/object");
		return $models->execute_kw($this->database, $this->uid, $this->api_key, $model, "create", $field_values);
	}

}

?>
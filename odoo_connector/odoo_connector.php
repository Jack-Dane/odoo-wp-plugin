<?php

require_once(__DIR__ . "/ripcord/ripcord.php");

class OdooConnector {

	function __construct ($username, $api_key, $database, $url) {
		$this->username = $username;
		$this->api_key = $api_key;
		$this->database = $database;
		$this->url = $url;
		$this->uid = null;
	}

	private function get_user_id () {
		$common = ripcord::client($this->url . "/xmlrpc/2/common");
		$version = $common->version();
		$this->uid = $common->authenticate($this->database, $this->username, $this->api_key, $version);
	}

	public function createObject ($model, $field_values) {
		$this->get_user_id();

		$models = ripcord::client($this->url . "/xmlrpc/2/object");
		$models->execute_kw($this->database, $this->uid, $this->api_key, $model, 'create', $field_values);
	}

}

?>
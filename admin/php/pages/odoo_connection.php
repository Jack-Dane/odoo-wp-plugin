<?php 

function odoo_connection_page () {
	wp_register_script(
    	"odoo-connection", plugins_url("/odoo-conn/admin/php/pages/odoo_connection.js"), array("jquery"), "1.0.0", true
    );
    wp_enqueue_script( "odoo-connection" );
?>
<div class="wrap">
	<h1>Connections</h1>

	<a href="#" id="create-data" class="create-database-record">Create new data</a>
	<form method="POST" onsubmit="submitConnection();" id="form-data" class="submit-database" style="display: none;">
		<h2>Create a new connection: </h2>
		<input type="text" name="name" id="name" placeholder="Name" /><br/>
		<input type="text" name="username" id="username" placeholder="Username" /><br/>
		<input type="text" name="api_key" id="api_key" placeholder="API Key" /><br/>
		<input type="text" name="url" id="url" placeholder="URL" /><br/>
		<input type="text" name="database_name" id="database_name" placeholder="Database Name" /><br/>
		<input type="Submit" name="submit" />
	</form>

	<table class="database-table"></table>
	<div id="pageination-display"></div>

</div>

<script type="text/javascript">
	function getFormData () {
		let formData = new FormData();
		formData.append("name", document.getElementById("name").value);
		formData.append("username", document.getElementById("username").value);
		formData.append("api_key", document.getElementById("api_key").value);
		formData.append("url", document.getElementById("url").value);
		formData.append("database_name", document.getElementById("database_name").value);
		return formData;
	}

	function submitConnection () {
		let formData = getFormData();

		fetch("/wp-json/odoo-conn/v1/create-odoo-connection", {
			method: "POST",
			body: formData
		});
	}
</script>
<?php
}
?>
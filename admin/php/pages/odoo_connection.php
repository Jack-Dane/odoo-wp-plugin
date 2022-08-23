<?php 
require_once(__DIR__ . "/../pageHelpers/tableDisplay.php");

function odoo_connection_page () {
?>
<div class="wrap">
	<h1>Connections</h1>

	<form method="POST" onsubmit="submitConnection();">
		<h2>Create a new connection: </h2>
		<input type="text" name="name" id="name" placeholder="Name" /><br/>
		<input type="text" name="username" id="username" placeholder="Username" /><br/>
		<input type="text" name="api_key" id="api_key" placeholder="API Key" /><br/>
		<input type="text" name="url" id="url" placeholder="URL" /><br/>
		<input type="text" name="database_name" id="database_name" placeholder="Database Name" /><br/>
		<input type="Submit" name="submit" />
	</form>

	<?php
	get_connection_data();
	?>
</div>

<script type="text/javascript">
	function submitConnection () {
		let formData = new FormData();
		formData.append("name", document.getElementById("name").value);
		formData.append("username", document.getElementById("username").value);
		formData.append("api_key", document.getElementById("api_key").value);
		formData.append("url", document.getElementById("url").value);
		formData.append("database_name", document.getElementById("database_name").value);

		fetch("/wp-json/odoo-conn/v1/create-odoo-connection", {
			method: "POST",
			body: formData
		});
	}
</script>
<?php
}
?>
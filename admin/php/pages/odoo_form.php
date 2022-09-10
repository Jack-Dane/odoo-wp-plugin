<?php 
require_once(__DIR__ . "/../pageHelpers/table_display.php");

function odoo_form_page () {
	$form_table_data = new FormTableData();
?>
<div class="wrap">
	<h1>Odoo Form</h1>
	
	<a href="#" id="create-data" class="create-database-record">Create new data</a>
	<form method="POST" onsubmit="submitOdooForm();" id="form-data" class="submit-database" style="display: none;">
		<label for="odoo_connection_id">Odoo Connection</label>
		<select id="odoo_connection_id" name="odoo_connection_id" /><br/>
		<input type="text" name="odoo_model" id="odoo_model" placeholder="Odoo Model" /><br/>
		<input type="text" name="name" id="name" placeholder="Name" /><br/>
		<label for="contact_7_id">Contact 7 Form</label>
		<select name="contact_7_id" id="contact_7_id"/><br/>
		<input type="Submit" name="submit" />
	</form>

	<?php 
	$form_table_data->echo_table_data();
	?>
</div>

<script type="text/javascript">
	function submitOdooForm () {
		let formData = new FormData();
		formData.append("odoo_connection_id", document.getElementById("odoo_connection_id").value);
		formData.append("odoo_model", document.getElementById("odoo_model").value);
		formData.append("name", document.getElementById("name").value);
		formData.append("contact_7_id", document.getElementById("contact_7_id").value);

		console.log(formData);
		fetch("/wp-json/odoo-conn/v1/create-odoo-form", {
    		method: 'POST',
    		body: formData
		});
	}

	async function setSelectData () {
		connectionsSelect = jQuery("#odoo_connection_id");
		connectionsSelect.empty();

		let connections = await fetch("/wp-json/odoo-conn/v1/get-odoo-connections").then(function (response) {
			return response.json();
		}).then(function (jsonResponse){
			return jsonResponse;
		});

		connections.forEach( function(connection) {
			let option = jQuery(
				"<option></option>", {
					"value": connection["id"],
					"text": connection["name"]
				}
			).appendTo(connectionsSelect);
		});

		c7FormsSelect = jQuery("#contact_7_id");
		c7FormsSelect.empty();

		let c7Forms = await fetch("/wp-json/odoo-conn/v1/get-contact-7-forms").then(function (response) {
			return response.json();
		}).then(function (jsonResponse){
			return jsonResponse;
		});

		c7Forms.forEach( function(c7Form) {
			let option = jQuery(
				"<option></option>", {
					"value": c7Forms["id"],
					"text": c7Form["post_title"]
				}
			).appendTo(c7FormsSelect);
		});
	}
</script>
<?php
}
?>
<?php 
require_once(__DIR__ . "/../pageHelpers/tableDisplay.php");

function odoo_form_page () {
?>
<div class="wrap">
	<h1>Odoo Form</h1>
	
	<form method="POST" onsubmit="submitOdooForm();">
		<h2>Create a new Form: </h2>
		<input type="text" name="odoo_connection_id" id="odoo_connection_id" placeholder="Odoo Connection Id" /><br/>
		<input type="text" name="odoo_model" id="odoo_model" placeholder="Odoo Model" /><br/>
		<input type="text" name="name" id="name" placeholder="Name" /><br/>
		<input type="text" name="contact_7_id" id="contact_7_id" placeholder="Contact 7 Form Id" /><br/>
		<input type="Submit" name="submit" />
	</form>

	<h1>Form Submits</h1>
	<?php 
	get_form_data();
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
</script>
<?php
}
?>
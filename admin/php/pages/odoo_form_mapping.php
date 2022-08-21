<?php 
require_once(__DIR__ . "/../pageHelpers/tableDisplay.php");

function odoo_form_mapping_page () {
?>
<div class="wrap">
	<h1>Odoo Form Mappings</h1>

	<form method="POST" onsubmit="form_mapping_submit();">
		<h2>Create a New Form Mapping</h2>
		<input type="text" id="odoo_submit_id" name="odoo_submit_id" placeholder="Odoo Submit Id" /><br/>
		<input type="text" id="cf7_field_id" name="cf7_field_id" placeholder="Contact 7 Id" /><br/>
		<input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name" /><br/>
		<input type="Submit" name="submit">
	</form>

	<?php
	get_odoo_mappings();
	?>
</div>
<script type="text/javascript">
	function form_mapping_submit () {
		let formData = new FormData();
		formData.append("odoo_submit_id", document.getElementById("odoo_submit_id").value);
		formData.append("cf7_field_id", document.getElementById("cf7_field_id").value);
		formData.append("odoo_field_name", document.getElementById("odoo_field_name").value);

		fetch("/wp-json/odoo-conn/v1/create-odoo-form-mapping", {
			method: "POST",
			body: formData
		});
	}
</script>
<?php
}
?>
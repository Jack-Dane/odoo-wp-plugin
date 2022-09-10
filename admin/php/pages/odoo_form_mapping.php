<?php 
require_once(__DIR__ . "/../pageHelpers/table_display.php");

function odoo_form_mapping_page () {
	$form_mapping_table_data = new FormMappingTableData();
?>
<div class="wrap">
	<h1>Odoo Form Mappings</h1>

	<a href="#" id="create-data" class="create-database-record">Create new data</a>
	<form method="POST" onsubmit="return form_mapping_submit();" id="form-data" class="submit-database" style="display: none;">
		<h2>Create a New Form Mapping</h2>
		<input type="text" id="odoo_form_id" name="odoo_form_id" placeholder="Odoo Submit Id" /><br/>
		<input type="checkbox" id="value_type" name="value_type">
		<label for="value_type"> Constant Value</label><br>
		<input type="text" id="cf7_field_name" name="cf7_field_name" placeholder="Contact 7 Field Name" />
		<input type="text" id="constant_value" name="constant_value" placeholder="Constant Value" style="display: none;"/><br/>
		<input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name" />
		<input type="Submit" name="submit">
	</form>

	<?php
	$form_mapping_table_data->echo_table_data();
	?>
</div>
<script type="text/javascript">
	function form_mapping_submit () {
		let formData = new FormData();
		formData.append("odoo_form_id", document.getElementById("odoo_form_id").value);
		formData.append("cf7_field_name", document.getElementById("cf7_field_name").value);
		formData.append("odoo_field_name", document.getElementById("odoo_field_name").value);
		formData.append("constant_value", document.getElementById("constant_value").value);

		if (formData.get("cf7_field_name") != "" && formData.get("constant_value") != "") {
			alert("You cannot have both an CF7 Field Name and a Constant Value");
			return false;
		}

		fetch("/wp-json/odoo-conn/v1/create-odoo-form-mapping", {
			method: "POST",
			body: formData
		});
	}

	jQuery("#value_type").click(function () {
		let checked = jQuery(this).is(":checked");

		if (checked) {
			jQuery("#cf7_field_name").hide();
			jQuery("#constant_value").show();
		} else {
			jQuery("#constant_value").hide();
			jQuery("#cf7_field_name").show();
		}
	});
</script>
<?php
}
?>
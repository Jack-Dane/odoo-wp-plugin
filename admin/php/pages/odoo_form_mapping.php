<?php 
require_once(__DIR__ . "/../pageHelpers/table_display.php");

function odoo_form_mapping_page () {
	$form_mapping_table_data = new FormMappingTableData();
?>
<div class="wrap">
	<h1>Odoo Form Mappings</h1>

	<a href="#" onclick="createOdooFormMapping();" id="create-new-form-mapping" class="create-database-record">Create a new Form Mapping</a>
	<form method="POST" onsubmit="form_mapping_submit();" id="odoo-form-mapping-submit" class="submit-database" style="display: none;">
		<h2>Create a New Form Mapping</h2>
		<input type="text" id="odoo_form_id" name="odoo_form_id" placeholder="Odoo Submit Id" /><br/>
		<input type="text" id="cf7_field_name" name="cf7_field_name" placeholder="Contact 7 Id" /><br/>
		<input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name" /><br/>
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

		fetch("/wp-json/odoo-conn/v1/create-odoo-form-mapping", {
			method: "POST",
			body: formData
		});
	}

	function createOdooFormMapping () {
		let odooFormMapping = jQuery("#odoo-form-mapping-submit");
		let odooFormLabel = jQuery("#create-new-form-mapping");
		if (odooFormMapping.css("display") == "none") {
			odooFormMapping.slideDown();
			odooFormLabel.text("Hide");
		} else {
			odooFormMapping.slideUp();
			odooFormLabel.text("Create a new Form Mapping");
		}
	}

</script>
<?php
}
?>
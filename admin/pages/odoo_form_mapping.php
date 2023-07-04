<?php 
require_once(__DIR__ . "/../pageHelpers/table_display.php");

function odoo_form_mapping_page () {
	wp_register_script(
    	"odoo-form-mapping", plugins_url("odoo_form_mapping.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_enqueue_script( "odoo-form-mapping" );
?>
<div class="wrap">
	<h1>Odoo Form Mappings</h1>

	<a href="#" id="create-data" class="create-database-record button-primary" value="Create a new Form Mapping">Create a new Form Mapping</a>
	<form method="POST" onsubmit="return formMappingSubmit();" id="form-data" class="submit-database" style="display: none;">
		<label for="odoo_form_id">Odoo Form</label>
		<select id="odoo_form_id" name="odoo_form_id" ></select><br/>
		<label for="value_type"> Constant Value</label>
		<p class="input-wrapper">
			<input type="checkbox" id="value_type" name="value_type" /><br/>
		</p>
		<input type="text" id="cf7_field_name" name="cf7_field_name" placeholder="Contact 7 Field Name" />
		<input type="text" id="constant_value" name="constant_value" placeholder="Constant Value" style="display: none;"/><br/>
		<input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name" />
		<input type="Submit" name="submit" class="button-primary" />
	</form>

	<table class="database-table"></table>
	<div id="pageination-display"></div>

</div>
<?php
}
?>
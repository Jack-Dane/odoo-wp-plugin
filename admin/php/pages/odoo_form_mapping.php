<?php 
require_once(__DIR__ . "/../pageHelpers/table_display.php");

function odoo_form_mapping_page () {
	wp_register_script(
    	"odoo-form-mapping", plugins_url("/odoo_conn/admin/php/pages/odoo_form_mapping.js"), array("jquery"), "1.0.0", true
    );
    wp_enqueue_script( "odoo-form-mapping" );
?>
<div class="wrap">
	<h1>Odoo Form Mappings</h1>

	<a href="#" id="create-data" class="create-database-record">Create new data</a>
	<form method="POST" onsubmit="return formMappingSubmit();" id="form-data" class="submit-database" style="display: none;">
		<h2>Create a New Form Mapping</h2>
		<label for="odoo_form_id">Odoo Form</label>
		<select id="odoo_form_id" name="odoo_form_id" ></select><br/>
		<label for="value_type"> Constant Value</label>
		<input type="checkbox" id="value_type" name="value_type" /><br/>
		<input type="text" id="cf7_field_name" name="cf7_field_name" placeholder="Contact 7 Field Name" />
		<input type="text" id="constant_value" name="constant_value" placeholder="Constant Value" style="display: none;"/><br/>
		<input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name" />
		<input type="Submit" name="submit">
	</form>

	<table class="database-table"></table>
	<div id="pageination-display"></div>

</div>
<?php
}
?>
<?php 
require_once(__DIR__ . "/../pageHelpers/table_display.php");

function odoo_form_mapping_page () {
	wp_register_script(
    	"odoo-form-mapping", plugins_url("/odoo-conn/admin/php/pages/odoo_form_mapping.js"), array("jquery"), "1.0.0", true
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
<script type="text/javascript">
	function getFormData () {
		let formData = new FormData();
		formData.append("odoo_form_id", document.getElementById("odoo_form_id").value);
		formData.append("cf7_field_name", document.getElementById("cf7_field_name").value);
		formData.append("odoo_field_name", document.getElementById("odoo_field_name").value);
		formData.append("constant_value", document.getElementById("constant_value").value);
		formData.append("value_type", document.getElementById("value_type").checked);  // .checked returns a string boolean
		return formData;
	}


	function formMappingSubmit() {
		let formData = getFormData();

		if (formData.get("value_type") === "true") {
			if (formData.get("constant_value") == "" ) {
				alert("You have not filled in a constant value");
				return false;
			}
			formData.delete("cf7_field_name");
		}

		if (formData.get("value_type") === "false") {
			if (formData.get("cf7_field_name") == "") {
				alert("You have not filled in a Odoo Field Name");
				return false;
			}
			console.log("Deleting constant value");
			formData.delete("constant_value");
		}

		formData.delete("value_type");

		let object = {};
		formData.forEach( function( value, key ) {
			object[key] = value;
		});
		let json = JSON.stringify(object);

		fetch("/wp-json/odoo-conn/v1/create-odoo-form-mapping", {
			method: "POST",
			body: json,
			credentials: 'include',
			headers: {
				'content-type': 'application/json',
				'X-WP-Nonce': wpApiSettings.nonce
			}
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

	async function setSelectData () {
		formSelect = jQuery("#odoo_form_id");
		formSelect.empty();

		let forms = await fetch("/wp-json/odoo-conn/v1/get-odoo-forms",
			{
				credentials: 'include',
				headers: {
					'content-type': 'application/json',
					'X-WP-Nonce': wpApiSettings.nonce
				}
			}
		).then(function (response) {
			return response.json();
		}).then(function (jsonResponse){
			return jsonResponse;
		});

		forms.forEach( function(connection) {
			let option = jQuery(
				"<option></option>", {
					"value": connection["id"],
					"text": connection["name"]
				}
			).appendTo(formSelect);
		});
	}
</script>
<?php
}
?>
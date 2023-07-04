<?php 

function odoo_form_page () {
	wp_register_script(
    	"odoo-form", plugins_url("odoo_form.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_localize_script("table-display", "wpApiSettings", array(
        "root" => esc_url_raw(rest_url()), "nonce" => wp_create_nonce("wp_rest")
    ));
    wp_enqueue_script("odoo-form");
?>
<div class="wrap">
	<h1>Odoo Forms</h1>
	
	<a href="#" id="create-data" class="create-database-record button-primary" value="Create a new Form">Create a new Form</a>
	<form method="POST" onsubmit="return submitOdooForm();" id="form-data" class="submit-database" style="display: none;">
		<label for="odoo_connection_id">Odoo Connection</label>
		<select id="odoo_connection_id" name="odoo_connection_id" /><br/>
		<input type="text" name="odoo_model" id="odoo_model" placeholder="Odoo Model" /><br/>
		<input type="text" name="name" id="name" placeholder="Name" /><br/>
		<label for="contact_7_id">Contact 7 Form</label>
		<select name="contact_7_id" id="contact_7_id"/><br/>
		<input type="Submit" name="submit" class="button-primary" />
	</form>

	<table class="database-table"></table>
	<div id="pageination-display"></div>

</div>
<?php
}
?>
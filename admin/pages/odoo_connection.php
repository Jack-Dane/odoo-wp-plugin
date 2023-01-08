<?php 

function odoo_connection_page () {
	wp_register_script(
    	"odoo-connection", plugins_url("/odoo_conn/admin/pages/odoo_connection.js"), array("jquery"), "1.0.0", true
    );
    wp_localize_script("odoo-connection", "wpApiSettings", array(
        "root" => esc_url_raw(rest_url()), "nonce" => wp_create_nonce("wp_rest")
    ));
    wp_enqueue_script( "odoo-connection" );
?>
<div class="wrap">
	<h1>Odoo Connections</h1>

	<a href="#" id="create-data" class="create-database-record button-primary" value="Create a new Connection">Create a new Connection</a>
	<form method="POST" onsubmit="return submitConnection();" id="form-data" class="submit-database" style="display: none;">
		<input type="text" name="name" id="name" placeholder="Name" /><br/>
		<input type="text" name="username" id="username" placeholder="Username" /><br/>
		<input type="text" name="api_key" id="api_key" placeholder="API Key" /><br/>
		<input type="text" name="url" id="url" placeholder="URL" /><br/>
		<input type="text" name="database_name" id="database_name" placeholder="Database Name" /><br/>
		<input type="Submit" name="submit" class="button-primary" />
	</form>

	<table class="database-table"></table>
	<div id="pageination-display"></div>

</div>
<?php
}
?>
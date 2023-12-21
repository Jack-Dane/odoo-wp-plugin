<form method="POST" action="?page=odoo-form" id="form-data" class="submit-database">
    <?php wp_nonce_field(); ?>
    <input type="hidden" name="id" value="<?= $_REQUEST["id"] ?? "" ?>"/>
    <input type="hidden" name="odoo_connection_edit_id" id="odoo_connection_edit_id" value="<?= $odoo_conn_data->odoo_connection_id ?? "" ?>"/>
    <input type="hidden" name="odoo_7_edit_id" id="odoo_7_edit_id" value="<?= $odoo_conn_data->contact_7_id ?? "" ?>"/>

    <label for="odoo_connection_id">Odoo Connection</label>
    <select id="odoo_connection_id" name="odoo_connection_id"></select><br/>
    <label for="odoo_model">Odoo Model</label>
    <input type="text" name="odoo_model" id="odoo_model" value="<?= $odoo_conn_data->odoo_model ?? "" ?>"/><br/>
    <label for="name">Form Name</label>
    <input type="text" name="name" id="name" value="<?= $odoo_conn_data->name ?? "" ?>"/><br/>
    <label for="contact_7_id">Contact 7 Form</label>
    <select name="contact_7_id" id="contact_7_id"></select><br/>

    <div id="submit-wrapper">
        <input type="Submit" name="submit" class="button-primary"/>
    </div>
</form>
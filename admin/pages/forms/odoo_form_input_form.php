<form method="POST" action="?page=odoo-form" id="form-data" class="submit-database">
    <input type="hidden" name="id" value="<?= $_REQUEST["id"] ?? "" ?>"/>

    <label for="odoo_connection_id">Odoo Connection</label>
    <select id="odoo_connection_id" name="odoo_connection_id"></select><br/>
    <input type="text" name="odoo_model" id="odoo_model" placeholder="Odoo Model" value="<?= $odoo_conn_data->odoo_model ?? "" ?>"/><br/>
    <input type="text" name="name" id="name" placeholder="Name" value="<?= $odoo_conn_data->name ?? "" ?>"/><br/>
    <label for="contact_7_id">Contact 7 Form</label>
    <select name="contact_7_id" id="contact_7_id"></select><br/>
    <input type="Submit" name="submit" class="button-primary"/>
</form>
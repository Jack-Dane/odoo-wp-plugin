<form method="POST" action="?page=odoo-form-mapping" id="form-data" class="submit-database">
    <input type="hidden" name="id" value="<?= $_REQUEST["id"] ?? "" ?>"/>
    <input type="hidden" name="odoo_form_edit_id" id="odoo_form_edit_id" value="<?= $odoo_conn_data->odoo_form_id ?? "" ?>" />
    <input type="hidden" name="constant_value_checkbox" id="constant_value_checkbox" value="<?= ($odoo_conn_data->constant_value ?? false) && ($_REQUEST["id"] ?? false) ?>" />

    <label for="odoo_form_id">Odoo Form</label>
    <select id="odoo_form_id" name="odoo_form_id"></select><br/>
    <label for="value_type"> Constant Value</label>
    <p class="input-wrapper">
        <input type="checkbox" id="value_type" name="value_type"/><br/>
    </p>
    <input type="text" id="cf7_field_name" name="cf7_field_name" placeholder="Contact 7 Field Name" value="<?= $odoo_conn_data->cf7_field_name ?? "" ?>"/>
    <input type="text" id="constant_value" name="constant_value" placeholder="Constant Value" style="display: none;" value="<?= $odoo_conn_data->constant_value ?? "" ?>"/><br/>
    <input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name" value="<?= $odoo_conn_data->odoo_field_name ?? "" ?>"/>
    <input type="Submit" name="submit" class="button-primary"/>
</form>
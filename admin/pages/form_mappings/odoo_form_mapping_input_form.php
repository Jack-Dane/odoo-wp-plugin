<form method="POST" action="?page=odoo-form-mapping" id="form-data" class="submit-database">
    <?php wp_nonce_field(); ?>
    <input type="hidden" name="id" value="<?= $_REQUEST["id"] ?? "" ?>" />
    <input type="hidden" name="odoo_form_edit_id" id="odoo_form_edit_id" value="<?= esc_attr($odoo_conn_data->odoo_form_id ?? "") ?>" />
    <input type="hidden" name="constant_value_checkbox" id="constant_value_checkbox" value="<?= esc_attr(($odoo_conn_data->constant_value ?? false) && ($_REQUEST["id"] ?? false)) ?>" />

    <label for="odoo_form_id">Odoo Form</label>
    <select id="odoo_form_id" name="odoo_form_id"></select><br/>
    <label for="value_type"> Constant Value</label>
    <p class="input-wrapper">
        <input type="checkbox" id="value_type" name="value_type"/><br/>
    </p>

    <label for="cf7_field_name" id="cf7_field_name_label">Contact 7 Field Name</label>
    <input type="text" id="cf7_field_name" name="cf7_field_name" value="<?= esc_attr($odoo_conn_data->cf7_field_name ?? "") ?>"/>
    <label for="constant_value" id="constant_value_label" style="display: none;">Constant Value</label>
    <input type="text" id="constant_value" name="constant_value" style="display: none;" value="<?= esc_attr($odoo_conn_data->constant_value ?? "") ?>"/><br/>
    <label for="x_2_many">Many2Many/One2Many field</label>
    <p class="input-wrapper">
        <input type="checkbox" name="x_2_many" id="x_2_many" <?= esc_attr(($odoo_conn_data->x_2_many ?? "") === "1" ? "checked" : "") ?>/><br/>
    </p>
    <label for="odoo_field_name">Odoo Field Name</label>
    <input type="text" id="odoo_field_name" name="odoo_field_name" value="<?= esc_attr($odoo_conn_data->odoo_field_name ?? "") ?>"/>

    <div id="submit-wrapper">
        <input type="Submit" name="submit" class="button-primary"/>
    </div>
</form>
<form method="POST" action="?page=odoo-form-mapping" id="form-data" class="submit-database">
    <label for="odoo_form_id">Odoo Form</label>
    <select id="odoo_form_id" name="odoo_form_id"></select><br/>
    <label for="value_type"> Constant Value</label>
    <p class="input-wrapper">
        <input type="checkbox" id="value_type" name="value_type"/><br/>
    </p>
    <input type="text" id="cf7_field_name" name="cf7_field_name" placeholder="Contact 7 Field Name"/>
    <input type="text" id="constant_value" name="constant_value" placeholder="Constant Value"
           style="display: none;"/><br/>
    <input type="text" id="odoo_field_name" name="odoo_field_name" placeholder="Odoo Field Name"/>
    <input type="Submit" name="submit" class="button-primary"/>
</form>
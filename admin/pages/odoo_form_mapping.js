class FormMappings extends TableDisplay {

    constructor() {
        let tableData = new TableData(
            "get-odoo-form-mappings",
            "update-odoo-form-mapping",
            "delete-odoo-form-mapping"
        );
        super(tableData);
    }

    getUserFriendlyColumnNames() {
        return [
            "Id",
            "Odoo Form Id",
            "Contact Form 7 Field Name",
            "Odoo Field Name",
            "Constant Value"
        ];
    }

    getDisplayColumns() {
        return [
            "id",
            "odoo_form_name",
            "cf7_field_name",
            "odoo_field_name",
            "constant_value"
        ];
    }

    getForeignKeys() {
        return {
            "odoo_form_name": {
                "keyColumn": "odoo_form_id",
                "endpoint": "get-odoo-forms",
                "primaryKey": "id",
                "foreignColumnName": "name"
            }
        }
    }

}


let tableDisplay = new FormMappings();
tableDisplay.displayTable();

function getFormData() {
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
        formData.set("cf7_field_name", "");
        if (formData.get("constant_value") == "") {
            alert("You have not filled in a constant value");
            return false;
        }
    }

    if (formData.get("value_type") === "false") {
        formData.set("constant_value", "");
        if (formData.get("cf7_field_name") == "") {
            alert("You have not filled in a Odoo Field Name");
            return false;
        }
    }

    formData.delete("value_type");

    let object = {};
    formData.forEach(function (value, key) {
        object[key] = value;
    });
    let json = JSON.stringify(object);

    fetch(wpApiSettings.root + "odoo_conn/v1/create-odoo-form-mapping", {
        method: "POST",
        body: json,
        credentials: 'include',
        headers: {
            'content-type': 'application/json',
            'X-WP-Nonce': wpApiSettings.nonce
        }
    }).then(function () {
        tableDisplay.displayTable();
    });
    return false;
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

async function setSelectData() {
    formSelect = jQuery("#odoo_form_id");
    formSelect.empty();

    let forms = await fetch(wpApiSettings.root + "odoo_conn/v1/get-odoo-forms",
        {
            credentials: 'include',
            headers: {
                'content-type': 'application/json',
                'X-WP-Nonce': wpApiSettings.nonce
            }
        }
    ).then(function (response) {
        return response.json();
    }).then(function (jsonResponse) {
        return jsonResponse;
    });

    forms.forEach(function (connection) {
        let option = jQuery(
            "<option></option>", {
                "value": connection["id"],
                "text": connection["name"]
            }
        ).appendTo(formSelect);
    });
}

class OdooForms extends TableDisplay {

    constructor() {
        let tableData = new TableData(
            "get-odoo-forms",
            "update-odoo-form",
            "delete-odoo-form"
        );
        super(tableData);
    }

    getUserFriendlyColumnNames() {
        return [
            "Id",
            "Odoo Connection",
            "Odoo Model",
            "Name",
            "Contact 7 Form"
        ];
    }

    getDisplayColumns() {
        return [
            "id",
            "odoo_connection_name",
            "odoo_model",
            "name",
            "contact_7_title"
        ];
    }

    getForeignKeys() {
        return {
            "odoo_connection_name": {
                "keyColumn": "odoo_connection_id",
                "endpoint": "get-odoo-connections",
                "primaryKey": "id",
                "foreignColumnName": "name"
            },
            "contact_7_title": {
                "keyColumn": "contact_7_id",
                "endpoint": "get-contact-7-forms",
                "primaryKey": "ID",
                "foreignColumnName": "post_title"
            }
        }
    }

}


let tableDisplay = new OdooForms();
tableDisplay.displayTable();

function getFormData() {
    let formData = new FormData();
    formData.append("odoo_connection_id", document.getElementById("odoo_connection_id").value);
    formData.append("odoo_model", document.getElementById("odoo_model").value);
    formData.append("name", document.getElementById("name").value);
    formData.append("contact_7_id", document.getElementById("contact_7_id").value);
    return formData;
}

function submitOdooForm() {
    let formData = getFormData();
    let object = {};
    formData.forEach(function (value, key) {
        object[key] = value;
    });
    let json = JSON.stringify(object);

    fetch(wpApiSettings.root + "odoo_conn/v1/create-odoo-form", {
        method: 'POST',
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

async function setSelectData() {
    connectionsSelect = jQuery("#odoo_connection_id");
    connectionsSelect.empty();

    let connections = await fetch(wpApiSettings.root + "odoo_conn/v1/get-odoo-connections",
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

    connections.forEach(function (connection) {
        let option = jQuery(
            "<option></option>", {
                "value": connection["id"],
                "text": connection["name"]
            }
        ).appendTo(connectionsSelect);
    });

    c7FormsSelect = jQuery("#contact_7_id");
    c7FormsSelect.empty();

    let c7Forms = await fetch(wpApiSettings.root + "odoo_conn/v1/get-contact-7-forms",
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

    c7Forms.forEach(function (c7Form) {
        let option = jQuery(
            "<option></option>", {
                "value": c7Form["ID"],
                "text": c7Form["post_title"]
            }
        ).appendTo(c7FormsSelect);
    });
}

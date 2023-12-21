
jQuery(document).ready(function () {
    setSelectData();
});

async function setSelectData() {
    let connectionsSelect = jQuery("#odoo_connection_id");
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

    let existingConnectionId = jQuery("#odoo_connection_edit_id").val();
    connections.forEach(function (connection) {
        let option = jQuery(
            "<option></option>", {
                "value": connection["id"],
                "text": connection["name"]
            }
        );

        if (existingConnectionId === connection["id"]) {
            option.attr("selected", "selected");
        }

        option.appendTo(connectionsSelect);
    });

    let c7FormsSelect = jQuery("#contact_7_id");
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

    let existingContactFormId = jQuery("#odoo_7_edit_id").val();
    c7Forms.forEach(function (c7Form) {

        let option = jQuery(
            "<option></option>", {
                "value": c7Form["ID"],
                "text": c7Form["post_title"]
            }
        );

        if (existingContactFormId === c7Form["ID"]) {
            option.attr("selected", "selected");
        }

        option.appendTo(c7FormsSelect);
    });
}

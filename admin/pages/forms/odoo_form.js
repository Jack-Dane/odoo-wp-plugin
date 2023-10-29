
jQuery(document).ready(function () {
    setSelectData();
});

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
        jQuery(
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
        jQuery(
            "<option></option>", {
                "value": c7Form["ID"],
                "text": c7Form["post_title"]
            }
        ).appendTo(c7FormsSelect);
    });
}

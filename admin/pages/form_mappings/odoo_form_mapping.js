
jQuery(document).ready(function () {
    setSelectData();
});

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

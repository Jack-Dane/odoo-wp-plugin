
jQuery(document).ready(function () {
    setConstantValue();
    setSelectData();
});

jQuery("#value_type").click(function () {
    checkboxEvent();
});

function checkboxEvent() {
    let checked = jQuery("#value_type").prop("checked");

    if (checked) {
        jQuery("#cf7_field_name").hide();
        jQuery("#cf7_field_name_label").hide();
        jQuery("#constant_value").show();
        jQuery("#constant_value_label").show();
    } else {
        jQuery("#constant_value").hide();
        jQuery("#constant_value_label").hide();
        jQuery("#cf7_field_name").show();
        jQuery("#cf7_field_name_label").show();
    }
}

function setConstantValue () {
    let constantValue = jQuery("#constant_value_checkbox").val();
    jQuery("#value_type").prop("checked", constantValue);

    checkboxEvent();
}

async function setSelectData() {
    let formSelect = jQuery("#odoo_form_id");
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

    let existingFormId = jQuery("#odoo_form_edit_id").val();
    forms.forEach(function (form) {
        let option = jQuery(
            "<option></option>", {
                "value": form["id"],
                "text": form["name"]
            }
        );

        if (existingFormId === form["id"]) {
            option.attr("selected", "selected")
        }

        option.appendTo(formSelect);
    });
}

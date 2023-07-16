var tableDisplay = new OdooConnections();
tableDisplay.displayTable();

function getFormData() {
    let formData = new FormData();
    formData.append("name", document.getElementById("name").value);
    formData.append("username", document.getElementById("username").value);
    formData.append("api_key", document.getElementById("api_key").value);
    formData.append("url", document.getElementById("url").value);
    formData.append("database_name", document.getElementById("database_name").value);
    return formData;
}

function submitConnection() {
    let formData = getFormData();
    let object = {};
    formData.forEach(function (value, key) {
        object[key] = value;
    });
    let json = JSON.stringify(object);

    fetch(wpApiSettings.root + "odoo_conn/v1/create-odoo-connection", {
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

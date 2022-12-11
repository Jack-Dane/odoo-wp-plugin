
let tableDisplay = new OdooForms();
tableDisplay.displayTable();

function getFormData () {
	let formData = new FormData();
	formData.append("odoo_connection_id", document.getElementById("odoo_connection_id").value);
	formData.append("odoo_model", document.getElementById("odoo_model").value);
	formData.append("name", document.getElementById("name").value);
	formData.append("contact_7_id", document.getElementById("contact_7_id").value);
	return formData;
}

function submitOdooForm () {
	let formData = getFormData();
	let object = {};
	formData.forEach(function(value, key){
	    object[key] = value;
	});
	let json = JSON.stringify(object);

	fetch("/wp-json/odoo_conn/v1/create-odoo-form", {
		method: 'POST',
		body: json,
		credentials: 'include',
		headers: {
			'content-type': 'application/json',
			'X-WP-Nonce': wpApiSettings.nonce
		}
	});
}

async function setSelectData () {
	connectionsSelect = jQuery("#odoo_connection_id");
	connectionsSelect.empty();

	let connections = await fetch("/wp-json/odoo_conn/v1/get-odoo-connections",
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

	connections.forEach( function(connection) {
		let option = jQuery(
			"<option></option>", {
				"value": connection["id"],
				"text": connection["name"]
			}
		).appendTo(connectionsSelect);
	});

	c7FormsSelect = jQuery("#contact_7_id");
	c7FormsSelect.empty();

	let c7Forms = await fetch("/wp-json/odoo_conn/v1/get-contact-7-forms",
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

	c7Forms.forEach( function(c7Form) {
		let option = jQuery(
			"<option></option>", {
				"value": c7Form["ID"],
				"text": c7Form["post_title"]
			}
		).appendTo(c7FormsSelect);
	});
}

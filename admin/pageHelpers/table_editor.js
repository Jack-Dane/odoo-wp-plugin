
function getUpdateData (id) {
	let formData = {}
	jQuery("." + id).each(function () {
		let fieldName = jQuery(this).data("table-field");
		let fieldValue = "";
		if (jQuery(this).data("editable")) {
			fieldValue = jQuery(this).val();
		} else {
			fieldValue = jQuery(this).text();
		}
		formData[fieldName] = fieldValue;
	});
	return formData;
}

function updateData (id, endpoint) {
	let updateData = getUpdateData(id);
	let joinParam = wpApiSettings.root.includes("?") ? "&" : "?";

	return fetch(
		wpApiSettings.root + "odoo_conn/v1/" + endpoint + joinParam + new URLSearchParams(
			updateData
		), 
		{
			method: "PUT",
			credentials: 'include',
			headers: {
				'content-type': 'application/json',
				'X-WP-Nonce': wpApiSettings.nonce
			}
		}
	);
}

async function deleteRow (id, endpoint) {
	let joinParam = wpApiSettings.root.includes("?") ? "&" : "?";
	await fetch(
		wpApiSettings.root + "odoo_conn/v1/" + endpoint + joinParam + new URLSearchParams(
			{
				id: id
			}
		),
		{
			method: "DELETE",
			credentials: 'include',
			headers: {
				'content-type': 'application/json',
				'X-WP-Nonce': wpApiSettings.nonce
			}
		}
	);
}

async function getForeignKeyData (foreignKeyData) {
	return await fetch (
		wpApiSettings.root + "odoo_conn/v1/" + foreignKeyData,
		{
			credentials: 'include',
			headers: {
				'content-type': 'application/json',
				'X-WP-Nonce': wpApiSettings.nonce
			}
		}
	).then( function (response) {
		return response.json();
	}).then( function (jsonResponse) {
		return jsonResponse
	});
}

function closeFields (id) {
	jQuery("." + id).each(function () {
		let editable = jQuery(this).data("editable");
		if (!editable) {
			return; // equal to continue in a javascript loop
		}
		let text = jQuery(this).val();
		let tableField = jQuery(this).data("table-field");

		let span = jQuery("<span></span>");
		span.data("editable", editable);
		span.data("table-field", tableField);
		span.text(text);
		jQuery(this).replaceWith(span);
	});
}

function openFieldsForEdit (id) {
	jQuery("." + id).each(async function () {
		let element = jQuery(this);
		let editable = element.data("editable");
		if (!editable) {
			return; // will continue to the next element in the loop
		}
		let dropDown = element.data("foreign-key-endpoint");
		let tableField = element.data("table-field");

		if (dropDown) {
			foreignKeyData = await getForeignKeyData(
				element.data("foreign-key-endpoint")
			);

			let dropDown = jQuery("<select></select>");
			dropDown.data("editable", editable);
			dropDown.data("table-field", tableField);
			dropDown.addClass(id);

			let selectedValue = element.data("foreign-key-value");
			foreignKeyData.forEach( function (foreignKeyObject) {
				let id = foreignKeyObject[element.data(
					"foreign-key-column-primary-key"
				)];
				let name = foreignKeyObject[element.data(
					"foreign-key-column-name"
				)];

				let option = jQuery("<option></option>");
				option.attr("value", id);
				option.text(name);

				if (selectedValue == id) {
					option.attr("selected", true);
				}
				dropDown.append(option);
			});
			element.replaceWith(dropDown);
		} else {
			let text = element.text();

			let input = jQuery("<input></input>");
			input.data("editable", editable);
			input.data("table-field", tableField);
			input.addClass(id);
			input.attr("value", text);

			element.replaceWith(input);
		}
	});
}

jQuery(document).ready(function () {
	jQuery(".database-table").on("click", ".table-row-edit", function () {
		let id = jQuery(this).data("row-class");
		jQuery(this).hide();
		findElementTableRowEdit(".table-row-save", id).show();
		findElementTableRowEdit(".table-row-close", id).show();
		openFieldsForEdit(id);
	});

	jQuery(".database-table").on("click", ".table-row-close", function () {
		let id = jQuery(this).data("row-class");
		jQuery(this).hide();
		findElementTableRowEdit(".table-row-save", id).hide();
		findElementTableRowEdit(".table-row-edit", id).show();
		closeFields(id);
		tableDisplay.displayTable();
	});

	jQuery(".database-table").on("click", ".table-row-save", async function () {
		let id = jQuery(this).data("row-class");
		jQuery(this).hide();
		findElementTableRowEdit(".table-row-close", id).hide();
		findElementTableRowEdit(".table-row-edit", id).show();
		let endpoint = jQuery(this).data("endpoint");
		updateData(id, endpoint).then(function (response) {
			closeFields(id);
			tableDisplay.displayTable();
			
			if (response.status != 200) {
				jsonPromise = Promise.resolve(response.json());

				jsonPromise.then(function (jsonResponse) {
					// get the error message from the failed response
					alert(jsonResponse["message"]);
				});
			}
		});
	});

	jQuery(".database-table").on("click", ".table-row-delete", async function() {
		let rowId = jQuery(this).data("row-id");
		let deleteEndpoint = jQuery(this).data("endpoint");
		await deleteRow(rowId, deleteEndpoint);
		tableDisplay.displayTable();
	});
});

function findElementTableRowEdit (className, id) {
	let element = null;
	jQuery(className).each(function () {
		if (jQuery(this).data("row-class") == id) {
			element = jQuery(this);
			return;
		}
	});
	return element;
}

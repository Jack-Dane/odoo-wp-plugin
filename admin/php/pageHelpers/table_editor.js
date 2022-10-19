
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

async function updateData (id, endpoint) {
	let updateData = getUpdateData(id);

	await fetch(
		"/wp-json/odoo-conn/v1/" + endpoint + "?" + new URLSearchParams(updateData), 
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
	await fetch(
		"/wp-json/odoo-conn/v1/" + endpoint + "?" + new URLSearchParams(
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

function closeFields (id) {
	jQuery("." + id).each(function () {
		let editable = jQuery(this).data("editable");
		if (!editable) {
			return; // equal to continue in a javascript loop
		}
		let text = jQuery(this).val();
		let tableField = jQuery(this).data("table-field");
		jQuery(this).replaceWith(
			"<span class='" + id + "' data-editable='" + editable + "' data-table-field='" + tableField + "'>" + text + "</span>"
		);
	});
}

function openFieldsForEdit (id) {
	jQuery("." + id).each(async function () {
		let element = jQuery(this);
		let editable = element.data("editable");
		if (!editable) {
			return; // equal to continue in a javascript loop
		}
		let dropDown = element.data("foreign-key-endpoint");
		let tableField = element.data("table-field");

		if (dropDown) {
			let foreignKeyData = await fetch (
				"/wp-json/odoo-conn/v1/" + element.data("foreign-key-endpoint"),
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

			let dropDown = jQuery("<select data-editable='" + editable + "' data-table-field='" + tableField + "' class='" + id + "'/>");
			let selectedValue = element.data("foreign-key-value");
			foreignKeyData.forEach( function (foreignKeyObject) {
				let id = foreignKeyObject[element.data("foreign-key-column-primary-key")];
				let name = foreignKeyObject[element.data("foreign-key-column-name")];

				let option = jQuery("<option value='" + id + "'>" + name + "</option>");
				console.log(selectedValue);
				if (selectedValue == id) {
					option.attr("selected", true);
				}
				dropDown.append(option);
			});
			element.replaceWith(dropDown);
		} else {
			let text = element.text();
			element.replaceWith(
				"<input type='text' data-editable='" + editable + "' data-table-field='" + tableField + "' class='" + id + "' value='" + text + "'/>"
			);
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
		await updateData(id, endpoint);
		closeFields(id);
		tableDisplay.displayTable();
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

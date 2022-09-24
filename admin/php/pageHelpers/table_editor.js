
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

	fetch(
		"/wp-json/odoo-conn/v1/" + endpoint + "?" + new URLSearchParams(updateData), 
		{
			method: "PUT",
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
	jQuery("." + id).each(function () {
		let editable = jQuery(this).data("editable");
		if (!editable) {
			return; // equal to continue in a javascript loop
		}
		let text = jQuery(this).text();
		let tableField = jQuery(this).data("table-field");
		jQuery(this).replaceWith(
			"<input type='text' data-editable='" + editable + "' data-table-field='" + tableField + "' class='" + id + "' value='" + text + "'/>"
		);
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

	jQuery(".database-table").on("click", ".table-row-save", function () {
		let id = jQuery(this).data("row-class");
		jQuery(this).hide();
		findElementTableRowEdit(".table-row-close", id).hide();
		findElementTableRowEdit(".table-row-edit", id).show();
		let endpoint = jQuery(this).data("endpoint");
		updateData(id, endpoint);
		closeFields(id);
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

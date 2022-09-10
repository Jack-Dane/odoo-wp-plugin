
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

function getEndpoint (id) {
	return jQuery("#" + id).data("endpoint");
}

function updateData (id) {
	let updateData = getUpdateData(id);
	console.log(updateData);
	let endpoint = getEndpoint(id);

	fetch(
		"/wp-json/odoo-conn/v1/" + endpoint + "?" + new URLSearchParams(updateData), 
		{
			method: "PUT",
		}
	);
}

jQuery(".table-row").click(function () {
	let id = jQuery(this).attr("id");
	if (jQuery(this).data("update-state") != "true") {
		jQuery(this).data("update-state", "true");
		jQuery(this).text("Save");
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
	} else {
		updateData(id);
		jQuery(this).data("update-state", "false");
		jQuery(this).text("Edit");
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
});

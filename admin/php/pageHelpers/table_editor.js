
jQuery(".table-row").click(function () {
	let id = jQuery(this).attr('id');
	if (jQuery(this).attr("data-update-state") != "true") {
		jQuery(this).attr("data-update-state", "true");
		jQuery(this).text("Save");
		jQuery("." + id).each(function () {
			let text = jQuery(this).text();
			jQuery(this).replaceWith("<input type='text' class='" + id + "' value='" + text + "'/>");
		});
	} else {
		jQuery(this).attr("data-update-state", "false");
		jQuery(this).text("Edit");
		jQuery("." + id).each(function () {
			let text = jQuery(this).val();
			jQuery(this).replaceWith("<span class='" + id + "'>" + text + "</span>");
		});
	}
});

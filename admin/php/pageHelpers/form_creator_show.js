
jQuery("#create-data").click(function() {
	let form = jQuery("#form-data");
	let formLabel = jQuery("#create-data");
	if (form.css("display") == "none") {
		form.slideDown();
		formLabel.text("Hide");
	} else {
		form.slideUp();
		formLabel.text("Create new data");
	}
});

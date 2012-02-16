$(function() {
	// Grab data from the web service and populate the fields of the page
	
	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/profile_prefs.php",
		dataType: "json",
		success: createDropDown,
		error: connectionError
	});	
	
	
	// TODO: display loading overlay
});


function createDropDown(data, textStatus, jqXHR) {

	// alert(data);
	var categories = data.categories;
	var select = $("select[name=category]");
	// alert(select);
	// select.append($('<option value="foo">test</option>'));
	
	
	$.each(categories, function() {
		select.append($('<option value="' + this.cat_id +
			'">' + this.name + '</option>'));
	});
}


function connectionError(jqXHR, textStatus, errorThrown) {
	alert("An error occurred: " + errorThrown);
}
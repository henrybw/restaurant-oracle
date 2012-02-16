$(function() {
	// Grab data from the web service and populate the fields of the page
	
	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/my_groups.php",
		dataType: "json",
		success: populateGroupMenu,
		error: connectionError
	});	
	
	
	// TODO: display loading overlay
});

function populateGroupMenu(data, textStatus, jqXHR) {
	var select = $("select[name='group']");	
	
	$.each(data, function() {
		select.append($('<option value="' + this.gid +
			'">' + this.name + '</option>'));
	});
}

function connectionError(jqXHR, textStatus, errorThrown) {
	alert("An error occurred: " + errorThrown);
}


function getSearchResults() {
	var isGroupSearch = $("input:radio[name='searchType']:checked").val() === "group";
	var id = isGroupSearch ? $("select[name='group']").val() : localStorage.getItem(USER_ID);
	
	alert("isGroupSearch: " + isGroupSearch + "\nGroup / user id: " + id);
}
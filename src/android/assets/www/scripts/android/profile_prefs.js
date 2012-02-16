$(function() {
	// Grab data from the web service and populate the fields of the page
	
	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/profile_prefs.php",
		dataType: "json",
		success: populateProfilePrefs,
		error: connectionError
	});	
	
	
	// TODO: display loading overlay
});


function populateProfilePrefs(data, textStatus, jqXHR) {

	// The drop-down category menu
	var categories = data.categories;
	var userCategories = data.user_prefs;
	var select = $("select[name=category]");
	
	$.each(categories, function() {
		select.append($('<option value="' + this.cat_id +
			'">' + this.name + '</option>'));
	});
	
	
	// The preferences the user has already entred	
	var even = true;
	var table = $("#preferenceTable");
	
	$.each(userCategories, function() {
		var row = $('<tr></tr>').addClass(even ? 'even' : 'odd');
		
		var nameCell = $('<td></td>').html(this.name);
		var ratingCell = $('<td></td>').html(this.rating);
		
		row.append(
			$('<td></td>'),
			nameCell,
			ratingCell,
			$('<td></td>'));
		table.append(row);
		
		even = !even;
		// row.append("<td>test</td>");
	});
	
	table.append($(	'<tr class="bottom">' +
					'	<td></td>' +
					'	<td></td>' +
					'	<td><div></div></td>' +
					'	<td></td>' +
					'</tr>'
	));
	
}


function connectionError(jqXHR, textStatus, errorThrown) {
	alert("An error occurred: " + errorThrown);
}
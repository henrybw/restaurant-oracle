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
	var guid = isGroupSearch ? $("select[name='group']").val() : localStorage.getItem(USER_ID);
	
	var formData = {isGroup: isGroupSearch, id: guid};
	
	//alert("isGroupSearch: " + isGroupSearch + "\nGroup / user id: " + id);

	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/results.php",
		data: formData,
		dataType: "json",
		success: getSearchResultsSuccess,
		error: getSearchResultsError
	});
	
}

function getSearchResultsSuccess(data, textStatus, jqXHR) {
	console.log("search results success! data: " + data);
	
	var restaurants = {
		0: {rid: 1105, name: "Jimmy John's"},
		1: {rid: 1, name: "1-2-3 Thai Food"},
		2: {rid: 493, name: "Chipotle"},
		3: {rid: 22, name: "A Burger Place"},
		5: {rid: 475, name: "China First"},
		6: {rid: 184, name: "Banana Leaf Cafe"}	
	};
	
	
	
	var table = $('<table></table>');
	
	$('#resultsTable').empty().append(table);
		
	
	table.append($(	'<tr>' +
					'	<th class="corner"><div class="left"></div></th>' +
					'	<th class="top">Restaurant ID</th>' +
					'	<th class="top">Restaurant</th>' +
					'	<th class="corner"><div class="right"></div></th>' +
					'</tr>'
	));
	
	var even = true;
	
	$.each(restaurants, function() {
		var row = $('<tr></tr>').addClass(even ? 'even' : 'odd');
		
		var cellOne = $('<td></td>').html(this.rid);
		var cellTwo = $('<td></td>').html(this.name);
		
		
		row.append(
			$('<td></td>'),
			cellOne,
			cellTwo,
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

function getSearchResultsError(jqXHR, textStatus, errorThrown){
	console.log("search results error: " + errorThrown);
}
var position = {latitude: undefined, longitude: undefined};

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

	// Calculates geolocation coordinates once per page
	$(function() {
		if (navigator.geolocation)  {
			navigator.geolocation.getCurrentPosition(
				function(pos) { 
					position.latitude = pos.coords.latitude;
					position.longitude = pos.coords.longitude;
				},
				function(error) {
					switch(error.code) {
						case error.TIMEOUT:
							alert('Geolocation error: Timeout');
							break;
						case error.POSITION_UNAVAILABLE:
							alert('Geolocation error: Position unavailable');
							break;
						case error.PERMISSION_DENIED:
							alert('Geolocation error: Permission denied');
							break;
						case error.UNKNOWN_ERROR:
							alert('Geolocation error: Unknown error');
							break;
					}
				}
			);
		}
	});
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


function getSearchResults(uid) {
	var isGroupSearch = $("input:radio[name='searchType']:checked").val() === "group";
	var guid = isGroupSearch ? $("select[name='group']").val() : localStorage.getItem(USER_ID);
	var excludeClosedSetting = $("#excludeClosed").attr('checked') === 'checked';
	var excludeUnknownHoursSetting = $("#excludeUnknownHours").attr('checked') === 'checked';
	var maxDist = parseFloat($("#distance").val());
	var priceRangeCode = parseInt($("select[name='priceRange']").val());
	var reservationVal = $("#reservations").attr('checked') === 'checked';
	var creditCards = $("#acceptsCreditCards").attr('checked') === 'checked';
	
	var formData = {
		id: guid,
		isGroup: isGroupSearch,
		latitude: position.latitude,
		longitude: position.longitude,
		maxDistance: maxDist,
		reservations: reservationVal,
		acceptsCreditCards: creditCards,
		price: priceRangeCode,
		excludeClosed: excludeClosedSetting,
		excludeUnknownHours: excludeUnknownHoursSetting,
		currentTime: new Date().getTime()
	};
	
	//alert("isGroupSearch: " + isGroupSearch + "\nGroup / user id: " + guid);

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
	
	
	var table = $('<table></table>');
	
	$('#resultsTable').empty().append(table);	
		
	
	table.append($(	'<tr>' +
					'	<th class="corner"><div class="left"></div></th>' +
					'	<th class="top">Name</th>' +
					'	<th class="top">Distance</th>' +
					'	<th class="top">Status</th>' +
					'	<th class="corner"><div class="right"></div></th>' +
					'</tr>'
	));
	
	var even = true;
	
	$.each(data, function() {
		var row = $('<tr></tr>').addClass(even ? 'even' : 'odd');
		
		var name = $('<td></td>').html('<a href="details.php?id=' + this.rid + '">' + this.name + '</a>');
		var distance = $('<td class="center"></td>').html(round(this.distance));
		var status = $('<td class="center"></td>').html(this.status).addClass('center');
		
		
		row.append(
			$('<td></td>'),
			name,
			distance,
			status,
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
					'	<td></td>' +
					'</tr>'
	));
}

function round(num) {
	return Math.round(num * 100) / 100;
}

function getSearchResultsError(jqXHR, textStatus, errorThrown){
	console.log(jqXHR.responseText);
	console.log("search results error: " + errorThrown);
}
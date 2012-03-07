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
		
		var name = $('<td></td>').html('<a href="#" onclick="showDetails(' + this.rid + ')">' + this.name + '</a>');
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

function showDetails(rid) {
	$("#main").css('display', 'none');
	$("#details").css('display', 'block');

	// Clear all metadata
	$("#metadata").empty();

	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/details.php",
		dataType: "json",
		data: { id: rid },
		success: populateDetails,
		error: connectionError
	});

	// TODO: show error overlay
}

function populateDetails(data, textStatus, jqXHR) {
	// This should always exist
	$('#restaurantName').html(data.name);

	if (data.address) {
		$('#metadata').append('<dt>Address:</dt>');
		$('#metadata').append('<dd>' + data.address + '</dd>');
	}
	if (data.phone_number) {
		$('#metadata').append('<dt>Phone:</dt>');
		$('#metadata').append('<dd>' + data.phone_number + '</dd>');
	}
	if (data.hours) {
		$('#metadata').append('<dt>Hours:</dt>');
		$('#metadata').append('<dd>' + data.hours + '</dd>');
	}
	if (data.price) {
		var priceStr = '';

		for (var i = 0; i < data.price; i++)
			priceStr += '$';

		$('#metadata').append('<dt>Price Range:</dt>');
		$('#metadata').append('<dd>' + priceStr + '</dd>');
	}
	if (data.accepts_credit_cards) {
		$('#metadata').append('<dt>Accepts Credit Cards:</dt>');
		$('#metadata').append('<dd>' + data.accepts_credit_cards + '</dd>');
	}
	if (data.reservations) {
		$('#metadata').append('<dt>Reservations:</dt>');
		$('#metadata').append('<dd>' + data.reservations + '</dd>');
	}
	if (data.takeout) {
		$('#metadata').append('<dt>Takeout:</dt>');
		$('#metadata').append('<dd>' + data.takeout + '</dd>');
	}
	if (data.outdoor_seating) {
		$('#metadata').append('<dt>Outdoor Seating:</dt>');
		$('#metadata').append('<dd>' + data.outdoor_seating + '</dd>');
	}
	if (data.parking) {
		$('#metadata').append('<dt>Parking:</dt>');
		$('#metadata').append('<dd>' + data.parking + '</dd>');
	}
	if (data.alcohol) {
		$('#metadata').append('<dt>Alcohol:</dt>');
		$('#metadata').append('<dd>' + data.alcohol + '</dd>');
	}

	displayMap(data.latitude, data.longitude);
}

function displayMap(latitude, longitude) {
	var map = new GMap2(document.getElementById("mapCanvas"));
	var marker = new GMarker(new GLatLng(latitude, longitude));
	
	map.setCenter(new GLatLng(latitude, longitude), 14);
	map.addOverlay(marker);
	map.setUIToDefault();
}

function showSearch() {
	$("#main").css('display', 'block');
	$("#details").css('display', 'none');
}
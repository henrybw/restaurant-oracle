$(function() {
	toggleLoadingOverlay(true);
	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/details.php",
		dataType: "json",
		success: populateDetailsPage,
		error: detailsError
	});

});

function detailsError(jqXHR, textStatus, errorThrown) {
	console.log("connection error in details page");
	toggleLoadingOverlay(false);
}

function populateDetailsPage(data, textStatus, jqXHR) {
	
	var detail = $("#detailGroup");
	
	var header = $("<h2></h2>").html(data.name);
	
	detail.append(header);
	
	
	$.each(data, function(index, element) {
		if (index != 0) {
			var p = $("<p></p>").html(element);
			detail.append(p);
		}
	});

	toggleLoadingOverlay(false);
}
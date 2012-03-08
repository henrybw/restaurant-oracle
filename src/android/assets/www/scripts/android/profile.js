/*
 * Page-specific JavaScript functionality.
 *
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

$(function() {
	// Grab data from the web service and populate the fields of the page
	toggleLoadingOverlay(true);

	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/profile.php",
		dataType: "json",
		success: function(data, textStatus, jqXHR) {
			if (data) {
				$('#uid').html(data.uid);
				$('#fname').html(data.fname);
				$('#lname').html(data.lname);
				$('#email').html(data.email);
			} else {
				// TODO: uhh probably should handle errors somehow...
			}
			
			toggleLoadingOverlay(false);
		},
		error: connection_error
	});	
});

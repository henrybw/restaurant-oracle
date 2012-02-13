/*
 * @author Coral Peterson
 */


// Call this function to animate and display a drop down menu.
// The HTML should be in the format (replace [name] with an actual name):
// 
// <div id="[name]">
// 		<a href="#" id="[name]DisplayLink" onclick="toggleDropDown('[name]');"
//			class="button dropDown">
//			[Text to display]
// 		</a>
// 		<div id="[name]Details" class="hidden dropDownDetails">
// 			<!-- What you want to be displayed when the dropdown button is clicked -->
// 		</div>
// </div>
function toggleDropdown(name) {
	
	var detailsId = "#" + name + "Details";
	var linkId = "#" + name + "DisplayLink";
	
	$(detailsId).slideToggle(300);
	var timeout = 0;
	// if we are sliding down the window, then we want the class
	// to be toggled right away, but if we are hiding it, we don't
	// want it to be toggled until the animation is done
	
	if ($(linkId).hasClass("open")) {
		timeout = 300;
	}
	
	window.setTimeout(function() {
		$(linkId).toggleClass("open");
	}, timeout);	
}


function login_submit() {
	$("#login_form").submit();
}

function joinGroup() {
	
}

function joinGroupSuccess() {

}

function joinGroupError() {

}

function add_category() {
 
    var categoryData = $('select[name=category]').val();
    var ratingData = $('input:radio[name=rating]:checked').val();
    
    var formData = {category : categoryData, rating: ratingData};


    $.ajax({
		type: "POST",
		url: "services/profile_prefs_update.php",
		data: formData,
		success: addCategorySuccess,
		dataType: 'json',
		error: addCategoryError
    });    
}

function addCategorySuccess(data, textStatus, jqXHR) {
    console.log("add category success!");
    toggleDropdown("addCategory");
	
	if (data.update === 'updated') {
		var p = '#pref_' + data.cat['name'] + ' .rating';
		$(p).html(data.rating);
	} else {
		var tds = "<tr><td></td><td class='cat_name'>" + data.cat['name'] + "</td><td class='rating'>" + data.rating + "</td><td></td></tr>";

		$("#preferenceTable").append(tds);
	}
}

function addCategoryError(jqXHR, textStatus, errorThrown) {
    console.log("add category error");
	toggleDropdown("addCategory");
}

function createGroup() {
	var nameData = $('input:input[name=gname]').val();
	
	var formData = {name : nameData};
	
	$.ajax({
		type: "POST",
		url: "services/create_group.php",
		data: formData,
		success: createGroupSuccess,
		dataType: 'json',
		error: createGroupError
	});
}

function createGroupSuccess(data, textStatus, jqXHR) {
	console.log("create group success!");
}

function createGroupError(jqXHR, textStatus, errorThrown) {
	console.log("create group error. ):");
}

function create_profile() {
    var email_data = $('input:input[name=email]').val();
    var fname_data = $('input:input[name=fname]').val();
    var lname_data = $('input:input[name=lname]').val();

    var blob = {email: email_data, fname: fname_data, lname: lname_data };

    $.ajax({
		type: "POST",
		url: "services/create_profile.php",
		data: blob,
		success: create_profile_success,
		dataType: 'json',
		error: create_profile_error

    });
}

function create_profile_success(data, textStatus, jqXHR) {
    console.log("success!");
    
    /*$("#create_profile_status").html("Success!");
    $("#create_profile_status").css('backgroundColor', '#98FB98');
    $("#create_profile_status").slideDown(400);*/
	
	window.location.href = "profile.php";
}

function create_profile_error(jqXHR, textStatus, errorThrown){
    console.log("error! D:");
}
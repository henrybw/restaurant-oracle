/*
 * @author Coral Peterson
 */


var add_category_visible = false; 
var login_visible = false; 

function display_login() {
    $("#login_details").slideDown(400);
	$("#display_login_link").addClass("open");
	login_visible = true;
}

function hide_login() {
	$("#login_details").slideUp(100);
	$("#display_login_link").removeClass("open");
	login_visible = false;
}

function toggle_login() {
	if (login_visible === true) {
		hide_login();
	} else {
		display_login();
	}
}

function login_submit() {
	$("#login_form").submit();
}



/* These category things should be abstracted...*/
function toggle_add_category() {
	if (add_category_visible === true) {
		hide_add_category();
	} else {
		display_add_category();
	}
}

function display_add_category() {
    $("#add_category_link").addClass("open");
	$("#addCategoryDetails").slideDown(400);
	add_category_visible = true;
}

function hide_add_category() {
	$("#addCategoryDetails").slideUp(200);	
	$("#add_category_link").removeClass("open");
	add_category_visible = false;
}


function add_category() {
 
    var category_data = $('select[name=category]').val();
    var rating_data = $('input:radio[name=rating]:checked').val();
    
    var test = {category : category_data, rating: rating_data};


    $.ajax({
		type: "POST",
		url: "services/profile_prefs_update.php",
		data: test,
		success: add_category_success,
		dataType: 'json',
		error: add_category_error
    });    
}

function add_category_success(data, textStatus, jqXHR) {
    console.log("add category success!");
    hide_add_category();
	
	if (data.update === 'updated') {
		var p = '#pref_' + data.cat['name'] + ' .rating';
		$(p).html(data.rating);
	} else {
		var tds = "<tr><td></td><td class='cat_name'>" + data.cat['name'] + "</td><td class='rating'>" + data.rating + "</td><td></td></tr>";

		$("#preference_table").append(tds);
	}
}

function add_category_error(jqXHR, textStatus, errorThrown) {
    
    console.log("add category error");
	
	hide_add_category();
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
    
    $("#create_profile_status").html("Success!");
    $("#create_profile_status").css('backgroundColor', '#98FB98');
    $("#create_profile_status").slideDown(400);

}

function create_profile_error(jqXHR, textStatus, errorThrown){
    console.log("error! D:");
}
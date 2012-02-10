/*
 * @author Coral Peterson
 */

function display_login() {
    $("#login").slideDown(400);
}

function display_add_category() {
    $("#add_category").slideDown(400);
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
    $("#add_category").slideUp(400);
	
	if (data.update === 'updated') {
		var p = '#pref_' + data.cat['name'] + ' .rating';
		$(p).html(data.rating);
	} else {
		var tds = "<tr><td class='cat_name'>" + data.cat['name'] + "</td><td class='rating'>" + data.rating + "</td></tr>";

		$("#preference_table").append(tds);
	}
}

function add_category_error(jqXHR, textStatus, errorThrown) {
    
    console.log("add category error");

    $("#add_category").slideUp(400);
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
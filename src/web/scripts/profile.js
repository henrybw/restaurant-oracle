function display_login() {
    $("#login").slideDown(400);
}

function display_add_category() {
    $("#add_category").slideDown(400);
}



function add_category() {
 
    var category_data = $('input:input[name=category]').val();
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

    var tds = "<tr><td>" + data.cat + "</td><td>" + data.rating + "</td></tr>";


    $("#preference_table").append(tds);
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
}

function create_profile_error(jqXHR, textStatus, errorThrown){
    console.log("error! D:");
}
function display_login() {
    $("#login").slideDown(400);
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
	dataType: 'text',
	error: add_category_error

    });
		
    
}

function add_category_success(data, textStatus, jqXHR) {
    console.log("add category success!");
}

function add_category_error(jqXHR, textStatus, errorThrown) {
    
    console.log("add category error");
}
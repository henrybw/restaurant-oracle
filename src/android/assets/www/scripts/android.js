/*
 * Stuff for Android-specific functionality. This should be included in all
 * of the HTML pages used to create the Android UI.
 *
 * Differences in the Android app:
 *
 *   - Similar to most native phone apps, 
 *
 * @author Henry Baba-Weiss <htw@cs.washington.edu>
 */

var LOCAL_BASE_URL = 'file:///android_asset/www/';
var EXTERNAL_BASE_URL = 'http://cubist.cs.washington.edu/~htw/';
var USER_ID = 'ra_login_email';

// Listen for Android startup routine
document.addEventListener('deviceready', androidInit, false);

// General initialization
$(function() {
	/*/////
	$.get(EXTERNAL_BASE_URL + 'services/logout.php');
	localStorage.removeItem(USER_ID);
	/////
	$.ajax({
		type: "GET",
		url: "http://cubist.cs.washington.edu/~htw/services/current_user.php",
		dataType: "json",
		success: function(data, textStatus, jqXHR) { alert('success'); },
		error: function(jqXHR, textStatus, errorThrown) { alert('error'); }
    });
	
	setup_session();*/
});

//
// Android event handlers
//

// Android-specific initialization
function androidInit() {
	// Register Android event handlers
	$(document).on('resume', setup_session);  // Make sure user is still logged in on resume
	
	// Handlers for Android button behavior
	$(document).on('searchbutton', search);
	$(document).on('menubutton', show_menu);
	
	
	
	var ajax = new XMLHttpRequest();
     ajax.open("GET","http://cubist.cs.washington.edu/~htw/services/current_user.php",true);
     ajax.send();
 
     ajax.onreadystatechange=function(){
          if(ajax.readyState==4 && (ajax.status==200)){
               alert('it works! ' + ajax.responseText);
          }
     }
}

//
// Android button handlers
//

function search() {
	window.location = 'search.html';
}

function show_menu() {
	// TODO: this should actually bring up a native Android menu
	alert('Not implemented yet!');
}

//
// Session management
// 

// Sets up session stuff. If the user isn't logged in, redirects
// to the login page. Otherwise, this will ensure that the user
// is logged in on the server-side as well.
function setup_session() {
	if (window.location != LOCAL_BASE_URL + 'login.html') {
		var login_email = localStorage.getItem(USER_ID);

		if (login_email) {
			server_login(login_email);
		} else {
			window.location = LOCAL_BASE_URL + 'login.html';
		}
	}
}

function server_login(login_email) {
	$.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/profile.php",
		data: {ra_login_email: login_email},
		dataType: "json",
		success: login_success,
		error: login_error
    });
}

function login_success(data, textStatus, jqXHR) {
    alert('success ' + data);
}

function login_error(jqXHR, textStatus, errorThrown) {
	alert('error: ' + jqXHR.status + ', ' + jqXHR.responseText);
}
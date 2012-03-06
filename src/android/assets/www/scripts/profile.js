/*
 * @author Coral Peterson
 *
 * Tweaked for Android by Henry Baba-Weiss <htw@cs.washington.edu>
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

// [ANDROID] Modified to submit the form via AJAX instead of via normal form submission.
function login_submit() {
	// On Android we want to submit all via AJAX and then return the user to the main screen
	var login_email = $('input:input[name=ra_login_email]').val();
	login(login_email);
}

function joinGroup(gid) {
	console.log("joinGroup called with gid: " + gid);	
	
	var formData = {groupId : gid};

    $.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/join_group.php",
		data: formData,
		success: joinGroupSuccess,
		dataType: 'json',
		error: joinGroupError
    });	
}

function joinGroupSuccess(data, textStatus, jqXHR) {
	console.log("join group success!");
	
	//find td with id joinGroup_<gid>
	if (data && data.success === true) {
		$("#joinGroup_" + data.groupId).empty().html("Joined!");
	}
}

function joinGroupError(jqXHR, textStatus, errorThrown) {
	console.log("join group error");
}

function findGroup() {
	var nameData = $('input:input[name=gname]').val();
	var formData = {name : nameData};
	
	$.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/find_group.php",
		data: formData,
		success: findGroupSuccess,
		dataType: 'json',
		error: findGroupError
	});
}

function findGroupSuccess(data, textStatus, jqXHR) {
	console.log("find group success!");
	
	var groupList = data.groups;
	
	// $('#groupList').empty();
	var table = $('<table id="joinGroupList"></table>');
	
	$('#groupList').empty().append(table);	
		
	if (groupList.length > 0) {
		table.append($(	'<tr>' +
						'	<th class="corner"><div class="left"></div></th>' +
						'	<th class="top">Group name</th>' +
						'	<th class="top">Join</th>' +
						'	<th class="corner"><div class="right"></div></th>' +
						'</tr>'
		));
		
		var even = true;
		
		$.each(groupList, function() {
			var row = $('<tr></tr>').addClass(even ? 'even' : 'odd');
			
			var nameCell = $('<td></td>').html(this.name);
			var joinCell = $('<td id="joinGroup_' + this.gid + '"></td>')
				.append($('<a class="androidbutton button submit" ' +
					'onclick="joinGroup(' + this.gid + ');">Join</a>'));
			
			
			row.append(
				$('<td></td>'),
				nameCell,
				joinCell,
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
						'</tr>'
		));
	} else {
		var noGroupsMsg = $('<p class="message">No groups found</p>');
		$('#groupList').append(noGroupsMsg);
	}
}

function findGroupError(jqXHR, textStatus, errorThrown) {
	console.log("find group error!");
}

function createGroup() {
	var nameData = $('input:input[name=gname]').val();
	
	var formData = {name : nameData};
	
	$.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/create_group.php",
		data: formData,
		success: createGroupSuccess,
		dataType: 'json',
		error: createGroupError
	});

}

function createGroupSuccess(data, textStatus, jqXHR) {
	console.log("create group success!");
	
	window.location.href = LOCAL_BASE_URL + "my_groups.html";
}

function createGroupError(jqXHR, textStatus, errorThrown) {
	console.log("create group error. ):");
}

function create_profile() {
    var email_data = $('input:input[name=email]').val();
    var fname_data = $('input:input[name=fname]').val();
    var lname_data = $('input:input[name=lname]').val();
	
	// [ANDROID] Save registered email so we can log in server side as well
	localStorage.setItem(USER_EMAIL, email_data);

    var blob = {email: email_data, fname: fname_data, lname: lname_data };

    $.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/create_profile.php",
		data: blob,
		success: create_profile_success,
		dataType: 'json',
		error: create_profile_error

    });
}

function create_profile_success(data, textStatus, jqXHR) {
    console.log("success!");
	
	// [ANDROID] Instead of going to the profile page, creating a profile
	// transports the user to the main page
	login(localStorage.getItem(USER_EMAIL));
}

function create_profile_error(jqXHR, textStatus, errorThrown){
    console.log("error! D:");
	
	// [ANDROID] If there was an error, clear user data
	localStorage.removeItem(USER_EMAIL);
	localStorage.removeItem(USER_ID);
}
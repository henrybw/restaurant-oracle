/*
 * @author Coral Peterson, Henry Baba-Weiss
 */


var position = {latitude: undefined, longitude: undefined};

// Calculates geolocation coordinates once per page
$(function() {
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

function joinGroup(gid) {
	console.log("joinGroup called with gid: " + gid);	
	
	var formData = {groupId : gid};

    $.ajax({
		type: "POST",
		url: "services/join_group.php",
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

function leaveGroup(gid) {
	console.log("leaveGroup called with gid: " + gid);
	
	var formData = {groupId: gid};
	
	$.ajax({
		type: "POST",
		url: "services/leave_group.php",
		data: formData,
		success: leaveGroupSuccess,
		dataType: 'json',
		error: leaveGroupError
	});
}

function leaveGroupSuccess(data, textStatus, jqXHR) {
	console.log("leave group success");
	
	var row = $("#groups_table tr#group_" + data.gid);
	row.find(".button").remove();
	row.addClass("removed");
}

function leaveGroupError(jqXHR, textStatus, errorThrown) {
	console.log("leave group error");
}

function add_category() {
 
    var categoryData = $('select[name=category]').val();
    var ratingData = $('input:radio[name=cat_rating]:checked').val();
    
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
		$("#pref_" + data.cat['name']).addClass("new_row");
	} else {
		var tds =	"<tr class='category_row new_row'>" +
						"<td></td>" +
						"<td class='cat_name'>" + data.cat['name'] + "</td>" +
						"<td class='rating'>" + data.rating + "</td>" +
						"<td></td>" +
					"</tr>";
	
	
		var allFoods = $(".category_row");
		var idToFind = "pref_" + data.cat['name'];
		var insertBefore = $("#preferenceTable tr.bottom")[0];
		
		var insertRow = findInsertBeforeRow(allFoods, idToFind);
		if (insertRow) {
			insertBefore = insertRow;
		}
		
		$(tds).insertBefore(insertBefore);
	
	}
}

function addCategoryError(jqXHR, textStatus, errorThrown) {
    console.log("add category error");
	toggleDropdown("addCategory");
}

function addFood() {
	
	var foodData = $('select[name=food]').val();
	var ratingData = $('input:radio[name=food_rating]:checked').val();
	
	var formData = {fid: foodData, rating: ratingData};
	
	$.ajax({
		type: "GET",
		url: "services/profile_food_update.php",
		data: formData,
		success: addFoodSuccess,
		dataType: 'json',
		error: addFoodError
	});
}

function addFoodSuccess(data, textStatus, jqXHR) {
	console.log("add food success");
	toggleDropdown("addFood");
	
	if (data.update === 'updated') {
		var p = '#food_' + data.food + ' .food_rating';
		$(p).html(data.rating);
		$("#food_" + data.food).addClass("new_row");
	} else {
		var tds =	"<tr class='food_row new_row' id='food_" + data.food + "'>" +
						"<td></td>" +
						"<td class='food_name'>" + data.food + "</td>" +
						"<td class='food_rating'>" + data.rating + "</td>" +
						"<td></td>" +
					"</tr>";

		
		var allFoods = $(".food_row");
		var idToFind = "food_" + data.food;
		var insertBefore = $("#foodTable tr.bottom")[0];
		
		var insertRow = findInsertBeforeRow(allFoods, idToFind);
		if (insertRow) {
			insertBefore = insertRow;
		}
		
		$(tds).insertBefore(insertBefore);
	}
}

function findInsertBeforeRow(toSearch, idToFind) {
	var elem = null;
	$.each(toSearch, function(index, element) {
		if (idToFind < element.id){
			elem = element;
			return false;
		}
	});
	return elem;
}

function addFoodError(jqXHR, textStatus, errorThrown) {
	console.log("add food error: " + errorThrown);
	toggleDropdown("addFood");
}

function findGroup() {
	var nameData = $('input:input[name=gname]').val();
	var formData = {name : nameData};
	
	$.ajax({
		type: "POST",
		url: "services/find_group.php",
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
	var table = $('<table></table>');
	
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
				.append($('<a class="button submit" ' +
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
		url: "services/create_group.php",
		data: formData,
		success: createGroupSuccess,
		dataType: 'json',
		error: createGroupError
	});

}

function createGroupSuccess(data, textStatus, jqXHR) {
	console.log("create group success!");
	
	window.location.href = "my_groups.php";
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

function getSearchResults(uid) {
	var isGroupSearch = $("input:radio[name='searchType']:checked").val() === "group";
	var guid = isGroupSearch ? $("select[name='group']").val() : uid;
	
	var maxDist = parseInt($("input[id='distance']").val());
	var priceRangeCode = parseInt($("select[name='priceRange']").val());
	
	
	
	var formData = {
		isGroup: isGroupSearch,
		id: guid,
		latitude: position.latitude,
		longitude: position.longitude,
		currentTime: new Date().getTime(),
		maxDistance: maxDist,
		maxPrice: priceRangeCode
	};
	
	//alert("isGroupSearch: " + isGroupSearch + "\nGroup / user id: " + guid);

	$.ajax({
		type: "GET",
		url: "services/results.php",
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
		
		var name = $('<td></td>').html(this.name);
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
	console.log("search results error: " + errorThrown);
}
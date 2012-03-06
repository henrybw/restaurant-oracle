
/*
 * @author Henry 
 */
$(function() {
	// Grab data from the web service and populate the fields of the page
	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/my_groups.php",
		dataType: "json",
		success: createGroupTable,
		error: connection_error
	});
	
	// TODO: display loading overlay
});

/*
 * @author Coral
 */
function createGroupTable(groupList) {
	
	// $('#groupList').empty();
	var table = $('<table></table>').attr("id", "groups_table");
	
	$('#groupList').empty().append(table);	
		
	
	table.append($(	'<tr>' +
					'	<th class="corner"><div class="left"></div></th>' +
					'	<th class="top">My Groups</th>' +
					'	<th class="top"></th>' +
					'	<th class="corner"><div class="right"></div></th>' +
					'</tr>'
	));
	
	var even = true;
	
	$.each(groupList, function() {
		var row = $('<tr></tr>').addClass(even ? 'even' : 'odd')
			.attr("id", "group_" + this.gid);
		
		var nameCell = $('<td></td>').html(this.name);
		var leaveCell = $('<td></td>');
		
		var leaveLink = $('<a class="button submit" onclick="leaveGroup(' +
			this.gid + ')"></a>').html("X");
		leaveCell.append(leaveLink);
		
		
		row.append(
			$('<td></td>'),
			nameCell,
			leaveCell,
			$('<td></td>'));
		table.append(row);
		
		even = !even;
		// row.append("<td>test</td>");
	});
	
	table.append($(	'<tr class="bottom">' +
					'	<td></td>' +
					'	<td><div></div></td>' +
					'	<td></td>' +
					'	<td></td>' +
					'</tr>'
	));

}

function leaveGroup(gid) {
	console.log("leaveGroup called with gid: " + gid);
	
	var formData = {groupId: gid};
	
	$.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/leave_group.php",
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
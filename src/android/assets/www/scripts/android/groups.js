
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
	var table = $('<table></table>');
	
	$('#groupList').empty().append(table);	
		
	
	table.append($(	'<tr>' +
					'	<th class="corner"><div class="left"></div></th>' +
					'	<th class="top">My Groups</th>' +
					'	<th class="corner"><div class="right"></div></th>' +
					'</tr>'
	));
	
	var even = true;
	
	$.each(groupList, function() {
		var row = $('<tr></tr>').addClass(even ? 'even' : 'odd');
		
		var nameCell = $('<td></td>').html(this.name);
		
		row.append(
			$('<td></td>'),
			nameCell,
			$('<td></td>'));
		table.append(row);
		
		even = !even;
		// row.append("<td>test</td>");
	});
	
	table.append($(	'<tr class="bottom">' +
					'	<td></td>' +
					'	<td><div></div></td>' +
					'	<td></td>' +
					'</tr>'
	));

}
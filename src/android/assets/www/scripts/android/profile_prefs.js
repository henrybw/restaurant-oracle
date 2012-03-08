$(function() {
	// Grab data from the web service and populate the fields of the page
	toggleLoadingOverlay(true);
	
	$.ajax({
		type: "GET",
		url: EXTERNAL_BASE_URL + "services/profile_prefs.php",
		dataType: "json",
		success: populateProfilePrefs,
		error: connectionError
	});	
});


function populateProfilePrefs(data, textStatus, jqXHR) {

	// The drop-down category menu for preferences
	var categories = data.categories;
	var userCategories = data.user_prefs;
	var select = $("select[name=category]");
	
	$.each(categories, function() {
		select.append($(
			'<option value="' + this.cat_id + '">' +
				this.name +
			'</option>'
		));
	});
	
	// The drop-down category menu for foods
	var foods = data.foods;
	var userFoods = data.food_prefs;
	var foodSelect = $("select[name=food]");
	
	$.each(foods, function() {
		foodSelect.append($(
			'<option value="' + this.fid + '">' +
				this.food +
			'</option>'
		));
	});
	
	
	// The preferences the user has already entred	
	var even = true;
	var table = $("#preferenceTable");
	
	$.each(userCategories, function() {
		var row = $('<tr id="pref_' + this.name + '"></tr>')
			.addClass(even ? 'even' : 'odd').addClass('category_row');
		
		var nameCell = $('<td></td>').html(this.name);
		var ratingCell = $('<td></td>').html(this.rating).addClass('center');
		
		row.append(
			$('<td></td>'),
			nameCell,
			ratingCell,
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
	
	
	// The foods the user has already entered
	even = true;
	table = $("#foodTable");
	
	$.each(userFoods, function() {
		var row = $('<tr id="food_' + this.name + '"></tr>')
			.addClass(even ? 'even' : 'odd').addClass('food_row');
		
		var nameCell = $('<td></td>').html(this.name);
		var ratingCell = $('<td></td>').html(this.rating).addClass('center');
		
		row.append(
			$('<td></td>'),
			nameCell,
			ratingCell,
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

	toggleLoadingOverlay(false);
}


function connectionError(jqXHR, textStatus, errorThrown) {
	alert("An error occurred: " + errorThrown);
	toggleLoadingOverlay(false);
}

function add_category() {
 
    var categoryData = $('select[name=category]').val();
    var ratingData = $('input:radio[name=rating]:checked').val();
    
    var formData = {category : categoryData, rating: ratingData};


    $.ajax({
		type: "POST",
		url: EXTERNAL_BASE_URL + "services/profile_prefs_update.php",
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
	
	
		var allCats = $(".category_row");
		var idToFind = "pref_" + data.cat['name'];
		var insertBefore = $("#preferenceTable tr.bottom")[0];
		
		var insertRow = findInsertBeforeRow(allCats, idToFind);
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
		url: EXTERNAL_BASE_URL + "services/profile_food_update.php",
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
		console.log("Looking at element: " + element.id);
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
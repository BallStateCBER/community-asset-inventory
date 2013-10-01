function withinViewport(el) {
	var elOffset = $(el).cumulativeOffset(el);
	vpOffset = document.viewport.getScrollOffsets();
	elDim = $(el).getDimensions();
	vpDim = document.viewport.getDimensions();
	if (elOffset[1] + elDim.height < vpOffset[1] || elOffset[1] > vpOffset[1] + vpDim.height ||
		elOffset[0] + elDim.width < vpOffset[0]  || elOffset[0] > vpOffset[0] + vpDim.width) {
		return false;
	}
	return true;
}

// county is simplified county name
function showFullReport(county) {
	// Select the same county in the dropdown menu and remove blank options
	var options = $('#select_county option');
	var len = options.length;
	for (var i = 0; i < len; i++) {
		if (options[i].value == county) {
			options[i].selected = true;
		}
	}
	
	// Remove blank options
	for (var i = 0; i < len; i++) {
		if (options[i].value == '') {
			$(options[i]).remove();
		}
	}
	
	removeCategoryHighlight();
	
	if (! county) {
		county = $('#select_county').val();
	}
	window.location.hash = county+'_county';
	
	// Display the full report
	$('#content').load('/reports/county/'+county);
}

function removeCategoryHighlight() {
	$('#categories .selected').removeClass('selected');
}

/* This occasionally produces an 'element.parentNode is null' error if
 * the user clicks on a county (invoking showFullReport()) and then moves
 * the cursor (creating another tooltip) before the full report loads. */
function removeTooltips() {
	$('.tooltip').remove();
}

function selectCategoryMode(mode) {
	if (mode == 'map') {
		$('#category_report .map_wrapper').first().show();
		$('#show_map').addClass('selected');
		$('#category_report .table_wrapper').first().hide();
		$('#show_table').removeClass('selected');
	} else if (mode == 'table') {
		$('#category_report .map_wrapper').first().hide();
		$('#show_map').removeClass('selected');
		$('#category_report .table_wrapper').first().show();
		$('#show_table').addClass('selected');
	}
}

var setupMap = function() {
	$('#county_paths path').each(function() {
		$(this).qtip({
			content: $('#'+this.id+'_details').remove(),
			solo: true,
			effect: false,
			show: {delay: 0, effect: false},
			hide: {delay: 0, effect: false},
			position: {
				my: 'bottom center',
				at: 'top center',
				target: 'mouse',
				adjust: {
					y: -20,
					mouse: true
				}
			}
		});
		this.addEventListener('click', function(evt) {
			showFullReport(this.id.replace('in_map_', ''));
		});
	});
	// Hide county info in sidebar when cursor leaves state
	$('#counties').mouseout(function(event) {
		$('#county_tooltips > div').hide();
	});
};

function showMap(slug) {
	removeCategoryHighlight();
	$('#showmap_'+slug).closest('li').addClass('selected');
	$('#content').load('/reports/category/'+slug);
};

function showPage(page) {
	removeCategoryHighlight();
	$('#content').load('/pages/'+page);
}

function processHash(categories, counties) {
	if (! window.location.hash) {
		return;
	}
	
	var hash = window.location.hash.substring(1);
	if (categories.indexOf(hash) != -1) {
		return showMap(hash);
	}
	
	if (hash.indexOf('_county') != -1) {
		var county_name = hash.replace('_county', '');
		if (counties.indexOf(county_name) != -1) {
			return showFullReport(county_name);
		}
	}
	
	switch (hash) {
		case 'sources':
		case 'faq':
		case 'credits':
		case 'home':
			showPage(hash);
			break;
		default:
			showMap('People');
	}
}
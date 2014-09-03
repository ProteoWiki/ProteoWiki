/*global $ document jQuery console mw window */

$('.createfromfile-link').live('click', function() {
			
	console.log("Create from File");
	
	var param = {};
			
	param.file = $(this).attr('data-file');
	param.template = $(this).attr('data-template');
	param.title = $(this).attr('data-title');
	param.userparam = $(this).attr('data-userparam');
	param.delimiter="\t";
	param.enclosure='"';
	param.start = $(this).attr('data-start');
	param.username = $(this).attr('data-username');
	param.extrainfo = $(this).attr('data-extrainfo');

	// TODO: This should be changed rather to POST
	$.get( mw.util.wikiScript(), {
		format: 'json',
		action: 'ajax',
		rs: 'CreateFromFile::createfromfileJS',
		rsargs: [param.file, param.template, param.title, param.delimiter, param.enclosure, param.userparam, param.start, param.username, param.extrainfo] // becomes &rsargs[]=arg1&rsargs[]=arg2...
	}, function(data) {
		// var jsonobj = jQuery.parseJSON(data);
		alert("Samples are being created");
		window.setTimeout('location.reload()', 1500);
	});
});

$('.createfromSpread-link').live('click', function() {
				
	var param = {};

	param.selector = $(this).attr('data-selector');
	param.template = $(this).attr('data-template');
	param.title = $(this).attr('data-title');
	param.userparam = $(this).attr('data-userparam');
	param.delimiter="\t";
	param.enclosure='"';
	param.start = $(this).attr('data-start');
	param.username = $(this).attr('data-username');
	param.extrainfo = $(this).attr('data-extrainfo');
	
	//Let's get data from selector
	var textstr = convertData2str( $( param.selector ).handsontable( 'getData' ) );

	// TODO: This definetily should be changed to POST
	//$.get( mw.util.wikiScript(), {
	//	format: 'json',
	//	action: 'ajax',
	//	rs: 'CreateFromFile::createfromSpreadJS',
	//	rsargs: [textstr, param.template, param.title, param.delimiter, param.enclosure, param.userparam, param.start, param.username, param.extrainfo] // becomes &rsargs[]=arg1&rsargs[]=arg2...
	//}, function(data) {
	//	// console.log(data);
	//	// var jsonobj = jQuery.parseJSON(data);
	//	alert("Samples are being created");
	//	window.setTimeout('location.reload()', 1500);
	//});
	param.textstr = textstr;

	$.ajax({
			// request type ( GET or POST )
		type: "POST",
	 
			// the URL to which the request is sent
		url: "/w/index.php?format=json&action=ajax&rs=CreateFromFile%3A%3AcreatefromSpreadJS2",
	 
			// data to be sent to the server
		data: param,
	 
			// The type of data that you're expecting back from the server
		dataType: 'json',
	 
			// Function to be called if the request succeeds
		success: function( jsondata ){
			console.log( jsondata );
		}
	});

});


/** Load SpreadSheet **/
$(document).ready( function() {
	
	var readonly = false;
	var extrarows = 5;

	// If no links, we hide data
	if ( $('.createspread-link').length === 0 ) {
		$('.createspread-data').hide();
		readonly = true;
		extrarows = 0;
	}

	$('.createspread-data').each( function() {

		var data = [];

		var text = $(this).children('pre').text();
		var rows = text.split("\n");
	
		for ( var i = 0; i< rows.length; i++ ) {
			var row = rows[i].split("\t");
			data.push( row );
		}

		// We put custom cols
		var cols = true;
		var colstr = $(this).attr("data-cols");
		var uniqcolstr = $(this).attr("data-uniqcols");

		// Unique columns restriction
		var uniqcols = [];
		if ( typeof uniqcolstr !== 'undefined' && uniqcolstr !== false ) {
			uniqcols = uniqcolstr.split(";");
		}

		if ( typeof colstr !== 'undefined' && colstr !== false ) {
			cols = colstr.split(";");
		}

		if (  Object.prototype.toString.call( cols ) === '[object Array]' && cols.length < 1 ) {
			cols = true;
		}
 
		// We'll iterate over here
		var valsuniq = {};
		var lenguniq = 0;

		if ( Object.prototype.toString.call( cols ) === '[object Array]' && cols.length > 0 ) {

			for ( var u = 0; u < uniqcols.length; u++ ) {

				for ( var c = 0; c < cols.length; c++ ) {
					if ( uniqcols[u] === cols[c] ) {
						valsuniq[uniqcols[u]] = getDataCol( data, c );
						lenguniq = valsuniq[uniqcols[u]].length;
					}
				}
			}
		}

		if ( lenguniq > 0 ) {

			var queryRows = [];

			for ( var l = 0; l < lenguniq; l++ ) {
				var queryRow = {};

				for ( var key in valsuniq ) {
					if ( valsuniq.hasOwnProperty(key) ) {
						queryRow[key] = valsuniq[key][l];
					}
				}

				queryRows.push( queryRow );
			}

			// We send restriction to be search
			semanticSearchJS( queryRows );
		}



		$(this).parent().children('.createspread-show').handsontable({
			data: data,
			colHeaders: cols,
			contextMenu: true,
			minSpareRows: extrarows,
			autoWrapRow: true,
			readOnly: readonly,
			cells: function (row, col, prop) {
				// We make first row as non readable
				var cellProperties = {};
				if ( row === 0 ) {
					cellProperties.readOnly = true;
				}
				return cellProperties;
			}
		});

	});

});

$('.createspread-link').live('click', function() {

	$(this).parent().children( ".createspread-saved" ).remove();
	$(this).parent().append( "<p class='createspread-saved'>Data confirmed. Please save the form below to keep it.</p>" );

	var target = $(this).attr( 'data-target' );
	var $container = $(this).parent().children( ".createspread-show" );
	
	// We process everything at one for avoiding async problem;
	$( "."+target+" > input" ).val( convertData2str( $container.handsontable( 'getData' ) ) );

});


$( ".createspread-show" ).each(function(){

	$(this).handsontable('getInstance').addHook('afterChange', function( changes, source ) {
		if ( source === "edit" || source === "autofill" || source === "paste" ) {

			var $parent = $(this.rootElement).parent();

			$parent.children( ".createspread-saved" ).remove();
			$parent.append( "<p class='createspread-saved'>Data saved.</p>" );
			var target = $parent.children( ".createspread-link" ).attr( 'data-target' );

			if ( target ) {
	
				var $container = $(this.rootElement);
	
				// We process everything at one for avoiding async problem;
				$( "."+target+" > input" ).val( convertData2str( $container.handsontable( 'getData' ) ) );

			}
		}
	});

});


/** @param Array
  * return string
**/
function convertData2str ( data ) {
	
	var str = "";
	var newArr = [];
	if ( data.length > 0 ) {

		// We put \\n or \\t for ensuring proper conversion afterwards
		for ( var i = 0;  i < data.length; i++ ) {
			var rowstr = data[i].join("\\t");
			newArr.push( rowstr );
		}
		str = newArr.join("\\n");
	}

	return str;
}


/** @matrix Array
  * @col int
  * return Array
**/

function getDataCol(matrix, col){
	var column = [];
	for(var i=0; i<matrix.length; i++){
		column.push(matrix[i][col]);
	}
	return column;
}

/** @matrix Array
  * return ...
**/
function semanticSearchJS( rows ) {

	$.get( mw.util.wikiScript(), {
		format: 'json',
		action: 'ajax',
		rs: 'CreateFromFileSMW::searchJS',
		rsargs: [rows] // becomes &rsargs[]=arg1&rsargs[]=arg2...
	}, function(data) {
		var jsonobj = jQuery.parseJSON(data);
		if ( jsonobj.length > 0 ) {

			$(".createfromSpread-link").hide();
			$(".createspread-view").append("<h3>Already exisiting entries</h3>");

			$.each( jsonobj, function( index, container ) {

				var strArr = [];
				for ( var key in container ) {
					if ( container.hasOwnProperty(key) ) {
						strArr.push( "<span><strong>"+key+"</strong>: "+container[key]+"</span>");
					}
				}
				$(".createspread-view").append("<p class='duplicated'>"+strArr.join("&nbsp;")+"</p>");
			});
			
		}

	});
}


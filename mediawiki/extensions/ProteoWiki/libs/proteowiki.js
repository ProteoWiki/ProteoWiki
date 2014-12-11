/*global $ document jQuery console mw window wgScriptPath alert location */

/** Load SpreadSheet **/
$(document).ready( function() {

	var readonly = true;
	var extrarows = 0;

	var numdata = 0;

	$('.proteowikiconf').each( function() {

		var celldata = [];

		var text = $(this).text();
		var lines = text.split("\n");
	
		for ( var i = 0; i < lines.length; i++ ) {
			if ( lines[i] !== "" ) {
				var row = lines[i].split(",");
				celldata.push( row );
			}
		}

		var divval = "proteowikiconf-"+numdata;
		$(this).parent().append("<div id='"+divval+"'>");

		$(this).hide();

		$('#'+divval).handsontable({
			data: celldata,
			readOnly: readonly,
			minSpareRows: extrarows,
			contextMenu: true
		});

		numdata = numdata +1 ;

	});
});
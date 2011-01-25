// Run the script on DOM ready:
$(function(){
 $('table').visualize({type: 'pie', height: '300px', width: screen.width/1.2 +'px'});
	$('table').visualize({type: 'bar', width: screen.width/1.2 + 'px'});
	$('table').visualize({type: 'area', width: screen.width/1.2 +'px'});
	$('table').visualize({type: 'line', width: screen.width/1.2 +'px'});
});
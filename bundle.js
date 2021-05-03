
(function(l, r) { if (l.getElementById('livereloadscript')) return; r = l.createElement('script'); r.async = 1; r.src = '//' + (window.location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1'; r.id = 'livereloadscript'; l.getElementsByTagName('head')[0].appendChild(r) })(window.document);
(function () {
	'use strict';

	document.addEventListener("DOMContentLoaded", function () {
	  var tile_container = document.getElementById("#response"); // console.log(tile_container);

	  var tiles = document.querySelectorAll("#response .item"); // console.log(tiles);
	}); // jQuery(function($){
	// 	$('#filter').submit(function(){
	// 		var filter = $('#filter');
	// 		$.ajax({
	// 			url:filter.attr('action'),
	// 			data:filter.serialize(), // form data
	// 			type:filter.attr('method'), // POST
	// 			beforeSend:function(xhr){
	// 				filter.find('button').text('Processing...'); // changing the button label
	// 			},
	// 			success:function(data){
	// 				console.log(data);
	// 				filter.find('button').text('Apply filter'); // changing the button label back
	// 				$('#response').html(data); // insert data
	// 			}
	// 		});
	// 		return false;
	// 	});
	// });

}());

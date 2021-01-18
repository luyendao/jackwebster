
jQuery(function($){

	function nextPageHandler(){
		$.ajax({
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'loadmorebutton', // the parameter for admin-ajax.php
				'query': misha_loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : misha_loadmore_params.current_page, // current page
				'first_page' : misha_loadmore_params.first_page
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				// $('#misha_loadmore').text('Loading...'); // some type of preloader
			},
			success : function( posts ){
				if( posts ) {
	
					// $('#misha_loadmore').text( 'More posts' );
					// $('#misha_loadmore').remove();
					// $('#misha_pagination').before(data).remove();s
					$('#results_container').html( posts ); // insert new posts
					misha_loadmore_params.current_page++;
	
					
	
					// if ( misha_loadmore_params.current_page == misha_loadmore_params.max_page ) {
					// 	$('#misha_loadmore').hide(); // if last page, HIDE the button
					// }
	
				} else {
					// $('#misha_loadmore').hide(); // if no data, HIDE the button as well
				}
			}
		});
		return false;
	}

	function previousPageHandler(){
		$.ajax({
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'previousbutton', // the parameter for admin-ajax.php
				'query': misha_loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : misha_loadmore_params.current_page, // current page
				'first_page' : misha_loadmore_params.first_page
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				// $('#misha_loadmore').text('Loading...'); // some type of preloader
			},
			success : function( posts ){
				if( posts ) {
	
					// $('#misha_loadmore').text( 'More posts' );
					// $('#misha_loadmore').remove();
					// $('#misha_pagination').before(data).remove();
					$('#results_container').html( posts ); // insert new posts
					misha_loadmore_params.current_page--;
	
					
	
					// if ( misha_loadmore_params.current_page == misha_loadmore_params.max_page ) {
					// 	$('#misha_loadmore').hide(); // if last page, HIDE the button
					// }
	
				} else {
					// $('#misha_loadmore').hide(); // if no data, HIDE the button as well
				}
			}
		});
		return false;
	}

	function seekPageHandler(e){
		// Set loader to be visible (display: block)
		$.ajax({
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'seekbutton', // the parameter for admin-ajax.php
				'query': misha_loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : misha_loadmore_params.current_page, // current page
				'first_page' : misha_loadmore_params.first_page,
				'seek_page' : e.target.dataset.page
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				// $('#misha_loadmore').text('Loading...'); // some type of preloader
			},
			success : function( posts ){
				if( posts ) {
	
					// $('#misha_loadmore').text( 'More posts' );
					// $('#misha_loadmore').remove();
					// $('#misha_pagination').before(data).remove();
					$('#results_container').html( posts); // insert new posts
					misha_loadmore_params.current_page--;
	
					
	
					// if ( misha_loadmore_params.current_page == misha_loadmore_params.max_page ) {
					// 	$('#misha_loadmore').hide(); // if last page, HIDE the button
					// }
	
				} else {
					// $('#misha_loadmore').hide(); // if no data, HIDE the button as well
				}
			}
		});
		return false;
	}
 
	/*
	 * Load More
	 */
	// $('#misha_loadmore').click(function(){
	// $('body').on('click', '#misha_loadmore', nextPageHandler);
	$('body').on('click', '#next_selector', nextPageHandler);
	$('body').on('click', '#previous_selector', previousPageHandler);
	$('body').on('click', '.page_num_selector', seekPageHandler);
 
	/*
	 * Filter
	 */
	$('#misha_filters').submit(function(){
 
		$.ajax({
			url :  misha_loadmore_params.ajaxurl,
			data : $('#misha_filters').serialize(), // form data
			dataType : 'json', // this data type allows us to receive objects from the server
			type : 'POST',
			beforeSend : function(xhr){
				$('#misha_filters').find('button').text('Filtering...');
			},
			success : function( data ){
 
				// when filter applied:
				// set the current page to 1
				misha_loadmore_params.current_page = 1;
 
				// set the new query parameters
				misha_loadmore_params.posts = data.posts;
 
				// set the new max page parameter
				misha_loadmore_params.max_page = data.max_page;
 
				// change the button label back
				$('#misha_filters').find('button').text('Apply filter');
 
				// insert the posts to the container
				$('#results_container').html(data.content);
 
				// hide load more button, if there are not enough posts for the second page
				// if ( data.max_page < 2 ) {
				// 	$('#misha_loadmore').hide();
				// } else {
				// 	$('#misha_loadmore').show();
				// }
			}
		});
 
		// do not submit the form
		return false;
 
	});
 
});

jQuery(function($){
	$('#filter').submit(function(){
		var filter = $('#filter');
		$.ajax({
			url:filter.attr('action'),
			data:filter.serialize(), // form data
			type:filter.attr('method'), // POST
			beforeSend:function(xhr){
				filter.find('button').text('Processing...'); // changing the button label
			},
			success:function(data){
				console.log(data);
				filter.find('button').text('Apply filter'); // changing the button label back
				$('#response').html(data); // insert data
			}
		});
		return false;
	});
});
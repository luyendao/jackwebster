<?php

function jack_webster_enqueue_parent_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

function jack_webster_enqueue_scripts()
{
    wp_enqueue_script('custom-scripts', get_stylesheet_directory_uri() . '/bundle.js');
    wp_enqueue_style('custom-styles', get_stylesheet_directory_uri() . '/style.min.css');
}

add_action('wp_enqueue_scripts', 'jack_webster_enqueue_parent_styles');
add_action('wp_enqueue_scripts', 'jack_webster_enqueue_scripts');

function wpdocs_register_private_taxonomy() {
    $args = array(
        'label'        => __( 'Filter Year', 'textdomain' ),
        'public'       => true,
        'rewrite'      => false,
        'hierarchical' => true
    );
     
    register_taxonomy( 'filter_year', 'post', $args );
}
add_action( 'init', 'wpdocs_register_private_taxonomy', 0 );

add_action( 'wp_enqueue_scripts', 'misha_script_and_styles');
 
function misha_script_and_styles() {
	// absolutely need it, because we will get $wp_query->query_vars and $wp_query->max_num_pages from it.
	global $wp_query;
 
	// when you use wp_localize_script(), do not enqueue the target script immediately
	wp_register_script( 'misha_scripts', get_stylesheet_directory_uri() . '/src/js/filter.js', array('jquery') );
 
	// passing parameters here
	// actually the <script> tag will be created and the object "misha_loadmore_params" will be inside it 
	wp_localize_script( 'misha_scripts', 'misha_loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
        'max_page' => $wp_query->max_num_pages,
        'first_page' => get_pagenum_link(1),
        'seek_page' => 1
	) );
 
 	wp_enqueue_script( 'misha_scripts' );
}

add_action('wp_ajax_loadmorebutton', 'misha_loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmorebutton', 'misha_loadmore_ajax_handler');
 
function misha_loadmore_ajax_handler(){
	// prepare our arguments for the query
	$params = json_decode( stripslashes( $_POST['query'] ), true ); // query_posts() takes care of the necessary sanitization 
	$params['paged'] = $_POST['page'] + 1; // we need next page to be loaded
    $params['post_status'] = 'publish';

	// it is always better to use WP_Query but not here
	query_posts( $params );
 
    if( have_posts() ) :
        // run the loop
        ?><div id="response"><?php
        while( have_posts() ): the_post();

        ?>
                <a href="<?= get_permalink( $wp_query->post->ID )?>" id="filter_results">
                    <?= get_the_post_thumbnail( $wp_query->post->ID ) ?>
                    <?php switch (get_the_category()[0]->name) {
                        case 'Award Winner':
                            echo '<div class="filter_subtitles"><strong>Category </strong><span>' . get_field("category", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Name </strong><span>' . get_field("name", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Media Outlet </strong><span>' . get_field("media_outlet", $wp_query->post->ID) . '</span></div>';
                        break;
                        case 'Updates':
                            echo '<div class="filter_subtitles"><strong><span>' . get_field("title", $wp_query->post->ID) . '</span></strong></div>';
                            echo '<div class="filter_subtitles"><span>' . get_field("excerpt", $wp_query->post->ID) . '</span></div>';
                            echo get_the_date( 'j F, Y' );
                        break;
                    }?>
                </a>
                

        <?php endwhile;
            ?></div><?php

    misha_paginator( $_POST['first_page'] );
        
	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_previousbutton', 'previous_ajax_handler');
add_action('wp_ajax_nopriv_previousbutton', 'previous_ajax_handler');
 
function previous_ajax_handler(){
 
	// prepare our arguments for the query
	$params = json_decode( stripslashes( $_POST['query'] ), true ); // query_posts() takes care of the necessary sanitization 
	$params['paged'] = $_POST['page'] - 1; // we need next page to be loaded
    $params['post_status'] = 'publish';

	// it is always better to use WP_Query but not here
	query_posts( $params );
 
    if( have_posts() ) :
		// run the loop
        ?><div id="response"><?php
        while( have_posts() ): the_post();

        ?>
                <a href="<?= get_permalink( $wp_query->post->ID )?>" id="filter_results">
                    <?= get_the_post_thumbnail( $wp_query->post->ID ) ?>
                    <?php switch (get_the_category()[0]->name) {
                        case 'Award Winner':
                            echo '<div class="filter_subtitles"><strong>Category </strong><span>' . get_field("category", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Name </strong><span>' . get_field("name", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Media Outlet </strong><span>' . get_field("media_outlet", $wp_query->post->ID) . '</span></div>';
                        break;
                        case 'Updates':
                            echo '<div class="filter_subtitles"><strong><span>' . get_field("title", $wp_query->post->ID) . '</span></strong></div>';
                            echo '<div class="filter_subtitles"><span>' . get_field("excerpt", $wp_query->post->ID) . '</span></div>';
                            echo get_the_date( 'j F, Y' );
                        break;
                    }?>
                </a>

        <?php endwhile;
            ?></div><?php

        misha_paginator( $_POST['first_page'] );
        
	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_seekbutton', 'seek_ajax_handler');
add_action('wp_ajax_nopriv_seekbutton', 'seek_ajax_handler');
 
function seek_ajax_handler(){
 
	// prepare our arguments for the query
	$params = json_decode( stripslashes( $_POST['query'] ), true ); // query_posts() takes care of the necessary sanitization 
	$params['paged'] = $_POST['seek_page']; // we need next page to be loaded
    $params['post_status'] = 'publish';
 
	// it is always better to use WP_Query but not here
	query_posts( $params );
 
    if( have_posts() ) :
		// run the loop
        ?><div id="response"><?php
        while( have_posts() ): the_post();

        ?>
                <a href="<?= get_permalink( $wp_query->post->ID )?>" id="filter_results">
                    <?= get_the_post_thumbnail( $wp_query->post->ID ) ?>
                    <?php switch (get_the_category()[0]->name) {
                        case 'Award Winner':
                            echo '<div class="filter_subtitles"><strong>Category </strong><span>' . get_field("category", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Name </strong><span>' . get_field("name", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Media Outlet </strong><span>' . get_field("media_outlet", $wp_query->post->ID) . '</span></div>';
                        break;
                        case 'Updates':
                            echo '<div class="filter_subtitles"><strong><span>' . get_field("title", $wp_query->post->ID) . '</span></strong></div>';
                            echo '<div class="filter_subtitles"><span>' . get_field("excerpt", $wp_query->post->ID) . '</span></div>';
                            echo get_the_date( 'j F, Y' );
                        break;
                    }?>
                </a>

        <?php endwhile;
            ?></div><?php

        misha_paginator( $_POST['first_page'] );
        
	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}
 
 
add_action('wp_ajax_mishafilter', 'misha_filter_function'); 
add_action('wp_ajax_nopriv_mishafilter', 'misha_filter_function');
 
function misha_filter_function(){
 
	// $params = array(
    //     'posts_per_page' => 2,
    //     'orderby' => 'date', // we will sort posts by date
    //     'order'	=> 'ASC' // ASC or DESC
    // );
    
    // for taxonomies / categories
    if( isset( $_POST['yearfilter'] ) )
        $params = array(
            'posts_per_page' => 3,
            'post_type' => 'post',
            'orderby' => 'date', // we will sort posts by date
            'order'	=> 'ASC', // ASC or DESC
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'filter_year',
                    'field'    => 'id',
                    'terms'    => $_POST['yearfilter'],
                ),
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $_POST['cat'],
                ),
            ),
        );
 
 
	query_posts( $params );
 
    global $wp_query;
 
	if( have_posts() ) :
 
         ob_start(); // start buffering because we do not need to print the posts now
         
         ?><div id="response"><?php
        while( have_posts() ): the_post();

        ?>
                <a href="<?= get_permalink( $wp_query->post->ID )?>" id="filter_results">
                    <?= get_the_post_thumbnail( $wp_query->post->ID ) ?>
                    <?php switch (get_the_category()[0]->name) {
                        case 'Award Winner':
                            echo '<div class="filter_subtitles"><strong>Category </strong><span>' . get_field("category", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Name </strong><span>' . get_field("name", $wp_query->post->ID) . '</span></div>';
                            echo '<div class="filter_subtitles"><strong>Media Outlet </strong><span>' . get_field("media_outlet", $wp_query->post->ID) . '</span></div>';
                        break;
                        case 'Updates':
                            echo '<div class="filter_subtitles"><strong><span>' . get_field("title", $wp_query->post->ID) . '</span></strong></div>';
                            echo '<div class="filter_subtitles"><span>' . get_field("excerpt", $wp_query->post->ID) . '</span></div>';
                            echo get_the_date( 'j F, Y' );
                        break;
                    }?>
                </a>

        <?php endwhile;
            ?></div><?php

        misha_paginator( get_pagenum_link() );

 		$posts_html = ob_get_contents(); // we pass the posts to variable
   		ob_end_clean(); // clear the buffer
	else:
		$posts_html = '<p>Nothing found for your criteria.</p>';
	endif;
 
	// no wp_reset_query() required
 
 	echo json_encode( array(
		'posts' => json_encode( $wp_query->query_vars ),
		'max_page' => $wp_query->max_num_pages,
		'found_posts' => $wp_query->found_posts,
		'content' => $posts_html
	) );
 
	die();
}


function misha_paginator( $first_page_url ){
 
	// the function works only with $wp_query that's why we must use query_posts() instead of WP_Query()
	global $wp_query;
 
	// remove the trailing slash if necessary
	$first_page_url = untrailingslashit( $first_page_url );
 
 
	// it is time to separate our URL from search query
	$first_page_url_exploded = array(); // set it to empty array
	$first_page_url_exploded = explode("/?", $first_page_url);
	// by default a search query is empty
	$search_query = '';
	// if the second array element exists
	if( isset( $first_page_url_exploded[1] ) ) {
		$search_query = "/?" . $first_page_url_exploded[1];
		$first_page_url = $first_page_url_exploded[0];
	}
 
	// get parameters from $wp_query object
	// how much posts to display per page (DO NOT SET CUSTOM VALUE HERE!!!)
	$posts_per_page = (int) $wp_query->query_vars['posts_per_page'];
	// current page
	$current_page = (int) $wp_query->query_vars['paged'];
	// the overall amount of pages
	$max_page = $wp_query->max_num_pages;
    // we don't have to display pagination or load more button in this case

    if( $max_page <= 1 ) return;
 
	// set the current page to 1 if not exists
	if( empty( $current_page ) || $current_page == 0) $current_page = 1;
 
	// you can play with this parameter - how much links to display in pagination
	$links_in_the_middle = 4;
	$links_in_the_middle_minus_1 = $links_in_the_middle-1;
 
	// the code below is required to display the pagination properly for large amount of pages
	// I mean 1 ... 10, 12, 13 .. 100
	// $first_link_in_the_middle is 10
	// $last_link_in_the_middle is 13
	$first_link_in_the_middle = $current_page - floor( $links_in_the_middle_minus_1/2 );
	$last_link_in_the_middle = $current_page + ceil( $links_in_the_middle_minus_1/2 );
 
	// some calculations with $first_link_in_the_middle and $last_link_in_the_middle
	if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
	if( ( $last_link_in_the_middle - $first_link_in_the_middle ) != $links_in_the_middle_minus_1 ) { $last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_minus_1; }
	if( $last_link_in_the_middle > $max_page ) { $first_link_in_the_middle = $max_page - $links_in_the_middle_minus_1; $last_link_in_the_middle = (int) $max_page; }
	if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
 
    // begin to generate HTML of the pagination
    $pagination = '<div id="paginator">';
	$pagination .= '<nav id="misha_pagination" class="navigation pagination" role="navigation"><div class="nav-links">';
 
	// when to display "..." and the first page before it
	if ($first_link_in_the_middle >= 3 && $links_in_the_middle < $max_page) {
		$pagination.= '<a href="'. $first_page_url . $search_query . '" class="page-numbers">1</a>';
 
		if( $first_link_in_the_middle != 2 )
			$pagination .= '<span class="page-numbers extend">...</span>';
 
	}
 
	// arrow left (previous page)
	if ($current_page != 1) {
        // $pagination.= '<a href="'. $first_page_url . '/page/' . ($current_page-1) . $search_query . '" class="prev page-numbers">' . "Left" . '</a>';
        $pagination.= '<div id="previous_selector">' . "Previous" . '</div>';
    }
 
 
	// loop page links in the middle between "..." and "..."
	for($i = $first_link_in_the_middle; $i <= $last_link_in_the_middle; $i++) {
		if($i == $current_page) {
			$pagination.= '<span class="page-numbers current">'.$i.'</span>';
		} else {
            // $pagination .= '<a href="'. $first_page_url . '/page/' . $i . $search_query .'" class="page-numbers">'.$i.'</a>';
            $pagination.= '<div class="page_num_selector" data-page="' . $i . '">' . $i . '</div>';
		}
	}
 
	// arrow right (next page)
    if ($current_page != $last_link_in_the_middle ) {
        // $pagination.= '<a href="'. $first_page_url . '/page/' . ($current_page+1) . $search_query .'" class="next page-numbers">' . "Right" . '</a>';
        $pagination.= '<div id="next_selector">' . "Next" . '</div>';
    }
 
 
	// when to display "..." and the last page after it
	if ( $last_link_in_the_middle < $max_page ) {
 
		if( $last_link_in_the_middle != ($max_page-1) )
			$pagination .= '<span class="page-numbers extend">...</span>';
        $pagination.= '<div class="page_num_selector" data-page="' . $max_page . '">' . $max_page . '</div>';
	}
 
	// end HTML
	$pagination.= "</div></nav></div>\n";
 
	// haha, this is our load more posts link
	// if( $current_page < $max_page )
	// 	$pagination.= '<div id="misha_loadmore">More posts</div>';
 
	// replace first page before printing it
    // echo str_replace(array("/page/1?", "/page/1\""), array("?", "\""), $pagination);
    
    echo $pagination;
}

?>

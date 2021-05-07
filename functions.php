<?php

function jack_webster_enqueue_parent_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css?1231231');
}

function jack_webster_enqueue_scripts()
{
    wp_enqueue_style('custom-styles', get_stylesheet_directory_uri() . '/style.min.css?1231123');
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

// Increase timeout for WP Forms DropZone file upload
function wpf_dev_modern_file_upload_timeout() {
        ?>
        <script type="text/javascript">
                window.addEventListener( 'load', function() {
                        if ( typeof wpforms.dropzones === 'undefined' )  {
                                return;
                        }
                        wpforms.dropzones.forEach(function( dropzone ) {
                                dropzone.options.timeout = 600000; // The timeout for the XHR requests in milliseconds. Default is 300000.
                        });
                } );
        </script>
        <?php
}
//add_action( 'wpforms_wp_footer', 'wpf_dev_modern_file_upload_timeout' );


function jw_call_forms() {
    $call_to_submission_forms= array (
	2591    
);

    return $call_to_submission_forms;
}



// Validate Coupon Code
add_action( 'wpforms_wp_footer', 'wpf_dev_modern_file_upload_timeout' );

/**
 * Add coupon code field validation.
 *
 * @link   https://wpforms.com/developers/how-to-add-coupon-code-field-validation-on-your-forms/
 *
 */
function wpf_dev_validate_coupon( $fields, $entry, $form_data ) {
       

    $forms_with_coupons = jw_call_forms();

    //get the value of the coupon code field the user entered
    $coupon = $fields[47]['value'];

    // If current form is in array of accepted forms and coupon field is set
    if ( !in_array( $form_data['id'], $forms_with_coupons )) {
        return $fields;
    }
     
    //coupon code array, each coupon separated by comma
    $coupon_code_list = array( 
        'jw7433', 
        'jw5287',
        'jw9594',
        'jw6181',
        'jw5995',
    );
     
    // check if value entered is not in the approved coupon list array      
    if (!in_array($coupon, $coupon_code_list)) {  
            // Add to global errors. This will stop form entry from being saved to the database.
            // Uncomment the line below if you need to display the error above the form.
            // wpforms()->process->errors[ $form_data['id'] ]['header'] = esc_html__( 'Some error occurred.', 'plugin-domain' );    
   
            // Check the field ID 5 and show error message at the top of form and under the specific field
               wpforms()->process->errors[ $form_data['id'] ] [ '30' ] = esc_html__( 'Coupon code not found, please confirm the code and try again.', 'plugin-domain' );
   
            // Add additional logic (what to do if error is not displayed)
        }
    }
//add_action( 'wpforms_process', 'wpf_dev_validate_coupon', 10, 3 );

/**
 * Set today's date as default date for all date pickers.
 *
 * @link https://wpforms.com/developers/how-to-set-a-default-date-for-your-date-picker-form-field/
 *
 */
 
function wpf_dev_date_picker_range() {
    ?>
    <script type="text/javascript">
        window.wpforms_datepicker = {
            defaultDate: "today",
        disableMobile: "true"
        }
    </script>
    <?php
}
//add_action( 'wpforms_wp_footer', 'wpf_dev_date_picker_range' );

/**
 * WPForms Add new address field scheme (Canada)
 *
 * @link   https://wpforms.com/developers/create-additional-schemes-for-the-address-field/
 *
 * @param  array $schemes
 * @return array
 */
function wpf_dev_new_address_scheme( $schemes ) {
    $schemes['canada'] = array(
        'label'          => 'Canada',
        'address1_label' => 'Address Line 1',
        'address2_label' => 'Address Line 2',
        'city_label'     => 'City',
        'postal_label'   => 'Postal Code',
        'state_label'    => 'Province',
        'states'         => array(
            //'AB' => 'Alberta',
            'BC' => 'British Columbia',
            //'MB' => 'Manitoba',
            //'NB' => 'New Brunswick',
            //'NL' => 'Newfoundland and Labrador',
            //'NS' => 'Nova Scotia',
            //'ON' => 'Ontario',
            //'PE' => 'Prince Edward Island',
            //'WQ' => 'Quebec',
            //'SK' => 'Saskatchewan',
        ),
    );
    return $schemes;
}
add_filter( 'wpforms_address_schemes', 'wpf_dev_new_address_scheme' );

/**
 * Show all fields in the confirmation message
 *
 * @link https://wpforms.com/developers/how-to-show-all-fields-in-your-confirmation-message/
 *
 */
 
function wpf_dev_frontend_confirmation_message( $message, $form_data, $fields, $entry_id ) {

$call_to_submission_forms = jw_call_forms();

// If current form is in not in array of accepted forms return default message
if ( !in_array($form_data['id'], $call_to_submission_forms )) {
	return $message;
}

$cat_name = $fields['59']['value'];
$fname = $fields['29']['value'];
$lname = $fields['30']['value'];
$phone = $fields['33']['value'];
$email = $fields['32']['value'];
$page_url = $fields['45']['value'];
$address = $fields['34']['value'];

$address1 = $fields['34']['address1'];
$city = $fields['34']['city'];
$postal = $fields['34']['postal'];

$link = sprintf('<a class="confirmation_button" href="%s?first_name=%s&last_name=%s&email=%s&phone=%s&address1=%s&city=%s&postal_code%s">Submit Another Entry</a>',$page_url,$fname,$lname,$email,$phone,$address_arr, $city, $postal);

$body = sprintf('<p><strong>Submitting another entry?</strong></p><p>If you are submitting more than one entry, use the link below to have your contact information auto-filled.</p>%s<p>Please note that this submission is final, and no additional changes can be made to your entry.  You should receive a receipt for your submission fee shortly.  If you do not and require this, and if you have other questions, please contact the Foundation at <a href="mailto:info@jackwebster.com">info@jackwebster.com</a>. <br /> Finalists will be announced in September and the online Webster Awards will be held Nov. 3rd, 7:00 p.m.  PDT.</p>',$link); 
     
$message = sprintf('<h6>Thank you for your submission.<br />%s!</h6><p>%s</p>', $cat_name, $body);

return $message;
}
add_filter( 'wpforms_frontend_confirmation_message', 'wpf_dev_frontend_confirmation_message', 10, 4 );


/**
 * Injection into WP Forms after form has loaded only 
 *
 * @link https://wpforms.com/developers/how-to-change-the-captcha-theme-on-google-checkbox-v2-recaptcha/
 *
 */
  
function wpf_js_inject() {
// Get Page ID
$page_id = get_queried_object_id();
?>
<script type="text/javascript">
    jQuery(function($){

	// Refer to Google Sheet for order of categories, mapped to pages
	var webster_pages = [
	"page-id-2653","page-id-2658","page-id-2657","page-id-2668","page-id-2666","page-id-2672","page-id-2493","page-id-2659","page-id-2670","page-id-2674","page-id-2676","page-id-2678","page-id-2680","page-id-2682","page-id-2684","page-id-2686"];

	// JS variable of page ID
	var page_id = "<?php echo 'page-id-' . $page_id; ?>";
	
	// Get page ID position in our array
	var pos = webster_pages.indexOf(page_id);
	
	//console.log(page_id);
	//console.log(pos);

	// Set WP Forms drop-down selected index to our position
	$('.dropdown_webster_category select').get(0).selectedIndex = pos;
	});
    </script>
<?php
}

// We execute before the front end is output to ensure conditional logic isn't interrupted 
add_action( 'wpforms_frontend_output', 'wpf_js_inject', 30 );


/**
 * WPForms, update total field
 * @link https://www.billerickson.net/update-form-field-values-in-wpforms/
 *
 * @param array $fields Sanitized entry field values/properties.
 * @param array $entry Original $_POST global.
 * @param array $form_data Form settings/data
 * @return array $fields
 */
function be_wpforms_update_total_field( $fields, $entry, $form_data ) {

		if( 2519 != $form_data['id'] )
		return $fields;


$fields[13]['value'] =  sprintf('<a href="%s">Digital File Link</a>',$fields[11]['value'][0]);

	// Add red shirts (field ID 3) and blue shirts (field ID 4) into total (field ID 5)
	//$fields[5]['value'] = intval( $fields[3]['value'] ) + intval( $fields[4]['value'] );

	return $fields;
}
add_filter( 'wpforms_process_filter', 'be_wpforms_update_total_field', 10, 3 );


?>

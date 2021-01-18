<?php
	/*
	Template Name: Awards Gallery
	Template Post Type: page
	*/
?>

<?php

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">

<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( ! $is_page_builder_used ) : ?>

					<h1 class="entry-title main_title"><?php the_title(); ?></h1>
				<?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_featured_image';
					$titletext = get_the_title();
					$alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
					$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
						print_thumbnail( $thumb, $thumbnail["use_timthumb"], $alttext, $width, $height );
				?>

				<?php endif; ?>

					<div class="entry-content">
					<?php
						the_content();

						if ( ! $is_page_builder_used )
							wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
					?>
					</div> <!-- .entry-content -->

				<?php
					if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
				?>

				</article> <!-- .et_pb_post -->

			<?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->

<?php endif; ?>

<!-- ************************************************************************************************************************************************************** -->


	<div class="filter_container"> 
		<div id="filter_bar">
			<p style="padding-right: 20px;">Sort By:</p>
			<form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="misha_filters">
				
					<?php if( $terms = get_terms( array(
							'taxonomy' => 'filter_year', // to make it simple I use default categories
							'orderby' => 'name',
							'hide_empty' => false
						) ) ) : 
						// if categories exist, display the dropdown
						echo '<select name="yearfilter" id="yearfilter" onchange="document.getElementById('."'submitButton'".').click();"><option value="">Select Year...</option>';
						for ($x = 0; $x <= count($terms); $x++) {
							if($x == count($terms)-1) {
								echo '<option value="' . $terms[$x]->term_id . '" selected >' . $terms[$x]->name . '</option>'; // ID of the category as an option value	
							}
							else {
								echo '<option value="' . $terms[$x]->term_id . '">' . $terms[$x]->name . '</option>'; // ID of the category as an option value
							}
						}
						
						echo '</select>';
					endif; ?>

					<button style="display:none;" id="submitButton">Apply Filter</button>
					<input type="hidden" name="cat" value="award-winner" />
					<input type="hidden" name="action" value="mishafilter" />
				
			</form>
		</div>
	</div>

	<div class="filter_container" id="results_container">
	<script>
		jQuery(function(){
			jQuery('#submitButton').click();
		});
	</script>
			
	</div>
	
	
	</div> <!-- #main-content -->
	

<?php

get_footer();
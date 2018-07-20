<?php
/**
 * The template for displaying standard single project posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bbgRedesign
 */
/* we go through the loop once and reset it in order to get some vars for our og tags */
if ( have_posts() ) {
	the_post();
	$metaAuthor = get_the_author();
	$ogTitle = get_the_title();

	$metaKeywords = strip_tags(get_the_tag_list('',', ',''));

	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'Full' );
	$ogImage = $thumb['0'];

	$socialImageID = get_post_meta( $post->ID, 'social_image',true );
	$coordinates = get_post_meta( $post->ID, 'media_dev_coordinates',true );
	//var_dump($coordinates); die();
	if ($socialImageID) {
		$socialImage = wp_get_attachment_image_src( $socialImageID , 'Full');
		$ogImage = $socialImage[0];
	}

	$ogDescription = get_the_excerpt();
	rewind_posts();
}

get_header(); ?>

<!-- <main id="main" class="site-main" role="main">
	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container"> -->
				<?php
					while (have_posts()) {
						the_post();
						get_template_part('template-parts/content', 'single'); 
						echo '<div class="bbg__article-footer usa-grid">';
						if ( !in_category('Project') &&(comments_open() || get_comments_number())):
							comments_template();
						endif;
						echo '</div>';
					}
				?>
<!-- 				</div>
			</div>
		</div>
	</div>
</main> -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
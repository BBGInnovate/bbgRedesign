<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 */

include 'inc/bbg-functions-assemble.php';

$parent_title = "";
if( $post -> post_parent ) {
	$parent = $wpdb -> get_row( "SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent" );
	$parent_title = $parent -> post_title;
}

get_header();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">
	<?php 
		while (have_posts()) {
			the_post();
			if ($parent_title == "Legislation") {
				get_template_part('template-parts/content-law', 'page');
			} else {
				get_template_part('template-parts/content', 'page');
			}
		 }
	 ?>
</main>

<?php get_footer(); ?>
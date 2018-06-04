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

$parentTitle = "";
if( $post -> post_parent ) {
	$parent = $wpdb -> get_row( "SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent" );
	$parentTitle = $parent -> post_title;
}

get_header(); ?>
<!-- save -->
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div class="usa-grid-full">
			<?php 
				while (have_posts()) {
					the_post();
					if ( $parentTitle == "Legislation" ) {
						get_template_part('template-parts/content-law', 'page');
					} else {
						get_template_part('template-parts/content', 'page');
					}

					echo '<div class="bbg-post-footer">';
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					echo '</div>';
				 }
			 ?>
		</div><!-- .usa-grid-full -->
	</main><!-- #main -->
</div><!-- #primary -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>

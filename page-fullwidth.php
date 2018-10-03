<?php
/**
 * The template for displaying full-width pages.
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Full-width
 */

require 'inc/bbg-functions-assemble.php';
$secondaryColumnContent = get_field( 'secondary_column_content', '', true );



get_header();

if (have_posts()) :
	while (have_posts()) : the_post();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" class="site-main" role="main">

	<div class="outer-container">
		<header class="page-header">
			<?php
				if($post->post_parent) {
					//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
					$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
					$parent_link = get_permalink($post->post_parent);

					$parent_label  = '<h5 class="bbg__label--mobile large">';
					$parent_label .= 	'<a href="' . $parent_link . '">' . $parent->post_title . '</a>';
					$parent_label .= '</h5>';
					// echo $parent_label;
				}
			?>
		</header>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<?php
				echo '<h2>' . get_the_title() . '</h2>';
				the_content();
			?>
		</div>
	</div>

	<div class="outer-container">
		<footer class="entry-footer bbg-post-footer 1234">
			<?php
				edit_post_link(
					sprintf(
						/* translators: %s: Name of current post */
						esc_html__( 'Edit %s', 'bbginnovate' ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</footer>
	</div>

	<div class="bbg-post-footer">
	<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	?>
	</div>

</main><!-- #main -->

<?php
	endwhile; // have_post()
endif; // have_post()
?>
<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>

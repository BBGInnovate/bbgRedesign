<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 */

get_header();
require 'inc/bbg-functions-assemble.php';
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php
			if (have_posts()) :
				// $page_header  = '<div class="outer-container">';
				// $page_header .= 	'<div class="grid-container">';
				// $page_header .= 		'<h2>' . get_the_archive_title() . '</h2>';
				// $page_header .= 	'</div>';
				// $page_header .= '</div>';
				// echo $page_header;

				$counter = 0;
				while (have_posts()) : the_post();
					$counter++;

					// ONLY SHOW FEATURED IF IT'S NOT PAGINATED
					if ((!is_paged() && $counter == 1 || is_category('BBG360'))) {
						$featured_media_result = get_feature_media_data();
						if ($featured_media_result != "") {
							echo $featured_media_result;
						}

						echo '<div class="outer-container">';
						echo 	'<div class="grid-container">';
						echo 		'<h3>' . get_the_title() . '</h3>';
						echo 		'<span class="lead-in">' . get_the_excerpt() . '</span>';
						echo 	'</div>';
						echo '</div>';
					} else {
						if ((!is_paged() && $counter == 2) || (is_paged() && $counter == 1)) {
							echo '</div><!-- huh -->';
							echo '<div class="usa-grid">';
							echo 	'<div class="bbg-grid--1-1-1-2 secondary-stories">';
						}
						elseif ((!is_paged() && $counter == 4) || (is_paged() && $counter == 3)) {
							echo '</div><!-- left column -->';
							echo '<div class="bbg-grid--1-1-1-2 tertiary-stories">';
							$moreLabel = "More News";
							if (is_category('Board Meetings')) {
								$moreLabel = "More Board Meetings";
							}
							echo 	'<h5>' . $moreLabel . '</h5>';

							//These values are used for every excerpt >=4
							$includeImage = false;
							$includeMeta = false;
							$includeExcerpt = false;
						}
						get_template_part('template-parts/content-excerpt-list', get_post_format());
					}

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
				endwhile;
				the_posts_navigation();
			else :
				get_template_part('template-parts/content', 'none');
			endif;
		?>

		</div><!-- .usa-grid -->
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
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
				$counter = 0;
				while (have_posts()) : the_post();
					$counter++;
					if ($counter < 4) {
						$in_sidebar = false;
					} else {
						$in_sidebar = true;
					}
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
					} 

					if ($counter == 2) {
						echo '<div class="outer-container">';
						echo 	'<div class="custom-grid-container">';
						echo 		'<div class="inner-container">';
					}

					if ((!is_paged() && $counter > 1)) {
						if ((!is_paged() && $counter == 2) || (is_paged() && $counter == 1)) {
							echo '</div>';
							echo '<div class="main-content-container">';
						}
						elseif ((!is_paged() && $counter == 4) || (is_paged() && $counter == 3)) {
							echo '</div>';
							echo '<div class="side-content-container">';
							$moreLabel = "More News";
							if (is_category('Board Meetings')) {
								$moreLabel = "More Board Meetings";
							}
							echo 	'<h5>' . $moreLabel . '</h5>';
						}

						$article_markup  = '<article id="'. get_the_ID() . '" style="margin-bottom: 1.5rem">';
						if ($in_sidebar == false) {
							$article_markup .= '<h4><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h4>';
							$article_markup .= '<p class="aside date-meta">' . get_the_date() . '</p>';
							$article_markup .= '<p>' . get_the_excerpt() . '</p>';
						} else {
							$article_markup .= '<h6><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h6>';
						}
						$article_markup .= '</article>';
						echo $article_markup;
					}
				endwhile;

				echo 		'</div>';
				echo get_the_posts_navigation();
				echo 	'</div>';
				echo '</div>';
			else :
				get_template_part('template-parts/content', 'none');
			endif;
		?>

		</div><!-- .usa-grid -->
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
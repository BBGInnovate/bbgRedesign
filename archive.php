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

<?php
$featured_media_result = get_feature_media_data();
if ($featured_media_result != "") {
	echo $featured_media_result;
}
?>

<main id="main" role="main">

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
					echo '<div class="outer-container">';
					echo 	'<div class="grid-container">';
					echo 		'<h3 class="article-title"><a href="'. get_the_permalink() . '">' . get_the_title() . '</a></h3>';
					echo 		'<span class="lead-in">' . get_the_excerpt() . '</span>';
					echo 	'</div>';
					echo '</div>';
				}

				if ($counter == 2) {
					echo '<div class="outer-container">';
					echo 	'<div class="custom-grid-container">';
					echo 		'<div class="inner-container">';
					echo 			'<div class="main-content-container">';
				}
				if ($counter == 4) {
					echo 			'</div>';
					echo 			'<div class="side-content-container">';
					$moreLabel = "More News";
					if (is_category('Board Meetings')) {
						$moreLabel = "More Board Meetings";
					}
					echo 	'<h2 class="sidebar-section-header">' . $moreLabel . '</h2>';
				}

				if ($counter > 1) {
					$article_markup  = '<article id="'. get_the_ID() . '" style="margin-bottom: 1.5rem">';
					if ($in_sidebar == false) {
						$article_markup .= '<h3 class="article-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
						$article_markup .= '<p class="date-meta">' . get_the_date() . '</p>';
						$article_markup .= '<p>' . get_the_excerpt() . '</p>';
					} else {
						$article_markup .= '<h3 class="sidebar-article-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
					}
					$article_markup .= '</article>';
					echo $article_markup;
				}
			endwhile;

			echo 		'</div><!-- end .side-content-container -->';
			echo get_the_posts_navigation();
			echo 	'</div>';
			echo '</div>';
		else :
			get_template_part('template-parts/content', 'none');
		endif;
	?>

	</div><!-- .outer-container -->
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
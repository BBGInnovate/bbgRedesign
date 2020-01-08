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
remove_filter('term_description','wpautop');
$term = get_queried_object();
$cat_image_url = get_field('category_image', $term);
if ($cat_image_url != "") {
	echo '<div class="feautre-banner">';
	echo 	'<div class="page-post-featured-graphic">';
	echo 		'<div class="bbg__article-header__banner" ';
	echo 			'style="background-image: url(' . $cat_image_url . ');">';
	echo 		'</div>';
	echo 	'</div>';
	echo '</div>';
}
?>

<main id="main" role="main">

	<?php
		if (have_posts()) {
			echo '<div class="outer-container">';
			echo 	'<div class="grid-container">';
			echo 		'<h2 class="section-header">' . get_the_archive_title() . '</h2>';
			echo 	'</div>';
			echo '</div>';

			echo '<div class="outer-container">';
			echo 	'<div class="grid-container">';
			while (have_posts()) {
				the_post();
				$article_markup  = '<article id="'. get_the_ID() . '" style="margin-bottom: 1.5rem">';
				$article_markup .= '<h3 class="article-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
				if (is_category('global-media-matters')) {
					$article_markup .= '<p class="date-meta">' . get_field('byline_override', get_the_ID()) . '</p>';
				} else {
					$article_markup .= '<p class="date-meta">' . get_the_date() . '</p>';
				}
				$article_markup .= '<p>' . get_the_excerpt() . '</p>';

				$article_markup .= '</article>';
				echo $article_markup;
			}
			echo get_the_posts_navigation();
			echo '</div><!-- .grid-container -->';
		} else {
			get_template_part('template-parts/content', 'none');
			echo '</div><!-- .outer-container -->';
		}
	?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
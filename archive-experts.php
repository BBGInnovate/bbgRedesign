<?php
/**
 * The template for displaying the Experts archive page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 */

get_header();

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
		// if (have_posts()) {
			echo '<div class="outer-container">';
			echo 	'<div class="grid-container">';
			echo 		'<h2 class="section-header">USAGM Experts</h2>';
			if (!empty(get_the_archive_description())) {
				echo 	'<p class="lead-in">' . get_the_archive_description() . '</p>';
			}
			echo usagm_experts_list();
			echo '</div><!-- .grid-container -->';
		// }
	?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
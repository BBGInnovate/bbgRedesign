<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package bbgRedesign
 */
require 'inc/bbg-functions-assemble.php';

get_header();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">
	<div class="outer-container">
		<div class="grid-container">
			<?php
				if (have_posts()) {
					echo '<header>';
					echo 	'<h2>';
					printf(esc_html__('Search Results for: %s', 'bbginnovate'), '<span>' . get_search_query() . '</span>' );
					echo 	'</h2>';
					echo '</header>';

					while (have_posts() ) {
						the_post();
						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part('template-parts/content', 'search');
					}

					the_posts_navigation();
			} else {
				get_template_part('template-parts/content', 'none');
			}
			?>
		</div>
	</div>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
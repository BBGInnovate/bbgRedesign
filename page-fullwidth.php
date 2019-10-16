<?php
/**
 * The template for displaying full-width pages.
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Full-width
 */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_id = get_the_ID();
		$featured_media_result = get_feature_media_data();
		$page_title = get_the_title($page_id);
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}

get_header();
?>

<?php
	if ($featured_media_result != "" && !is_page('awards-archive')) {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">
	<div class="outer-container">
		<div class="grid-container">
			<?php
				echo '<h2 class="section-header">' . $page_title . '</h2>';
				echo $page_content;
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
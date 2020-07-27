<?php
/**
 * The template for containing maps created using our ammap.js Vector maps
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 * template name: Map Container
 */

$page_content = "";
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_content = get_the_content();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}
wp_reset_postdata();
wp_reset_query();

get_header();

echo getNetworkExcerptJS();

echo getMapData();

echo getMapScripts();

?>

<main id="main" role="main">

	<div class="outer-container">
		<div class="grid-container">
			<?php echo '<h2 class="section-header">' . get_the_title() . '</h2>'; ?>
			<?php echo '<p>' . get_the_excerpt() . '</p>'; ?>
		</div>
	</div>

	<?php
		echo getMapMarkup();
	?>
</main>

<?php get_footer(); ?>
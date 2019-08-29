<?php
/**
 * The template for displaying highlights from across the 5 BBG entities.
 * Features a banner map of recent headlines about the entities
 * and a subsection for each of the entities.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Affiliates
 */

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$pageTitle   = get_the_title();
		$cur_page_id = get_the_ID();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}
wp_reset_postdata();
wp_reset_query();

$secondaryColumnLabel = get_field('secondary_column_label', '', true);
$secondaryColumnContent = get_field('secondary_column_content', '', true);

get_header();

// SHOW THE AFFILIATES MAP INSTEAD OF A FEATURED IMAGE
require 'inc/affiliates-map.php';
?>

<main id="main" role="main">
	<div class="custom-grid-container">
		<div class="inner-container">
			<div class="main-content-container">
				<?php
					echo '<h2 class="section-header">' . $pageTitle . '</h2>';
					echo $page_content;
				?>
			</div>
			<div class="side-content-container">
				<?php
					if ($secondaryColumnContent != "") {
						if ($secondaryColumnLabel != "") {
							echo '<h3 class="sidebar-section-header">' . $secondaryColumnLabel . '</h3>';
						}
						echo $secondaryColumnContent;
					}
				?>
			</div>
		</div>
	</div>
</main><!-- #main -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
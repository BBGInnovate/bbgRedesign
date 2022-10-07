<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: 2-column
 */

// FUNCTION THAT BUILD SECTIONS
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

require 'inc/bbg-functions-assemble.php';
include 'inc/shared_sidebar.php';

$addFeaturedGallery = get_post_meta( get_the_ID(), 'featured_gallery_add', true );

$headline = get_field('headline', '', true);
$listsInclude = get_field('sidebar_dropdown_include', '', true);

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
		$ogDescription = get_the_excerpt();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}
wp_reset_postdata();
wp_reset_query();

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
			<?php echo '<h2 class="section-header">' . get_the_title() . '</h2>'; ?>
		</div>
		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<?php
							echo '<div class="page-content">';
							echo 	'<p>' . $page_content . '</p>';
							echo '</div>';

							$relatedCategory = get_field('related_category_posts', $id);
							if ($relatedCategory != "") {
								$qParams2 = array(
									'post_type' => array('post'),
									'posts_per_page' => 2,
									'cat' => $relatedCategory->term_id,
									'orderby' => 'date',
									'order' => 'DESC'
								);
								$categoryUrl = get_category_link($relatedCategory->term_id);
								$custom_query = new WP_Query($qParams2);

								if ($custom_query->have_posts()) {
									echo '<h6 class="bbg__label"><a href="' . $categoryUrl . '">' . $relatedCategory->name . '</a></h6>';
									echo '<div class="usa-grid-full">';
										while ($custom_query->have_posts())  {
											$custom_query->the_post();
											get_template_part('template-parts/content-portfolio', get_post_format());
										}
									echo '</div>';
								}
								wp_reset_postdata();
							}
						?>
					</div>
					<div class="side-column divider-left">
						<?php
							$secondaryColumnLabel = get_field('secondary_column_label');
							$secondaryColumnContent = get_field('secondary_column_content');

							if ($secondaryColumnContent != "") {
								echo '<aside>';
								if ($secondaryColumnLabel != "") {
									echo '<h2 class="sidebar-section-header">' . $secondaryColumnLabel . '</h2>';
								}
								echo $secondaryColumnContent;
								echo '</aside>';
							}
							if ($includeSidebar) {
								echo '<aside>';
								echo 	$sidebar;
								echo '</aside>';
							}
							if ($listsInclude) {
								echo '<aside>';
								echo 	$sidebarDownloads;
								echo '</aside>';
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
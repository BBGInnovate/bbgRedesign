<?php
/**
 * Custom landing page for the "Who we are" and "Our Work" sections
 *
 * template name: About
 *
 * @author Gigi Frias <gfrias@bbg.gov>
 * @package bbgRedesign
 */

/* @Check if number of pages is odd or even
*  Return BOOL (true/false) */

// FUNCTION THAT BUILD SECTIONS
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

require 'inc/bbg-functions-assemble.php';

function isOdd($pageTotal) {
	return ($pageTotal % 2) ? true : false;
}

if (have_posts()) :
	while (have_posts()) : the_post();
		$id = get_the_id();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	endwhile;
endif;

$umbrella_group = array();
if (have_rows('about_flexible_page_rows')) {
	while (have_rows('about_flexible_page_rows')) {
		the_row();
		if (get_row_layout() == 'umbrella') {
			// GET UMBRELLA'S MAIN DATA
			$main_umbrella_data = get_umbrella_main_data();
			$main_umbrella_parts = build_umbrella_main_parts($main_umbrella_data);
			// DETERMINE GRID
			$content_counter = count(get_sub_field('umbrella_content'));
			$grid_class = 'grid-half';
			if (isOdd($content_counter)) {
				$grid_class = 'grid-third';
			}
			$umbrella_main_data_group = assemble_umbrella_main($main_umbrella_parts);
			array_push($umbrella_group, $umbrella_main_data_group);
			// GET UMBRELLA'S CONTENT DATA
			$content_parts_group = array();
			while (have_rows('umbrella_content')) {
				the_row();
				$umbrella_content_type = get_row_layout();
				$content_data_result = get_umbrella_content_data($umbrella_content_type, $grid_class);
				$content_parts_result = build_umbrella_content_parts($content_data_result);
				array_push($content_parts_group, $content_parts_result);
			}
			$umbrella_content_markup = assemble_umbrella_content_section($content_parts_group);
		}
		array_push($umbrella_group, $umbrella_content_markup);
	}
}

wp_reset_postdata();
wp_reset_query();

get_header();
?>

<main id="main" class="site-main" role="main">
<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
	$page_header  = '<div class="outer-container">';
	$page_header .= 	'<div class="grid-container">';
	$page_header .= 		'<h2>' . get_the_title() . '</h2>';
	// FLEXIBLE ROWS: OFFICE
	if (!empty($office_module)) {
		$page_header .= 	$office_module;
	}
	$page_header .= 	'</div>';
	$page_header .= '</div>';
	echo $page_header;

	$body_copy  = '<div class="outer-container" id="outside-build-function">';
	$body_copy .= 	'<div class="grid-container page-content">';
	$body_copy .= 		$page_content;
	$body_copy .= 	'</div>';
	$body_copy .= '</div>';
	echo $body_copy;

	foreach($umbrella_group as $umbrella) {
		echo $umbrella;
	}

	// NETWORKS
	$show_networks = get_field('about_networks_row', $id);
	if (!empty($show_networks)) {
		// OPTIONS: $entity_placement arguments ["entity-main" | "entity-side"]
		$entity_data = get_entity_data("entity-main");
	}
?>
</main>

<?php get_footer(); ?>
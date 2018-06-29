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

if (have_rows('about_flexible_page_rows')) {
	$umbrella_group = array();
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
			array_push($umbrella_group, $umbrella_content_markup);
		}
		elseif (get_row_layout() == 'about_office') {
			$office_data_result = get_office_data();
			$office_parts_result = build_office_parts($office_data_result);
			$office_module = assemble_office_module($office_parts_result);
		}
		elseif (get_row_layout() == 'marquee') {
			$marquee_data_result = get_marquee_data();
			$marquee_parts_result = build_marquee_parts($marquee_data_result);
			$marquee_module = assemble_umbrella_marquee($marquee_parts_result);
		}
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
	if (!empty($office_module)) {
		$page_header .= 	$office_module;
	}
	$page_header .= 	'</div>';
	$page_header .= '</div>';
	echo $page_header;

	$body_copy  = '<div class="outer-container">';
	$body_copy .= 	'<div class="grid-container page-content">';
	$body_copy .= 		$page_content;
	$body_copy .= 	'</div>';
	$body_copy .= '</div>';
	echo $body_copy;

	if (is_page('who-we-are')) {
		$first_umbrella = array_slice($umbrella_group, 1, 1);
		$umbrella_end = array_splice($umbrella_group, 2);

		echo '<div class="outer-container">';
		echo 	'<div class="small-side-content-container box-special">';
		echo 		$marquee_module;
		echo 	'</div>';
		echo 	'<div class="small-main-content-container">';
		foreach ($umbrella_group as $umbrella_array_bit) {
			echo $umbrella_array_bit;
		}
		echo 	'</div>';
		echo '</div>';

		echo '<div class="outer-container">';
		foreach ($umbrella_end as $rest_of_umbrella) {
			echo $rest_of_umbrella;
		}
		echo '</div>';
	}
	else {
		echo '<div class="outer-container">';
		echo 	$marquee_module;
		echo '</div>';

		echo '<div class="outer-container">';
		foreach($umbrella_group as $umbrella) {
			echo $umbrella;
		}
		echo '</div>';
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
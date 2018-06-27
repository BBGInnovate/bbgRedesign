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

// GET ELEMENTS FOR FLEXIBLE ROWS SECTION
$flexible_rows = get_field('about_flexible_page_rows', $id);
$umbrella_rows = array();
foreach ($flexible_rows as $flex_row) {
	if ($flex_row['acf_fc_layout'] == 'about_ribbon_page') {
		echo 'ribbon';
	}
	elseif ($flex_row['acf_fc_layout'] == 'about_office') {
		$offic_row = $flex_row;
		$office_data = get_office_data($offic_row);
		$office_parts = build_office_parts($office_data);
		$office_module = assemble_office_module($office_parts);
	}
	elseif ($flex_row['acf_fc_layout'] == 'umbrella') {
		$umbrella_row = $flex_row;
		$main_umbrella_data = get_umbrella_main_data($umbrella_row);
		$main_umbrella_parts = build_umbrella_main_parts($main_umbrella_data);

		// COUNT BLOCKS TO MAKE GRID CLASS (put this in function to lessen clutter here)
		$cur_umbrella_content = $umbrella_row['umbrella_content'];
		$content_count = count($cur_umbrella_content);
		$content_counter = 0;
		$containerClass = 'grid-half';
		if (isOdd($content_count)) {
			$containerClass = 'grid-third';
		}
		// EACH UMBRELLA ROW'S CONTENT
		$content_blocks = array();
		while ($content_counter < $content_count) {
			$umbrella_content_result = get_umbrella_content_data($cur_umbrella_content[$content_counter]);
			$umbrella_content_chunks = build_umbrella_content_parts($umbrella_content_result, $containerClass);
			array_push($content_blocks, $umbrella_content_chunks);
			$content_counter++;
		}

		$umbrella_markup = assemble_umbrella_content_section($main_umbrella_parts, $content_blocks);
		if (!empty($umbrella_markup)) {
			array_push($umbrella_rows, $umbrella_markup);
		}
	}
	elseif($flex_row['acf_fc_layout'] == 'marquee') {
		$marquee_row = $flex_row;
		$marquee_data = get_marquee_data($marquee_row);
		$marquee_parts = build_marquee_parts($marquee_data);
		$marquee_module = assemble_umbrella_marquee($marquee_parts);
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

	$body_copy  = '<div class="outer-container">';
	$body_copy .= 	'<div class="grid-container page-content">';
	$body_copy .= 		$page_content;
	$body_copy .= 	'</div>';
	$body_copy .= '</div>';
	echo $body_copy;

	// FLEXIBLE ROWS: UMBRELLA
	if (count($umbrella_rows)) {
		foreach ($umbrella_rows as $umbrella) {
			echo $umbrella;
		}
	}

	// NETWORKS
	$show_networks = get_field('about_networks_row', $id);
	if (!empty($show_networks)) {
		// $entity_placement arguments ["entity-main" | "entity-side"]
		$entity_data = get_entity_data("entity-main");
	}
?>
</main>

<?php get_footer(); ?>
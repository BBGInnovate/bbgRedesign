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

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_id();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}

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
		elseif (get_row_layout() == 'marquee') {
			$marquee_data_result = get_marquee_data();
			$marquee_parts_result = build_marquee_parts($marquee_data_result);
			$marquee_module = assemble_marquee_module($marquee_parts_result);
		}
		elseif (get_row_layout() == 'about_ribbon_page') {
			$ribbon_data_result = get_ribbon_data();
			$ribbon_parts_result = build_ribbon_parts($ribbon_data_result);
			$ribbon_module = assemble_ribbon_module($ribbon_parts_result);
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
	$page_header .= 	'</div>';
	$page_header .= '</div>';
	echo $page_header;

	// PAGE CONTENT
	if ($page_content != "") {
		$body_copy  = '<div class="outer-container">';
		$body_copy .= 	'<div class="grid-container page-content">';
		$body_copy .= 		$page_content;
		$body_copy .= 	'</div>';
		$body_copy .= '</div>';
		echo $body_copy;
	}

	// OFFICE PAGE OFFICE INFORMATION
	$office_intro_result = get_office_intro_data();

	$office_contact_data_results = get_office_contact_data();
	$office_contact_parts_results = build_office_contact_parts($office_contact_data_results);
	$office_contact_module = assemble_office_contact_module($office_contact_parts_results);

	$office_highlights_data_result = get_office_highlights_data();
	$office_highlights_parts_result = build_office_highlights_parts($office_highlights_data_result);
	$office_highlights_module = build_office_highlights_module($office_highlights_parts_result);

	$office_map_data = get_office_map_data($id);
	// $office_map_parts = build_office_map_parts($office_map_data);
	// $office_map_module = build_office_map_module($office_map_parts);

	$office_information_chuncks  = '<div class="outer-container office-page">';
	$office_information_chuncks .= 	'<div class="custom-grid-container">';
	$office_information_chuncks .= 		'<div class="inner-container">';
	$office_information_chuncks .= 			'<div class="main-content-container">';
	$office_information_chuncks .= 				$office_intro_result;
	$office_information_chuncks .= 				'<div id="map"></div>';
	$office_information_chuncks .= 				$office_highlights_module;
	$office_information_chuncks .= 			'</div>';
	$office_information_chuncks .= 			'<div class="side-content-container">';
	$office_information_chuncks .= 				$office_contact_module;
	$office_information_chuncks .= 			'</div>';
	$office_information_chuncks .= 		'</div>';
	$office_information_chuncks .= 	'</div>';
	$office_information_chuncks .= '</div>';
	echo $office_information_chuncks;

	// FLEXIBLE ROWS
	if (is_page('who-we-are')) {
		$first_umbrella = array_slice($umbrella_group, 1, 1);
		$umbrella_end = array_splice($umbrella_group, 2);

		echo '<div class="outer-container">';
		echo 	'<div class="medium-side-content-container box-special">';
		echo 		$marquee_module;
		echo 	'</div>';
		echo 	'<div class="medium-main-content-container">';
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
		if (!empty($marquee_module)) {
			echo '<div class="outer-container">';
			echo 	$marquee_module;
			echo '</div>';
		}
		if (!empty($umbrella_group)) {
			echo '<div class="outer-container">';
			foreach($umbrella_group as $umbrella) {
				echo $umbrella;
			}
			echo '</div>';
		}
		if (!empty($ribbon_module)) {
			echo $ribbon_module;
		}
	}

	// FLEXIBLE ROWS: NETWORKS
	$show_networks = get_field('about_networks_row', $id);
	if (!empty($show_networks)) {
		// OPTIONS: $entity_placement arguments ["entity-main" | "entity-side"]
		$entity_data = get_entity_data("entity-main");
	}
?>
</main>
<?php
// IF MAP, LOAD JS, CSS
if (!empty($office_map_data)) {
	echo 'look: ' . $office_map_data['lat'];
?>
	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />

	<script type="text/javascript">
		L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var map = L.mapbox.map('map', 'mapbox.streets')
		<?php echo '.setView(['. $office_map_data['lat'] . ', ' . $office_map_data['lng'] . '], ' . $office_map_data['zoom'] . ');'; ?>

		map.scrollWheelZoom.disable();

		L.mapbox.featureLayer({
			type: 'Feature',
			geometry: {
				type: 'Point',
				coordinates: [
					<?php echo $office_map_data['lng'] . ', ' . $office_map_data['lat']; ?>
				]
			},
			properties: {
				title: '<?php echo $mapHeadline; ?>',
				description: '<?php echo $mapDescription; ?>',
				'marker-size': 'large',
				'marker-color': '#981b1e',
				'marker-symbol': ''
			}
		}).addTo(map);
	</script>
<?php } ?>
<?php get_footer(); ?>
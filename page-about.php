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
	$all_flex_rows = array();
	$umbrella_group = array();
	while (have_rows('about_flexible_page_rows')) {
		the_row();
		if (get_row_layout() == 'umbrella') {
			// GET UMBRELLA'S MAIN DATA
			$main_umbrella_data = get_umbrella_main_data();
			$main_umbrella_parts = build_umbrella_main_parts($main_umbrella_data);
			// DETERMINE GRID
			$content_counter = count(get_sub_field('umbrella_content'));
			$grid_class = 'bbg-grid--1-2-2';
			if (isOdd($content_counter)) {
				$grid_class = 'bbg-grid--1-2-3';
			}
			$umbrella_main_data_group = assemble_umbrella_main($main_umbrella_parts);
			array_push($umbrella_group, $umbrella_main_data_group);
			// echo $umbrella_main_data_group;
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
			array_push($all_flex_rows, $umbrella_group);
			$umbrella_group = array();
		}
		elseif (get_row_layout() == 'marquee') {
			$marquee_data_result = get_marquee_data();
			$marquee_parts_result = build_marquee_parts($marquee_data_result);
			$marquee_module = assemble_marquee_module($marquee_parts_result);
			array_push($all_flex_rows, $marquee_module);
		}
		elseif (get_row_layout() == 'about_ribbon_page') {
			$ribbon_data_result = get_ribbon_data();
			$ribbon_parts_result = build_ribbon_parts($ribbon_data_result);
			$ribbon_module = assemble_ribbon_module($ribbon_parts_result);
			array_push($all_flex_rows, $ribbon_module);
		}
	}
}

if (is_page('media-development')) {
	$qParams = array(
		'post_type' => array('post'),
		'cat' => get_cat_id('Media Development Map'),
		'posts_per_page' => 999,
		'post_status' => array('publish')
	);
	$custom_query_args= $qParams;
	$custom_query = new WP_Query($custom_query_args);

	$features = array();
	$years = array();

	if ($custom_query->have_posts()) {
		$counter = 0;
		$imgCounter = 0;
		$trainingByYear = array();
		$error_note = "";

		while ($custom_query->have_posts()) {
			$custom_query->the_post();

			$id = get_the_ID();
			// $location = get_post_meta($id, 'media_dev_coordinates', true);
			$location = get_field('media_dev_coordinates', $id);
			$story_link = get_permalink($id);
			$training_name = get_post_meta($id, 'media_dev_name_of_training', true);
			$training_year = get_post_meta($id, 'media_dev_years', true);
			$country = get_post_meta( $id, 'media_dev_country', true);
			$description = get_post_meta( $id, 'media_dev_description', true);
			$participants = get_post_meta( $id, 'media_dev_number_of_participants', true);
			$training_date = get_post_meta( $id, 'media_dev_date', true);
			$training_photo = get_field( 'media_dev_photo', $id, true);
			$mapDescription = get_post_meta( $id, 'media_dev_description', true);
			$map_headline = "<h5><a target='blank' href='". $story_link ."'>" . $training_name . '</a></h5>';

			$years = explode(",", $training_year);
			for ($i = 0; $i < count($years); $i++) {
				$year = $years[$i];
				$o = array(
					'title' => $training_name,
					'country' => $country,
					'trainingDate' => $training_date,
					'storyLink' => $story_link
				);
				if (!isset($trainingByYear[$year])) {
					$trainingByYear[$year] = array();
				}
				array_push($trainingByYear[$year],  $o);

			}

			$popupBody = '<span class="bbg__map__infobox__date" style="font-weight: bold;"">' . $training_date . ' in ' . $country . '</span>';
			
			if ($training_photo) {
				$imgCounter++;
				$training_photo_url = $training_photo['sizes']['medium'];
				// ASSIGN WIDTH AND HEIGHT FOR PROPER SCROLLING
				$w = $training_photo['sizes']['medium-width'];
				$h = $training_photo['sizes']['medium-height'];
				$popupBody .= '<div class="u--show-medium-large"><br><br><img src="' . $training_photo_url . '"></div>';
			} else {
				$mediumFeature = wp_get_attachment_image_src( get_post_thumbnail_id( $id), "medium" );
				$training_photo_url = $mediumFeature[0];
				$w = $training_photo = $mediumFeature[1];
				$h = $training_photo = $mediumFeature[2];

				$popupBody .= '<div class="u--show-medium-large"><br><br><img src="' . $training_photo_url . '"></div>';
			}
			$popupBody .= '<br><br>' . $mapDescription . ' &nbsp;&nbsp;<a style="font-weight: bold;" href="' . $story_link . '" target="_blank">Read More &gt; &gt;</a>';

			$pinColor = '#ff0000';
			if ( !isset($location['lng']) || !isset($location['lat'])) {
				$error_note .= '<!-- check lat/lng for ' . $map_headline . ' -->';
			}

			if (!empty($location)) {				
				$features[] = array(
					'type' => 'Feature',
					'geometry' => array( 
						'type' => 'Point',
						'coordinates' => array($location['lng'],$location['lat'])
					),
					'properties' => array(
						'title' => $map_headline,
						'description' => $popupBody,
						'year' => $training_year,
						'marker-color' => $pinColor,
						'marker-size' => 'large', 
						'marker-symbol' => ''
					)
				);
			}
		}

		$post_accordion  = '';
		$post_accordion .= '<h5>Trainings by year</h5>';
		for ($i = 2030; $i >= 2000; $i--) {
			if (isset($trainingByYear[$i])) {
				$post_accordion .= '<div class="usa-accordion bbg__committee-list">';
				$post_accordion .= 	'<ul class="usa-unstyled-list">';
				$post_accordion .= 		'<li>';
				$post_accordion .= 			'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-' . $i . '">';
				$post_accordion .=  				$i . ' Trainings';
				$post_accordion .= 			'</button>';
				$post_accordion .= 			'<div id="collapsible-' . $i . '" aria-hidden="true" class="usa-accordion-content">';
				$yearContent = $trainingByYear[$i];
				for ($j = 0; $j < count($yearContent); $j++) {
					$o = $yearContent[$j];
					$link = $o['storyLink'];
					$title = $o['title'];
					$training_date = $o['trainingDate'];
					$country = $o['country'];
					$story_link = $o['storyLink'];

					$post_accordion .= 			'<h6><a href="' . $story_link . '">' . $title . '</a><h6>';
					$post_accordion .= 			'<p class="aside">' . $country . '</p>';
				}
				$post_accordion .= 			'</div>';
				$post_accordion .= 		'</li>';
				$post_accordion .= 	'</ul>';
				$post_accordion .= '</div>';
			}
		}
		$training_posts_accordion  = $post_accordion;
		$training_posts_accordion .= $error_note;

		$geojsonObj = array(array(
			'type' => 'FeatureCollection',
			'features' => $features
		));
		$geojsonStr = json_encode(new ArrayValue($geojsonObj), JSON_PRETTY_PRINT, 10);

		echo '<script type="text/javascript">';
		echo 	'var geojson = ' . $geojsonStr . ';';
		echo '</script>';
	}
}
?>
<?php if (is_page('media-development')) { ?>
	<style>
		div.usa-accordion-content {padding: 1.5rem !important;}
		div.usa-accordion-content a {font-weight : bold;}
	</style>
<?php } ?>

<?php
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

<main id="main" class="site-main" role="main">
<?php
	// PAGE CONTENT
	$body_copy  = '<div class="outer-container">';
	$body_copy .= 	'<div class="grid-container">';
	$body_copy .= 		'<h2>' . get_the_title() . '</h2>';
	$body_copy .= 	'</div>';
	if ($page_content != "") {
		$body_copy .= '<div class="grid-container page-content">';
		$body_copy .= 	$page_content;
		$body_copy .= '</div>';
	}
	$body_copy .= '</div>';
	echo $body_copy;

	// OFFICE PAGE OFFICE INFORMATION
	$office_intro_result = get_office_intro_data();
	if (!empty($office_intro_result)) {
		$office_contact_data_results = get_office_contact_data();
		$office_contact_parts_results = build_office_contact_parts($office_contact_data_results);
		$office_contact_module = assemble_office_contact_module($office_contact_parts_results);

		$office_highlights_data_result = get_office_highlights_data();
		$office_highlights_parts_result = build_office_highlights_parts($office_highlights_data_result);
		$office_highlights_module = assemble_office_highlights_module($office_highlights_parts_result);

		$office_information_chuncks  = '<div class="outer-container office-page">';
		$office_information_chuncks .= 	'<div class="custom-grid-container">';
		$office_information_chuncks .= 		'<div class="inner-container">';
		$office_information_chuncks .= 			'<div class="main-content-container">';
		$office_information_chuncks .= 				$office_intro_result;
		if (is_page('media-development')) {
			$office_information_chuncks .= 				'<div id="map" class="bbg__map--banner"></div>';
			$office_information_chuncks .= 				'<p class="bbg__article-header__caption">This map displays the training opportunities that the USAGM has offered over on a year by year basis.</p>';
		}
		$office_information_chuncks .= 				$office_highlights_module;
		$office_information_chuncks .= 			'</div>';
		$office_information_chuncks .= 			'<div class="side-content-container">';
		$office_information_chuncks .= 				$office_contact_module;
		if (is_page('media-development')) {
			$office_information_chuncks .= 			$training_posts_accordion;
		}
		$office_information_chuncks .= 			'</div>';
		$office_information_chuncks .= 		'</div>';
		$office_information_chuncks .= 	'</div>';
		$office_information_chuncks .= '</div>';
		echo $office_information_chuncks;
	}

	// FLEXIBLE ROWS
	if (is_page('who-we-are')) {
		$second_umbrella = array_slice($all_flex_rows, 1, 1);
		$umbrella_end = array_splice($all_flex_rows, 2);

		echo '<div class="outer-container">';
		echo 	'<div class="medium-side-content-container box-special">';
		echo 		$marquee_module;
		echo 	'</div>';
		echo 	'<div class="medium-main-content-container">';
		echo 		$second_umbrella[0][1];
		echo 	'</div>';
		echo '</div>';

		echo '<div class="outer-container">';
		foreach ($umbrella_end as $rest_of_umbrella) {
			foreach ($rest_of_umbrella as $umbrella_chunk) {
				echo $umbrella_chunk;
			}
		}
		echo '</div>';
	}
	elseif (!is_page('who-we-are') && !empty($all_flex_rows)) {
		foreach ($all_flex_rows as $flex_row) {
			if (is_array($flex_row)) {
				echo '<div class="outer-container">';
				foreach ($flex_row as $row) {
					echo $row;
				}
				echo '</div>';
			}
			else {
				echo $flex_row;
			}
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

<?php if (is_page('media-development')) { ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.3/leaflet.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.3/leaflet.js"></script>
	<link rel="stylesheet" href="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/MarkerCluster.css" />
	<link rel="stylesheet" href="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/MarkerCluster.Default.css" />
	<script src="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/leaflet.markercluster-src.js"></script>
	<script src="https://cdn.rawgit.com/ghybs/Leaflet.FeatureGroup.SubGroup/v1.0.0/dist/leaflet.featuregroup.subgroup-src.js"></script>
	<script src="https://cdn.rawgit.com/jseppi/Leaflet.MakiMarkers/master/Leaflet.MakiMarkers.js"></script>

	<style>
		.marker-cluster-small {background-color: rgba(241, 211, 87, 0);}
		.marker-cluster-small div {background-color: rgba(240, 194, 12, 1);}
		.marker-cluster-medium {background-color: rgba(253, 156, 115, 0);}
		.marker-cluster-medium div {background-color: rgba(241, 128, 23, 1);}
		.marker-cluster-large { background-color: rgba(255, 0, 0, 0);}
		.marker-cluster-large div {background-color: rgba(255, 0, 0, 1);}
	</style>

	<script type="text/javascript">
	(function($) {
		var mbToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var tilesetUrl = 'https://a.tiles.mapbox.com/v4/mapbox.emerald/{z}/{x}/{y}@2x.png?access_token=' + mbToken;
		var attribStr = '&copy; <a href="https://www.mapbox.com/map-feedback/">Mapbox</a>  &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>';
		var tiles = L.tileLayer(tilesetUrl, {
			maxZoom: 18,
			attribution: attribStr
		});
		var latlng = L.latLng(-37.82, 175.24);

		var map = L.map('map', {center: latlng, zoom: 13, layers: [tiles]});

		var mcg = new L.MarkerClusterGroup({
			maxClusterRadius:35,
			iconCreateFunction: function (cluster) {
				var childCount = cluster.getChildCount();
				var c = ' marker-cluster-';
				if (childCount < 10) {
					c += 'small';
				} else if (childCount < 100) {
					c += 'medium';
				} else {
					c += 'large';
				}
				return new L.DivIcon({ html: '<div><span><b>' + childCount + '</b></span></div>', className: 'marker-cluster' + c, iconSize: new L.Point(40, 40) });
			}
		});

		var maki = {};
		maki["2016"] = {"name": "library", "color":"#f00"};
		maki["2015"] = {"name": "library", "color":"#f00"};
		maki["2014"] = {"name": "library", "color":"#b0b"};
		maki["2013"] = {"name": "heliport", "color":"#ccc"};
		maki["2012"] = {"name": "ferry", "color":"#ccc"};

		var deliveryLayers={};    
		for (var deliveryPlatform in maki) {
			if (maki.hasOwnProperty(deliveryPlatform)) {
				var newLayer = L.featureGroup.subGroup(mcg);
				newLayer.addTo(map);
				deliveryLayers[deliveryPlatform] = newLayer;
			}
		}

		//First, specify your Mapbox API access token
		L.MakiMarkers.accessToken = mbToken;

		// An array of icon names can be found in L.MakiMarkers.icons or at https://www.mapbox.com/maki/
		for (var i = 0; i < geojson[0].features.length; i++) {
			var coords = geojson[0].features[i].geometry.coordinates;
			var title = geojson[0].features[i].properties.title;
			var description = geojson[0].features[i].properties['description'];
			var year = geojson[0].features[i].properties['year'];
			var icon = L.MakiMarkers.icon({icon: "circle", color: "#981b1e", size: "m"});

			var marker = L.marker(new L.LatLng(coords[1], coords[0]), {
				icon:icon
			});
			var popupText = title + description;
				
			//rather than just use html, do this - http://stackoverflow.com/questions/10889954/images-size-in-leaflet-cloudmade-popups-dont-seem-to-count-to-determine-popu
			var divNode = document.createElement('DIV');
			divNode.innerHTML = popupText;
			marker.bindPopup(divNode);

			var targetLayer = deliveryLayers["2016"];
			marker.addTo(targetLayer);
		}
		map.addLayer(mcg);
		map.scrollWheelZoom.disable();

		function centerMap(){
			map.fitBounds(mcg.getBounds());
		}

		centerMap();


		//Recenter the map on resize
		function resizeStuffOnResize() {
			waitForFinalEvent(function() {
				centerMap();
			}, 500, "some unique string");
		}
		$("#resetZoom").click(function() {
			centerMap();
		});

		// Wait for the window resize to 'end' before executing a function---------------
		var waitForFinalEvent = (function () {
			var timers = {};
			return function (callback, ms, uniqueId) {
				if (!uniqueId) {
					uniqueId = "Don't call this twice without a uniqueId";
				}
				if (timers[uniqueId]) {
					clearTimeout (timers[uniqueId]);
				}
				timers[uniqueId] = setTimeout(callback, ms);
			};
		})();

		window.addEventListener('resize', function(event){
			resizeStuffOnResize();
		});

		resizeStuffOnResize();
		function setSelectedPlatform(platform, displayMode) {
			for (var p in deliveryLayers) {
				if (deliveryLayers.hasOwnProperty(p)) {
					map.removeLayer(deliveryLayers[p]);
				}
			}
			if (platform == "all") {
				for (var p in deliveryLayers) {
					if (deliveryLayers.hasOwnProperty(p)) {
						map.addLayer(deliveryLayers[p]);
					}
				}
			} else {
				map.addLayer(deliveryLayers[platform]);
			}
			// ON MOBILE, WITH SELECT BOXES, IT HELPS TO RECENTER MAP AFTER CHANGING PLATEFORMS
			centerMap();
			
		}

		$(document).ready(function() {
			$('input[type=radio][name=trainingYear]').change(function() {
				setSelectedPlatform(this.value, 'radio');
			});
			$('select[name=trainingSelect]').change(function() {
				var selectedPlatform = $(this).val();
				setSelectedPlatform(selectedPlatform,'select');
			});
		});
	})(jQuery);
	</script>
<?php } ?>
<?php get_footer(); ?>
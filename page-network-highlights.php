<?php
/**
 * The template for displaying highlights from across the 5 BBG entities.
 * Features a banner map of recent headlines about the entities
 * and a subsection for each of the entities.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Network Highlights
 */

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_id = get_the_ID();
		$page_title = get_the_title();
	}
}
wp_reset_postdata();
wp_reset_query();

// GET PRESS RELEASES FOR EACH ENTITY
$entities = ['bbg', 'voa', 'rferl', 'ocb', 'rfa', 'mbn', 'otf'];
$entity_data = array();
foreach ($entities as $cur_entity) {
	$entity_title = $cur_entity;
	$entity_slug = $cur_entity;

	if ($entity_slug != "") {
		$prCategoryObj = get_category_by_slug($entity_slug);
		if (is_object($prCategoryObj)) {
			$prCategoryID = $prCategoryObj->term_id;
			$press_release_params = array(
				'post_type' => array('post'),
				'posts_per_page' => 5,
				'category__and' => array(
						$prCategoryID,
						get_cat_ID('Press Release')
				),
				'orderby', 'date',
				'order', 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => 'post-format-quote',
						'operator' => 'NOT IN'
					)
				)
			);
			$press_release_query = new WP_Query($press_release_params);
			if ($press_release_query->have_posts()) {
				$entity_press_releases = $press_release_query->posts;
			}
			wp_reset_postdata();
			wp_reset_query();

			$entity_group = array(
				'entity-title' => $entity_title,
				'press-releases' => $entity_press_releases,
			);
			$entity_data[] = $entity_group;
		}
	}
}

$pageTagline = get_post_meta(get_the_ID(), 'page_tagline', true);
if ($pageTagline && $pageTagline!=""){
	$pageTagline = '<p class="lead-in">' . $pageTagline . '</p>';
}

get_header();
?>

<!-- this section holds the map and is populated later in the page by javascript -->
<div class="feautre-banner">
	<div id="map" class="bbg__map--banner"></div>
</div>

<main id="main" role="main">
	<div class="outer-container">
		<div class="grid-container">
			<header class="page-header">
				<h2 class="section-header"><?php echo $page_title; ?></h2>
				<?php echo $pageTagline; ?>
			</header>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
					<?php
						$pressReleaseCatId = get_category_by_slug('press-release')->term_id;
						foreach($entity_data as $entity) {
							$cat_slug = get_category_by_slug($entity['entity-title']);
							$cat_id = $cat_slug->term_id;
							if ($entity['entity-title'] == 'bbg') {
								$entity['entity-title'] = 'usagm';
							} else if ($entity['entity-title'] == 'rferl') {
								$entity['entity-title'] = 'rfe/rl';
							}
							$press_release_markup  = '<div class="inner-container entity-press-release-group">';
							$press_release_markup .= 	'<div class="main-column">';
							$press_release_markup .= 		'<header>';
							$press_release_markup .= 			'<h3 class="section-subheader">';
							$press_release_markup .= 				'<a href="' . get_category_link($pressReleaseCatId) . '">' . strtoupper($entity['entity-title']) . '</a>';
							$press_release_markup .= 			'</3>';
							$press_release_markup .= 		'</header>';
							$press_release_markup .= 		'<div class="entity-press-release">';
							$press_release_markup .= 			'<header>';
							$press_release_markup .= 				'<h4 class="article-title">';
							$press_release_markup .=  					'<a href="' . get_the_permalink($entity['press-releases'][0]->ID) . '">' . $entity['press-releases'][0]->post_title . '</a>';
							$press_release_markup .= 				'</h4>';
							$press_release_markup .= 			'<header>';
							$press_release_markup .= 			'<p class="date-meta">' . get_the_date('F j, Y', $entity['press-releases'][0]->ID) . '</p>';
							$press_release_markup .= 			'<p>' . $entity['press-releases'][0]->post_excerpt . '</p>';
							$press_release_markup .= 		'</div>';
							$press_release_markup .= 	'</div>';
							$press_release_markup .= 	'<div class="side-column divider-left">';
							array_shift($entity['press-releases']);
							foreach($entity['press-releases'] as $addtl_release) {
								$press_release_markup .= 	'<div class="entity-press-release">';
								$press_release_markup .= 		'<header>';
								$press_release_markup .= 			'<h4 class="sidebar-article-title">';
								$press_release_markup .=  				'<a href="' . get_the_permalink($addtl_release->ID) . '">' . $addtl_release->post_title . '</a>';
								$press_release_markup .= 			'</h4>';
								$press_release_markup .= 		'</header>';
								$press_release_markup .= 		'<p class="date-meta">' . get_the_date('F j, Y', $addtl_release->ID) . '</p>';
								$press_release_markup .= 	'</div>';
							}
							$press_release_markup .= 	'<p class="read-more"><a href="' . get_category_link($pressReleaseCatId) . '">Read more ' . strtoupper($entity['entity-title']) . ' news</a></p>';
							$press_release_markup .= 	'</div>';
							$press_release_markup .= '</div>';

							echo $press_release_markup;
						}
					?>
			</div>
		</div>
	</div>
</main>


<?php
$postsPerPage = 50;
$qParams = array(
	'post_type' => array('post'),
	'category__and' => array(get_cat_id('Map it'), get_cat_id('Press Release')),
	'posts_per_page' => $postsPerPage,
	'post_status' => array('publish')
);

/*** late in the game we ran into a pagination issue, so we're running a second query here ***/
$custom_query_args = $qParams;
$custom_query = new WP_Query($custom_query_args);
$features = array();

if ($custom_query->have_posts()) :
		$counter = 0;
		while ($custom_query->have_posts()) : $custom_query->the_post();
			$id = get_the_ID();
			// $location = get_post_meta($id, 'map_location', true);
			$location = get_field('map_location');
			$storyLink = get_permalink();
			$mapHeadline = get_post_meta($id, 'map_headline', true);
			$mapDescription = get_the_title();
			$mapDate = get_the_date();
			$mapDescription = $mapDescription . ' <span class="bbg__map__infobox__date">(' . $mapDate . ')</span>';

			$pinColor = '#981b1e';
			if (has_category('VOA')){
				$pinColor = '#344998';
				$mapHeadline = '<h2 class="paragraph-header"><a href="' . $storyLink . '">VOA | ' . $mapHeadline . '</a></h2>';
			} elseif (has_category('RFA')){
				$pinColor = "#009c50";
				$mapHeadline = '<h2 class="paragraph-header"><a href="' . $storyLink . '">RFA | ' . $mapHeadline . '</a></h2>';
			} elseif (has_category('RFE/RL')){
				$pinColor = "#ea6828";
				$mapHeadline = '<h2 class="paragraph-header"><a href="' . $storyLink . '">RFE/RL | ' . $mapHeadline . '</a></h2>';
			} else {
				$mapHeadline = '<h2 class="paragraph-header"><a href="' . $storyLink . '">' . $mapHeadline . '</a></h2>';
			}
			if (!empty($location)) {
				$features[] = array(
					'type' => 'Feature',
					'geometry' => array(
						'type' => 'Point',
						'coordinates' => array($location['lng'],$location['lat'])
					),
					'properties' => array(
						'title' => $mapHeadline,
						'description' => $mapDescription,
						'marker-color' => $pinColor,
						'marker-size' => 'large',
						'marker-symbol' => ''
					)
				);
			}
		endwhile;
		$geojsonObj = array(array(
			'type' => 'FeatureCollection',
			'features' => $features
		));
		$geojsonStr = json_encode(new ArrayValue($geojsonObj), JSON_PRETTY_PRINT, 10);

		echo '<script type="text/javascript">';
		echo 	"geojson = $geojsonStr";
		echo "</script>";
endif;
?>

<?php /* include map stuff */ ?>
<script type="text/javascript" src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />

<script type="text/javascript" src='https://api.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css' rel='stylesheet' />
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css' rel='stylesheet' />

<style>
	.marker-cluster-small {
		background-color: rgba(255, 255, 255, 0.6) !important;
	}
	.marker-cluster-small div {
		background-color: rgba(255, 0, 0, 0.6) !important;
	}
</style>

<script type="text/javascript">
L.mapbox.accessToken = '<?php echo 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw'; ?>';

var map = L.mapbox.map('map', 'mapbox.emerald')
	var markers = new L.MarkerClusterGroup({
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

	for (var i = 0; i < geojson[0].features.length; i++) {
		var coords = geojson[0].features[i].geometry.coordinates;
		var title = geojson[0].features[i].properties.title; //a[2];
		var description = geojson[0].features[i].properties['description'];
		if (coords != '') {
			var marker = L.marker(new L.LatLng(coords[1], coords[0]), {
				icon: L.mapbox.marker.icon({
					'marker-symbol': '',
					'marker-color': geojson[0].features[i].properties['marker-color']
				})
			});
			var popupText = title + description;
			marker.bindPopup(popupText);
			markers.addLayer(marker);
		}
	}

	if (coords != '') {
		map.addLayer(markers);
	}

	// Disable the map scroll/zoom so that you can scroll the page.
	map.scrollWheelZoom.disable();

	function centerMap() {
		if (coords != '') {
			map.fitBounds(markers.getBounds());
		}
	}
	centerMap();


	// Recenter the map on resize
	function resizeStuffOnResize(){
		waitForFinalEvent(function(){
			centerMap();
		}, 500, "some unique string");
	}

	// Wait for the window resize to 'end' before executing a function
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
</script>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
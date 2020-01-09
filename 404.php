<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package bbgRedesign
 */
require 'inc/bbg-functions-assemble.php';
get_header();
?>

<div class="outer-container" style="margin-bottom: 30px;">
	<div class="inner-container">
		<h1><span style="color: #900;">404!</span> That page can’t be found.</h1>
	</div>
</div>
<!-- this section holds the map and is populated later in the page by javascript -->
<section class="page-post-featured-graphic">
	<div id="map" class="bbg__map--banner"></div>
</section>

<main id="main" role="main">
	<section class="outer-container">
		<div class="inner-container">
			<p class="lead-in">But here are some recent USAGM highlights from around the world.</p>
			<?php
				/* translators: %1$s: smiley */
				$archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives.', 'bbginnovate' ), convert_smilies( ':)' ) ) . '</p>';
				the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
			?>
		</div>
	</section>
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
				$mapHeadline = '<h5><a href="' . $storyLink . '">VOA | ' . $mapHeadline . '</a></h5>';
			} elseif (has_category('RFA')){
				$pinColor = "#009c50";
				$mapHeadline = '<h5><a href="' . $storyLink . '">RFA | ' . $mapHeadline . '</a></h5>';
			} elseif (has_category('RFE/RL')){
				$pinColor = "#ea6828";
				$mapHeadline = '<h5><a href="' . $storyLink . '">RFE/RL | ' . $mapHeadline . '</a></h5>';
			} else {
				$mapHeadline = '<h5><a href="' . $storyLink . '">' . $mapHeadline . '</a></h5>';
			}
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
		endwhile;
		$geojsonObj = array(array(
			'type' => 'FeatureCollection',
			'features' => $features
		));
		$geojsonStr = json_encode(new ArrayValue($geojsonObj), JSON_PRETTY_PRINT, 10);

		echo '<script type="text/javascript">';
		echo 	"geojson = $geojsonStr";
		echo '</script>';
endif;
?>

<?php /* include map stuff */ ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.3/leaflet.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.3/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/MarkerCluster.Default.css" />
<script type="text/javascript" src="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/leaflet.markercluster-src.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/ghybs/Leaflet.FeatureGroup.SubGroup/v1.0.0/dist/leaflet.featuregroup.subgroup-src.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/jseppi/Leaflet.MakiMarkers/master/Leaflet.MakiMarkers.js"></script>

<style>
	.marker-cluster-small {
		background-color: rgba(255, 255, 255, 0.6) !important;
	}
	.marker-cluster-small div {
		background-color: rgba(255, 0, 0, 0.6) !important;
	}
</style>

<script type="text/javascript">
	var mbToken = bbgConfig.MAPBOX_API_KEY;
	var tilesetUrl = 'https://a.tiles.mapbox.com/v4/mapbox.emerald/{z}/{x}/{y}@2x.png?access_token=' + mbToken;
	var attribStr = '&copy; <a href="https://www.mapbox.com/map-feedback/">Mapbox</a>  &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>';
	var tiles = L.tileLayer(tilesetUrl, {
		maxZoom: 18,
		attribution: attribStr
	});

	var map = L.map('map', {zoom: 13, layers: [tiles]});
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

	L.MakiMarkers.accessToken = mbToken;

	for (var i = 1; i < geojson[0].features.length; i++) {
		var coords = geojson[0].features[i].geometry.coordinates;
		var title = geojson[0].features[i].properties.title; //a[2];
		var description = geojson[0].features[i].properties['description'];
		var marker = L.marker(new L.LatLng(coords[1], coords[0]), {
			icon: L.MakiMarkers.icon({
				'marker-symbol': '',
				'marker-color': geojson[0].features[i].properties['marker-color']
			})
		});
		var popupText = title + description;
		marker.bindPopup(popupText);
		markers.addLayer(marker);
	}

	map.addLayer(markers);

	// Disable the map scroll/zoom so that you can scroll the page.
	map.scrollWheelZoom.disable();

	function centerMap(){
		map.fitBounds(markers.getBounds());
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




<?php get_footer(); ?>
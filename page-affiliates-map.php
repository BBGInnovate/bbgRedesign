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


$pageTagline = get_post_meta(get_the_ID(), 'page_tagline', true);
if ($pageTagline && $pageTagline != "") {
	$pageTagline = '<h6>' . $pageTagline . '</h6>';
}
$secondaryColumnLabel = get_field('secondary_column_label', '', true);
$secondaryColumnContent = get_field('secondary_column_content', '', true);

$fullPath = get_template_directory() . "/external-feed-cache/affiliates.json";

$string = file_get_contents( $fullPath);
$affiliates_group = json_decode($string, true);
$counter = 0;

foreach ($affiliates_group as $affiliate) {
	$counter++;
	if ($counter < 3000) {
		$title = $affiliate[0];
		$lat = $affiliate[1];
		$lon = $affiliate[2];
		$city = $affiliate[3];
		$country = $affiliate[4];
		$freq = $affiliate[5];
		$url = $affiliate[6];
		$smurl = $affiliate[7];
		$platform = $affiliate[8];
		$platformOther = $affiliate[9];

		// JBF 2/27/2017: Per discussions with affiliate team, we don't want to explose titles.
			// Uncomment this code if that ever changes back.
			//
			// $headline = "<h5>" . $title . "</h5>";
			// if ($url != "") {
			// 	if (strpos($url, "http") === false) {
			// 		///echo "fixing " . $url . "<BR>";
			// 		$url = "http://" . $url;
			// 	}
			// 	$headline = "<h5><a target='_blank' href='" . $url . "'>" . $title . "</a></h5>";
			// }
		$headline = "";

		$features[] = array(
			'type' => 'Feature',
			'geometry' => array( 
				'type' => 'Point',
				'coordinates' => array($lon,$lat)
			),
			'properties' => array(
				'title' => $headline,
				'description' => "<strong>Location: </strong>$city<BR><strong>Delivery Platform: </strong>$platform<BR>",
				'marker-color' => "#344998",
				'marker-size' => 'large', 
				'marker-symbol' => '',
				'platform' => $platform
			)
		);
	}
}
$geojsonObj= array(array(
	'type' => 'FeatureCollection',
	'features' => $features
));
$geojsonStr = json_encode(new ArrayValue($geojsonObj), JSON_PRETTY_PRINT, 10);

get_header();
?>
<style> 
	#mapFilters label { margin-left:15px; }
	
	/*  THREATS MAP LOOKS GOOD AT SCREEN < 480 WITHOUT THIS HEIGHT ADJUSTMENT
	 *  IT'S BOUNDS ALLOW A FURTHER IN ZOOM, BUT NEED TO ADJUST MAP TO PREVENT GRAY BARS AT SCREEN < 480
	 */
	
	@media screen and (max-width: 480px) {
		.bbg__map--banner  {
		  background-color: #f1f1f1;
		  height: 215px;
		  width: 100%;
		}
	}
	
	@media screen and (min-width: 900px) {
	  .bbg__map--banner {
	    height: 450px;
	  }
	}
</style>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

			<!-- this section holds the map and is populated later in the page by javascript -->
			<section class="map-banner" style="position: relative;">
				<div id="map" class="bbg__map--banner"></div>

				<img id="resetZoom" src="<?php echo get_template_directory_uri(); ?>/img/home.png" class="bbg__map__button"/>

				<div align="center" id="mapFilters" class="u--show-medium-large">
					<input type="radio" checked name="deliveryPlatform" id="delivery_all" value="all" /><label for="delivery_all"> All</label>
					<input type="radio" name="deliveryPlatform" id="delivery_radio" value="radio" /><label for="delivery_radio"> Radio</label>
					<input type="radio" name="deliveryPlatform" id="delivery_tv" value="tv" /><label for="delivery_tv"> TV</label>
					<input type="radio" name="deliveryPlatform" id="delivery_web" value="web" /><label for="delivery_web"> Digital</label>
				</div>

				<div align="center" id="mapFilters" class="u--hide-medium-large">
					<p></p><h3>Select a delivery platform</h3>
					<select name="deliverySelect">
						<option value="all">All</option>
						<option value="radio">Radio</option>
						<option value="tv">TV</option>
						<option value="web">Digital</option>
					</select>
				</div>
			</section>

			<div class="custom-grid-container">
				<div class="inner-container">
					<div class="main-content-container">
						<?php
							echo '<h2>' . $pageTitle . '</h2>';
							echo $page_content;
						?>
					</div>
					<div class="side-content-container">
						<?php
							if ($secondaryColumnContent != "") {
								if ($secondaryColumnLabel != "") {
									echo '<h5>' . $secondaryColumnLabel . '</h5>';
								}
								echo $secondaryColumnContent;
							}
						?>
					</div>
				</div>
			</div>
		</div>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
	echo "<script type='text/javascript'>\n";
	echo "geojson = $geojsonStr";
	echo "</script>";
	//echo $geojsonStr;
	//http://gis.stackexchange.com/questions/182442/whats-the-most-appropriate-way-to-load-mapbox-studio-tiles-in-leaflet
?>

<?php /* include map stuff */ ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.3/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.3/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/MarkerCluster.Default.css" />
<script src="https://cdn.rawgit.com/Leaflet/Leaflet.markercluster/v1.0.0-beta.2.0/dist/leaflet.markercluster-src.js"></script>
<script src="https://cdn.rawgit.com/ghybs/Leaflet.FeatureGroup.SubGroup/v1.0.0/dist/leaflet.featuregroup.subgroup-src.js"></script>
<script src="https://cdn.rawgit.com/jseppi/Leaflet.MakiMarkers/master/Leaflet.MakiMarkers.js"></script>

<style>
	[class*="marker-cluster-"] {background:rgba(0, 0, 0, 0);}
	.marker-cluster-all-small div {background-color: rgba(240, 194, 12, 1);}
	.marker-cluster-all-medium div {background-color: rgba(241, 128, 23, 1);}
	.marker-cluster-all-large div {background-color: rgba(255, 0, 0, 1);}
	/**** RADIO CLUSTERS ****/
	.marker-cluster-radio-small div {background-color: #80bfff;}
	.marker-cluster-radio-medium div {background-color: #475aff; color: #ffffffff;}
	.marker-cluster-radio-large div {background-color: #000066; color: #ffffff;}
	/**** TV CLUSTERS ****/
	.marker-cluster-tv-small div {background-color: #ba1c21 !important; color: #ffffff;}
	.marker-cluster-tv-medium div {background-color: #7a3336 !important; color: #ffffffff;}
	.marker-cluster-tv-large div {background-color: rgba(255, 0, 0, 1);}
	/**** DIGITAL CLUSTERS ****/
	.marker-cluster-web-small div {background-color: #bdbdbd;}
	.marker-cluster-web-medium div {color: #ffffffff; background-color: #333333;}
	.marker-cluster-web-large div {color: #ffffffff; background-color: #000000;}
</style>

<script type="text/javascript">
	//var tilesetUrl = 'https://api.mapbox.com/styles/v1/mapbox/emerald-v8/tiles/{z}/{x}/{y}?access_token=<?php //echo MAPBOX_API_KEY; ?>';
	selectedPlatform = "all";
	// var mbToken = '<?php //echo MAPBOX_API_KEY; ?>'
	var mbToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
	var tilesetUrl = 'https://a.tiles.mapbox.com/v4/mapbox.emerald/{z}/{x}/{y}@2x.png?access_token='+mbToken;
	var attribStr = '&copy; <a href="https://www.mapbox.com/map-feedback/">Mapbox</a>  &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>';
	<?php 
		if (file_exists($fullPath)) {
		    $lastUpdatedStr = date ("m/d/Y", filemtime($fullPath)) ;
		    echo "attribStr += '<BR><div align=\'right\'>Affiliates last updated $lastUpdatedStr</div>';"; 
		}
	?>
	//https://b.tiles.mapbox.com/v4/mapbox.emerald/2/0/1.png
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
			var c = ' marker-cluster-'+selectedPlatform+'-';
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
            	
    var iconImages= {};
    iconImages["radio"] = "Studio-mic-icon.png";
    iconImages["tv"] = "tv-icon.png";
    iconImages["newspaper"] = "3d-glasses-icon.png";
    iconImages["satellite"] = "3d-glasses-icon.png";
    iconImages["web"] = "modem-icon.png";
    iconImages["mobile"] = "iPhone-Icon.png";
    iconImages["other"] = "webcam-icon.png";

    var maki = {};
    maki["radio"] = {"name": "music", "color":"#0014CC"};
    maki["tv"] = {"name": "aerialway", "color":"#A30000"};
    maki["newspaper"] = {"name": "library", "color":"#ccc"};
    maki["satellite"] = {"name": "heliport", "color":"#b0b"};
    maki["web"] = {"name": "ferry", "color":"#000"};
    maki["mobile"] = {"name": "pitch", "color":"#0b0"};
    maki["other"] = {"name": "fuel", "color":"#FF6600"};

	var deliveryLayers={};    
    for (var deliveryPlatform in iconImages) {
     	if (iconImages.hasOwnProperty(deliveryPlatform)) {
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
        var title = geojson[0].features[i].properties.title; //a[2];
        var description = geojson[0].features[i].properties['description'];
        var platform = geojson[0].features[i].properties['platform'].toLowerCase();

        var icon = L.MakiMarkers.icon({icon: "circle", color: maki[platform].color, size: "m"});
        var marker = L.marker(new L.LatLng(coords[1], coords[0]), {
          icon:icon
        });
       
        var popupText = title + description;
        marker.bindPopup(popupText);
        var targetLayer = deliveryLayers[platform.toLowerCase()];
        marker.addTo(targetLayer);
    }

    map.addLayer(mcg);
	map.scrollWheelZoom.disable();

	function centerMap(){
		map.fitBounds(mcg.getBounds());
	}
	centerMap();

	//Recenter the map on resize
	function resizeStuffOnResize(){
	  waitForFinalEvent(function(){
			centerMap();
	  }, 500, "some unique string");
	}
	jQuery( "#resetZoom" ).click(function() {
		centerMap();
	});

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

	function setSelectedPlatform(platform, displayMode) {
		selectedPlatform = platform;
		for (var p in deliveryLayers) {
			if (deliveryLayers.hasOwnProperty(p)) {
				map.removeLayer(deliveryLayers[p]);
			}
		}
		if (platform == "all") {
			for (var p in deliveryLayers) {
				if (deliveryLayers.hasOwnProperty(p)) {
					console.log('adding layer ' + p);
					map.addLayer(deliveryLayers[p]);
				}
			}
		} else {
			map.addLayer(deliveryLayers[platform]);
		}
		// at mobile (when we're showing a select box) it helps to recenter the map after changing platforms
		if (displayMode=='select') {
			centerMap();	
		}
		
	}

	jQuery( document ).ready(function() {
		jQuery('input[type=radio][name=deliveryPlatform]').change(function() {
			setSelectedPlatform(this.value, 'radio');
		});
		jQuery('select[name=deliverySelect]').change(function() {
			var selectedPlatform = jQuery(this).val();
			setSelectedPlatform(selectedPlatform,'select');
		});
	});
</script>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
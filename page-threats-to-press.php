<?php
/**
 * The template for displaying the Threats to Press page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Threats to Press
 */

$args = array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_wp_page_template',
            'value' => 'page-threats-archive.php'
        )
    )
);
$the_pages = new WP_Query( $args );

if( $the_pages->have_posts() ){
    while( $the_pages->have_posts() ){
        $the_pages->the_post();
        echo get_the_title() . '<br>';
    }
}
wp_reset_postdata();

// PAGE INFORMATION
$page_content = "";
$page_title = "";
$page_excerpt = "";
$page_id = 0;
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_content = get_the_content();
		$page_title = get_the_title();
		$page_excerpt = get_the_excerpt();
		// $ogDescription = $page_excerpt;
		// $page_content = apply_filters('the_content', $page_content);
		// $page_content = str_replace(']]>', ']]&gt;', $page_content);
		$page_id = get_the_ID();
	}
}
wp_reset_postdata();
wp_reset_query();

$threats_category = get_category_by_slug('threats-to-press');
$threatsPermalink = get_category_link( $threats_category->term_id );



// MAP INFORMATION
$cutoff_date = get_field('threats_to_press_map_cutoff_date', $page_id, true);
$threats_posts_map_args = array(
	'post_type'=> 'threat_to_press',
	'post_status' => 'publish',
	'orderby' => 'post_date',
	'order' => 'desc',
	'posts_per_page' => -1,
	'date_query' => array(
        array(
            'after' => $cutoff_date
        )
    )
);
$threats_posts_map_query = new WP_Query($threats_posts_map_args);

$threats_map_data_package = array();
if ($threats_posts_map_query->have_posts()) {
	while ($threats_posts_map_query->have_posts()) { 
		$threats_posts_map_query->the_post();

		$map_threat_id = get_the_ID();
		$country = get_post_meta($map_threat_id, 'threats_to_press_country', true);
		$target_names = get_post_meta($map_threat_id, 'threats_to_press_target_names', true);
		$networks = get_post_meta($map_threat_id, 'threats_to_press_network', true);
		$coordinates = get_post_meta($map_threat_id, 'threats_to_press_coordinates', true);
		$status = get_post_meta($map_threat_id, 'threats_to_press_status', true);
		$link = get_post_meta($map_threat_id, 'threats_to_press_link', true);
		
		$threat_map_data = array(
			'country' => $country,
			'name' => $target_names,
			'date' => get_the_date(),
			'year' => get_the_date('Y'),
			'nice_date' => get_the_date('M d, Y'), 
			'status' => $status,
			'description' => get_the_excerpt(),
			'mugshot' => '',
			'network' => $networks,
			'link' => $link,
			'latitude' => $coordinates['lat'],
			'longitude' => $coordinates['lng'],
			'headline' => get_the_title()
		);
		$threats_map_data_package[] = $threat_map_data;
	}
}
wp_reset_postdata();
wp_reset_query();

$threats_map_caption = get_field( 'threats_to_press_map_caption' );
$trailingDays = get_post_meta($page_id, 'threats_to_press_map_trailing_days', true);
$maxClusterRadius = get_post_meta($page_id, 'threats_to_press_map_maximum_cluster_radius', true);

$threats_map_json  = '<script type="text/javascript">';
$threats_map_json .= 	'threats=' . json_encode(new ArrayValue($threats_map_data_package), JSON_PRETTY_PRINT, 10) . ';';
$threats_map_json .= '</script>';



// GET POSTS OF THREATS TO PRESS FOR PAGE
$threats_posts_args = array(
	'post_type' => array('post'),
	'cat' => get_cat_id('Threats to Press'),
	'posts_per_page' => 6,
	'post_status' => array('publish')
);
$threats_posts_query = new WP_Query($threats_posts_args);
$threats_posts = $threats_posts_query->posts;


// GET FALLEN JOURNALIST INFORMATION
$wall = "";
$journalist = "";
$journalist_name = "";
$mugshot = "";
$altText = "";

$fallen_journalists = get_field('fallen_journalists_section');

if ($fallen_journalists) {
	foreach ($fallen_journalists as $cur_fallen_journalist) {
		$cur_fallen_journalist_id = $cur_fallen_journalist->ID;
		$mugshot = '/wp-content/media/2016/07/blankMugshot.png';
		$date = get_field('profile_date_of_passing', $cur_fallen_journalist_id, true); 
		$date_precision = get_field('profile_date_of_passing_precision', $cur_fallen_journalist_id, true); 
		if ($date_precision == 'month') {
			$dateObj = explode('/', $date);
			$date = $dateObj[0] . '/' . $dateObj[2];
		}
		$link = get_the_permalink($cur_fallen_journalist_id);
		$name = get_the_title($cur_fallen_journalist_id);
		/*** Get the profile photo mugshot ***/
		$profilePhotoID = get_post_meta( $cur_fallen_journalist_id, 'profile_photo', true );
		$profilePhoto = "";
		if ($profilePhotoID) {
			$profilePhoto = wp_get_attachment_image_src( $profilePhotoID , 'mugshot');
			$mugshot = $profilePhoto[0];
		}
		$altText = "";
		$imgSrc = '<img src="' . $mugshot . '" alt="' . $altText . '" class="bbg__profile-grid__profile__mugshot"/>';

		//JBF 2/8/2017: not using link until we fill out profiles.
		if ($link != "") {
			$journalist_name = '<a href="' . $link . '">' . $name . "</a>";
			$imgSrc = '<a href="' . $link . '">' . $imgSrc . "</a>";
		} else {
			$journalist_name = $name;
		}

		$journalist  = "";
		$journalist .= '<div class="bbg__profile-grid__profile">';
		$journalist .= 	$imgSrc;
		$journalist .= 	'<h4 class="bbg__profile-grid__profile__name">' . $journalist_name . '</h4>';
		$journalist .= 	'<h5 class="bbg__profile-grid__profile__dates">Killed ' . $date . '</h5>';
		$journalist .= 	'<p class="bbg__profile-grid__profile__description"></p>';
		$journalist .= '</div>';
		$wall .= $journalist;
	}
}
wp_reset_postdata();
wp_reset_query();


// CUSTOM QUOTATION SPECIFIC TO THIS PAGE (IN WORDPRESS BACKEND)
$include_custom_quotation = get_field('quotation_include', '', true);
$quotation = "";
if ($include_custom_quotation) {
	$quotation_text = get_field('quotation_text', '', false);
	$quotation_speaker = get_field('quotation_speaker', '', false);
	$quotation_tagline = get_field('quotation_tagline', '', false);

	$quote_mugshot_id = get_field('quotation_mugshot', '', false);
	$quote_mugshot = "";

	if ($quote_mugshot_id) {
		$quote_mugshot = wp_get_attachment_image_src($quote_mugshot_id , 'mugshot');
		$quote_mugshot = $quote_mugshot[0];
	}

	$quotation  = '<img class="quote-image"  src="' . $quote_mugshot .'" alt="Mugshot">';
	$quotation .= '<h4 class="quote-text">'. $quotation_text .'</h4>';
	$quotation .= '<p  class="quote-byline">';
	$quotation .= 	$quotation_speaker .'<br>';
	$quotation .= 	'<span class="occupation">'. $quotation_tagline .'</span>';
	$quotation .= '</p>';
}


get_header();

// DISPLAY MAP
echo $threats_map_json;
?>

<style>
	.map-legends, .map-tooltip {
		/*
		JBF 2/8/2017: max-width & width were useful with custom legend
		view https://www.mapbox.com/mapbox.js/example/v1.0.0/custom-legend/ to see an example
		max-width:120px !important;
		width:120px;
		*/
		padding:3px 5px 3px 5px;

	}

	.map-legend {
		padding:0px !important; 
	}
	.legend label, .legend span {
		display:block;
		float:left;
		height:15px;
		width:33%;
		text-align:center;
		font-size:9px;
		color:#808080;
	}
	.legend label {
		margin-top:0px;
		margin-bottom:5px;
	}
	/*** temp fix for this page ***/
	#mapFilters label {margin-left: 15px;}
	.leaflet-right {display: none;}

	@media screen and (max-width: 599px) {
		.bbg-grid--1-2-2 { 
			padding-top:1rem;
		}
	}
</style>

<div id="map-threats" class="bbg__map--banner" style="margin-bottom: 10px;"></div>
<div id="threats-map-controls" class="outer-container">
	<div class="grid-container">
		<p class="aside"><?php echo $threats_map_caption ?></p>
		<div align="center" id="mapFilters" class="u--show-medium-large">
			<input type="radio" checked name="trainingYear" id="delivery_all" value="all" /><label for="delivery_all"> All</label>
			<input type="radio" name="trainingYear" id="trainingYear_2019" value="2019" /><label for="trainingYear_2019"> 2019</label>
			<input type="radio" name="trainingYear" id="trainingYear_2018" value="2018" /><label for="trainingYear_2018"> 2018</label>
			<input type="radio" name="trainingYear" id="trainingYear_2017" value="2017" /><label for="trainingYear_2017"> 2017</label>
			<input type="radio" name="trainingYear" id="trainingYear_2016" value="2016" /><label for="trainingYear_2016"> 2016</label>
			<input type="radio" name="trainingYear" id="trainingYear_2015" value="2015" /><label for="trainingYear_2015"> 2015</label>
			<input type="radio" name="trainingYear" id="trainingYear_2014" value="2014" /><label for="trainingYear_2014"> 2014</label>
			<input type="radio" name="trainingYear" id="trainingYear_2013" value="2013" /><label for="trainingYear_2013"> 2013</label>
		</div>

		<div align="center" id="mapFilters" class="u--hide-medium-large">
			<p class="paragraph-header">Select a year</p>
			<select name="trainingSelect">
				<option value="all">All</option>
				<option value="2019">2019</option>
				<option value="2018">2018</option>
				<option value="2017">2017</option>
				<option value="2016">2016</option>
				<option value="2015">2015</option>
				<option value="2014">2014</option>
				<option value="2013">2013</option>
			</select>
		</div>
	</div>
</div>

<main id="main" role="main">
	<section class="outer-container" style="margin-top: 3rem;">
		<div class="grid-container">
			<?php
				echo '<h2>' . $page_title . '</h2>';
				echo '<p class="lead-in">' . $page_content . '</p>';
			?>
		</div>
	</section>

	<?php
		// NEWS AND UPDATES
		$threat_structure  = '<section class="outer-container threats-box">';
		$threat_structure .= 	'<div class="grid-half" id="threats-main-column">';
		$threat_structure .= 			'<article>';
		$threat_structure .= 				'<div class="article-image">';
		$threat_structure .= 					'<a href="' . get_the_permalink($threats_posts[0]) . '">';
		$threat_structure .= 						'<img src="' . get_the_post_thumbnail_url($threats_posts[0], 'large') . '" alt="Image link to ' . get_the_title($threats_posts[0]) . ' post">';
		$threat_structure .= 					'</a>';
		$threat_structure .= 				'</div>';
		$threat_structure .= 				'<div class="article-info">';
		$threat_structure .= 					'<h4><a href="' . get_the_permalink($threats_posts[0]) . '">' . get_the_title($threats_posts[0]) . '</a></h4>';
		$threat_structure .= 				'</div>';
		$threat_structure .= 			'</article>';
		$threat_structure .= 	'</div>';
		$threat_structure .= 	'<div class="grid-half" id="threats-side-column">';
		$secondary_threats = array_shift($threats_posts);
		foreach ($threats_posts as $recent_threat) {
			$threat_structure .= 		'<article>';
			$threat_structure .= 			'<h4><a href="' . get_the_permalink($recent_threat) . '">' . get_the_title($recent_threat) . '</a></h4>';
			$threat_structure .= 		'</article>';
		}
		$threat_structure .= 	'</div>';
		$threat_structure .= '</section>';
		echo $threat_structure;
	?>

	<?php
		$featuredJournalists = "";
		$profilePhoto = "";

		if (have_rows('featured_journalists_section')) {
		    while (have_rows('featured_journalists_section')) {
		    	the_row();
				$featuredJournalistsSectionLabel = get_sub_field('featured_journalists_section_label');

				if (have_rows('featured_journalist')) {
					$featuredJournalists .= '<div class="outer-container">';
					$featuredJournalists .= 	'<div class="grid-container">';
					$featuredJournalists .= 		'<h3>' . $featuredJournalistsSectionLabel . '</h3>';
					$featuredJournalists .=  		'<div class="nest-container">';
					$featuredJournalists .=  			'<div class="inner-container">';

					while (have_rows('featured_journalist')) {
						the_row();	
						$relatedPages = get_sub_field('featured_journalist_profile');
						$profileTitle = $relatedPages->post_title;
						$profileName = $relatedPages->first_name . ' ' . $relatedPages->last_name;
						$profileOccupation = $relatedPages->occupation;
						$profilePhoto = $relatedPages->profile_photo;
						$profileUrl = get_permalink($relatedPages->ID);
						$profileExcerpt = my_excerpt($relatedPages->ID);

						$profileOccupation = '<span class="bbg__profile-excerpt__occupation">' . $profileOccupation .'</span>';

						if ($profilePhoto) {
							$profilePhoto = wp_get_attachment_image_src( $profilePhoto , 'Full');
							$profilePhoto = $profilePhoto[0];
							$profilePhoto = '<a href="' . $profileUrl . '"><img src="' . $profilePhoto . '" class="bbg__profile-excerpt__photo" alt="Profile photo"></a>';
						}

						$featuredJournalists .= '<div class="grid-half profile-clears">';
						$featuredJournalists .= 	$profilePhoto;
						$featuredJournalists .= 	'<h4 class="bbg__profile-excerpt__name"><a href="' . $profileUrl . '">'. $profileName .'</a></h4>';
						$featuredJournalists .= 	'<p class="bbg__profile-excerpt__text">' . $profileOccupation . $profileExcerpt . '</p>';
						$featuredJournalists .= '</div>';
					}
					$featuredJournalists .= 			'</div>';
					$featuredJournalists .= 		'</div>';
					$featuredJournalists .= 	'</div>';
					$featuredJournalists .= '</div>';
				}
			}
		}
		echo $featuredJournalists;
	?>

	<div class="outer-container bbg__memorial">
		<div class="grid-container">
			<h3>Fallen journalists</h3>
			<div class="usa-grid">
				<?php echo $wall; ?>
			</div>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<div class="usagm-quotation ">
				<?php echo $quotation; ?>
			</div>
		</div>
	</div>


	<script type="text/javascript" src='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.js'></script>
	<link href='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.css' rel='stylesheet' />


	<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.0.3/leaflet.markercluster.js'></script>
	<link href='https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.0.3/MarkerCluster.css' rel='stylesheet' />
	<link href='https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.0.3/MarkerCluster.Default.css' rel='stylesheet' />
	<!-- <script type="text/javascript" src="https://cdn.rawgit.com/ghybs/Leaflet.FeatureGroup.SubGroup/v1.0.0/dist/leaflet.featuregroup.subgroup-src.js"></script>-->
	<script type="text/javascript" src="https://cdn.rawgit.com/ghybs/Leaflet.FeatureGroup.SubGroup/master/src/subgroup.js"></script>

	<style>
		.marker-cluster-small, .marker-cluster-small div, .marker-cluster-medium, .marker-cluster-medium div  {
			/* D4A5A8 */
			background-color: #ba1c21 !important;
			color:#FFF;
		}
		
		.marker-cluster-large, .marker-cluster-large div {
			/* #981b1e */
			background-color: #7a3336 !important;
			color:#FFF;
			/* font-size:15px; */

		} 
		.mapBubbleDate {
			font-style: italic;
			margin-bottom:4px;
		}
		/*
		.marker-cluster-killed div {
			background-color: rgba(0, 0, 0, 1) !important;
			color:#FFF;
		}
		*/
	</style>

	<script type="text/javascript">
		L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var initialCenter = [28.304380682962783, 22.148437500000004];
		
		/**** this calculation of initial zoom based on the window width is done to prevent a dramatic zoom done right when you start the map ***/
		var initialZoom = 3;
		if (window.innerWidth < 600) {
			initialZoom=1;
		} else if (window.innerWidth < 1160) {
			initialZoom = 2;
		}
		var map = L.mapbox.map('map-threats', 'mapbox.emerald', {attributionControl:false}).setView(initialCenter, initialZoom);
		// map.legendControl.addLegend(document.getElementById('legend').innerHTML);
		var attribStr = '';
		var attribution = L.control.attribution({prefix:false, position:'topright'}).addTo(map);
		 // attribution.setPrefix(attribStr);
		 // //attribution.addAttribution(attribStr);
		 // attribution.addTo(map);

		var mcg = new L.MarkerClusterGroup({
			maxClusterRadius: <?php echo $maxClusterRadius; ?>,
			iconCreateFunction: function (cluster) {
				var childCount = cluster.getChildCount();
				var c = ' marker-cluster-';
				if (childCount < 10) {
				    c += 'small';
				} else if (childCount < 20) {
				    c += 'medium';
				} else {
				    c += 'large';
				}
				return new L.DivIcon({ html: '<div><span><b>' + childCount + '</b></span></div>', className: 'marker-cluster' + c, iconSize: new L.Point(0, 0), iconAnchor: new L.Point(20,20) }); 
			}
		});
		// var killedMarkers = new L.MarkerClusterGroup({
		// 	iconCreateFunction: function (cluster) {
		// 		var childCount = cluster.getChildCount();
		// 		var c = ' marker-cluster-killed';
		// 		return new L.DivIcon({ html: '<div><span><b>' + childCount + '</b></span></div>', className: 'marker-cluster' + c, iconSize: new L.Point(40, 40) });
		// 	}
		// });

		layers = {};    
		layersNoCluster = {};

	    for (var year=2020; year >= 2013; year--) {
     		var newLayer = L.featureGroup.subGroup(mcg);
     		newLayer.addTo(map);
     		layers[year] = newLayer;
     		var newLayer2 =  L.featureGroup();
     		layersNoCluster[year] = newLayer2;
	    }

		var markerColor = "#900";
		for (var i = 0; i < threats.length; i++) {
			var t = threats[i];

			var headline = t.name;
			if (t.headline != "") {
				headline = t.headline;
			}
			var titleLink = "<h5>" + headline + "</h5>";
			if (t.link != "") {
				titleLink="<h5><a href='" + t.link + "'>" + headline + "</a></h5>";
			}

			if (false && t.status == "Killed"){
				//this code should never get executed. It's legacy code from when the map had different behavior.  feel free to eventually delete.
				markerColor = "#000";
				var marker = L.marker(new L.LatLng(t.latitude, t.longitude), {
					icon: L.mapbox.marker.icon({
						'marker-symbol': '',
						'marker-color': markerColor
					})
				});
				marker.bindPopup(titleLink + t.description + '<BR><BR>' + t.nice_date);
				killedMarkers.addLayer(marker);
				//marker.addTo(map);

			} else {

				// JBF 3/8/2017: this comment block is here for the future when we re-enable colors based on status

				// if ( t.status == "threatened" || t.status == "arrested" || t.status == "detained") {
				// 	markerColor = "#B66063";
				// } else if ( t.status == "missing" || t.status == "attacked") {
				// 	markerColor = "#981b1e";
				// } else if (t.status == "killed") {
				// 	markerColor = "#000";
				// } else {
				// 	//check this pin to see what the status is
				// 	markerColor = "#F0F";
				// }
				markerColor = "#DB6266";
				var marker = L.marker(new L.LatLng(t.latitude, t.longitude), {
					icon: L.mapbox.marker.icon({
						'marker-symbol': '',
						'marker-color': markerColor
					})
				});

				marker.bindPopup(titleLink + '<div class="mapBubbleDate">' + t.nice_date + '</div>' + t.description);
				var targetLayer = layers[t.year];
				marker.addTo(targetLayer);

				//we need a separate instance of the markers for the individual years because they're not supposed to have clustering
				var marker2 = L.marker(new L.LatLng(t.latitude, t.longitude), {
					icon: L.mapbox.marker.icon({
						'marker-symbol': '',
						'marker-color': markerColor
					})
				});
				marker2.bindPopup(titleLink + '<div class="mapBubbleDate">' + t.nice_date + '</div>' + t.description);
				marker2.addTo(layersNoCluster[t.year])
			}
		}

	    map.addLayer(mcg);
	    //map.addLayer(killedMarkers);

		//Disable the map scroll/zoom so that you can scroll the page.
		map.scrollWheelZoom.disable();

		function centerMap(){
			if (activeYear == "all") {
				lGroup = mcg;
			} else {
				lGroup = layersNoCluster[activeYear];
			}
			if (lGroup) {
				map.fitBounds(lGroup.getBounds());
			}
			
		}

		//Recenter the map on resize
		function resizeStuffOnResize(){
		  waitForFinalEvent(function(){
				centerMap();
		  }, 500, "some unique string");
		}

		jQuery( "#resetZoom" ).click(function() {
			centerMap();
		});

		//Wait for the window resize to 'end' before executing a function---------------
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

		

		function setSelectedYear(year, displayMode) {
			activeYear = year;
			for (var p in layers) {
				if (layers.hasOwnProperty(p)) {
					map.removeLayer(layers[p]);
					map.removeLayer(layersNoCluster[p]);
				}
			}
			if (year == "all") {
				for (var p in layers) {
					if (layers.hasOwnProperty(p)) {
						map.addLayer(layers[p]);
					}
				}
			} else {
				map.addLayer(layersNoCluster[year]);
			}
			//at mobile (when we're showing a select box) it helps to recenter the map after changing platforms
			//if (displayMode=='select') {
			//	centerMap();	
			//}
			
		}
		jQuery( document ).ready(function() {
			jQuery('input[type=radio][name=trainingYear]').change(function() {
				setSelectedYear(this.value, 'radio');
			});
			jQuery('select[name=trainingSelect]').change(function() {
				var year = jQuery(this).val();
				setSelectedYear(year,'select');
			});
			//initialize the year to 'all' at startup
			setSelectedYear('all');
		});

		/*
		//Test if zoomed in
		//Could be used for hiding or graying the home/reset button
		function zoomLevel(){
			console.log('check zoom: ' + map.getZoom());
			return map.getZoom();
		}

		map.on('click', zoomLevel);
		markers.on('click', zoomLevel);
		*/
	</script>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
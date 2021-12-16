<?php

	function pressFreedomMap() {
	$freeNotFreeObj = array_map('str_getcsv', file(get_template_directory() . '/data/freeNotFree.csv'));
	if (count($freeNotFreeObj)) {

		//remove the first row from the array because it's headers, not data
		array_shift($freeNotFreeObj);
	}
	$freeNotFreeStr = json_encode(new ArrayValue($freeNotFreeObj), JSON_PRETTY_PRINT);	
    ob_start();
?>
		<!-- Styles -->
		<style>
			/* start expanding the print styles */
			#chartdiv {
				border: 1px solid #CCC;
				font-size: 11px;
				height: 200px;
				width: 100%;
		}
		@media screen and (min-width: 600px) {
			#chartdiv {
				height: 400px;
			}
		}
		@media screen and (min-width: 900px) {
			#chartdiv {
				height: 500px;
			}
		}
		.amcharts-legend-div {
			position: fixed !important;
			top: 520px !important;
			padding: 10px;
		}

		.amcharts-chart-div > a {
			display: none !important;
		}

		#loading {
			display: none;
			z-index: 9997; 
			position: absolute;
			bottom: 5%;
			left: 5%;
			width: 50px;
			height: 50px;
		}
		.legendBox {
			width: 15px;
			height: 15px;
			display:inline-block;
			background: #000000;
		}
		#main-content {
			padding-top: 0px !important;
		}
		#legendContainer {
			margin-top: 1rem;
		}
	</style>
	<!-- Resources -->
	<script type="text/javascript" src="https://www.amcharts.com/lib/3/ammap.js"></script>
	<script type="text/javascript" src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>

	<script type="text/javascript" src='<?php echo get_stylesheet_directory_uri(); ?>/data/threats.js'></script>
	<script type="text/javascript" src='<?php echo get_stylesheet_directory_uri(); ?>/js/map-pressfreedom.js'></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- <h2>Press Freedom Scores</h2>
	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non lacus velit. Proin porta ultricies ex non vulputate. Aenean maximus convallis varius. Invidual threats are mapped below with the <i class="fa fa-map-pin" aria-hidden="true"></i> icon and you may <a style="text-decoration: underline;" href="https://www.bbg.gov/2016-threats-archive/" target="_blank">view a full list</a> on the bbg.gov site.</p> -->
	
	<div class="bbg__map-area__container " style="postion: relative;">
		<div id="chartdiv"></div>
		<div align="center" id="legendContainer">
					<div align="center" >
						<div class="legendBox free"></div> Free 
						<div class="legendBox partially-free"></div> Partially Free 
						<div class="legendBox not-free"></div> Not Free 
					</div>
				</div>
	</div>
<?php 
	echo '<script type="text/javascript">';
	echo "freeNotFree = $freeNotFreeStr";
	echo "</script>";
	$str = ob_get_clean();
	return $str;
	}

add_shortcode( 'pressFreedomMap', 'pressFreedomMap' );

/*$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'Full' );
	$ogImage = $thumb['0'];
<section class="usa-section">
<div class="usa-grid">
</div>
</section>*/

	function getFeaturedMapGeoJsonStr( $featuredMapItems ) {
		$features = [];

		for ($i=0; $i < count($featuredMapItems); $i++) {
			$item = $featuredMapItems[$i];
			$featuredMapItemLocation = $item['featured_map_item_coordinates'];
			$featuredMapItemTitle = $item['featured_map_item_title'];
			$featuredMapItemDescription = $item['featured_map_item_description'];
			$featuredMapItemLink = $item['featured_map_item_link'];
			$featuredMapItemVideoLink = $item['featured_map_item_video_link'];
			$im = $item['featured_map_item_image'];

			$featuredMapItemImageUrl = $im['sizes']['medium'];

			$popupBody = "";
			if ($featuredMapItemLink != "") {
				$popupBody .= "<h5><a style='font-weight: bold; ' href='$featuredMapItemLink'>$featuredMapItemTitle</a></h5><div class='u--show-medium-large'><img src='$featuredMapItemImageUrl' alt='Featured map item'></div><BR>$featuredMapItemDescription";
			} else {
				$popupBody .= "<h5><span style='font-weight: bold;'>$featuredMapItemTitle</span></h5><div class='u--show-medium-large'><img src='$featuredMapItemImageUrl' alt='Featured map item'></div><BR>$featuredMapItemDescription";
			}

			$features[] = array(
				'type' => 'Feature',
				'geometry' => array(
					'type' => 'Point',
					'coordinates' => array($featuredMapItemLocation['lng'],$featuredMapItemLocation['lat'])
				),
				'properties' => array(
					'title' => "<a href='$featuredMapItemLink'>$featuredMapItemTitle</a>",
					'description' => $popupBody,
					'marker-size' => 'large',
					'marker-symbol' => ''
				)
			);
		}
		$geojsonObj= array(array(
			'type' => 'FeatureCollection',
			'features' => $features
		));
		$geojsonStr=json_encode(new ArrayValue($geojsonObj), JSON_PRETTY_PRINT, 10);
		return $geojsonStr;
	}

	function getMapData() {
		$entities = array(
			'voa' => array(
				'countries' => array(),
				'services' => array()
			),
			'rferl' => array(
				'countries' => array(),
				'services' => array()
			),
			'ocb' => array(
				'countries' => array(),
				'services' => array()
			),
			'rfa' => array(
				'countries' => array(),
				'services' => array()
			),
			'mbn' => array(
				'countries' => array(),
				'services' => array()
			),
		);

		$qParams = array(
			'post_type' => 'country',
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'orderby' => 'post_title',
			'order' => 'asc'
		);
		$custom_query = new WP_Query($qParams);
		$countries = array();
		while ($custom_query -> have_posts())  {
			$custom_query -> the_post();
			$id = get_the_ID();
			$countryName = get_the_title();
			$countryAmmapCode = get_post_meta($id, 'country_ammap_country_code', true);
			$networks = array();
			$terms = get_the_terms($id, "language_services" , array('hide_empty' => false));
			if ($terms) {
				$categoryHierarchy = array();
				sort_terms_hierarchically($terms, $categoryHierarchy);
				foreach ($categoryHierarchy as $t) {
					$n1 = array(
						'networkName' => $t -> name,
						'services' => array()
					);
					$entities [strtolower($t -> name)]['countries'][$countryName] = 1;
					foreach ($t -> children as $service) {
						$n1['services'][] = $service -> name;
					}
					$networks [] = $n1;
				}
			}
			$countries[$countryName] = array(
				'countryName' => $countryName,
				'ammapCode' => $countryAmmapCode,
				'networks' => $networks
			);

		}
		$terms = get_terms( array(
			'taxonomy' => 'language_services',
			'hide_empty' => false,
		));

		$parentTerms = array();
		foreach ($terms as $t) {
			$isParent = ($t -> parent == 0);
			if ($isParent) {
				$parentTerms[$t -> term_id] = $t -> name;
			}
		}

		$servicesByName = array();
		foreach ($terms as $t) {
			$isParent = ($t -> parent == 0);
			$parentTerm = "";

			if (!$isParent) {
				$parentTerm = $parentTerms[$t -> parent];
			}

			$termMeta = get_term_meta($t -> term_id);

			$siteName = "";
			$siteUrl = "";
			if (count( $termMeta )) {
				$siteName = $termMeta['language_service_site_name'][0];
				$siteUrl = $termMeta['language_service_site_url'][0];
			}
			$servicesByName[$t -> name] = array(
				'serviceName' => $t -> name,
				'siteName' => $siteName,
				'siteUrl' => $siteUrl,
				'parent' => $parentTerm,
				'countries' => array() //filled out by JS
			);
		}

		wp_reset_postdata();
		wp_reset_query();

		$countryStr = json_encode(new ArrayValue($countries), JSON_PRETTY_PRINT);
		$entityStr = json_encode(new ArrayValue($entities), JSON_PRETTY_PRINT);
		$serviceStr = json_encode(new ArrayValue($servicesByName), JSON_PRETTY_PRINT);

		$js_map_attributes  = '<script type="text/javascript">';
		$js_map_attributes .= 	'var entitiesByName = ' . $entityStr . ';';
		$js_map_attributes .= 	'var servicesByName = ' . $serviceStr . ';';
		$js_map_attributes .= 	'var countriesByName = ' . $countryStr . ';';
		$js_map_attributes .= '</script>';
		return $js_map_attributes;
	}

	function getMapScripts() {
		$scripts = '';
		$scripts .= '<script type="text/javascript">';
		$scripts .= '    var template_directory_uri = "' . get_template_directory_uri() . '";';
		$scripts .= '    var uploads_dir = "' . (wp_get_upload_dir()['baseurl']) . '";';
		$scripts .= '    for (serviceName in servicesByName) {';
		$scripts .= '        if (servicesByName.hasOwnProperty(serviceName)) {';
		$scripts .= '            s = servicesByName[serviceName];';
		$scripts .= '            if (s.parent != "") {';
		$scripts .= '                entitiesByName[s.parent.toLowerCase()]["services"].push(serviceName);';
		$scripts .= '            }';
		$scripts .= '        }';
		$scripts .= '    }';
		$scripts .= '</script>';
		$scripts .= '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/vendor/ammap.js"></script>';
		$scripts .= '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/mapdata-worldLow.js"></script>';
		$scripts .= '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/map-entity-reach.js"></script>';

		return $scripts;
	}
	function getMapMarkup() {
		$markup = '
				<section class="outer-container">
					<div class="grid-container">
						<div class="btn-group entity-buttons" role="group" aria-label="..." style="display: inline; clear: none;">
							<button type="button" title="USAGM" class=" btn-default usagm"><span class="bbg__map__button-text">USAGM</span></button><!--
						--><button type="button" title="VOA" class=" btn-default voa"><span class="bbg__map__button-text">VOA</span></button><!--
						--><button type="button" title="RFA" class=" btn-default rfa"><span class="bbg__map__button-text">RFA</span></button><!--
						--><button type="button" title="RFERL" class=" btn-default rferl"><span class="bbg__map__button-text">RFERL</span></button><!--
						--><button type="button" title="OCB" class=" btn-default ocb"><span class="bbg__map__button-text">OCB</span></button><!--
						--><button type="button" title="MBN" class=" btn-default mbn"><span class="bbg__map__button-text">MBN</span></button>
						</div>
					</div>

					<div class="outer-container">
						<div class="main-content-container bbg__map-area">
							<div class="bbg__map-area__container">
								<div id="chartdiv"></div>
									<h4 class="country-label-tooltip"><span id="country-name"></span></h4>
									<h4 class="service-label"></h4>
								</div>
								<select id="country-list">
									<option value="0">Select a country…</option>
								</select>
							</div>

							<div class="side-content-container bbg__map-area__text">
								<div id="countryDisplay">
									<h2 id="countryName" class="bbg__map-area__country-name">Country Name</h2>
									<div class="country-details">
										<div style="display:none;" id="languagesSupported">
											<p><strong>Languages supported: </strong><span class="languages-served"></span></p>
										</div>
									</div>
									<div class="service-block"></div>
								</div>

							<div id="entityDisplay" class="bbg__map__entity">
								<div id="entityLogo" class="bbg__map__entity-logo__container"></div>
								<div class="bbg__map__entity-text">
									<h2 id="entityName" class="bbg__map__entity-text__name">Entity Name</h2>
								</div>
								<div id="entityDescription" class="bbg__map__entity-text__details">Entity description</div>
								<div id="serviceDropdownBlock">
									<select id="service-list">
										<option value="0"></option>
									</select>
									<button id="view-on-map">View on map</button>
									<button id="submit">Visit site</button>
								</div>
								<div id="globalBlock"></div>
							</div>
							<div id="languageServiceDisplay"></div>
						</div>
					</div>
				</section>';

		return $markup;
	}
?>
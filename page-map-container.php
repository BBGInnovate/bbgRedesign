<?php
/**
 * The template for containing maps created using our ammap.js Vector maps
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 * template name: Map Container
 */

$page_content = "";
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_content = get_the_content();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}
wp_reset_postdata();
wp_reset_query();

get_header();

echo getNetworkExcerptJS();

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
	echo $js_map_attributes;
}

getMapData();
?>


<script type="text/javascript">
// setting a variable with the base url to pass into the entity map
var template_directory_uri = '<?php echo get_template_directory_uri(); ?>';

for (serviceName in servicesByName) {
	if (servicesByName.hasOwnProperty(serviceName)) {
		s = servicesByName[serviceName];
		if (s.parent != "") {
			entitiesByName[s.parent.toLowerCase()]['services'].push(serviceName);
		}
	}
}
</script>
<script type="text/javascript" src='<?php echo get_template_directory_uri(); ?>/js/vendor/ammap.js'></script>
<script type="text/javascript" src='<?php echo get_template_directory_uri(); ?>/js/mapdata-worldLow.js'></script>
<script type="text/javascript" src='<?php echo get_template_directory_uri(); ?>/js/map-entity-reach.js'></script>

<main id="main" role="main">

	<div class="outer-container">
		<div class="grid-container">
			<?php echo '<h2>' . get_the_title() . '</h2>'; ?>
			<?php echo '<p>' . get_the_excerpt() . '</p>'; ?>
		</div>
	</div>

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
	</section>
</main>

<?php get_footer(); ?>
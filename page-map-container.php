<?php
/**
 * The template for containing maps created using our ammap.js Vector maps
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 * template name: Map Container
 */

function getNetworkExcerptJS2() {
	/* used on map container */
	$entityParentPage = get_page_by_path('networks');
	$qParams=array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $entityParentPage->ID
	);

	$e = array();
	$custom_query = new WP_Query($qParams);
	if ($custom_query -> have_posts()) {
		while ( $custom_query -> have_posts() )  {
			$custom_query->the_post();
			$id=get_the_ID();
			$fullName=get_post_meta( $id, 'entity_full_name', true );
			if ($fullName != "") {
				$abbreviation=strtolower(get_post_meta( $id, 'entity_abbreviation', true ));
				$abbreviation=str_replace("/", "",$abbreviation);
				$description=get_post_meta( $id, 'entity_description', true );
				$link=get_permalink( get_page_by_path( "/broadcasters/$abbreviation/" ) );
				$url = get_post_meta( $id, 'entity_site_url', true );

				$imgSrc=get_template_directory_uri().'/img/logo_'.$abbreviation.'--circle-200.png'; //need to fix this
				$e[$abbreviation] = array(
					'description' => $description,
					'fullName' => $fullName,
					'url' => $url
				);
			}
		}
	}
	wp_reset_postdata();
	$e['bbg'] = array(
		'description' => 'The mission of the Broadcasting Board of Governors is to inform, engage, and connect people around the world in support of freedom and democracy.',
		'fullName' => 'Broadcasting Board of Governors',
		'url' => 'https://www.bbg.gov'
	);
	$s = "<script type='text/javascript'>\n";
	$entityJson = json_encode(new ArrayValue($e), JSON_PRETTY_PRINT);
	$entityJson = str_replace("\/", "/", $entityJson);
	$s .= "entities=" . $entityJson . ";";
	$s .="</script>";

	return $s;
}

$pageContent="";
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$pageContent = get_the_content();
		$pageContent = apply_filters('the_content', $pageContent);
		$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
	endwhile;
endif;
wp_reset_postdata();
wp_reset_query();

get_header();
echo getNetworkExcerptJS2();

function getMapData() {

	$entities = array(
		'mbn' => array(
			'countries' => array()
		),
		'ocb' => array(
			'countries' => array()
		),
		'rfa' => array(
			'countries' => array()
		),
		'rferl' => array(
			'countries' => array()
		),
		'voa' => array(
			'countries' => array()
		)
	);

	$qParams=array(
		'post_type' => 'country'
		,'post_status' => array('publish')
		,'posts_per_page' => -1
		,'orderby' => 'post_title'
		,'order' => 'asc'
	);
	$custom_query = new WP_Query($qParams);
	$countries = array();

	while ( $custom_query -> have_posts() )  {
		$custom_query->the_post();
		$id = get_the_ID();
		$countryName = get_the_title();
		$countryAmmapCode = get_post_meta( $id, 'country_ammap_country_code', true );
		
		$services = array();
		$terms = get_the_terms($id, "language_services");
		if ($terms) {
			foreach ($terms as $t) {
				
				$s = array(
					'serviceName' => $t->name
					,'isParent' => ( ($t -> parent == 0))
				);

				if (isset($entities[strtolower($t->name)])) {
					$entities [strtolower($t->name)]['countries'][$countryName] = true;
				}

				$termMeta = get_term_meta( $t->term_id);
				if (count($termMeta)) {
					$s['siteName'] = $termMeta['language_service_site_name'][0];
					$s['siteUrl'] = $termMeta['language_service_site_url'][0];
				} else {
					$s['siteName'] = "";
					$s['siteUrl'] = "";
				}
				$services []= $s;
			}
		} else {
			//echo "no language services for " . $countryName . "<BR>";
		}
		$countries[$countryName] = array(
			"countryName" => $countryName,
			"ammapCode" => $countryAmmapCode,
			"services" => $services
		);
	}
	$countryStr = json_encode(new ArrayValue($countries), JSON_PRETTY_PRINT);
	$entityStr = json_encode(new ArrayValue($entities), JSON_PRETTY_PRINT);
	
	echo "<script type='text/javascript'>\n";
	echo "entitiesByName = $entityStr";
	echo "</script>";

	echo "<script type='text/javascript'>\n";
	echo "countriesByName = $countryStr";
	echo "</script>";

}

?>



<style>
/*temp styles */
</style>

<?php getMapData(); ?>

	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/vendor/ammap.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/mapdata-worldLow.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/map-entity-reach-noapi.js'></script>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="usa-grid-full" style="margin-bottom: 5rem;">
				<div class="usa-grid">
					<header class="page-header">

						<?php if($post->post_parent) {
							//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
							$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
							$parent_link = get_permalink($post->post_parent);
						?>
						<h5 class="bbg__label--mobile large"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h5>

						<?php } ?>

						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

					</header><!-- .page-header -->
					<h3 id="site-intro" class="usa-font-lead"><?php echo get_the_excerpt(); ?> <!--<a href="/who-we-are/" class="bbg__read-more">LEARN MORE »</a>--></h3>
				</div><!-- div.usa-grid -->
			</div><!-- div.usa-grid-full -->

			<section class="usa-section">

				<div class="usa-grid">
					<div class="btn-group entity-buttons" role="group" aria-label="..." style="display: inline; clear: none;">
						<button type="button" title="BBG" class=" btn-default bbg"><span class="bbg__map__button-text">BBG</span></button><!--
						--><button type="button" title="VOA" class=" btn-default voa"><span class="bbg__map__button-text">VOA</span></button><!--
						--><button type="button" title="RFA" class=" btn-default rfa"><span class="bbg__map__button-text">RFA</span></button><!--
						--><button type="button" title="RFERL" class=" btn-default rferl"><span class="bbg__map__button-text">RFERL</span></button><!--
						--><button type="button" title="OCB" class=" btn-default ocb"><span class="bbg__map__button-text">OCB</span></button><!--
						--><button type="button" title="MBN" class=" btn-default mbn"><span class="bbg__map__button-text">MBN</span></button>
					</div>
					<!--<h5 class="bbg__map__entity-buttons__instructions" style=""> (Select a network) </h5>-->
				</div>


				<div class="usa-grid">
					<div class="usa-grid-full" style="/*background-color: #F1F1F1;*/">
						<div class="usa-width-two-thirds bbg__map-area">
							<div class="bbg__map-area__container " style="position: relative; background-color: #FFF;">
								<div id="chartdiv"></div>
								 <h4 class="country-label-tooltip"><span id="country-name"></span></h4>
								 <h4 class="service-label"></h4>
							</div>
							<select id="country-list">
								<option value="0">Select a country...</option>
							</select>
						</div>
						<div class="usa-width-one-third bbg__map-area__text" style="">
							<div id="countryDisplay">
								<h2 id="countryName" class="bbg__map-area__country-name">Country Name</h2>
								<div class="country-details">
									<div style="display:none;" id="languagesSupported">
										<p><strong>Languages supported: </strong><span class="languages-served"></span></p>
									</div>
								</div>
								<div class="groups-and-subgroups"></div>
								<div class="service-block">
									<select id="service-list">
										<option value="0">Select a subgroup...</option>
									</select>
									<button id="view-on-map">View on map</button>
									<button id="submit">Visit site</button>
								</div>
								<div class="other-subgroups"></div>
							</div>

							<div id="entityDisplay">
								<h2 id="entityName" class="bbg__map-area__country-name">Entity Name</h2>
								<div class="entity-details">
									<p class="detail"></p>
								</div>
							</div>

							<div id="languageServiceDisplay">
								
							</div>

						</div><!-- div.usa-width-one-third -->
					</div><!-- .usa-grid-full -->
				</div><!-- div.usa-grid -->

			</section><!-- map -->



			<section id="" class="usa-section usa-grid" style="margin-bottom: 2rem;">
				<?php /* echo $pageContent;*/ ?>
			</section>

			</div><!-- .usa-grid-full -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
<!-- 
<div class="usa-grid">
	<form style="margin-bottom: 2rem; max-width: none;">
		<label for="options" style="display: inline-block; font-size: 2rem; font-weight: bold;">Select an entity</label>
		<select id="entity" name="options" id="options" style=" display: inline-block;">
			<option value="bbg">BBG</option>
			<option value="voa">VOA</option>
			<option value="rfa">RFA</option>
			<option value="rferl">RFERL</option>
			<option value="ocb">OCB</option>
			<option value="mbn">MBN</option>
		</select>
	</form>
</div>
-->
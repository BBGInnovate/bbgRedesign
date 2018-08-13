<?php
/**
 * This is the template that displays the BBG entity pages.
 * VOA, RFE/RL, OCB, RFA and MBN
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Entity
 */


require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
		$ogDescription = get_the_excerpt(); //get_the_excerpt()
	}
}
wp_reset_postdata();
wp_reset_query();

$fullName = get_post_meta($id, 'entity_full_name', true);
$abbreviation = get_post_meta($id, 'entity_abbreviation', true);
$description = get_post_meta($id, 'entity_description', true);
$siteUrl = get_post_meta($id, 'entity_site_url', true);
$rssFeed = get_post_meta($id, 'entity_rss_feed', true);
$entityLogoID = get_post_meta($id, 'entity_logo',true);
$websiteName = get_post_meta($id, 'entity_website_name', true);

$entityLogo = "";
if ($entityLogoID) {
	$entityLogoObj = wp_get_attachment_image_src($entityLogoID , 'Full');
	$entityLogo = $entityLogoObj[0];
}

$awardSlug = get_post_meta($id, 'entity_award_recipient_taxonomy_slug', true);
$entityApiID = get_post_meta($id, 'entity_api_id', true);
$entityCategorySlug = get_post_meta($id, 'entity_category_slug', true);
$entityCategoryObj = get_category_by_slug($entityCategorySlug);
$entityAwardsPageLink = get_permalink( get_page_by_path('awards'));
$entityAwardsLinkFiltered = add_query_arg('entity', $awardSlug, $entityAwardsPageLink);

$entityMission = get_post_meta($id, 'entity_mission', true);
$subgroups = getEntityLinks_taxonomy($entityCategorySlug);

$site_select = "<h5>Explore the $abbreviation websites</h5>";
if (count($subgroups) < 4) {
	$site_select .= "<ul class='bbg__rss__list'>";
	foreach ($subgroups as $s) {
		if ($s->website_url != "") { // EX: mbn digital
			$site_select .= "<li class='bbg__rss__list-link'><a target='_blank' href='" . $s->website_url . "'>" . $s->name . "</a></li>";
		}
	}
	$site_select .= "</ul>";
} else {
	$site_select .= "<select name='entity_sites' id='entity_sites'>";
	$site_select .= "<option>Select a service</option>";
	foreach ( $subgroups as $s ) {
		if ( $s->website_url != "" ) { // EX: mbn digital
			$site_select .= "<option value='" . $s->website_url . "'>" . $s->name . "</option>";
		}
	}
	$site_select .= "</select><button class='usa-button' id='entityUrlGo'>Go</button>";
}

//Entity fast facts / by-the-numbers
$budget = get_post_meta($id, 'entity_budget', true);
$employees = get_post_meta($id, 'entity_employees', true);
$languages = get_post_meta($id, 'entity_languages', true);
$audience = get_post_meta($id, 'entity_audience', true);
$appLink = get_post_meta($id, 'entity_mobile_apps_link', true);
$primaryLanguage = get_post_meta($id, 'entity_primary_language', true);

if ($budget != "") {
	$budget = '<li><span class="paragraph-header">Annual budget: </span>' . $budget . '</li>';
}
if ($employees != "") {
	$employees = number_format( floatval( $employees ), 0, '.', ',' );
	$employees = '<li><span class="paragraph-header">Employees: </span>' . $employees . '</li>';
}
if ($languages != "") {
	if ($languages == "1"){
		$languages = '<li><span class="paragraph-header">Language supported: </span>' . $primaryLanguage . '</li>';
	} else {
		$languages = '<li><span class="paragraph-header">Languages supported: </span>' . $languages . '</li>';
	}
}
if ($audience != "") {
	$audience = '<li><span class="paragraph-header">Audience estimate: </span>' . $audience . '</li>';
}
if ($appLink != "") {
	$app_link_markup  = '<h5>Download the apps</h5>';
	$app_link_markup .= '<p class="aside">' . $appLink . '<br><a href="https://www.bbg.gov/apps/">Visit the apps page</a></p>';
}

// SOCIAL, CONTACT LINKS
$twitterProfileHandle = get_post_meta($id, 'entity_twitter_handle', true);
$facebook = get_post_meta($id, 'entity_facebook', true);
$instagram = get_post_meta($id, 'entity_instagram', true);


// CONTACT INFORMATION
$email = get_post_meta($id, 'entity_email', true);
$phone = get_post_meta($id, 'entity_phone', true);
$street = get_post_meta($id, 'entity_street', true);
$city = get_post_meta($id, 'entity_city', true);
$state = get_post_meta($id, 'entity_state', true);
$zip = get_post_meta($id, 'entity_zip', true);
$learnMore = get_post_meta($id, 'entity_learn_more', true);

$address = "";
$map = "";
$mapLink = "";
$includeContactBox = false;

if ($email != "") {
	$email_li  = '<li>';
	$email_li .= 	'Email: ';
	$email_li .= 	'<a href="mailto:' . $email . '" title="Email ' . $abbreviation . '">';
	$email_li .= 		$email;
	$email_li .= 	'</a>';
	$email_li .= '</li>';
}
if (!empty($phone)) {
	$phone_li  = '<li>';
	$phone_li .= 	'Tel: ';
	$phone_li .= 	$phone;
	$phone_li .= '</li>';
}
if (!empty($learnMore)) {
	$learnMore  = '<li>';
	$learnMore .= 	'<a href="'. $learnMore . '">Learn more</a> about ' . $abbreviation;
	$learnMore .= '</li>';
}


if (!empty($street) && !empty($city) && !empty($state) && !empty($zip)) {
	$address = $street . '<br/>' . $city . ', ' . $state . ' ' . $zip;

	// STRIP SPACES FOR URL ENCODING
	$street = str_replace(" ", "+", $street);
	$city = str_replace(" ", "+", $city);
	$state = str_replace(" ", "+", $state);
	$size = 400;
	$zoom = 14;
	$map = 'http://maps.googleapis.com/maps/api/staticmap?center=' . $street . ',+' . $city . ',+' . $state . '+' . $zip . "&zoom=" . $zoom . "&scale=false&size=" . $size . "x" . $size . "&maptype=roadmap&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:1%7C".$street.',+'.$city.',+'.$state . ');';
	$mapLink = 'https://www.google.com/maps/place/' . $street . ',+' . $city . ',+' . $state . '+' . $zip . '/';
	$address = '<p><a href="'. $mapLink . '">' . $address . '</a></p>';
}

// ADD A MAP
$includeMap = get_post_meta(get_the_ID(), 'map_include', true);
if ($includeMap) {
	$mapLocation = get_post_meta(get_the_ID(), 'map_location', true);
	$mapHeadline = get_post_meta(get_the_ID(), 'map_headline', true);
	$mapDescription = get_post_meta(get_the_ID(), 'map_description', true);
	$mapPin = get_post_meta(get_the_ID(), 'map_pin', true);
	$mapZoom = get_post_meta(get_the_ID(), 'map_zoom', true);

	$key = '<?php echo MAPBOX_API_KEY; ?>';
	$zoom = 8;
	if ($mapZoom > 0 && $mapZoom < 20) {
		$zoom = $mapZoom;
	}

	$lat = $mapLocation['lat'];
	$lng = $mapLocation['lng'];
	$pin = "";

	if ($mapPin) {
		$pin = "pin-s+990000(" . $lng .",". $lat .")/";
	}
	//Static map like this:
	//$map = "https://api.mapbox.com/v4/mapbox.emerald/" . $pin . $lng . ",". $lat . "," . $zoom . "/170x300.png?access_token=" . $key;
}

if ($address != "" || $phone_li != "" || $email_li != "") {
	$includeContactBox = true;
}

// Default adds a space above header if there's no image set
$featuredImageClass = " bbg__article--no-featured-image";
$bannerPosition = get_post_meta($id, 'adjust_the_banner_image', true);
$videoUrl = "";

/**** BEGIN CREATING rssItems array *****/
$entityJson = getFeed($rssFeed, $id);
$rssItems = array();
$itemContainer = false;
$languageDirection = "";

if (property_exists($entityJson, 'channel') && property_exists($entityJson->channel, 'item')) {
	$itemContainer = $entityJson->channel;
} else {
	$itemContainer = $entityJson;
}
if ($itemContainer) {
	if (property_exists($itemContainer, 'language')) {
		if ($itemContainer -> language == "ar"){
			$languageDirection = " rtl";
		}
	}
	foreach ($itemContainer -> item as $e) {
		$title = $e -> title;
		$url = $e -> link;
		$description = $e -> description;
		$enclosureUrl = "";
		if (property_exists($e, 'enclosure') && property_exists($e -> enclosure, '@attributes') && property_exists($e -> enclosure -> {'@attributes'}, 'url') ) {
			$enclosureUrl = ( $e -> enclosure -> {'@attributes'} -> url );
		}
		$rssItems[] = array( 'title' => $title, 'url' => $url, 'description' => $description, 'image' => $enclosureUrl );
	}
}

/**** DONE CREATING rssItems array *****/

/**** START FETCH related press releases ****/
$prCategorySlug = get_post_meta( $id, 'entity_pr_category', true );
$pressReleases = array();
if ( $prCategorySlug != "" ) {
	$prCategoryObj = get_category_by_slug( $prCategorySlug );
	if ( is_object($prCategoryObj) ) {
		$prCategoryID = $prCategoryObj -> term_id;
		$qParams = array(
			'post_type' => array( 'post' ),
			'posts_per_page' => 5,
			'category__and' => array( $prCategoryID ),
			'orderby', 'date',
			'order', 'DESC'
		);
		$custom_query = new WP_Query( $qParams );
		if ( $custom_query -> have_posts() ) {
			while ( $custom_query -> have_posts() )  {
				$custom_query -> the_post();
				$id = get_the_ID();
				$pressReleases[] = array( 'url' => get_permalink($id), 'title' => get_the_title($id), 'excerpt' => get_the_excerpt());
			}
		}
		wp_reset_postdata();
	}
}
$s = "";
if (count($pressReleases)) {
	foreach ( $pressReleases as $pr ) {
		$url = $pr['url'];
		$title = $pr['title'];

		$press_markup .= '<div>';
		$press_markup .= 	'<h4><a href="' . $url . '">' . $title . '</a></h4>';
		$press_markup .= 	'<p>' . $pr['excerpt'] . '</p>';
		$press_markup .= '</div>';
	}
	$press_markup .= '<div>';
	$entityCategoryLink = get_category_link($entityCategoryObj -> term_id);
	$press_markup .= 	'<p>';
	$press_markup .= 		'<a href="' . $entityCategoryLink . '">View all ' . $abbreviation . '  highlights »</a>';
	$press_markup .= 	'</p>';
	$press_markup .= "</div>";
}
$page_content = str_replace("[press releases]", $press_markup, $page_content);
/**** END FETCH related press releases ****/

/**** START FETCH AWARDS ****/
$awards = array();
$qParams = array(
	'posts_per_page' => 5,
	'post_type' => 'award',
	'orderby' => 'date',
	'order' => 'DESC',
	'meta_key' => 'standardpost_award_recipient',
	'meta_value' => $awardSlug
);
$custom_query = new WP_Query($qParams);
if ($custom_query -> have_posts()) {
	while ( $custom_query -> have_posts() )  {
		$custom_query -> the_post();
		$id = get_the_ID();

		$awardYears  = get_post_meta( $id, 'standardpost_award_year' );
		$awardTitle = get_post_meta( $id, 'standardpost_award_title', true );
		$orgTerms = get_field( 'standardpost_award_organization', $id );
	    $organizations = array();
	    $organizations[] = $orgTerms -> name;


		$recipients = get_post_meta( $id, 'standardpost_award_recipient' );
		$awards[] = array(
			'id'=>$id,
			'url'=>get_permalink($id),
			'title'=> get_the_title($id),
			'excerpt'=> get_the_excerpt(),
			'awardYears'=> $awardYears,
			'awardTitle'=> $awardTitle,
			'organizations'=> $organizations,
			'recipients'=> $recipients
		);
	}
}
wp_reset_postdata();
$s = "";
if ( count($awards) ) {
	foreach ( $awards as $a ) {
		$id = $a['id'];
		$url = $a['url'];
		$title = $a['title'];
		$awardYears = $a['awardYears'];
		$awardTitle = $a['awardTitle'];
		$organizations = $a['organizations'];
		$recipients = $a['recipients'];

		$awards_markup .= '<div>';
		$awards_markup .= 	'<h4><a href="' . $url . '">' . $title . '</a></h4>';
		$awards_markup .= 	'<p>';
		$awards_markup .= 		'<span>' . $awardTitle . ', ' . join($organizations) . '</span>';
		$awards_markup .= 		'(' . join($awardYears) . ')';
		$awards_markup .= 	'</p>';
		$awards_markup .= 	'<p>' . $a['excerpt'] . '</p>';
		$awards_markup .= '</div>';
	}
	$awards_markup .= '<div>';
	$awards_markup .= 	'<a href="' . $entityAwardsLinkFiltered . '">View all ' . $abbreviation . ' awards »</a>';
	$awards_markup .= '</div>';
}
$page_content = str_replace("[awards]", $awards_markup, $page_content);
/**** END FETCH AWARDS ****/

/**** START FETCH threats to press ****/
$threats = array();
$threatsCategoryObj = get_category_by_slug("threats-to-press");
$threatsCategoryID = $threatsCategoryObj->term_id;
if ($entity_category_slug != "") {
	if (is_object($entityCategoryObj)) {
		$entityCategoryID = $entityCategoryObj->term_id;
		$qParams = array(
			'post_type' => array('post'),
			'posts_per_page' => 3,
			'category__and' => array(
									$entityCategoryID,
									$threatsCategoryID
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
		$custom_query = new WP_Query($qParams);
		if ($custom_query -> have_posts()) {
			while ( $custom_query -> have_posts() )  {
				$custom_query->the_post();
				$id=get_the_ID();
				$threats[]=array('url'=>get_permalink($id), 'title'=> get_the_title($id), 'excerpt'=>get_the_excerpt(), 'thumb'=>get_the_post_thumbnail( $id, 'small-thumb' ));
			}
		}
		wp_reset_postdata();
	}
}
/**** END FETCH threats to press ****/
$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
$abbreviation = str_replace("/", "", $abbreviation);
$network_logo = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png';

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php
			$featured_media_result = get_feature_media_data();
			if ($featured_media_result != "") {
				echo $featured_media_result;
			}
		?>

		<div class="outer-container">
			<div class="main-content-container">
				<div class="nest-container">
					<div class="inner-container">
						<div class="icon-side-content-container">
							<img src="<?php echo $entityLogo; ?>">
							
							<h5>Website</h5>
							<p class="aside"><?php echo strtolower($websiteName); ?></p>

							<?php
								if (!empty($facebook) || !empty($twitterProfileHandle) || !empty($instagram)) {
									echo '<article>';
									echo 	'<h5>Social Media</h5>';
									if (!empty($facebook)) {
										echo '<a href="' . $facebook . '" title="Like ' . get_the_title() . ' on Facebook">';
										echo 	'<span class="bbg__article-share__icon facebook"></span>';
										echo '</a>';
									}
									if (!empty($twitterProfileHandle)) {
										echo '<a href="https://twitter.com/' . $twitterProfileHandle . '" title="Follow ' . get_the_title() . ' on Twitter">';
										echo 	'<span class="bbg__article-share__icon twitter"></span>';
										echo '</a>';
									}
									if (!empty($instagram)) {
										echo '<a href="https://instagram.com/' . $instagram . '" title="Follow ' . get_the_title() . ' on Instagram">';
										echo 	'<span class="bbg__article-share__icon instagram"></span>';
										echo '</a>';
									}
									echo '</article>';
								}
							?>
						</div>
						<div class="icon-main-content-container">
							<?php echo '<h2>' . $fullName . '</h2>'; ?>
							<div class="page-content">
								<?php echo $page_content; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- BEGIN SIDEBAR -->
			<aside class="side-content-container">
				<article>
					<div id="social-share">
						<h5>Share</h5>
						<a href="<?php echo $fbUrl; ?>">
							<span class="bbg__article-share__icon facebook"></span>
						</a>
						<a href="<?php echo $twitterURL; ?>">
							<span class="bbg__article-share__icon twitter"></span>
						</a>
					</div>
				</article>

				<article>
				<?php
					if ($entityMission!="") { 
						echo $entityMission;
					}
				?>
				</article>

			<!-- FAST FACTS -->
			<?php
				echo '<article>';
				if ($budget != "" || $employees != "" || $languages != "" || $audience != "" || $app_link_markup != "") {
					echo '<h5>Fast facts</h5>';
				}
				echo 	'<ul>';
				echo 		$budget;
				echo 		$employees;
				echo 		$languages;
				echo 		$audience;
				echo 	'</ul>';
				echo '</article>';

				$ethics_data = get_journalistic_code_of_ethics_data();
				$ethics_parts = build_ethics_file_parts($ethics_data);
				if (!empty($ethics_parts)) {
					echo '<aside>';
					echo '<h5>Journalistic Standards</h5>';
					foreach($ethics_parts as $ethic) {
						echo $ethic;
					}
					echo '</aside>';
				}


				echo '<article>';
				echo 	$app_link_markup;
				echo '</article>';

				if (count($rssItems)) {
					$rss_markup  = '<article class="inner-container">';
					$rss_markup .= 	'<h5>Recent stories from ' . $websiteName . '</h5>';
					$maxRelatedStories = 3;
					for ($i = 0; $i < min($maxRelatedStories, count($rssItems)); $i++) {
						$o = $rssItems[$i];
						$rss_markup .= '<div class="nest-container">';
						$rss_markup .= 	'<div class="inner-container">';
						$rss_markup .= 		'<div class="side-content-container">';
						if ($o['image'] != "") {
							$rss_markup .= 		'<a href="' . $o['url'] . '">';
							$rss_markup .= 			'<img src="' . $o['image'] . '" />';
							$rss_markup .= 		'</a>';
						}
						$rss_markup .= 		'</div>';
						$rss_markup .= 		'<div class="main-content-container">';
						$rss_markup .= 			'<h6>';
						$rss_markup .= 				'<a href="' . $o['url'] . '">' . $o['title'] . '</a>';
						$rss_markup .= 			'</h6>';
						$rss_markup .= 		'</div>';
						$rss_markup .= 	'</div>';
						$rss_markup .= '</div>';
					}
					$rss_markup .= '</article>';

					echo $rss_markup;

					if (count($threats)) {
						$maxThreatsStories = 3;

						$threats_markup  = '<h6 class="bbg__label small"><a href="/threats-to-press/">Threats to Press</a></h6>';
						$threats_markup .= '<ul class="bbg__rss__list">';
						for ($i = 0; $i < min($maxRelatedStories, count($threats)); $i++) {
							$o = $threats[$i];
							$threats_markup .= '<li class="bbg__rss__list-link">';
							$threats_markup .= 	'<a href="' . $o['url'] . '">';
							$threats_markup .= 		$o['title'];
							$threats_markup .= 	'</a>';
							$threats_markup .= '</li>';
						}
						$threats_markup .= '</ul>';

						echo '<article>';
						echo 	$threats_markup;
						echo '</article>';
					}
				}
				echo '<article>';
				echo 	$site_select;
				echo '</article>';

				// CONTACT INFORMATION
				if ($includeContactBox) {
					$contact_box  = 	'<article class="inner-container bbg__contact-card';
					if ($includeMap) {
						$contact_box .= ' bbg__contact-card--include-map';
					}
					$contact_box .= 	'">';
					if ($includeMap) {
						$contact_box .= 	'<div id="map" class="bbg__contact-card__map"></div>';
					}
					$contact_box .= 		'<div class="grid-container">';
					$contact_box .= 			'<h4>Contact information</h4>';
					$contact_box .= 			$address;
					$contact_box .= 			'<ul class="no-list-style">';
					$contact_box .= 				$phone_li;
					$contact_box .= 				$email_li;
					$contact_box .= 				$learnMore;
					$contact_box .= 			'</ul>';
					$contact_box .= 		'</div>';
					$contact_box .= 	'</article>';
					echo $contact_box;
				}
			?>
			</aside>
		</div>

		<div class="outer-container">
			<footer class="entry-footer bbg-post-footer 1234">
				<?php
					edit_post_link(
						sprintf(
							/* translators: %s: Name of current post */
							esc_html__('Edit %s', 'bbginnovate'),
							the_title('<span class="screen-reader-text">"', '"</span>', false)
						),
						'<span class="edit-link">',
						'</span>'
					);
				?>
			</footer><!-- .entry-footer -->

			<?php
				$quote_result = getRandomQuote($entityCategorySlug, array());
				if ($quote_result) {
					echo '<div class="bbg__entity__pullquote">';
					outputQuote($quote_result);
					echo '</div>';
				}
			?>
		</div>

		<div class="bbg-post-footer">
		</div>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
/* if the map is set, then load the necessary JS and CSS files */
if ($includeMap) {
?>
	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />

	<script type="text/javascript">
	L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
	var map = L.mapbox.map('map', 'mapbox.streets')
		//.setView([38.91338, -77.03236], 16);
		<?php echo '.setView(['. $lat . ', ' . $lng . '], ' . $zoom . ');'; ?>

	map.scrollWheelZoom.disable();

	L.mapbox.featureLayer({
		// this feature is in the GeoJSON format: see geojson.org
		// for the full specification
		type: 'Feature',
		geometry: {
			type: 'Point',
			// coordinates here are in longitude, latitude order because
			// x, y is the standard for GeoJSON and many formats
			coordinates: [
				//-77.03221142292,
				//38.913371603574
				<?php echo $lng . ', ' . $lat; ?>
			]
		},
		properties: {
			title: '<?php echo $mapHeadline; ?>',
			description: '<?php echo $mapDescription; ?>',
			// one can customize markers by adding simplestyle properties
			// https://www.mapbox.com/guides/an-open-platform/#simplestyle
			'marker-size': 'large',
			'marker-color': '#981b1e',
			'marker-symbol': ''
		}
	}).addTo(map);

	</script>
<?php } ?>

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
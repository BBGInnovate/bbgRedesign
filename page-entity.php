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
		$entity_page_id = get_the_ID();
		$page_link = get_the_permalink();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
		$ogDescription = get_the_excerpt();
	}
}
wp_reset_postdata();
wp_reset_query();


$entity_full_name = get_post_meta($entity_page_id, 'entity_full_name', true);
$abbreviation = get_post_meta($entity_page_id, 'entity_abbreviation', true);
$description = get_post_meta($entity_page_id, 'entity_description', true);
$siteUrl = get_post_meta($entity_page_id, 'entity_site_url', true);
$entityLogoID = get_post_meta($entity_page_id, 'entity_logo',true);
$websiteName = get_post_meta($entity_page_id, 'entity_website_name', true);

/**
 * For some reason the url for the OTF page is not turning into an anchor tag
 * Force <a> tags here
 */
if (is_page('Open Technology Fund')) {
	$websiteName = '<a href="' . $siteUrl . '">' . $websiteName . '</a>';
}


// GET RSS FEED
$rss_xml = get_post_meta($id, 'rss_xml', true);
if (!empty($rss_xml)) {
	include 'inc/rss-data-structure.php';
	$rss_markup = create_rss_markup($id, $rss_xml, $websiteName);
}


$entityLogo = "";
if ($entityLogoID) {
	$entityLogoObj = wp_get_attachment_image_src($entityLogoID , 'Full');
	$entityLogo = $entityLogoObj[0];
}

$awardSlug = get_post_meta($entity_page_id, 'entity_award_recipient_taxonomy_slug', true);
$entityApiID = get_post_meta($entity_page_id, 'entity_api_id', true);
$entityCategorySlug = get_post_meta($entity_page_id, 'entity_category_slug', true);
$entityCategoryObj = get_category_by_slug($entityCategorySlug);
$entityAwardsPageLink = get_permalink(get_page_by_path('awards'));
$entityAwardsLinkFiltered = add_query_arg('entity', $awardSlug, $entityAwardsPageLink);

$entityMission = get_post_meta($entity_page_id, 'entity_mission', true);
$entity_link_groups = getEntityLinks_taxonomy($entityCategorySlug);
$site_select = '<h3 class="sidebar-section-header">Explore the ' . $abbreviation . ' websites</h3>';
if (count($entity_link_groups) < 4) {
	$site_select .= '<ul class="bbg__rss__list">';
	foreach ($entity_link_groups as $entity_link) {
		if ($entity_link->website_url != "") { // EX: mbn digital
			$site_select .= '<li class="bbg__rss__list-link">';
			$site_select .= 	'<a target="_blank" href="' . $entity_link->website_url . '">' . $entity_link->name . '</a>';
			$site_select .= '</li>';
		}
	}
	$site_select .= '</ul>';
} else {
	$site_select .= '<select name="entity_sites" id="entity_sites">';
	$site_select .= '<option>Select a service</option>';
	foreach ($entity_link_groups as $entity_link) {
		if ($entity_link->website_url != "") { // EX: mbn digital
			$site_select .= '<option value="' . $entity_link->website_url . '">' . $entity_link->name . '</option>';
		}
	}
	$site_select .= '</select><button class="usa-button" id="entityUrlGo">Go</button>';
}

//Entity fast facts / by-the-numbers
$budget = get_post_meta($entity_page_id, 'entity_budget', true);
$employees = get_post_meta($entity_page_id, 'entity_employees', true);
$languages = get_post_meta($entity_page_id, 'entity_languages', true);
$audience = get_post_meta($entity_page_id, 'entity_audience', true);
$appLink = get_post_meta($entity_page_id, 'entity_mobile_apps_link', true);
$factSheet = get_post_meta($entity_page_id, 'fact_sheet', true);

$primaryLanguage = get_post_meta($entity_page_id, 'entity_primary_language', true);

if ($budget != "") {
	$budget = '<li><span class="sidebar-article-title">Annual budget: </span>' . $budget . '</li>';
}
if ($employees != "") {
	$employees = number_format( floatval( $employees ), 0, '.', ',' );
	$employees = '<li><span class="sidebar-article-title">Employees: </span>' . $employees . '</li>';
}
if ($languages != "") {
	if ($languages == "1"){
		$languages = '<li><span class="sidebar-article-title">Language supported: </span>' . $primaryLanguage . '</li>';
	} else {
		$languages = '<li><span class="sidebar-article-title">Languages supported: </span>' . $languages . '</li>';
	}
}
if ($audience != "") {
	$audience = '<li><span class="sidebar-article-title">Audience estimate: </span>' . $audience . '</li>';
}
if ($factSheet != "") {
	$factSheetUrl = wp_get_attachment_url($factSheet);
	$factSheet = '<a href="' . $factSheetUrl . '">Download the ' . $abbreviation . ' Fact Sheet</a>';
}
if ($appLink != "") {
	$app_link_markup  = '<h3 class="sidebar-section-header">Download the apps</h3>';
	$app_link_markup .= '<p class="sans">' . $appLink . '<br><a href="https://www.bbg.gov/apps/">Visit the apps page</a></p>';
}


// PRESS 1. GET CITED PRESS CLIPS OF THIS ENTITY
$cur_press_entity = strtolower($abbreviation);
	// TEMPORARILY SWITCH BECUASE OUTLET NAME IN TAXONOMY HAS NO SLASH
	if ($cur_press_entity == "rfe/rl") {$cur_press_entity = "rferl";}

$press_clip_query_args = array(
	'post_type' => 'media_clips',
	'posts_per_page' => 5,
	'meta_query'	=> array(
		'relation' => 'OR',
		array(
			'key' => 'outlet_citations',
			'value' => $cur_press_entity,
			'compare' => 'LIKE'
		)
	)
);
$all_media_clips = new WP_Query($press_clip_query_args);

// PRESS 2. GO TO functions.php AND PERFORM FUNCTION, RETURN THE POST'S DATA
// $press_clippings_data = request_media_query_data($all_media_clips);

// PRESS 3. CSS SPECIFIC TO PRESS CLIPPINGS
$press_clippings_style  = '<style>';
$press_clippings_style .= 	'.entity_press_clippings .sidebar-article-title {margin-bottom: 1rem}';
$press_clippings_style .= 	'.entity_press_clippings .sidebar-article-title .sans {font-size: 0.9em; font-weight: 500;} ';
$press_clippings_style .= '</style>';
echo $press_clippings_style;


// SOCIAL, CONTACT LINKS
$twitter_profile_handle = get_post_meta($entity_page_id, 'entity_twitter_handle', true);
$facebook = get_post_meta($entity_page_id, 'entity_facebook', true);
$instagram = get_post_meta($entity_page_id, 'entity_instagram', true);


// CONTACT INFORMATION
$street = get_post_meta($entity_page_id, 'entity_street', true);
$city = get_post_meta($entity_page_id, 'entity_city', true);
$state = get_post_meta($entity_page_id, 'entity_state', true);
$zip = get_post_meta($entity_page_id, 'entity_zip', true);
$phone = get_post_meta($entity_page_id, 'entity_phone', true);
$email = get_post_meta($entity_page_id, 'entity_email', true);
$learnMore = get_post_meta($entity_page_id, 'entity_learn_more', true);

$address = "";
$map = "";
$mapLink = "";
$includeContactBox = false;

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
	$address = '<a href="'. $mapLink . '" target="_blank">' . $address . '</a>';
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

if ($address != "" || $phone != "" || $email != "") {
	$includeContactBox = true;
}

// Default adds a space above header if there's no image set
$featuredImageClass = " bbg__article--no-featured-image";
$bannerPosition = get_post_meta($entity_page_id, 'adjust_the_banner_image', true);
$videoUrl = "";


// START FETCH RELATED PRESS RELEASES
$prCategorySlug = get_post_meta($entity_page_id, 'entity_pr_category', true);
$pressReleases = array();
if ($prCategorySlug != '') {
	$prCategoryObj = get_category_by_slug($prCategorySlug);
	if (is_object($prCategoryObj)) {
		$prCategoryID = $prCategoryObj -> term_id;
		// CATEGORIES TO EXCLUDE: 2280: USAGM Experts (dev testing 2276)
		$qParams = array(
			'post_type' => array('post'),
			'posts_per_page' => 3,
			'category__and' => array($prCategoryID),
			'category__not_in' => array(2280),
			'orderby', 'date',
			'order', 'DESC'
		);
		$custom_query = new WP_Query( $qParams );
		if ($custom_query -> have_posts()) {
			while ($custom_query -> have_posts())  {
				$custom_query -> the_post();
				$pr_page_id = get_the_ID();
				$pressReleases[] = array( 'url' => get_permalink($pr_page_id), 'title' => get_the_title($pr_page_id), 'excerpt' => get_the_excerpt());
			}
		}
		wp_reset_postdata();
	}
}
$press_markup = '';
if (count($pressReleases)) {
	foreach ($pressReleases as $pr) {
		$url = $pr['url'];
		$title = $pr['title'];

		$press_markup .= '<div>';
		$press_markup .= 	'<h4 class="article-title"><a href="' . $url . '">' . $title . '</a></h4>';
		$press_markup .= 	'<p>' . $pr['excerpt'] . '</p>';
		$press_markup .= '</div>';
	}
	$press_markup .= '<div>';
	$entityCategoryLink = get_category_link($entityCategoryObj -> term_id);
	$press_markup .= 	'<p class="read-more sans">';
	$press_markup .= 		'<a href="' . $entityCategoryLink . '">View all ' . $abbreviation . '  highlights »</a>';
	$press_markup .= 	'</p>';
	$press_markup .= '</div>';
}
// PRESS RELEASE SHORTCODE
$page_content = str_replace("[press releases]", $press_markup, $page_content);


// START FETCH AWARDS
$awards = array();
$qParams = array(
	'posts_per_page' => 3,
	'post_type' => 'award',
	'orderby' => 'date',
	'order' => 'DESC',
	'meta_key' => 'standardpost_award_recipient',
	'meta_value' => $awardSlug
);
$custom_query = new WP_Query($qParams);
if ($custom_query -> have_posts()) {
	while ($custom_query -> have_posts())  {
		$custom_query -> the_post();
		$award_post_id = get_the_ID();

		$awardYears  = get_post_meta($award_post_id, 'standardpost_award_year');
		$awardTitle = get_post_meta($award_post_id, 'standardpost_award_title', true);
		$orgTerms = get_field('standardpost_award_organization', $award_post_id);
	    $organizations = array();
	    if (!empty($organizations)) {
	    	$organizations[] = $orgTerms -> name;
	    } else {
	    	$organizations = '';
	    }


		$recipients = get_post_meta( $award_post_id, 'standardpost_award_recipient' );
		$awards[] = array(
			'id'=> $award_post_id,
			'url'=> get_permalink($award_post_id),
			'title'=> get_the_title($award_post_id),
			'excerpt'=> get_the_excerpt(),
			'awardYears'=> $awardYears,
			'awardTitle'=> $awardTitle,
			'organizations'=> $organizations,
			'recipients'=> $recipients
		);
	}
}
wp_reset_postdata();
$awards_markup = '';
if (count($awards)) {
	foreach ($awards as $cur_award) {
		$award_post_id = $cur_award['id'];
		$url = $cur_award['url'];
		$title = $cur_award['title'];
		$awardYears = $cur_award['awardYears'];
		$awardTitle = $cur_award['awardTitle'];
		$organizations = $cur_award['organizations'];
		$recipients = $cur_award['recipients'];
		$excerpt = $cur_award['excerpt'];

		// Concat this beginning variable to show more than one. It collects five posts now. See line 308. Same with press releases, line: 244
		$awards_markup .= '<div>';
		$awards_markup .= 	'<h4 class="article-title"><a href="' . $url . '">' . $title . '</a></h4>';
		$awards_markup .= 	'<p class="date-meta">';
		if (!empty($organizations)) {
			$awards_markup .= 		'<span>' . $awardTitle . ', ' . join($organizations) . '</span>';
		}
		$awards_markup .= 		'(' . join($awardYears) . ')';
		$awards_markup .= 	'</p>';
		$awards_markup .= 	'<p>' . $excerpt . '</p>';
		$awards_markup .= '</div>';
	}
	$awards_markup .= '<div>';
	$awards_markup .= 	'<p class="read-more sans">';
	$awards_markup .= 		'<a href="' . $entityAwardsLinkFiltered . '">View all ' . $abbreviation . ' awards »</a>';
	$awards_markup .= 	'</p>';
	$awards_markup .= '</div>';
}
// AWARDS SHORTCODE
$page_content = str_replace("[awards]", $awards_markup, $page_content);
/**** END FETCH AWARDS ****/



/**** START FETCH threats to press ****/
$threats = array();
$threatsCategoryObj = get_category_by_slug("threats-to-press");
$threatsCategoryID = $threatsCategoryObj->term_id;
if (!empty($entity_category_slug)) {
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
			while ($custom_query -> have_posts())  {
				$custom_query->the_post();
				$entity_threat_id = get_the_ID();
				$threats[] = array(
					'url'=>get_permalink($entity_threat_id), 
					'title'=> get_the_title($entity_threat_id), 
					'excerpt'=>get_the_excerpt(), 
					'thumb'=>get_the_post_thumbnail($entity_threat_id, 'small-thumb')
				);
			}
		}
		wp_reset_postdata();
	}
}
/**** END FETCH threats to press ****/

get_header();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">

	<div class="outer-container">
		<div class="main-content-container">
			<div class="nest-container">
				<div class="inner-container">
					<section class="icon-side-content-container">
						<img src="<?php echo $entityLogo; ?>" alt="<?php echo $abbreviation; ?> image">
						<div id="reach-entity">
							<div class="entity-left-article">
								<h3 class="sidebar-section-header">Website</h3>
								<p class="sans"><?php echo strtolower($websiteName); ?></p>
							</div>

							<?php
								if (!empty($facebook) || !empty($twitter_profile_handle) || !empty($instagram)) {
									echo '<div class="entity-left-article">';
									echo 	'<h3 class="sidebar-section-header">Social Media</h3>';
									echo 	'<p class="social-media-icons">';
									if (!empty($facebook)) {
										echo '<a class="facebook-icon" href="' . $facebook . '" title="Like ' . get_the_title() . ' on Facebook">';
										echo 	'<i class="fab fa-facebook-square"></i>';
										echo '</a>';
									}
									if (!empty($twitter_profile_handle)) {
										echo '<a class="twitter-icon" href="https://twitter.com/' . $twitter_profile_handle . '" title="Follow ' . get_the_title() . ' on Twitter">';
										echo 	'<i class="fab fa-twitter-square"></i>';
										echo '</a>';
									}
									if (!empty($instagram)) {
										echo '<a class="instagram-icon" href="https://instagram.com/' . $instagram . '" title="Follow ' . get_the_title() . ' on Instagram">';
										echo 	'<i class="fab fa-instagram"></i>';
										echo '</a>';
									}
									echo 	'</p>';
									echo '</div>';
								}
							?>
						</div>
					</section>
					<div class="icon-main-content-container">
						<?php echo '<h2 class="section-header">' . $entity_full_name . '</h2>'; ?>
						<div class="page-content">
							<?php echo $page_content; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- BEGIN SIDEBAR -->
		<div class="side-content-container">
			<?php
				// SHARE THIS PAGE
				$share_icons = social_media_share_page($entity_page_id);
				if (!empty($share_icons)) {
					echo $share_icons;
				}

				echo '<aside>';
				if ($entityMission!="") { 
					echo $entityMission;
				}
				echo '</aside>';
			?>

		<!-- FAST FACTS -->
		<?php
			echo '<aside>';
			if ($budget != "" || $employees != "" || $languages != "" || $audience != "" || $app_link_markup != "") {
				echo '<h3 class="sidebar-section-header">Fast facts</h3>';
			}
			echo 	'<ul class="unstyled-list">';
			echo 		$budget;
			echo 		$employees;
			echo 		$languages;
			echo 		$audience;
			echo 		$factSheet;
			echo 	'</ul>';
			echo '</aside>';

			$ethics_data = get_journalistic_code_of_ethics_data();
			$ethics_parts = build_ethics_file_parts($ethics_data);
			if (!empty($ethics_parts)) {
				echo '<aside>';
				echo '<h3 class="sidebar-section-header">Journalistic Standards</h3>';
				foreach($ethics_parts as $ethic) {
					echo $ethic;
				}
				echo '</aside>';
			}

			// $press_clippings_markup  = '<aside>';
			// $press_clippings_markup .= 	'<h3 class="sidebar-section-header">Press Clippings</h3>';
			// $press_clippings_markup .= 	'<div class="media-clips-entities-dropdown">';
			// $press_clippings_markup .= 		'<ul class="unstyled-list" style="margin-top: 0;">';
			// $press_clippings_markup .= 			'<li>';
			// $press_clippings_markup .= 				'<h4 class="sidebar-section-subheader">';
			// $press_clippings_markup .= 					'<a href="' . add_query_arg('clip-type', 'about-' . $abbreviation . '', '/press-clippings-archive/') . '">ABOUT ' . $abbreviation . '<i class="fas fa-angle-right"></i></a>';
			// $press_clippings_markup .= 				'</h4>';
			// $press_clippings_markup .= 			'</li>';
			// $press_clippings_markup .= 			'<li>';
			// $press_clippings_markup .= 				'<h4 class="sidebar-section-subheader">';
			// $press_clippings_markup .= 					'<a href="' . add_query_arg('clip-type', 'citation-' . $abbreviation . '', '/press-clippings-archive/') . '">' . $abbreviation . ' CITATIONS<i class="fas fa-angle-right"></i></a>';
			// $press_clippings_markup .= 				'</h4>';
			// $press_clippings_markup .= 			'</li>';
			// $press_clippings_markup .= 		'</ul>';
			// $press_clippings_markup .= 	'</div>';
			// $press_clippings_markup .= '</aside>';

			// $press_clippings_markup .= '<aside>';
			// $press_clippings_markup .= 	$app_link_markup;
			// $press_clippings_markup .= '</aside>';
			// echo $press_clippings_markup;

			// DISPLAY RSS REED
			if (!empty($rss_markup)) {
				echo $rss_markup;
			}

			if (count($threats)) {
				$maxThreatsStories = 3;

				$threats_markup  = '<h4 class="sidebar-article-title" class="bbg__label small"><a href="/threats-to-press/">Threats to Press</a></h4>';
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

				echo '<div>';
				echo 	$threats_markup;
				echo '</div>';
			}

			// SHOW CITED POSTS
			if (!empty($press_clippings_data)) {
				$cited_post_list  = '<div class="entity_press_clippings">';
				// SWITCH BACK FOR HEADER SO IT'S MORE STYLED
				if ($cur_press_entity == "rferl") {$cur_press_entity = "rfe/rl";}

				$cited_post_list .= 	'<h3 class="sidebar-section-header">' . $cur_press_entity . ' Cited in the News</h3>';
				foreach ($press_clippings_data as $cited_post) {
					$cited_post_list .= '<h4 class="sidebar-article-title"><a href="' . $cited_post['story_link'] . '" target="_blank">' . $cited_post['title'] . '</a> ';
					$cited_post_list .= '<span class="sans">(' . $cited_post['outlet'] . ')</span></h4>';
				}
				$cited_post_list .= '<p><a class="read-more" href="' . add_query_arg('entity', $cur_press_entity, '/press-citing-listing/') . '">Click here to see full list</a></p>';
				$cited_post_list .= '</div>';
				echo $cited_post_list;
			}

			// SEARCH ENTITY WEBSITE
			// Not needed for the OTF page
			if (!is_page('Open Technology Fund')) {
				echo '<aside>';
				echo 	$site_select;
				echo '</aside>';
			}

			// CONTACT INFORMATION
			if ($includeContactBox) {
				$contact_box  = '<div class="inner-container">';
				$contact_box .= 	'<h3 class="sidebar-section-header">Contact information</h3>';
				if ($includeMap) {
					$contact_box .= '<div class="bbg__contact-card bbg__contact-card--include-map">';
					$contact_box .= 	'<div id="map" class="bbg__contact-card__map"></div>';
					$contact_box .= '</div>';
				}
				$contact_box .= 	'<div class="bbg__contact-card">';
				if (is_page('rferl')) {
					if (is_page('rferl')) {
					$contact_box .= 	'<h4 class="sidebar-section-subheader">Headquarters</h4>';
					$contact_box .= 	'<p class="sans" style="margin-bottom: 1.5rem;">';
					$contact_box .= 		'<a href="https://www.google.com/maps/place/Radio+Free+Europe+-+Radio+Liberty+-+Library/@50.0789865,14.4764293,17z/data=!3m1!4b1!4m5!3m4!1s0x470b936ea832e949:0x2d1da818fb7b6706!8m2!3d50.0789865!4d14.4786233" target="_blank">';
					$contact_box .= 			'Vinohradska 159A<br>100 00 Prague 10<br>Czech Republic';
					$contact_box .= 		'</a><br>';
					$contact_box .= 		'Tel: +420.221.122.111';
					$contact_box .= 	'</p>';
				}
					$contact_box .= 	'<h4 class="sidebar-section-subheader">Corporate Office</h4>';
				}
				$contact_box .= 	'<p class="sans">';
				$contact_box .= 		$address;
				if (!empty($phone)) {
					$contact_box .= 	'<br>Tel: ' . $phone;
				}
				if (!empty($email)) {
					$contact_box .= 	'<br>Email: <a href="mailto:' . $email . '" title="Email ' . $abbreviation . '">' . $email . '</a>';
				}
				if (!empty($learnMore)) {
					$contact_box .= 	'<br><br><a href="'. $learnMore . '">Learn more about ' . $abbreviation . '</a>';
				}
				$contact_box .= 		'</p>';
				$contact_box .= 	'</div>';
				$contact_box .= '</div>';
				echo $contact_box;
			}
		?>
		</div>
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
				$quotes  =  '<div class="bbg__entity__pullquote">';
				$quotes .= output_quote($quote_result);
				$quotes .= '</div>';
				echo $quotes;
			}
		?>
	</div>

	<div class="bbg-post-footer">
	</div>

</main><!-- #main -->

<?php
/* if the map is set, then load the necessary JS and CSS files */
if ($includeMap) {
?>
	<script type="text/javascript" src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
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
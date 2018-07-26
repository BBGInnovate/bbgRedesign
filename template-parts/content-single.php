<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */

require get_template_directory() . '/inc/bbg-functions-assemble.php';
include get_template_directory() . '/inc/shared_sidebar.php';

$dateline = "";
$includeDateline = get_post_meta(get_the_ID(), 'include_dateline', true);
if (in_category('Press Release') && $includeDateline) {
	$dateline  = '<span class="bbg__article-dateline">';
	$dateline .= 	get_post_meta( get_the_ID(), 'dateline_location', true);
	$dateline .= " — </span>";
}

// DATELINE GOES INSIDE FIRST PARAGRAPH TAG FOR FORMATTING
$pageContent = get_the_content();
$pageContent = apply_filters('the_content', $pageContent);
$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
if ($dateline != "") {
	$needle = "<p>";
	$replaceNeedle = "<p>" . $dateline;
	$pos = strpos($pageContent, $needle);
	if ($pos !== false) {
		$pageContent = substr_replace($pageContent, $replaceNeedle, $pos, strlen($needle));
	}
}

// RELATIVE PROFILE? SHOW IN RIGHT SIDEBAR
$relatedProfileID = get_post_meta(get_the_ID(), 'statement_related_profile', true);
$includeRelatedProfile = false;

if ($relatedProfileID) {
	$includeRelatedProfile = true;
	$alternatePhotoID = get_post_meta(get_the_ID(), 'statement_alternate_profile_image', true);

	if ($alternatePhotoID) {
		$profilePhotoID = $alternatePhotoID;
	} else {
		$profilePhotoID = get_post_meta( $relatedProfileID, 'profile_photo', true );
	}

	$profilePhoto = "";
	if ($profilePhotoID) {
		$profilePhoto = wp_get_attachment_image_src($profilePhotoID, 'Large Mugshot');
		$profilePhoto = $profilePhoto[0];
	}
	$twitterProfileHandle = get_post_meta($relatedProfileID, 'twitter_handle', true);
	$profileName = get_the_title($relatedProfileID);
	$occupation = get_post_meta($relatedProfileID, 'occupation', true);
	$profileLink = get_page_link($relatedProfileID);
	$profileExcerpt = my_excerpt($relatedProfileID);

	$relatedProfile  = '<div class="bbg__sidebar__primary">';
	$relatedProfile .= 		'<a href="' . $profileLink . '"><img class="bbg__sidebar__primary-image" src="'.$profilePhoto.'"/></a>';
	$relatedProfile .= 		'<h3 class="bbg__sidebar__primary-headline"><a href="' . $profileLink . '">' . $profileName . '</a></h3>';
	$relatedProfile .= 		'<span class="bbg__profile-excerpt__occupation">' . $occupation . '</span>';
	$relatedProfile .= 		'<p class="">' . $profileExcerpt . '</p>';
	$relatedProfile .= '</div>';
}

$listsInclude = get_field('sidebar_dropdown_include', '', true);

$includeMap = get_post_meta( get_the_ID(), 'map_include', true );
$mapLocation = get_post_meta( get_the_ID(), 'map_location', true );

// FAKE A MAP LOCATION
if (get_post_type() == "threat_to_press") {
	$mapLocation = get_post_meta( get_the_ID(), 'threats_to_press_coordinates', true );
	if ($mapLocation) {
		$includeMap = true;
	}
}

if ($includeMap && $mapLocation) {
	$mapHeadline = get_post_meta( get_the_ID(), 'map_headline', true );
	$mapDescription = get_post_meta( get_the_ID(), 'map_description', true );
	$mapPin = get_post_meta( get_the_ID(), 'map_pin', true );
	$mapZoom = get_post_meta( get_the_ID(), 'map_zoom', true );

	$key = 	'<?php echo MAPBOX_API_KEY; ?>';
	$zoom = 4;
	if ( $mapZoom > 0 && $mapZoom < 20 ) {
		$zoom = $mapZoom;
	}

	$lat = $mapLocation['lat'];
	$lng = $mapLocation['lng'];
	$pin = "";

	if ( $mapPin ){
		$pin = "pin-s+990000(" . $lng .",". $lat .")/";
	}
	// STATIC MAP REFERENCE 
	// $map = "https://api.mapbox.com/v4/mapbox.emerald/" . $pin . $lng . ",". $lat . "," . $zoom . "/170x300.png?access_token=" . $key;
}

// SIDEBAR: LIST OF PROJECT WORKERS
$team_roster = "";
if (have_rows('project_team_members')) {
	$single_roster  = '<div class="bbg__project-team">';
	$single_roster .= 	'<h5 class="bbg__project-team__header">Project team</h5>';
	while (have_rows('project_team_members')) {
		the_row();
		if (get_row_layout() == 'team_member') {
			$teamMemberName = get_sub_field('team_member_name');
			$teamMemberRole = get_sub_field('team_member_role');
			$teamMemberTwitterHandle = get_sub_field('team_member_twitter_handle');

			if ($teamMemberTwitterHandle && $teamMemberTwitterHandle != ""){
				$teamMemberName = '<a href="https://twitter.com/' . $teamMemberTwitterHandle . '">' . $teamMemberName . '</a>';
			}

			$single_roster .= '<p>';
			$single_roster .= 	'<span class="bbg__project-team__name">' . $teamMemberName . ', </span>';
			$single_roster .= 	'<span class="bbg__project-team__role">' . $teamMemberRole . '</span>';
			$single_roster .= '</p>';
		}
	}
	$single_roster .= '</div>';
	$team_roster .= $single_roster;
}

// IF PRESS RELEASE HAS AN ENTITY CATEGORY, GET THE LOGO
$categories_to_show_entities = ['Press Release', 'Project', 'Media Advisory'];
$entity_categories = ['voa', 'rfa', 'mbn', 'ocb', 'rferl'];

$entity_logos = array();
if (in_category($categories_to_show_entities))  {
	if (in_category($entity_categories)) {
		foreach ($entity_categories as $eCat) {
			if (in_category($eCat)) {
				$broadcastersPage = get_page_by_title('Our Networks');
				$args = array(
					'post_type' => 'page',
					'posts_per_page' => 1,
					'post_parent' => $broadcastersPage -> ID,
					'name' => str_replace('-press-release', '', $eCat)
				);
				$custom_query = new WP_Query($args);

				if ($custom_query->have_posts()) {
					while ($custom_query->have_posts())  {
						$custom_query->the_post();
						$id = get_the_ID();
						$entityLogoID = get_post_meta($id, 'entity_logo', true);
						$entityLogo = "";
						$entityLink = get_the_permalink($id);
						if ($entityLogoID) {
							$entityLogoObj = wp_get_attachment_image_src($entityLogoID , 'Full');
							$entityLogo = $entityLogoObj[0];
						}
						$entity_logos[] = array(
							'logo' => $entityLogo,
							'link' => $entityLink
						);
					}
				}
				wp_reset_postdata();
				wp_reset_query();
			}
		}
	}
}

// IF THREATS TO PRESS POST, SHOW PROFILE
$threat_category_id = get_cat_id('threats-to-press');
// $isThreat = has_category($threat_category_id);

$advisoryExpertsStr = '';
if ( has_category('Media Advisory')) {
	$includeExperts = get_field( 'media_advisory_include_experts', get_the_ID(), true );
	if ($includeExperts) {
		$expertLabel =  get_field( 'media_advisory_experts_label', get_the_ID(), true );
		$expertDescription =  get_field( 'media_advisory_experts_description', get_the_ID(), true );

		$advisoryExpertsStr .= '<h3 class="bbg__sidebar-label">' . $expertLabel . '</h3>';
		$advisoryExpertsStr .= '<p class="bbg-tagline bbg-tagline--main">' . $expertDescription . '</p>';

		if ( have_rows( 'media_advisory_list_of_experts' )) {
			$advisoryExpertsStr .= '<ul class="usa-unstyled-list">';
			while ( have_rows('media_advisory_list_of_experts') ) : the_row();
				$expertName = get_sub_field( 'expert_name' );
				$expertTitle = get_sub_field( 'expert_title' );
					$advisoryExpertsStr .= '<li>';
						$advisoryExpertsStr .= '<h5 class="bbg__sidebar__primary-headline bbg__profile-excerpt__name">' . $expertName . '</h5>';
						$advisoryExpertsStr .= '<span class="bbg__profile-excerpt__occupation">' . $expertTitle . '</span>';
					$advisoryExpertsStr .= '</li>';
			endwhile;
			$advisoryExpertsStr .= '</ul>';
		}
	}
}

$journos = get_field('featured_journalists_section');
$featuredJournalists = "";
$profilePhoto = "";

// FLEXIBLE CONTENT FIELD HAVE ROWS OF DATA?
if($journos) {
	while (have_rows('featured_journalists_section')) {
		the_row();
		$featuredJournalistsSectionLabel = get_sub_field('featured_journalists_section_label');
		$featuredJournalistsObj = get_sub_field('featured_journalist');

		if ($featuredJournalistsObj) {
			$featuredJournalists .= '<div class="usa-grid-full">';
			$featuredJournalists .= 	'<header class="page-header">';
			$featuredJournalists .= 		'<h5 class="bbg__label">' . $featuredJournalistsSectionLabel . '</h5>';
			$featuredJournalists .= 	'</header>';

			foreach ($featuredJournalistsObj as $journalists) {
				foreach ($journalists as $journalist) {
					$profileTitle = $journalist->post_title;
					$profileName = $journalist->first_name . " " . $journalist->last_name;
					$profileOccupation = $journalist->occupation;
					$profilePhoto = $journalist->profile_photo;
					$profileUrl = get_permalink($journalist->ID);
					$profileExcerpt = my_excerpt($journalist->ID);

					$profileOccupation = '<span class="bbg__profile-excerpt__occupation">' . $profileOccupation .'</span>';
					if ($profilePhoto) {
						$profilePhoto = wp_get_attachment_image_src($profilePhoto, 'Full');
						$profilePhoto = $profilePhoto[0];
						$profilePhoto = '<a href="' . $profileUrl . '"><img src="' . $profilePhoto . '" class="bbg__profile-featured__profile__mugshot"/></a>';
					}

					$featuredJournalists .= '<div class="bbg__profile-excerpt--sidebar">';
					$featuredJournalists .= 	'<h3 class="bbg__profile__name"><a href="' . $profileUrl . '">'. $profileName .'</a></h3>';
					$featuredJournalists .= 	'<p class="bbg__profile-excerpt__text">' . $profilePhoto . $profileOccupation . $profileExcerpt . '</p>';
					$featuredJournalists .= '</div>';
				}
			}
			$featuredJournalists .= '</div>';
		}
	}
}

// test putting in the feature media function
// $addFeaturedGallery = get_post_meta(get_the_ID(), 'featured_gallery_add', true);
// $addFeaturedMap = get_post_meta(get_the_ID(), 'featured_map_add', true);
// $featuredMapCaption = get_post_meta(get_the_ID(), 'featured_map_caption', true);
$media_dev_map = get_field('media_dev_coordinates');
if ($media_dev_map) {
	$features = [];
	$location_title = $media_dev_map['address'];
	$media_dev_lat = $media_dev_map['lat'];
	$media_dev_lng = $media_dev_map['lng'];
	$zoom = 14;

	$media_dev_features[] = array(
		'type' => 'Feature',
		'geometry' => array(
			'type' => 'Point',
			'coordinates' => array($media_dev_map['lng'], $media_dev_map['lat'])
		),
		'properties' => array(
			'marker-size' => 'large',
			'marker-symbol' => ''
		)
	);
	$medGeojsonObj = array(array(
		'type' => 'FeatureCollection',
		'features' => $media_dev_features
	));
	$medGeojsonStr=json_encode(new ArrayValue($medGeojsonObj), JSON_PRETTY_PRINT, 10);

	echo "<script type='text/javascript'>\n";
	echo 	"var med_geojson = $medGeojsonStr;";
	echo "</script>";
}

if ($addFeaturedMap) {
	$featuredMapItems = get_field( 'featured_map_items', get_the_ID(), true);
	$features = [];

	for ($i = 0; $i < count($featuredMapItems); $i++) {
		$item = $featuredMapItems[$i];
		$featuredMapItemLocation = $item['featured_map_item_coordinates'];
		$featuredMapItemTitle = $item['featured_map_item_title'];
		$featuredMapItemDescription = $item['featured_map_item_description'];
		$featuredMapItemLink = $item['featured_map_item_link'];
		$featuredMapItemVideoLink = $item['featured_map_item_video_link'];
		$im = $item['featured_map_item_image'];
		$featuredMapItemImageUrl = $im['sizes']['medium'];

		if ($featuredMapItemLink != "") {
			$map_header = '<h5><a style="font-weight: bold;" href="' . $featuredMapItemLink . '">' . $featuredMapItemTitle . '</a></h5>';
		} else {
			$map_header = '<h5><span style="font-weight: bold;">' . $featuredMapItemTitle . '</span></h5>';
		}

		$popupBody = "";
		if ($featuredMapItemLink != "") {
			$popupBody .= $map_header;
			$popupBody .= '<div class="u--show-medium-large">';
			$popupBody .= 	'<img src="' . $featuredMapItemImageUrl . '">';
			$popupBody .= '</div>';
			$popupBody .= $featuredMapItemDescription;
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
	$geojsonObj = array(array(
		'type' => 'FeatureCollection',
		'features' => $features
	));
	$geojsonStr = json_encode(new ArrayValue($geojsonObj), JSON_PRETTY_PRINT, 10);

	echo '<script type="text/javascript">';
	echo 	'geojson = $geojsonStr';
	echo '</script>';
}

$media_dev_sponsors = "";
if (have_rows('media_dev_sponsors')) {
	$media_dev_sponsors .= '<h3 class="bbg__sidebar-label">Funders</h3>';
	if(have_rows('media_dev_sponsors')) {
		$media_dev_sponsors .= '<ul class="usa-unstyled-list">';
		while (have_rows('media_dev_sponsors')) {
			the_row();
			$sponsorName = get_sub_field('media_dev_participant_name');

			$media_dev_sponsors .= '<li>';
			$media_dev_sponsors .= 	'<h5 class="bbg__sidebar__primary-headline bbg__profile-excerpt__name">';
			$media_dev_sponsors .= 		$sponsorName;
			$media_dev_sponsors .= 	'</h5>';
			$media_dev_sponsors .= '</li>';
		}
		$media_dev_sponsors .= '</ul>';
	}
}

$media_dev_presenters = "";
if (have_rows('media_dev_presenters')) {
	$media_dev_sponsors .= '<h3 class="bbg__sidebar-label">Presenters</h3>';
	$media_dev_presenters .= '<ul class="usa-unstyled-list">';
	while (have_rows('media_dev_presenters')) {
		the_row();
		$presenterName = get_sub_field('media_dev_participant_name');
		$presenterTitle = get_sub_field('media_dev_participant_job_title');

		$media_dev_presenters .= '<li>';
		$media_dev_presenters .= 		'<h5 class="bbg__sidebar__primary-headline bbg__profile-excerpt__name">' . $presenterName . '</h5>';
		$media_dev_presenters .= 		'<span class="bbg__profile-excerpt__occupation">' . $presenterTitle . '</span>';
		$media_dev_presenters .= '</li>';
	}
	$media_dev_presenters .= "</ul>";
}

// AWARD INFO
// NOT YET IMPLEMENTED
$awardCategoryID = get_cat_id('Award');
$isAward = ('award' == get_post_type());
echo "<!---this is an award-->";

$isPodcast = has_category('podcast');

$soundcloudPlayer = "";
if ($isPodcast) {
	$podcastSoundcloudURL = get_post_meta( get_the_ID(), 'podcast_soundcloud_url', true );
	$podcastTranscript = get_post_meta( get_the_ID(), 'podcast_transcript', true );
	$podcastTranscript = apply_filters('the_content', $podcastTranscript);
	if ($podcastTranscript) {
		$podcastTranscript  = '<div id="podcastTranscript" class="usa-accordion-bordered bbg__committee-list">';
		$podcastTranscript .= 	'<ul class="usa-unstyled-list">';
		$podcastTranscript .= 		'<li>';
		$podcastTranscript .= 			'<button id="transcriptButton" class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-podcast-1">Transcript</button>';
		$podcastTranscript .= 			'<div id="collapsible-podcast-1" aria-hidden="true" class="usa-accordion-content">' . $podcastTranscript . '</div>';
		$podcastTranscript .= 		'<li>';
		$podcastTranscript .= 	'</ul>';
		$podcastTranscript .= '</div>';
	}
	$soundcloudPlayer  = '<div>';
	$soundcloudPlayer .= 	'<iframe width="100%" height="166" scrolling="no" frameborder="no" src="' . $podcastSoundcloudURL . '"></iframe>';
	$soundcloudPlayer .= 	'<a onClick=\"tButton = jQuery("#transcriptButton"); if (tButton.attr("aria-expanded") == false) {tButton.click();}\" href="#podcastTranscript" style="cursor: pointer;">View Transcript</a>';
	$soundcloudPlayer .= '</div>';
}

//the title/headline field, followed by the URL and the author's twitter handle
$twitterText  = '';
$twitterText .= html_entity_decode(get_the_title());
$twitterText .= ' by @bbggov';
$twitterText .= ' ' . get_permalink();

$twitterURL = '//twitter.com/intent/tweet?text=' . rawurlencode($twitterText);
$fbUrl = '//www.facebook.com/sharer/sharer.php?u=' . urlencode(get_permalink());
$hideFeaturedImage = false;

// DETERMINES WHETHER GRID IS TWO OR THREE COLUMNS
$numLogos = count($entity_logos);
if ($numLogos > 0 && $numLogos < 3) {
	$page_columns = 3;
} else {
	$page_columns = 2;
}
?>

<style>
.leaflet-popup-pane {min-width: 300px !important;}
</style>

<article id="post-<?php the_ID(); ?>" <?php post_class("bbg__article"); ?>>
	<?php
		$featured_media_result = get_feature_media_data();
		if ($featured_media_result != "") {
			echo $featured_media_result;
		}
	?>

	<div class="outer-container">
		<div class="grid-container">
		<?php
			$header_markup = '<header>';
			if (get_post_type() == "threat_to_press") {
				$threat_link = get_permalink(get_page_by_path('threats-to-press'));

				$threats_header  = '<h5 class="entry-category bbg__label">';
				$threats_header .= 		'<a href="' . $threat_link . '" title="Threats to Press">Threats to Press</a>';
				$threats_header .= '</h5>';

				$header_markup .= $threats_header;
			} else {
				$header_markup .= bbginnovate_post_categories();
			}
			$header_markup .= '<h3>' . get_the_title() . '</h3>';
			$header_markup .= 	'<div class="date-meta">';
			$header_markup .= 		bbginnovate_posted_on();
			$header_markup .= 	'</div>';
			$header_markup .= '</header>';
			echo $header_markup;
		?>
		</div>
	</div>

	<?php if ($page_columns == 3) { ?>
		<div class="outer-container">
			<div class="main-content-container">
				<div class="nest-container">
					<div class="inner-container">
						<div class="icon-side-content-container">
							<?php
								$numLogos = count($entity_logos);
								if ($numLogos > 0 && $numLogos < 3) {
									for ($i = 0; $i < $numLogos; $i++) {
										$e = $entity_logos[$i];
										$entityLink = $e['link'];
										$entityLogo = $e['logo'];
										$firstClass = "";
										// UTILITY CLASS FOR ADDED SPACE WHEN MULTIPLE ICONS
										if ($i == 0 && $numLogos > 0) {
											$firstClass = "bbg__entity-logo__press-release-first-of-many";
										}
										$entity_icons  = '<a href="' . $entityLink . '" title="Learn more">';
										$entity_icons .= 	'<img src="'. $entityLogo . '" class="bbg__entity-logo__press-release ' . $firstClass . '">';
										$entity_icons .= '</a>';
										echo $entity_icons;
									}
								}
							?>
						</div>
						<div class="icon-main-content-container">
	<?php } else { // TWO COLUMNS ?>
		<div class="outer-container">
			<div class="custom-grid-container">
				<div class="inner-container">
					<div class="main-content-container">
	<?php } ?>
						<?php
							if ($isPodcast) {
								echo $soundcloudPlayer;
							}
							echo $pageContent;

							if ($isPodcast) {
								echo $podcastTranscript;
							}
							// AWARD INFO
							if ($isAward) {
								$awardDescription = get_post_meta( get_the_ID(), 'standardpost_award_description', true );
								if (isset($awardDescription) && $awardDescription!= "") {
									$awardOrganization = get_field( 'standardpost_award_organization', get_the_ID(), true);
									$awardOrganization = $awardOrganization -> name;

									$awardLogo = get_post_meta( get_the_ID(), 'standardpost_award_logo', true );
									$awardLogoImage = "";
									if ($awardLogo) {
										$awardLogoImage = wp_get_attachment_image_src( $awardLogo , 'small-thumb-uncropped');
										$awardLogoImage = $awardLogoImage[0];
										$awardLogoImage = '<img src="' . $awardLogoImage . '" class="bbg__profile-excerpt__photo"/>';
									}
									$award_markup  = '<div class="usa-grid-full bbg__contact-box">';
									$award_markup .= 	'<h3>About ' . $awardOrganization . '</h3>';
									$award_markup .= 	$awardLogoImage;
									$award_markup .= 	'<p><span class="bbg__tagline">' . $awardDescription . '</span></p>';
									$award_markup .= '</div>';
									echo $award_markup;
								}
							}
							// CONTACT CARDS
							$contactPostIDs = get_post_meta( $post->ID, 'contact_post_id', true );
							renderContactCard($contactPostIDs);
						?>
	<?php if ($page_columns == 3) { ?>
						</div>
					</div>
				</div>
			</div>
			<!-- BEGIN SIDEBAR -->
			<aside class="side-content-container">
	<?php } else { // TWO COLUMNS ?>
					</div> <!-- END .main-content-container -->
					<div class="side-content-container">
	<?php } ?>
			<?php
				if ($includeRelatedProfile) {
					echo $relatedProfile;
				}

				echo $featuredJournalists;
				echo $advisoryExpertsStr;

				echo getInterviewees();

				if ($includeMap  && $mapLocation) {
					$sidebar_map  = '<h5>' . $mapHeadline . '</h5>';
					$sidebar_map .= '<div id="map" class="bbg__locator-map">';
					$sidebar_map .= 	'<p>' . $mapDescription . '</p>';
					$sidebar_map .= '</div>';
					echo $sidebar_map;
				}
				if ($isAward) {
					$award_info  = '<h5 class="bbg__label small bbg__sidebar__download__label">About the Award</h5>';
					$award_info .= '<div class="bbg__sidebar__primary">';
					$award_info .= 		getAwardInfo(get_the_ID(), true);
					$award_info .= '</div>';
					echo $award_info;
				}
				if ($includeSidebar) {
					echo $sidebar;
				}
				// if ($listsInclude) {
				// 	echo $sidebarDownloads;
				// }
				// echo $media_dev_sponsors;
				// echo $media_dev_presenters;
				// echo $team_roster;
				// echo getAccordion();
			?>

			<h5>Share </h5>
			<a href="<?php echo $fbUrl; ?>">
				<span class="bbg__article-share__icon facebook"></span>
			</a>
			<a href="<?php echo $twitterURL; ?>">
				<span class="bbg__article-share__icon twitter"></span>
			</a>
	<?php if ($page_columns == 3) { ?>
			</aside>
		</div><!-- .outer-container -->
	<?php } else { ?>
					</div>
				</div>
			</div>
		</div><!-- .outer-container -->
	<?php } ?>
</article><!-- #post-## -->


<?php
if ($media_dev_map) {
?>

<script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />
<script type="text/javascript">
	L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
	var map = L.mapbox.map('map-featured', 'mapbox.streets')
	<?php echo '.setView(['. $media_dev_lat . ', ' . $media_dev_lng . '], ' . $zoom . ');'; ?>
	// MEDIA DEV JS
	map.scrollWheelZoom.disable();
	var markers = L.mapbox.featureLayer();
	var coords = med_geojson[0].features[0].geometry.coordinates;
	var marker = L.marker(new L.LatLng(coords[1], coords[0]));
	var divNode = document.createElement('DIV');
	marker.bindPopup(divNode);
	marker.addTo(markers);
	markers.addTo(map);

	L.mapbox.featureLayer({
		type: 'Feature',
		geometry: {
			type: 'Point',
			coordinates: [
				<?php echo $media_dev_lng . ', ' . $media_dev_lat; ?>
			]
		},
		properties: {
			title: '<?php echo $mapHeadline; ?>',
			description: '<?php echo $mapDescription; ?>',
			'marker-size': 'large',
			'marker-color': '#981b1e',
			'marker-symbol': ''
		}
	}).addTo(map);
</script>

<?php } ?>

<?php
if ($addFeaturedMap && $media_dev_map == "") {
?>
	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />
	<script type="text/javascript">
		L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var map = L.mapbox.map('map-featured', 'mapbox.emerald')
	    var markers = L.mapbox.featureLayer();
	    for (var i = 0; i < geojson[0].features.length; i++) {
	        var coords = geojson[0].features[i].geometry.coordinates;
	        var title = geojson[0].features[i].properties.title; //a[2];
	        var description = geojson[0].features[i].properties['description'];
	        var marker = L.marker(new L.LatLng(coords[1], coords[0]));
	        var popupText = description;

	        //rather than just use html, do this - http://stackoverflow.com/questions/10889954/images-size-in-leaflet-cloudmade-popups-dont-seem-to-count-to-determine-popu
	       	var divNode = document.createElement('DIV');
			divNode.innerHTML =popupText;
	        marker.bindPopup(divNode);
	        marker.addTo(markers);
	    }
	    markers.addTo(map);
		map.scrollWheelZoom.disable();
		function centerMap(){
			map.fitBounds(markers.getBounds());
		}
		centerMap();

		//Recenter the map on resize
		function resizeStuffOnResize(){
		  waitForFinalEvent(function(){
				centerMap();
		  }, 500, "some unique string");
		}

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

	</script>
<?php } ?>

<?php
// IF MAP, LOAD JS, CSS
if ($includeMap && $mapLocation) {
?>
	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />

	<script type="text/javascript">
		L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var map = L.mapbox.map('map', 'mapbox.streets')
		<?php echo '.setView(['. $lat . ', ' . $lng . '], ' . $zoom . ');'; ?>

		map.scrollWheelZoom.disable();

		L.mapbox.featureLayer({
			type: 'Feature',
			geometry: {
				type: 'Point',
				coordinates: [
					<?php echo $lng . ', ' . $lat; ?>
				]
			},
			properties: {
				title: '<?php echo $mapHeadline; ?>',
				description: '<?php echo $mapDescription; ?>',
				'marker-size': 'large',
				'marker-color': '#981b1e',
				'marker-symbol': ''
			}
		}).addTo(map);
	</script>
<?php } ?>
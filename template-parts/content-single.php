<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */

require get_template_directory() . '/inc/bbg-functions-assemble.php';
require get_template_directory() . '/inc/shared_sidebar.php';

$dateline = "";
$includeDateline = get_post_meta(get_the_ID(), 'include_dateline', true);
if (in_category('Press Release') && $includeDateline) {
	$dateline  = '<span class="bbg__article-dateline">';
	$dateline .= 	get_post_meta( get_the_ID(), 'dateline_location', true);
	$dateline .= ' — </span>';
}

// DATELINE GOES INSIDE FIRST PARAGRAPH TAG FOR FORMATTING
$post_thumbnail_url = get_the_post_thumbnail_url();
$page_title = get_the_title();
$post_id = get_the_ID();
$post_date = get_the_date();
$page_content = get_the_content();
$page_content = apply_filters('the_content', $page_content);
$page_content = do_shortcode($page_content);

$post_subtitle = get_post_meta($post_id, 'post_subtitle', true) ?? '';

$post_byline = '';
if (get_field('include_byline', $post_id)) {
	$post_byline = get_post_meta($post_id, 'byline_override');
	$post_byline = $post_byline[0];
}


if ($dateline != "") {
	$needle = '>';
	$replaceNeedle = '>' . $dateline;
	$pos = strpos($page_content, $needle);
	if ($pos !== false) {
		$page_content = substr_replace($page_content, $replaceNeedle, $pos, strlen($needle));
	}
}

$show_featured_image_caption = get_post_meta($post_id, 'show_featured_image_caption', true) ? true : false;

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
	$relatedProfile .= 		'<a href="' . $profileLink . '"><img class="bbg__sidebar__primary-image" src="'.$profilePhoto.'" alt="Profile photo"></a>';
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
	$single_roster  = '<div class="sidebar-related">';
	$single_roster .= 	'<h3 class="sidebar-section-header">Project team</h3>';
	while (have_rows('project_team_members')) {
		the_row();
		if (get_row_layout() == 'team_member') {
			$teamMemberName = get_sub_field('team_member_name');
			$teamMemberRole = get_sub_field('team_member_role');
			$teamMemberTwitterHandle = get_sub_field('team_member_twitter_handle');

			if ($teamMemberTwitterHandle && $teamMemberTwitterHandle != ""){
				$teamMemberName = '<a href="https://twitter.com/' . $teamMemberTwitterHandle . '">' . $teamMemberName . '</a>';
			}

			$single_roster .= '<p class="sans">';
			$single_roster .= 	'<span class="sidebar-article-title">' . $teamMemberName . '</span><br>';
			$single_roster .= 	$teamMemberRole;
			$single_roster .= '</p>';
		}
	}
	$single_roster .= '</div>';
	$team_roster .= $single_roster;
}

// IF PRESS RELEASE HAS AN ENTITY CATEGORY, GET THE LOGO
$categories_to_show_entities = ['Press Release', 'Project', 'Media Advisory'];
$entity_categories = ['voa', 'rfa', 'mbn', 'ocb', 'rferl', 'otf'];

$entity_logos = array();
$entity_category_data = array();
if (in_category($categories_to_show_entities))  {
	if (in_category($entity_categories)) {
		foreach ($entity_categories as $entity_category) {
			if (in_category($entity_category)) {
				$entity_category_args = array(
					'post_type' => 'page',
					'posts_per_page' => 1,
					'title' => $entity_category
				);
				$entity_category_query = new WP_Query($entity_category_args);

				if ($entity_category_query->have_posts()) {
					$entity_category_query->the_post();
					$entity_id = get_the_ID();
					$entity_logo_information = get_field('entity_logo', $entity_id);
					$entity_logo_url = $entity_logo_information['url'];
					$entity_link = get_field('entity_site_url', $entity_id);

					$entity_category_data = array(
						'logo-url' => $entity_logo_url,
						'site-url' => $entity_link
					);
					$entity_logos[] = $entity_category_data;
				}
				wp_reset_postdata();
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
			$advisoryExpertsStr .= '<ul class="unstyled-list">';
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

$journos = get_field('featured_journalists_section', $post_id);
$featuredJournalists = "";
$profilePhoto = "";

// FLEXIBLE CONTENT FIELD HAVE ROWS OF DATA?
if($journos) {
	while (have_rows('featured_journalists_section', $post_id)) {
		the_row();
		$featuredJournalistsSectionLabel = get_sub_field('featured_journalists_section_label');
		$featuredJournalistsObj = get_sub_field('featured_journalist');

		if ($featuredJournalistsObj) {
			$featuredJournalists .= '<h3 class="sidebar-section-header">' . $featuredJournalistsSectionLabel . '</h3>';

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
						$profilePhoto = '<a href="' . $profileUrl . '"><img src="' . $profilePhoto . '" class="bbg__profile-featured__profile__mugshot" alt="Mugshot"></a>';
					}

					$featuredJournalists .= '<div class="bbg__profile-excerpt--sidebar">';
					$featuredJournalists .= 	'<h3 class="bbg__profile__name"><a href="' . $profileUrl . '">'. $profileName .'</a></h3>';
					$featuredJournalists .= 	'<p class="bbg__profile-excerpt__text">' . $profilePhoto . $profileOccupation . $profileExcerpt . '</p>';
					$featuredJournalists .= '</div>';
				}
			}
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

	echo '<script type="text/javascript">';
	echo 	"var med_geojson = $medGeojsonStr;";
	echo "</script>";
}

if (!empty($addFeaturedMap)) {
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
			$popupBody .= 	'<img src="' . $featuredMapItemImageUrl . '" alt="Featured map item">';
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

$media_dev_sidebar_meta = '';
if (in_category('media-development-map')) {
	$media_dev_sidebar_meta .= '<aside>';

	$media_dev_sidebar_meta .= '<h3 class="sidebar-section-header">Title</h3>';
	$trainingName = get_post_meta( $id, 'media_dev_name_of_training', true );
	$media_dev_sidebar_meta .= '<p class="sidebar-article-title">' . $trainingName . '</p>';

	$location = get_post_meta( $id, 'media_dev_coordinates', true );
	if ($location) {
		$media_dev_sidebar_meta .= '<h3 class="sidebar-section-header">Place</h3>';
		$media_dev_sidebar_meta .= '<p class="sidebar-article-title">' . $location['address'] . '</p>';
	}

	$trainingDate = get_post_meta( $id, 'media_dev_date', true );
	$media_dev_sidebar_meta .= '<h3 class="sidebar-section-header">Date</h3>';
	$media_dev_sidebar_meta .= '<p class="sidebar-article-title">' . $trainingDate . '</p>';

	if (have_rows('media_dev_presenters')) {
		$media_dev_sidebar_meta .= '<h3 class="sidebar-section-header">Trainers</h3>';
		while (have_rows('media_dev_presenters')) {
			the_row();
			$presenterName = get_sub_field('media_dev_participant_name');
			$presenterTitle = get_sub_field('media_dev_participant_job_title');

			$media_dev_sidebar_meta .= '<p class="sidebar-article-title">' . $presenterName . '</p>';
			$media_dev_sidebar_meta .= '<p class="sans">' . $presenterTitle . '</p>';
		}
	}

	$description = get_post_meta( $id, 'media_dev_description', true );
	if ($description) {
		$media_dev_sidebar_meta .= '<h3 class="sidebar-section-header">Details</h3>';
		$media_dev_sidebar_meta .= '<p class="sans">' . $description . '</p>';
	}
}

$media_dev_sponsors = "";
if (have_rows('media_dev_sponsors')) {
	$media_dev_sponsors .= '<aside>';
	$media_dev_sponsors .= 	'<h3 class="sidebar-section-header">Funders</h3>';
	if(have_rows('media_dev_sponsors')) {
		while (have_rows('media_dev_sponsors')) {
			the_row();
			$sponsor_name = get_sub_field('media_dev_participant_name');
			$media_dev_sponsors .= '<p class="sidebar-article-title">' . $sponsor_name . '</p>';
		}
	}
	$media_dev_sponsors .= '</aside>';
}

$media_dev_additional_files = '';
if (have_rows('media_dev_additional_files')) {
	while (have_rows('media_dev_additional_files')) {
		the_row();

		$mediaDevAdditionalFileObj = get_sub_field('media_dev_additional_file');
		if (!empty($mediaDevAdditionalFileObj)) {
			$fileID = $mediaDevAdditionalFileObj['ID'];
			$fileLink = $mediaDevAdditionalFileObj['url'];
			$file = get_attached_file($fileID);
			$ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
			$filesize = formatBytes(filesize($file));
			$title = $mediaDevAdditionalFileObj['title'];

			$media_dev_additional_files .= '<h4 class="sidebar-article-title">';
			$media_dev_additional_files .=     '<a target="_blank" href="' . $fileLink . '">' . $title . '</a>';
			$media_dev_additional_files .=     '<br /><span class="bbg__file-size">(' . $ext . ', ' . $filesize . ')</span>';
			$media_dev_additional_files .= '<h4>';
		}
	}
}

$media_dev_additional_internal_videos = '';
if (have_rows('media_dev_additional_internal_videos')) {
	$media_dev_additional_internal_videos .= '<aside>';
	$media_dev_additional_internal_videos .=     '<h3 class="sidebar-section-header">Videos</h3>';
	while (have_rows('media_dev_additional_internal_videos')) {
		the_row();

		$mediaDevAdditionalInternalVideoObj = get_sub_field('media_dev_additional_internal_video');

		if (!empty($mediaDevAdditionalInternalVideoObj)) {
			$fileUrl = $mediaDevAdditionalInternalVideoObj['url'];
			$fileMimeType = $mediaDevAdditionalInternalVideoObj['mime_type'];

			$media_dev_additional_internal_videos .= '<p class="sidebar-article-title">';
			$media_dev_additional_internal_videos .= '    <div display: flex;">';
			$media_dev_additional_internal_videos .= '        <video style="width: 100%;" controls muted playsinline>';
			$media_dev_additional_internal_videos .= '            <source src="' . $fileUrl . '" type="' . $fileMimeType . '"/>';
			$media_dev_additional_internal_videos .= '            Sorry, your browser doesn\'t support embedded videos.';
			$media_dev_additional_internal_videos .= '        </video>';
			$media_dev_additional_internal_videos .= '    </div>';
			$media_dev_additional_internal_videos .= '</p>';
		}
	}
	$media_dev_additional_internal_videos .= '</aside>';
}

// Need to use this logic due to side effect of old acf min/max values
if (!empty($media_dev_additional_files)) {
	$openingTags = '';
	$openingTags .= '<aside>';
	$openingTags .=     '<h3 class="sidebar-section-header">Supplemental Files</h3>';
	$closingTags = '</aside>';
	$media_dev_additional_files = $openingTags . $media_dev_additional_files . $closingTags;
}

$addtl_image_set = '';
$media_dev_addtl_images = get_field('media_dev_additional_images');
if (!empty($media_dev_addtl_images)) {
	foreach($media_dev_addtl_images as $addtl_dev_image) {
		foreach($addtl_dev_image as $media_image) {
			if (!empty($media_image)) {
				$addtl_image_set .= '<div><img src="' . $media_image['url'] . '" alt="Additional media image"></div>';
			}
		}
	}
}

$isPodcast = has_category('podcast');

$soundcloudPlayer = "";
if ($isPodcast) {
	$podcastSoundcloudURL = get_post_meta( get_the_ID(), 'podcast_soundcloud_url', true );
	$podcastTranscript = get_post_meta( get_the_ID(), 'podcast_transcript', true );
	$podcastTranscript = apply_filters('the_content', $podcastTranscript);
	if ($podcastTranscript) {
		$podcastTranscript  = '<div id="podcastTranscript" class="usa-accordion-bordered bbg__committee-list">';
		$podcastTranscript .= 	'<ul class="unstyled-list">';
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

$hideFeaturedImage = false;
?>

<style>
.leaflet-popup-pane {min-width: 300px !important;}
</style>


<main id="main" role="main">
	<section class="outer-container">
		<div class="grid-container">
			<?php
				$parent_header = '';
				if (get_post_type() == "threat_to_press") {
					$threat_link = get_permalink(get_page_by_path('threats-to-press'));

					$parent_header  = '<h2 class="section-header">';
					$parent_header .= 	'<a href="' . $threat_link . '">Threats to Press</a>';
					$parent_header .= '</h2>';
				}
				else if (has_category('deep-dive-series')) {
					$parent_header .= '<h2 class="section-header"><a href="our-work/strategy-and-results/deep-dive-series/">Deep Dive Series</a></h2>';
				}
				echo $parent_header;
			?>
		</div>

		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<?php
							echo '<header>';
							echo 	'<h3 class="article-title">' . $page_title . '</h3>';
							if (!empty($post_subtitle)) {
								echo 	'<h4 class="article-subtitle">' . $post_subtitle . '</h4>';
							}
							echo 	'<p class="date-meta">';
							if (!empty($post_byline)) {
								echo 	'by ' . $post_byline . '<br>';
							}
							echo 	$post_date . '</p>';
							echo '</header>';

							$video_url = get_field('featured_video_url', $post_id);
							if ($video_url != "") {
								if (strpos($video_url, 'facebook.com') !== false) {
									echo $featured_video = featured_video($video_url);
								} else {
									$featured_video = featured_video($video_url);
									$featured_video_markup  = '<iframe class="bbg-banner" scrolling="no" src="';
									$featured_video_markup .= 	$featured_video['url'];
									$featured_video_markup .= 	'" frameborder="0" allowfullscreen="" data-ratio="NaN" data-width="" data-height="" style="display: block; margin: 0 0 30px 0;">';
									$featured_video_markup .= '</iframe>';
									echo $featured_video_markup;
								}
							}
							elseif (!empty($post_thumbnail_url)) {
								echo '<img src="' . $post_thumbnail_url . '" alt="' . $page_title . '">';
								if ($show_featured_image_caption) {
									$caption = wp_get_attachment_caption(get_post_thumbnail_id($post_id));
									if (!empty($caption)) {
										echo '<p class="featured-image-caption">';
										echo $caption;
										echo '</p>';
									}
								}
							}

							if ($isPodcast) {
								echo $soundcloudPlayer;
							}
							if (!empty($entity_logos)) {
								echo '<div class="entity-category-logo-container">';
								foreach($entity_logos as $entity_logo) {
									echo '<a href="' . $entity_logo['site-url'] . '">';
									echo 	'<img class="entity-catgory-logo" src="'. $entity_logo['logo-url'] . '" alt="Entity logo">';
									echo '</a>';
								}
								echo '</div>';
							}
							echo $page_content;

							if ($isPodcast) {
								echo $podcastTranscript;
							}

							// CONTACT CARDS
							$contactPostIDs = get_post_meta($post_id, 'contact_post_id', true);
							renderContactCard($contactPostIDs);

							if (!empty($addtl_image_set)) {
								echo $addtl_image_set;
							}
						?>
					</div>
					<div class="side-column divider-left">
						<?php
							// DISPLAY AWARDS IN DROPDOWN
							// STANDALONE AWARDS
							$standalone_title = get_field('standardpost_award_title', get_the_ID());
							if (!empty($standalone_title)) {
								$stand_alone_award_data = get_standalone_award(get_the_ID());
								echo $stand_alone_award_data;
							}
							// PRESS RELEASE AWARDS
							$pr_awards = get_field('press_release_awards', $post_id);
							if ($pr_awards == 'yes') {
								$press_release_awards_data = get_press_release_award(get_the_ID());
								echo $press_release_awards_data;
							}

							if ($includeRelatedProfile) {
								echo $relatedProfile;
							}

							echo $featuredJournalists;
							echo $advisoryExpertsStr;

							echo getInterviewees();

							if ($includeMap  && $mapLocation) {
								$sidebar_map  = '<h3 class="sidebar-section-header">' . $mapHeadline . '</h3>';
								$sidebar_map .= '<div id="map" class="bbg__locator-map">';
								$sidebar_map .= 	'<p>' . $mapDescription . '</p>';
								$sidebar_map .= '</div>';
								echo $sidebar_map;
							}

							if ($includeSidebar) {
								echo $sidebar;
							}
							if ($listsInclude) {
								echo $sidebarDownloads;
							}

							echo $media_dev_sidebar_meta;
							echo $media_dev_sponsors;
							echo $media_dev_additional_files;
							echo $media_dev_additional_internal_videos;
							echo $team_roster;
							echo getAccordion();

							// SHARE THIS PAGE
							$share_icons = social_media_share_page($post_id);
							if (!empty($share_icons)) {
								echo $share_icons;
							}
						?>
					</div>
				</div>
			</div>
		</div><!-- .outer-container -->
	</section><!-- #post-## -->
</main><!-- END #main -->

<?php if (!empty($media_dev_map) && !empty($mapHeadline)) { ?>

<script type="text/javascript" src='https://api.tiles.mapbox.com/mapbox.js/v2.3.0/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.3.0/mapbox.css' rel='stylesheet' />
<script type="text/javascript">
	L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
	var map = L.mapbox.map('map-featured')
	.addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11'))
	<?php echo '.setView(['. $media_dev_map['lat'] . ', ' . $media_dev_map['lng'] . '], ' . $zoom . ');'; ?>
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
				<?php echo $media_dev_map['lng'] . ', ' . $media_dev_map['lat']; ?>
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
if (!empty($addFeaturedMap) && $media_dev_map == "") {
?>
	<script type="text/javascript" src='https://api.tiles.mapbox.com/mapbox.js/v2.3.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.3.0/mapbox.css' rel='stylesheet' />
	<script type="text/javascript">
		L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var map = L.mapbox.map('map-featured')
		.addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/light-v10'))
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
	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.3.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.3.0/mapbox.css' rel='stylesheet' />

	<script>
		L.mapbox.accessToken = 'pk.eyJ1IjoiYmJnd2ViZGV2IiwiYSI6ImNpcDVvY3VqYjAwbmx1d2tyOXlxdXhxcHkifQ.cD-q14aQKbS6gjG2WO-4nw';
		var map = L.mapbox.map('map')
		.addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11'))
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
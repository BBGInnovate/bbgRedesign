<?php
// ----------------
// CONTENT LIST:
// Highlight and find next to jump to section
// ----------------
// BBG SETTINGS
// HOMEPAGE OPTIONS
// FLEXIBLE ROWS
// ----------------

// BBG SETTINGS
function get_site_settings_data() {
	$intro_content = get_field('site_setting_mission_statement','options','false');
	$intro_link = get_field('site_setting_mission_statement_link', 'options', 'false');
	$site_setting_package = array('intro_content' => $intro_content, 'intro_link' => $intro_link);
	return $site_setting_package;
}

// HOMEPAGE OPTIONS
function get_soapbox_data() {
	$soapbox_toggle = get_field('soapbox_toggle', 'option');
	$soapbox_post = get_field('homepage_soapbox_post', 'option');

	if ($soapbox_post) {
		$postIDsUsed[] = $soapbox_post[0] -> ID;
	}

	$soap_index = $soapbox_post[0];
	$id = $soap_index -> ID;
	$soap_category = wp_get_post_categories($id);
	$soapPostPermalink = get_the_permalink($id);

	// IF PAGE HAS BYLINE, USE THAT
	$includeByline = get_post_meta($id, 'include_byline', true);
	if ($includeByline) {
		$bylineOverride = get_post_meta($id, 'byline_override', true);
	} else {
		$bylineOverride = "";
	}

	// POSTS RELATED TO MAIN POST
	$related_profile_id = get_post_meta($id, 'statement_related_profile', true);
	if ($related_profile_id) {
		$includeRelatedProfile = true;

		$profile_photo = "";
		$alternatePhotoID = get_post_meta($id, 'statement_alternate_profile_image', true);
		if ($alternatePhotoID) {
			$profile_photoID = $alternatePhotoID;
		} else {
			$profile_photoID = get_post_meta($related_profile_id, 'profile_photo', true);
		}

		if ($profile_photoID) {
			$profile_photo = wp_get_attachment_image_src($profile_photoID , 'medium-thumb');
			$profile_photo = $profile_photo[0];
		}

		$profile_name = "";
		if ($bylineOverride !== "") {
			$profile_name =  $bylineOverride;
		} else {
			$profile_name = get_the_title($related_profile_id);
		}
	}

	// CHANGE "READ MORE" TEXT?
	$readMoreLabel = "READ MORE";
	$homepageSoapboxReadMore = get_field( 'homepage_soapbox_read_more', 'options' );
	if ($homepageSoapboxReadMore != "") {
		$readMoreLabel = strtoupper($homepageSoapboxReadMore);
	}

	// CHANGE SOAPBOX LABEL
	$homepage_soapbox_label = get_field('homepage_soapbox_label', 'options');

	// BLUE BACKGROUND DEFAULT
	$soap_class = "bbg__voice--featured";
	$soapHeaderPermalink = "";
	$soapHeaderText = "";
	if ($homepage_soapbox_label != "") {
		$soapHeaderText = $homepage_soapbox_label;
		$soapHeaderPermalink = get_field('homepage_soapbox_link', 'options');
	} else {
		/** if the user did not manually enter a soapbox label, use the categories on this post
			to decide the class (background color) as well as the header link, and in some cases the profile photo/name
		**/
		$is_ceo_post = false;
		$isSpeech = false;

		foreach ($soap_category as $c) {
			$cat = get_category($c);
			$soapHeaderPermalink = get_category_link($cat -> term_id);
			$soapHeaderText = $cat -> name;

			if ($cat -> slug == "from-the-ceo") {
				$is_ceo_post = true;
				$soap_class = "bbg__voice--ceo";
				$soapHeaderText = "From the CEO";
				$profile_photo = get_field('homepage_soapbox_image', 'options');
				$profile_name = "John Lansing";
				break;
			}
			else if ($cat -> slug == "usim-matters") {
				$isSpeech = true;
				$soap_class = "bbg__voice--guest";
			} else if ($cat -> slug == "speech" ||  $cat -> slug == "statement" || $cat -> slug == "media-advisory") {
				$isMediaAdvisory = true;
				$soap_class = "bbg__voice--featured";
			}
		}
	}

	// IF POST HAS PHOTO, USE THAT
	if ($soapbox_post) {
		if (wp_get_attachment_image_src($soapbox_post, 'profile_photo')) {
			$profile_photo = wp_get_attachment_image_src($soapbox_post , 'profile_photo');
			$profile_photo = $profile_photo[0];
		}
	}

	// DEFAULT TO SOAPBOX CAPTION
	$soap_contents_caption = get_field('homepage_soapbox_image_caption', 'option');
	if ($soap_contents_caption != "") {
		$profile_name = $soap_contents_caption;
	}

	$soapbox_data = array(
		'toggle' => $soapbox_toggle, 
		'post_id' => $id, 
		'article_class' => $soap_class, 
		'title' => get_the_title($id), 
		'header_link' => $soapHeaderPermalink, 
		'header_text' => $soapHeaderText, 
		'post_link' => $soapPostPermalink, 
		'profile_image' => $profile_photo, 
		'profile_name' => $profile_name, 
		'read_more' => $readMoreLabel
	);
	return $soapbox_data;
}

function get_corner_hero_data() {
	$toggle = get_field('corner_hero_toggle', 'option');
	$homepage_hero_corner = get_field('homepage_hero_corner', 'option');

	if ($homepage_hero_corner  == 'event') {
		$featuredEvent = get_field('homepage_featured_event', 'option');
	}
	else if ($homepage_hero_corner == 'advisory') {
		$featuredAdvisory = get_field('homepage_featured_advisory', 'option');
	}
	else if ($homepage_hero_corner == "callout") {
		$featuredCallout = get_field('homepage_featured_callout', 'option');
	}

	$cornerHeroLabel = get_field('corner_hero_label', 'option');
	if  ($cornerHeroLabel == '') {
		$cornerHeroLabel = 'This week';
	}

	$postIDsUsed = array();
	if ($homepage_hero_corner == 'event' && $featuredEvent) {
		$postIDsUsed = $featuredEvent -> ID;
	}

	if (($homepage_hero_corner == 'event' && $featuredEvent) || ($homepage_hero_corner == 'advisory' && $featuredAdvisory)) {
		if ($homepage_hero_corner == 'event') {
			$c_type = 'event';
			$cornerHeroPost = $featuredEvent;
		} else {
			$c_type = 'advisory';
			$cornerHeroPost = $featuredAdvisory;
		}
		$cornerHeroClass = 'bbg__event-announcement';
		if (has_category('Media Advisory', $featuredEvent)) {
			$cornerHeroClass = 'bbg__advisory-announcement';
		}
		$id = $cornerHeroPost -> ID;
		$cornerHeroPermalink = get_the_permalink( $id );

		/* permalinks for future posts by default don't return properly. fix that. */
		if ($cornerHeroPost -> post_status == 'future') {
			$my_post = clone $cornerHeroPost;
			$my_post -> post_status = 'published';
			$my_post -> post_name = sanitize_title($my_post -> post_name ? $my_post -> post_name : $my_post -> post_title, $my_post -> ID);
			$cornerHeroPermalink = get_permalink($my_post);
		}

		$cornerHeroTitle = $cornerHeroPost -> post_title;
		$excerpt = my_excerpt($id);
		$corner_hero_package = array('toggle' => $toggle, 'type' => $c_type, 'class' => $cornerHeroClass, 'p_link' => $cornerHeroPermalink, 'label' => $cornerHeroLabel, 'title' => $cornerHeroTitle, 'excerpt' => $excerpt);

		// return build_corner_hero_parts($corner_hero_package);
		return $corner_hero_package;
	}
	else if ($homepage_hero_corner == 'callout' && $featuredCallout) {
		$c_type = 'callout';
		outputCallout($featuredCallout);
	}
	else {
		// EITHER USER SELECTED "quotes" or NO VALID CALLOUT, EVENT, ADVISORY SELECTED 
		$c_type = 'quote';
		$q = getRandomQuote('allEntities', $postIDsUsed);
		if ($q) {
			$postIDsUsed[] = $q['ID'];
			$quote = outputQuote($q, '');
		}
		// $quote_package = array('toggle' => $toggle, 'type' => $c_type);
		// return $quote_package;
	}
}

function get_impact_stories_data($qty) {
	global $includePortfolioDescription;
	global $postIDsUsed;

	$impactPostIDs = select_impact_story_id_at_random($postIDsUsed);
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $qty,
		'orderby' => 'post_date',
		'order' => 'desc',
		'post__in' => $impactPostIDs
	);
	query_posts($qParams);

	$post_id_group = array();
	if (have_posts()) {
		while (have_posts()) { 
			the_post();
			$includePortfolioDescription = false;
			$postIDsUsed[] = get_the_ID();
			array_push($post_id_group, get_the_ID());
		}
	}
	wp_reset_query();
	return $post_id_group;
}

function getThreatsPostQueryParams($numPosts, $used) {
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $numPosts,
		'orderby' => 'post_date',
		'order' => 'desc',
		'cat' => get_cat_id('Threats to Press'),
		'post__not_in' => $used
	);
	return $qParams;
}

function get_threats_to_press_data() {
	$threat_query_id_set = array();
	$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
	$randomFeaturedThreatsID = false;

	if ($threatsToPressPost) {
		$randKey = array_rand($threatsToPressPost);
		$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
	}

	$threatsUsedPosts = array();
	if ($randomFeaturedThreatsID) {
		$qParams = array(
			'post__in' => array(1, $randomFeaturedThreatsID)
		);
	} else {
		$qParams = getThreatsPostQueryParams($threatsUsedPosts);
	}
	query_posts($qParams);

	if (have_posts()) {
		while (have_posts()) {
			the_post();
			$id = get_the_ID();
			$threatsUsedPosts[] = $id;
			$postIDsUsed[] = $id;
			array_push($threat_query_id_set, $id);
		}
	}
	wp_reset_query();

	// ADDITIONAL THREAT POSTS
	$maxPostsToShow = 1;
	$qParams = getThreatsPostQueryParams($maxPostsToShow, $threatsUsedPosts);
	query_posts($qParams);

	if (have_posts()) {
		$counter = 0;
		while (have_posts()) : the_post();
			$counter++;
			$postIDsUsed[] = get_the_ID();
			array_push($threat_query_id_set, get_the_ID());
		endwhile;
	}
	wp_reset_query();

	return $threat_query_id_set;
}

// FLEXIBLE ROWS
function get_ribbon_data() {
	$label_text = get_sub_field('about_ribbon_label');
	$label_link = get_sub_field('about_ribbon_label_link');
	$headline_text = get_sub_field('about_ribbon_headline');
	$headline_link = get_sub_field('about_ribbon_headline_link');
	$image_url = get_sub_field('about_ribbon_image');
	$summary = get_sub_field('about_ribbon_summary');

	$ribbon_data = array(
		'label' => $label_text,
		'label_link' => $label_link,
		'headline' => $headline_text,
		'headline_link' => $headline_link,
		'image_url' => $image_url,
		'summary' => $summary
	);
}

function get_marquee_data($raw_marquee_row) {
	$marquee_heading = $raw_marquee_row['marquee_heading'];
	$marquee_link = $raw_marquee_row['marquee_link'];
	$marquee_content = $raw_marquee_row['marquee_content'];

	$marquee_data = array(
		'heading' => $marquee_heading, 
		'link' => $marquee_link, 
		'content' => $marquee_content
	);
	return $marquee_data;
}

function get_umbrella_main_data($raw_umbrella_main) {
	$section_heading = $raw_umbrella_main['umbrella_section_heading'];
	$section_heading_link = $raw_umbrella_main['umbrella_section_heading_link'];
	$section_intro_text = $raw_umbrella_main['umbrella_section_intro_text'];
	$force_content_labels = $raw_umbrella_main['umbrella_force_content_labels'];

	$umbrella_data = array(
		'header' => $section_heading,
		'header_link' => $section_heading_link,
		'intro_text' => $section_intro_text,
		'forced_label' => $force_content_labels,
	);
	return $umbrella_data;
}

function get_umbrella_content_data($raw_umbrella_content) {
	$column_title = $raw_umbrella_content['umbrella_content_internal_column_title'];
	$item_title_query = $raw_umbrella_content['umbrella_content_internal_include_item_title'];
	$item_title = $raw_umbrella_content['umbrella_content_internal_item_title'];
	$excerpt_query = $raw_umbrella_content['umbrella_content_internal_include_excerpt'];
	$include_featrued_image = $raw_umbrella_content['umbrella_content_internal_include_featured_image'];
	$link = $raw_umbrella_content['umbrella_content_internal_link'];
	$layout = $raw_umbrella_content['umbrella_content_internal_layout'];;

	$content_pacakge = array('column_title' => $column_title, 'include_title' => $item_title_query, 'item_title' => $item_title, 'include_excerpt' => $excerpt_query, 'image' => $include_featrued_image, 'link' => $link, 'layout' => $layout);
	return $content_pacakge;
}

function get_entity_data($grid_class) {
	// $grid_class can be ["entity-main" | "entity-side"]
	$entityParentPage = get_page_by_path('networks');
	$entity_id_group = array();
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $entityParentPage->ID,
		'orderby' => 'meta_value_num',
		'meta_key' => 'entity_year_established',
		'order' => 'ASC'
	);
	$custom_query = new WP_Query($qParams);

	if ($custom_query -> have_posts()) {
		while($custom_query -> have_posts()) {
			$custom_query -> the_post();
			array_push($entity_id_group, get_the_ID());
		}

	}
	array_push($entity_id_group, $grid_class);

	$entity_data_package = array('id_group' => $entity_id_group, 'placement' => $grid_class);
	return build_entity_parts($entity_data_package);
}

?>
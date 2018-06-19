<?php
// 1. COLLECT DATA
//    Get data from custom fields from Homepage Options
// 2. BUILD MODULES
//    Take data and build module. Other div of module has fluid width so it will adjust to parent size
// 3. INSERT MODULE IN GRID ARCHITECTURE
//    Insert parts into div architecture
// * INCLUDE A NOTE AS TO WHERE THE STYLES ARE LOCATED (AFTER PROPERLY PLACED)


// 1. COLLECT DATA
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

function get_entity_data($grid_class) {
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
// 2. BUILD MODULES
//    Make parts (complete tags, logic, etc) then assemble module
function build_soapbox_parts($soap_data, $layout) {
	$article_class = $soap_data['article_class'];

	// BUILD PARTS
	$soap_heading = '<header class="entry-header bbg__article-icons-container">';
	if (!empty($soap_data['post_link'])) {
		$soap_heading .= '<h2 class="mention-header"><a href="' . $soap_data['header_link'] . '">' . $soap_data['header_text'] . '</a></h2>';
	} else if (!empty($soap_data['header_text'])) {
		$soap_heading .= '<h2 class="mention-header">' . $soap_data['header_text'] . '</h2>';
	}
	$soap_heading .= '</header>';

	$soap_title .= '<h5>';
	$soap_title .= 	'<a href="' . $soap_data['header_link'] . '">';
	$soap_title .= 		$soap_data['title'];
	$soap_title .= 	'</a>';
	$soap_title .= '</h5>';

	$soap_content .= '<p>';
	$soap_content .= 	my_excerpt($soap_data['post_id']);
	// $soap_content .= 	' <a href="' . $soap_data['post_link'] . '" class="bbg__read-more">' . $soap_data['read_more'] . ' »</a>';
	$soap_content .= '</p>';

	if (!empty($soap_data['profile_image'])) {
		$soap_image  = '<div><img src="' . $soap_data['profile_image'] . '"></div>';
		if ($soap_data['profile_name'] != "") {
			$soap_image .= '<p class="profile-name">' . $soap_data['profile_name'] . '</p>';
		}
	}

	// INSERT PART INTO GRID
	// OUTER DIV MUST HAVE CLASS OF 'inner-container' TO BE ABLE TO FIT PARENT
	$soapbox_markup  = '<div class="inner-container soap-corner ' . $article_class . '">';
	// $soapbox_markup .= 	'<div class="nest-container">';
	if ($layout == 'image-left') {
		$soapbox_markup .= 	'<div class="small-side">';
		$soapbox_markup .= 		$soap_image;
		$soapbox_markup .= 	'</div>';
	}
	$soapbox_markup .= 		'<div class="large-side">';
	$soapbox_markup .= 			$soap_heading;
	$soapbox_markup .= 			$soap_title;
	$soapbox_markup .= 			$soap_content;
	$soapbox_markup .= 		'</div>';
	if ($layout == 'image-right') {
		$soapbox_markup .= 	'<div class="small-side">';
		$soapbox_markup .= 		$soap_image;
		$soapbox_markup .= 	'</div>';
	}
	// $soapbox_markup .= 	'</div>';
	$soapbox_markup .= '</div>';

	return $soapbox_markup;
}

function build_corner_hero_parts($corner_hero_data) {
	$type = $corner_hero_data['type'];

	// BUILD PARTS
	if ($type == 'event' || $type == 'advisory') {
		$corner_hero_image = '<img src="' . content_url($path = '/uploads/2018/06/usagm-touch-image.png') . '">';

		$corner_hero_header  = '<div class="bbg__article-icons-container">';
		$corner_hero_header .= 	'<h2 class="bbg__label bbg__label--outside">' . $corner_hero_data['label'] . '</h2>';
		$corner_hero_header .= 	'<div class="bbg__article-icon"></div>';
		$corner_hero_header .= '</div>';

		$corner_hero_title  = '<h5>';
		$corner_hero_title .= 	'<a href="' . $corner_hero_data['p_link'] . '" rel="bookmark">"' . $corner_hero_data['title'] . '"</a>';
		$corner_hero_title .= '</h5>';

		$corner_hero_content = '<p>' . $corner_hero_data['excerpt'] . '</p>';

		// INSERT PART INTO GRID
		// OUTER DIV MUST HAVE CLASS OF 'inner-container' TO BE ABLE TO FIT PARENT
		$corner_hero_markup  = '<div class="inner-container soap-corner">';
		$corner_hero_markup .= 	'<div class="small-side">';
		$corner_hero_markup .= 		$corner_hero_image;
		$corner_hero_markup .= 	'</div>';
		$corner_hero_markup .= 	'<div class="large-side">';
		$corner_hero_markup .= 		$corner_hero_header;
		$corner_hero_markup .= 		$corner_hero_title;
		$corner_hero_markup .= 		$corner_hero_content;
		$corner_hero_markup .= 	'</div>';
		$corner_hero_markup .= '</div>';

		return $corner_hero_markup;
	}
}

function build_impact_markup($impact_data) {
	$impact_markup_set = array();
	$i = 0;
	foreach($impact_data as $impact_id) {
		$cur_post = get_post($impact_id);

		$impact_linked_image =  	'<a href="' . get_permalink($impact_id) . '">';
		if (get_permalink($impact_id)) {
			$impact_linked_image .= 		get_the_post_thumbnail($impact_id);
		} else {
			$impact_linked_image .= 		'<img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="BBG Placeholder Image" />';
		}
		$impact_linked_image .= 	'</a>';

		$impact_header = 	'<h5><a href="' . get_permalink($impact_id) . '">' . $cur_post->post_title . '</a></h5>';
		$impact_content = 	'<p>' . wp_trim_words($cur_post->post_content, 70) . '</p>';

		$impact_markup  = '<div>';
		$impact_markup .= 	$impact_linked_image;
		$impact_markup .= 	$impact_header;
		$impact_markup .= 	$impact_content;
		$impact_markup .= '</div>';

		// DYNAMIC VARIABLE NAME FOR UNIQUE NAME TO POPULATE ARRAY
		${"impact_block" . $i} = $impact_markup;

		array_push($impact_markup_set, ${"impact_block" . $i});
		$i++;
	}
	return $impact_markup_set;
}

function build_threat_parts($threat_data) {
	$threat_markup_set = array();
	$i = 0;
	foreach($threat_data as $threat_id) {
		$cur_threat = get_post($threat_id);
		$threat_image  = '<a href="' . get_the_permalink($threat_id) . '" rel="bookmark" tabindex="-1">';
		$threat_image .= 	get_the_post_thumbnail($threat_id);
		$threat_image .= '</a>';

		$threat_content  = '<h5><a href="' . get_the_permalink($threat_id) . '">' . $cur_threat->post_title . '</a></h5>';
		$threat_content .= '<p>' . wp_trim_words($cur_threat->post_content, 40) . '</p>';

		$threat_markup  = '<div class="threat-article">';
		$threat_markup .= 	'<div class="inner-container">';
		$threat_markup .= 		'<div class="threat-image">';
		$threat_markup .= 			$threat_image;
		$threat_markup .= 		'</div>';
		$threat_markup .= 		'<div class="threat-content">';
		$threat_markup .= 			$threat_content;
		$threat_markup .= 		'</div>';
		$threat_markup .= 	'</div>';
		$threat_markup .= '</div>';

		${"threat_block" . $i} = $threat_markup;
		array_push($threat_markup_set, ${"threat_block" . $i});
		$i++;
	}
	return $threat_markup_set;
}

function build_entity_parts($entity_data) {
	$placement_class = $entity_data['placement'];
	$entity_set = array();
	$i = 0;
	foreach($entity_data['id_group'] as $entity_id) {
		$id = $entity_id;
		$fullName = get_post_meta($id, 'entity_full_name', true);
		$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
		$abbreviation = str_replace("/", "",$abbreviation);

		$description = get_post_meta($id, 'entity_description', true);
		$description = apply_filters('the_content', $description);
		
		$link = get_permalink( get_page_by_path("/networks/$abbreviation/"));
		$imgSrc = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png'; //need to fix this

		if (!empty($fullName)) {
			$entity_image  = '<a href="' . $link . '" tabindex="-1">';
			$entity_image .= 	'<img src="' . $imgSrc . '">';
			$entity_image .= '</a>';

			$entity_content  = 	'<h5><a href="' . $link . '">' . $fullName . '</a></h5>';
			$entity_content .= 	'<p class="">' . $description . '</p>';
			
			$entity_pieces = array('image' => $entity_image, 'content' => $entity_content);
			${"entity_block" . $i} = $entity_pieces;

			array_push($entity_set, ${"entity_block" . $i});
			$i++;
		}
	}
	$entity_parts_package = array('class' => $placement_class, 'parts' => $entity_set);
	assemble_entity_section($entity_parts_package);
}

// 3. INSERT MODULE IN GRID ARCHITECTURE
//    This is the sections outer grid with slots for modules
// Mention(a): BOTH SOAPBOX AND CORNER HERO
function assemble_mentions_full_width($mention_data, $impact_group) {
	$mention_full  = 			'<div class="inner-container">';
	$mention_full .= 				'<div class="grid-container soap-corner-full">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) { 
		$mention_full .= 				$data;
	}
	$mention_full .= 				'</div>';
	$mention_full .= 			'</div>';
	$mention_full .= 			'<div class="inner-container ">';
	// IMPACT STORIES
	foreach($impact_group as $impact) {
		$mention_full .= 				'<div class="split-grid">';
		$mention_full .= 					$impact;
		$mention_full .= 				'</div>';
	}
	$mention_full .= 			'</div>';
	echo $mention_full;
}
// Mention(b): EITHER SOAPBOX OR CORNER HERO 
function assemble_mentions_share_space($mention_data, $impact_group) {
	$mention_share  = '<div class="inner-container">';
	$mention_share .= 	'<div class="soap-corner-share-grid">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) { 
		$mention_share .= 				$data;
	}
	$mention_share .= 	'</div>';
	$mention_share .= 	'<div class="impacts-share">';
	// IMPACT STORY (ONLY ONE FOR THIS LAYOUT)
	$mention_share .= 		$impact_group[0];
	$mention_share .= 	'</div>';
	$mention_share .= '</div>';
	echo $mention_share;
}

function assemble_threats_to_press_ribbon($threat_data) {
	$theat_ribbon  = '<div class="bbg__ribbon threats-ribbon">';
	$theat_ribbon .= 	'<div class="outer-container">';
	$theat_ribbon .= 		'<div class="grid-container">';
	$theat_ribbon .= 			'<h2>Threats to Press</h2>';
	$theat_ribbon .= 			'<div class="threat-container">';
	foreach ($threat_data as $data) {
		$theat_ribbon .= 			$data;
	}
	$theat_ribbon .= 			'</div>';
	$theat_ribbon .= 		'</div>';
	$theat_ribbon .= 	'</div>';
	$theat_ribbon .= '</div>';
	echo $theat_ribbon;
}

function assemble_entity_section($entity_data) {
	$entity_class = $entity_data['class'];
	$entity_chuncks = $entity_data['parts'];

	$entity_markup  = 	'<section class="outer-container" id="entities">';
	$entity_markup .= 		'<div class="grid-container">';
	$entity_markup .= 			'<h1 class="header-outliner">Entities</h1>';
	$entity_markup .= 			'<h2><a href="' . get_permalink(get_page_by_path('networks')) . '">Our Networks</a></h2>';
	$entity_markup .= 			'<p class="lead-in">Every week, more than ' . do_shortcode('[audience]') . ' listeners, viewers and internet users around the world turn on, tune in and log onto U.S. international broadcasting programs. The day-to-day broadcasting activities are carried out by the individual BBG international broadcasters.</p>';
	$entity_markup .= 		'</div>';
	$entity_markup .= 	'<div class="outer-container">';
	foreach ($entity_chuncks as $entity_part) {
		$entity_markup .= '<div class="contain-entity ' . $entity_class . '">';
		$entity_markup .= 	'<div class="nest-container">';
		$entity_markup .= 		'<div class="inner-container">';
		$entity_markup .= 			'<div class="entity-icon">';
		$entity_markup .= 				$entity_part['image'];
		$entity_markup .= 			'</div>';
		$entity_markup .= 			'<div class="entity-desc">';
		$entity_markup .= 				$entity_part['content'];
		$entity_markup .= 			'</div>';
		$entity_markup .= 		'</div>';
		$entity_markup .= 	'</div>';
		$entity_markup .= '</div>';
	}
		$entity_markup .= 	'</div>';
		$entity_markup .= '</section>';
	echo $entity_markup;
}
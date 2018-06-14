<?php
// 1. COLLECT DATA
//    Get data from custom fields from Homepage Options
// 2. BUILD MODULES
//    Take data and build module. Other div of module has fluid width so it will adjust to parent size
// 3. INSERT MODULE IN GRID ARCHITECTURE
//    Insert parts into div architecture
// * INCLUDE A NOTE AS TO WHERE THE STYLES ARE LOCATED


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
	// return build_soapbox_parts($soapbox_data);
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

// 2. BUILD MODULES
//    Make parts (complete tags, logic, etc) then assemble module
function build_soapbox_parts($soap_data, $layout) {
	$article_class = $soap_data['article_class'];

	// BUILD PARTS
	$soap_heading = '<header class="entry-header bbg__article-icons-container">';
	if (!empty($soap_data['post_link'])) {
		$soap_heading .= '<h2><a href="' . $soap_data['header_link'] . '">' . $soap_data['header_text'] . '</a></h2>';
	} else if (!empty($soap_data['header_text'])) {
		$soap_heading .= '<h2>' . $soap_data['header_text'] . '</h2>';
	}
	$soap_heading .= '</header>';

	$soap_title .= '<h5>';
	$soap_title .= 	'<a href="' . $soap_data['header_link'] . '">';
	$soap_title .= 		$soap_data['title'];
	$soap_title .= 	'</a>';
	$soap_title .= '</h5>';

	$soap_content .= '<p>';
	$soap_content .= 	my_excerpt($soap_data['post_id']);
	$soap_content .= 	' <a href="' . $soap_data['post_link'] . '" class="bbg__read-more">' . $soap_data['read_more'] . ' »</a>';
	$soap_content .= '</p>';

	if (!empty($soap_data['profile_image'])) {
		$soap_image  = '<div><img src="' . $soap_data['profile_image'] . '"></div>';
		if ($soap_data['profile_name'] != "") {
			$soap_image .= '<p class="profile-name">' . $soap_data['profile_name'] . '</p>';
		}
	}

	// INSERT PART INTO GRID
	// OUTER DIV MUST HAVE CLASS OF 'inner-container' TO BE ABLE TO FIT PARENT
	$soapbox_markup  = '<div class="inner-container soap-corner ' . $article_class . '" style="border: 1px solid blue;">';
	if ($layout == 'image-left') {
		$soapbox_markup .= 	'<div class="small-side">';
		$soapbox_markup .= 		$soap_image;
		$soapbox_markup .= 	'</div>';
	}
	$soapbox_markup .= 	'<div class="large-side">';
	$soapbox_markup .= 		$soap_heading;
	$soapbox_markup .= 		$soap_title;
	$soapbox_markup .= 		$soap_content;
	$soapbox_markup .= 	'</div>';
	if ($layout == 'image-right') {
		$soapbox_markup .= 	'<div class="small-side">';
		$soapbox_markup .= 		$soap_image;
		$soapbox_markup .= 	'</div>';
	}
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
		$corner_hero_markup  = '<div class="inner-container soap-corner" style="border: 1px solid blue;">';
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

// 3. INSERT MODULE IN GRID ARCHITECTURE
function assemble_mentions_full_width($mention_data) {
	$mention_full  = 			'<div class="inner-container">';
	$mention_full .= 				'<div class="grid-container soap-corner-full">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) { 
		// VERY NEXT DIV MUST HAVE CLASS OF 'inner-container' TO BE ABLE TO FIT PARENT
		$mention_full .= 				$data;
	}
	$mention_full .= 				'</div>';
	$mention_full .= 			'</div>';
	$mention_full .= 			'<div class="inner-container " style="border: 1px solid green;">';
	// IMPACT STORIES
						// loop impact storie
	$mention_full .= 				'<div class="split-grid" style="border: 1px solid orange;">impact stories</div>';
						// end loop impact storie
	$mention_full .= 			'</div>';
	echo $mention_full;
}

function assemble_mentions_share_space($mention_data) {
	$mention_share  = '<div class="inner-container" style="border: 1px solid blue;">';
	$mention_share .= 	'<div class="soap-corner-share-grid" style="border: 1px solid green;">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) { 
		$mention_share .= 				$data;
	}
	$mention_share .= 	'</div>';
	$mention_share .= 	'<div class="impacts-share" style="border: 1px solid orange;">';
	$mention_share .= 		'<div>impact stories</div>';
	$mention_share .= 	'</div>';
	$mention_share .= '</div>';
	echo $mention_share;
}
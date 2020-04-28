<?php
// ----------------
// CONTENTS:
// ----------------
// BBG SETTINGS
// HOMEPAGE OPTIONS
// FLEXIBLE ROWS
// ENTITY FIELDS
// ABOUT (OFFICE)
// ----------------

// BBG SETTINGS
function get_site_settings_data() {
	$site_setting_package = array(
		'intro_content' => get_field('site_setting_mission_statement','options','false'),
		'intro_link' => get_field('site_setting_mission_statement_link', 'options', 'false')
	);
	return $site_setting_package;
}

// HOMEPAGE OPTIONS
function get_soapbox_data() {
	$soapbox_toggle = get_field('soapbox_toggle', 'option');
	$soapbox_post = get_field('homepage_soapbox_post', 'option');
	$soap_index = '';

	if (!empty($soapbox_post)) {
		if (count($soapbox_post) > 1) {
			// IF MULTIPLE STORIES ARE SELECTED, CHOOSE ONE RANDOMLY
			$index_selector = array_rand($soapbox_post, 1);
			$soap_index = $soapbox_post[$index_selector];
		} else {
			$postIDsUsed[] = $soapbox_post[0] -> ID;
			$soap_index = $soapbox_post[0];
		}
	}

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
			$profile_photo = wp_get_attachment_image_src($profile_photoID);
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
				$profile_name = "";
				break;
			}
			else if ($cat -> slug == "global-media-matters") {
				$isSpeech = true;
				$soap_class = "bbg__voice--guest";
				$profile_photo = get_the_post_thumbnail_url($id);
				$profile_name = '';
			} else if ($cat -> slug == "speech" ||  $cat -> slug == "statement" || $cat -> slug == "media-advisory") {
				$isMediaAdvisory = true;
				$soap_class = "bbg__voice--featured";
			}
		}
	}

	// IF POST HAS PHOTO, USE THAT
	$profile_photo = '';
	if ($soapbox_post) {
		if (wp_get_attachment_image_src($soapbox_post, 'profile_photo')) {
			$profile_photo = wp_get_attachment_image_src($soapbox_post , 'profile_photo');
			$profile_photo = $profile_photo[0];
		}
	}

	// DEFAULT TO SOAPBOX CAPTION
	$profile_name = '';
	$soap_contents_caption = get_field('homepage_soapbox_image_caption', 'option');
	if ($soap_contents_caption != "" && $is_ceo_post) {
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
	$featuredEvent = '';

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

	if ($cornerHeroLabel == '') {
		$cornerHeroLabel = 'This week';
	}

	$corner_hero_label_link = '';
	if (!empty(get_field('corner_hero_link', 'option'))) {
		$corner_hero_label_link = get_field('corner_hero_link', 'option');
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
		$corner_hero_id = $cornerHeroPost -> ID;

		/* permalinks for future posts by default don't return properly. fix that. */
		if ($cornerHeroPost -> post_status == 'future') {
			$my_post = clone $cornerHeroPost;
			$my_post -> post_status = 'published';
			$my_post -> post_name = sanitize_title($my_post -> post_name ? $my_post -> post_name : $my_post -> post_title, $my_post -> ID);
			$cornerHeroPermalink = get_permalink($my_post);
		}

		$corner_hero_package = array(
			'corner-hero-label-link' => $corner_hero_label_link,
			'post-id' => get_post($corner_hero_id)
		);

		return $corner_hero_package;
	}
	else if ($homepage_hero_corner == 'callout' && $featuredCallout) {
		$c_type = 'callout';
		return outputCallout($featuredCallout, $cornerHeroLabel);
	}
	else {
		// EITHER USER SELECTED "quotes" or NO VALID CALLOUT, EVENT, ADVISORY SELECTED 
		$c_type = 'quote';
		$q = getRandomQuote('allEntities', $postIDsUsed);
		if ($q) {
			$postIDsUsed[] = $q['ID'];
			$quote = output_quote($q, 'mention');
		}
		$quote_package = array('toggle' => $toggle, 'type' => $c_type, 'quote_data' => $quote);
		return $quote_package;
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
	$impact_query = new WP_Query($qParams);
	$post_group = $impact_query->posts;

	return $post_group;
}

function get_threats_to_press_posts($used_homepage_posts) {
	$all_threat_posts = array();
	$used_threats_posts = array();
	$threats_to_display = 7;

	// 1. GET SELECTED THREATS TO PRESS POSTS
	$selected_threats_posts = get_field('homepage_threats_to_press_post', 'option');

	if (!empty($selected_threats_posts)) {
		$featured_threat_array = array();
		$featured_threats_args = array(
			'post__in' => $selected_threats_posts,
			'post__not_in' => array($used_homepage_posts),
		);
		$featured_threats_query = new WP_Query($featured_threats_args);
		$featured_threat_array[] = $featured_threats_query->posts;

		// GET EACH ID TO ENSURE THEY ARE NOT USED AGAIN
		foreach ($featured_threat_array as $featured_posts) {
			foreach ($featured_posts as $post_for_id) {
				$used_threats_posts[] = $post_for_id->ID;
			}
		}
		// THE POSTS ARE INSIDE AN ARRAY
		$featured_threat_posts = $featured_threat_array[0];

		$threats_to_display = $threats_to_display - count($featured_threat_posts);
	}

	// 2. GET ALL THREATS TO PRESS POSTS
	$recent_threat_query_params = array(
		'post_type' => array('post'),
		'posts_per_page' => $threats_to_display,
		'cat' => 68,
		'orderby' => 'post_date',
		'order' => 'desc',
		'post__not_in' => $used_homepage_posts
	);
	$recent_threats_query = new WP_Query($recent_threat_query_params);
	$all_recent_threats[] = $recent_threats_query->posts;
	// THE POSTS ARE INSIDE AN ARRAY
	$all_recent_threats = $all_recent_threats[0];

	// COMBINE INTO ONE ARRAY (IF NECESSARY)
	if (!empty($featured_threat_posts)) {
		shuffle($featured_threat_posts);
		foreach ($featured_threat_posts as $cur_sel_post) {
			array_unshift($all_recent_threats, $cur_sel_post);
		}
		$all_threats = $all_recent_threats;
	}
	$all_threats = $all_recent_threats;

	return $all_threats;
}

// FLEXIBLE ROWS
function get_marquee_data() {
	$bg_color_option = get_sub_field('change_marquee_background_color');
	if (!empty($bg_color_option)) {
		$bg_color = get_sub_field('marquee_background_color');
	} else {
		$bg_color = '';
	}

	$marquee_data = array(
		'heading' => get_sub_field('marquee_heading'),
		'link' => get_sub_field('marquee_link'),
		'content' => get_sub_field('marquee_content'),
		'bg_color' => $bg_color
	);
	return $marquee_data;
}

function get_ribbon_data() {
	$ribbon_bg_option = get_sub_field('change_ribbon_background_color');
	if ($ribbon_bg_option == 'yes') {
		$ribbon_bg_color = get_sub_field('ribbon_background_color');
	} else {
		$ribbon_bg_color = '';
	}
	$ribbon_data_package = array(
		'label' => get_sub_field('ribbon_label'),
		'label_link' => get_sub_field('ribbon_label_link'),
		'headline' => get_sub_field('ribbon_headline'),
		'headline_link' => get_sub_field('ribbon_headline_link'),
		'summary' => get_sub_field('ribbon_summary'),
		'image_url' => get_sub_field('ribbon_image'),
		'image_position' => get_sub_field('ribbon_image_position'),
		'bg_color' => $ribbon_bg_color
	);
	return $ribbon_data_package;
}

function get_about_ribbon_data() {
	$ribbon_bg_option = get_sub_field('change_ribbon_background_color');
	if ($ribbon_bg_option == 'yes') {
		$ribbon_bg_color = get_sub_field('about_ribbon_background_color');
	} else {
		$ribbon_bg_color = '';
	}
	$ribbon_data_package = array(
		'label' => get_sub_field('about_ribbon_label'),
		'label_link' => get_sub_field('about_ribbon_label_link'),
		'headline' => get_sub_field('about_ribbon_headline'),
		'headline_link' => get_sub_field('about_ribbon_headline_link'),
		'summary' => get_sub_field('about_ribbon_summary'),
		'image_url' => get_sub_field('about_ribbon_image'),
		'image_position' => get_sub_field('about_ribbon_image_position'),
		'bg_color' => $ribbon_bg_color
	);
	return $ribbon_data_package;
}

function get_umbrella_main_data() {
	$new_umbrella_bg = get_sub_field('change_umbrella_background_color');
	if ($new_umbrella_bg == 'yes') {
		$bg_color = get_sub_field('umbrella_background_color');
	} else {
		$bg_color = '';
	}
	$umbrella_data = array(
		'bg_color' => $bg_color,
		'header' => get_sub_field('umbrella_section_heading'),
		'header_link' => get_sub_field('umbrella_section_heading_link'),
		'intro_text' => get_sub_field('umbrella_section_intro_text'),
		'forced_label' => get_sub_field('umbrella_force_content_labels'),
	);
	return $umbrella_data;
}

function get_umbrella_should_use_card($umbrella_content_type, $title, $thumb_src) {
    if ($umbrella_content_type == 'umbrella_content_external' || $umbrella_content_type == 'umbrella_content_internal') {
        if (!empty($title) && !empty($thumb_src)) {
            return true;
        }
    }
    return false;
}

function get_grid_class($umbrella_column_grouping, $content_counter, $should_use_card) {
    if ($should_use_card) {
        if ($content_counter == 1 || $content_counter == 2) {
            $height = 'normal';
        } else {
            $height = 'small';
        }

        $grid_class = 'cards--size-1-' . $content_counter . '-' . $height . '-small';
    } else {
        switch($umbrella_column_grouping) {
            case 'default':
                if (isOdd($content_counter)) {
                    $grid_class = 'bbg-grid--1-2-3';
                } else {
                    $grid_class = 'bbg-grid--1-2-2';
                }
                break;

            case 'four':
                $grid_class = 'grid-four';
                break;

            case 'five':
                $grid_class = 'grid-five';
                break;
        }
    }

    return $grid_class;
}

function get_umbrella_content_data($umbrella_content_type, $umbrella_column_grouping, $content_counter) {

	$force_content_labels = '';
	$file_ext = '';
	$file_size = '';
	$layout = '';
	$law_name = '';

	if ($umbrella_content_type == 'umbrella_content_internal') {
		$column_title = get_sub_field('umbrella_content_internal_column_title');
		$page_object = get_sub_field('umbrella_content_internal_link');
		if (!empty($page_object)) {
			$id = $page_object[0]->ID;
			$link = get_the_permalink($id);
			$secondary_headline = get_post_meta($id, 'headline', true);
			$law_name = get_post_meta($id, 'law_name', true);
		} else {
			$link = '';
		}
		$include_title = get_sub_field('umbrella_content_internal_include_item_title');
		$title_override = get_sub_field('umbrella_content_internal_title');
		$show_featured_image = get_sub_field('umbrella_content_internal_include_featured_image');
		$show_excerpt = get_sub_field('umbrella_content_internal_include_excerpt');
		$show_excerpt = get_sub_field('umbrella_content_internal_include_excerpt');
		$layout = get_sub_field('umbrella_content_internal_layout');

		$title = "";
		if ($include_title) {
			$title_override = get_sub_field('umbrella_content_internal_item_title');
			if ($title_override != "") {
				$title = $title_override;
			} else {
				if ($secondary_headline) {
					$title = $secondary_headline;
				} else {
					$title = $page_object[0]->post_title;
				}
			}
		}

		$thumb_src = "";
		if ($show_featured_image) {
			if (!empty($page_object)) {
				$thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($id) , 'medium-thumb');
				if ($thumb_src) {
					$thumb_src = $thumb_src[0];
				}
			}
		}

		$description = "";
		if ($show_excerpt && !empty($page_object)) {
			$description = my_excerpt($id);
		}
	}
	elseif ($umbrella_content_type == 'umbrella_content_external') {
		$column_title = get_sub_field('umbrella_content_external_column_title');
		$title = get_sub_field('umbrella_content_external_item_title');
		$description = get_sub_field('umbrella_content_external_description');
		$link = get_sub_field('umbrella_content_external_link');
		$thumbnail = get_sub_field('umbrella_content_external_thumbnail');
		$thumbnail_id = $thumbnail['ID'];
		$thumb_src = wp_get_attachment_image_src($thumbnail_id , 'medium-thumb');
		if ($thumb_src) {
			$thumb_src = $thumb_src[0];
		}
	}
	elseif ($umbrella_content_type == 'umbrella_content_file') {
		$column_title = get_sub_field('umbrella_content_file_column_title');
		$file_object = get_sub_field('umbrella_content_file_file');
		$title = get_sub_field('umbrella_content_file_item_title'); // FILENAME
		$description = get_sub_field('umbrella_content_file_description');
		$layout = get_sub_field('umbrella_content_file_layout');

		$thumbnail = get_sub_field('umbrella_content_file_thumbnail');
		$thumbnail_id = $thumbnail['ID'];
		$thumb_src = wp_get_attachment_image_src( $thumbnail_id , 'medium-thumb' );
		if ($thumb_src) {
			$thumb_src = $thumb_src[0];
		}

		$file_id = $file_object['ID'];
		$fileURL = $file_object['url'];
		$link = $fileURL;
		$file = get_attached_file($file_id);
		$file_ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
		$file_size = formatBytes(filesize($file));
	}

    $should_use_card = get_umbrella_should_use_card($umbrella_content_type, $title, $thumb_src);
    $grid_class = get_grid_class($umbrella_column_grouping, $content_counter, $should_use_card);

	// TRY DELETING 'force_content_labels'
	$data_package = array (
		'column_title' => $column_title,
		'item_title' => $title,
		'description' => $description,
		'link' => $link, 
		'thumb_src' => $thumb_src,
		'grid_class' => $grid_class,
		'force_content_labels' => $force_content_labels,
		'column_type' => $umbrella_content_type,
		'layout' => $layout,
		'sub_title' => $law_name,
		'file_ext' => $file_ext,
		'file_size' => $file_size,
        'should_use_card' => $should_use_card,
	);
	return $data_package;
}

// ENTITY FIELDS
function get_entity_data($grid_class) {
	// $grid_class can be ["grid-five" | "entity-side"]
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

function get_journalistic_code_of_ethics_data() {
	$ethics_set = array();
	// REG EXP TO REMOVE DATE, DASHES, EXTENSION FROM FILE NAME
	$ethics_regx = ['/\d+/', '/\- /', '/\-/', '/\_/', '/\.pdf/'];
	if (have_rows('journalistic_code_of_ethics')) {
		while(have_rows('journalistic_code_of_ethics')) {
			the_row();
			if (get_row_layout() == 'journalistic_ethics_file') {
				$file = get_sub_field('ethics_file');
				$file_name = $file['title'];
				foreach ($ethics_regx as $regx) {
					$file_name = preg_replace($regx, ' ', $file_name);
				}
				$ethics_details = array(
					'title' => $file_name,
					'url' => $file['url'],
					'description' => get_sub_field('ethics_file_description')
				);
			}
			if (get_row_layout() == 'journalistic_ethics_link') {
				$ethics_details = array(
					'title' => get_sub_field('ethics_link_title'),
					'url' => get_sub_field('ethics_link'),
					'description' => get_sub_field('ethics_link_description')
				);
			}
			array_push($ethics_set, $ethics_details);
		}
		return $ethics_set;
	}
}

// ABOUT (OFFICE)
function get_office_intro_data() {
	$office_introduction = get_field('office_page_introduction');
	return $office_introduction;
}

function get_office_contact_data() {
	$contact_data = array();
	if (have_rows('office_page_contact')) {
		while (have_rows('office_page_contact')) {
			the_row();
			array_push($contact_data, array(
				'name' => get_sub_field('office_contact_name'),
				'title' => get_sub_field('office_contact_title'),
				'phone' => get_sub_field('office_contact_phone'),
				'email' => get_sub_field('office_contact_email')
			));
		}
	}
	return $contact_data;
}

function get_office_highlights_data() {
	// EXCLUES: PROFILES(id:35)
	if (get_field('include_office_page_highlights') == 'yes') {
		$highlight_tags = get_field('office_page_highlights');

		$used_office_highlights = array();
		$office_highlight_param = array(
			'post_type' => array('post'),
			'posts_per_page' => 3,
			'order-by' => 'date',
			'posts__not_in' => $used_office_highlights,
			'category__not_in' => array(35)
		);

		$office_tag_ids = array();
		foreach($highlight_tags as $term) {
			array_push($office_tag_ids, $term->term_id);
		}

		if (count($highlight_tags)) {
			foreach ($office_tag_ids as $office_post) {
				$office_highlight_param['tag__and'] = $office_post;
			}
		}
		$office_highlights_query = new WP_Query($office_highlight_param);
	} else {
		$office_highlights_query = '';
	}
	return $office_highlights_query;
}

?>
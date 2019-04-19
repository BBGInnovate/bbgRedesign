<?php
/**
 * Media Clips Reference
 * Drop down showing entities for posts that are either written about or cited
 * @var string
 */

// BUILD RETURN DATA FOR PRESS CLIPPINGS FROM QUERY
function request_media_query_data($query_args) {
	if ($query_args->have_posts()) {
		$citing_post_list = array();
		while ($query_args->have_posts()) {
			$query_args->the_post();

			$press_post_id = get_the_ID();
			$press_post_outlet = wp_get_post_terms(get_the_ID(), 'outlet');
			$press_post_date = get_post_meta($press_post_id, 'media_clip_published_on', true);
			$date = new DateTime($press_post_date);
			$press_post_date = $date->format('F d, Y');

			if (!empty($press_post_outlet)) {
				$outlet_name = $press_post_outlet[0]->name;
			} else {
				$outlet_name = '';
			}

			$cited_post_data = array(
				'title' => get_the_title(),
				'outlet_name' => $outlet_name,
				'story_link' => get_post_meta($press_post_id, 'media_clip_story_url', true),
				'date' => $press_post_date,
				'description' => wp_trim_words(get_the_content(), 40),
				'query_var' => $query_args
			);
			array_push($citing_post_list, $cited_post_data);
		}
		wp_reset_postdata();
		return $citing_post_list;
	}
}

// BUILD CATEGORY COLLECTIONS DROPDOWN MENU
function build_media_clips_entity_dropdown($reference = NULL) {
	// KEEP ENTIRE DIV BLOCK IN THIS ARRAY
	$menu_set = array();

	// GET ALL THE NETWORK ENTITIES
	$network_entities = get_page_by_path('networks');
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $network_entities->ID,
		'orderby' => 'meta_value_num',
		'meta_key' => 'entity_year_established',
		'order' => 'ASC'
	);
	$custom_query = new WP_Query($qParams);
	if ($custom_query -> have_posts()) {
		$entity_set = array();
		while ($custom_query -> have_posts())  {
			$custom_query -> the_post();
			$id = get_the_ID();
			$full_name = get_post_meta($id, 'entity_full_name', true);
			if ($full_name != "") {
				$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
				$abbreviation = str_replace("/", "", $abbreviation);
				$link = get_permalink(get_page_by_path("/networks/$abbreviation/"));

				$entity_data = array(
					'abbr' => $abbreviation,
					'link' => $link
				);
				array_push($entity_set, $entity_data);
			}
		}
	}

	// CONTSTRUCT DIV BLOCK
	$entity_dropdown  = '<div class="media-clips-entities-dropdown">';
	$entity_dropdown .= 	'<ul class="unstyled-list">';
	// LOOP THROUGH PARAMETERS
	foreach ($reference as $ref) {
		if ($ref == 'About Networks') {
			$cat_param = 'about-';
		} else if ($ref == 'Citations') {
			$cat_param = 'citation-';
		}
		$entity_dropdown .= 	'<li>';
		$entity_dropdown .= 		'<h6>';
		if ($ref != 'Of Interest') {
			$entity_dropdown .= 		'<a href="javascript:void(0)">' . strtoupper($ref) . ' <i class="fas fa-angle-down"></i></a>';
		} else {
			$entity_dropdown .= 		'<a href="' . add_query_arg('clip-type', 'interest', '/press-clippings-archive/') . '">' . strtoupper($ref) . '</a>';
		}
		$entity_dropdown .= 		'</h6>';
		if ($ref != 'Of Interest') {
			$entity_dropdown .= 	'<ul class="clipping_nest_list">';
			if ($ref == 'About Networks') {
				$entity_dropdown .= 	'<li><a href="' . add_query_arg('clip-type', 'about-usagm', '/press-clippings-archive/') . '">USAGM / BBG</a></li>';
			}
			// LOOP ENTITIES FROM THE QUERY ABOVE
			foreach ($entity_set as $entity_item) {
				$entity_dropdown .= 	'<li><a href="' . add_query_arg('clip-type', $cat_param . $entity_item['abbr'], '/press-clippings-archive/') . '">';
				if ($entity_item['abbr'] == 'rferl') {
					$entity_dropdown .= 	'RFE/RL';
				} else {
					$entity_dropdown .= 	strtoupper($entity_item['abbr']);
				}
				$entity_dropdown .= 	'</a></li>';
			}
			$entity_dropdown .= 	'</ul>';
		}
		$entity_dropdown .= 	'</li>';

		array_push($menu_set, $entity_dropdown);
	}
	$entity_dropdown .= 		'<li><h6><a href="' . get_template_directory_uri() . '/press-clippings-archive">ARCHIVE</a></h6>';
	$entity_dropdown .= 	'</ul>';
	$entity_dropdown .= '</div>';

	wp_reset_postdata();
	return $entity_dropdown;
}

// BUILD THE ARTICLE DIV BLOCKS
function build_press_clipping_article_list($press_clip, $clip_type = NULL) {
	$press_clipping_block  = '<article>';
	$press_clipping_block .= 	'<h4><a href="' . $press_clip['story_link'] . '" target="_blank">' . $press_clip['title'] . '</a></h4>';
	$press_clipping_block .= 	'<p class="paragraph-header">';
	if (!empty($clip_type)) {
		if (is_array($clip_type)) {
			// FROM OUTLET WITH 'ABOUT' OR 'CITATION' CATEGORY
			$press_clipping_block .= 		'<a href="' . add_query_arg(array('outlet-name' => strtolower(str_replace(' ', '-', $press_clip['outlet_name'])), 'clip-type' => $clip_type[0], 'clip-entity' => $clip_type[1]), '/press-clippings-archive/') .'">' . $press_clip['outlet_name'] . '</a> &nbsp;';
		} else {
			// FROM OUTLET WITH 'OF INTEREST' CATEGORY
			$press_clipping_block .= 		'<a href="' . add_query_arg(array('outlet-name' => strtolower(str_replace(' ', '-', $press_clip['outlet_name'])), 'clip-type' => $clip_type), '/press-clippings-archive/') .'">' . $press_clip['outlet_name'] . '</a> &nbsp;';
		}
	} else {
		// FROM OUTLET
		$press_clipping_block .= 		'<a href="' . add_query_arg('outlet-name', strtolower(str_replace(' ', '-', $press_clip['outlet_name'])), '/press-clippings-archive/') . '">' . $press_clip['outlet_name'] . '</a> &nbsp;';
	}
	$press_clipping_block .= 		'<span class="sans">' . $press_clip['date'] . '</span>';
	$press_clipping_block .= 	'</p>';
	$press_clipping_block .= 	'<p>' . $press_clip['description'] . '</p>';
	$press_clipping_block .= '</article>';

	return $press_clipping_block;
}

// ADJUST URL FOR TAG CLOUD LINKS
add_filter('wp_tag_cloud', 'no_follow_tag_cloud_links');
function no_follow_tag_cloud_links($return) {
	// LIVE
	$return = str_replace('<a href="https://www.usagm.gov/outlet/', '<a href="?outlet-name=', $return);
	// DEV
	// $return = str_replace('<a href="http://dev.usagm.com/outlet/', '<a href="?outlet-name=', $return);
	return $return;
}
?>
<?php
// BUILD PARTS
// This file takes custom field data and build parts 
// (headings, links, images, tags, etc).
// Prepares elements for grid

// CONTENTS:
// HOMEPACE OPTIONS
// ABOUT FLEXIBLE ROWS

// HOMEPACE OPTIONS
function build_soapbox_parts($soap_data, $layout) {
	$article_class = $soap_data['article_class'];

	// BUILD PARTS
	// $soap_heading = '<header class="entry-header bbg__article-icons-container">';
	if (!empty($soap_data['post_link'])) {
		$soap_heading = '<h2><a href="' . $soap_data['header_link'] . '">' . $soap_data['header_text'] . '</a></h2>';
	} else if (!empty($soap_data['header_text'])) {
		$soap_heading = '<h2>' . $soap_data['header_text'] . '</h2>';
	}
	// $soap_heading .= '</header>';

	$soap_title .= '<h4>';
	$soap_title .= 	'<a href="' . $soap_data['header_link'] . '">';
	$soap_title .= 		$soap_data['title'];
	$soap_title .= 	'</a>';
	$soap_title .= '</h4>';

	$soap_content .= '<p class="aside">';
	$soap_content .= 	my_excerpt($soap_data['post_id']);
	// $soap_content .= 	' <a href="' . $soap_data['post_link'] . '" class="bbg__read-more">' . $soap_data['read_more'] . ' »</a>';
	$soap_content .= '</p>';

	if (!empty($soap_data['profile_image'])) {
		$soap_image  = '<div><img src="' . $soap_data['profile_image'] . '"></div>';
		if ($soap_data['profile_name'] != "") {
			$soap_image .= '<p class="aside">' . $soap_data['profile_name'] . '</p>';
		}
	}

	// INSERT PART INTO GRID
	// OUTER DIV MUST HAVE CLASS OF 'inner-container' TO BE ABLE TO FIT PARENT
	$soapbox_markup  = '<div class="inner-container soap-corner special-block ' . $article_class . '">';
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
		$corner_hero_header .= 	'<h2>' . $corner_hero_data['label'] . '</h2>';
		$corner_hero_header .= 	'<div class="bbg__article-icon"></div>';
		$corner_hero_header .= '</div>';

		$corner_hero_title  = '<h4>';
		$corner_hero_title .= 	'<a href="' . $corner_hero_data['p_link'] . '" rel="bookmark">"' . $corner_hero_data['title'] . '"</a>';
		$corner_hero_title .= '</h4>';

		$corner_hero_content = '<p>' . $corner_hero_data['excerpt'] . '</p>';

		// INSERT PART INTO GRID
		// OUTER DIV MUST HAVE CLASS OF 'inner-container' TO BE ABLE TO FIT PARENT
		$corner_hero_markup  = '<div class="inner-container soap-corner special-block">';
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

		$impact_header = 	'<h3><a href="' . get_permalink($impact_id) . '">' . $cur_post->post_title . '</a></h3>';
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

		$threat_content  = '<h4><a href="' . get_the_permalink($threat_id) . '">' . $cur_threat->post_title . '</a></h4>';
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

// ABOUT FLEXIBLE ROWS
function build_umbrella_main_parts($umbrella_main_data) {
	if ($umbrella_main_data['header'] != "") {
		$header  = '<h3>' . $umbrella_main_data['header'] . '</h3>';
	}
	if ($umbrella_main_data['intro_text'] != "") {
		$overhead_text  = '<p>' . $umbrella_main_data['intro_text'] . '</p>';
	}
	$umbrella_main_package = array(
		'section_header' => $header, 
		'intro_text' => $overhead_text
	);
	return $umbrella_main_package;
}

function build_office_parts($office_data) {
	$office_header = '<h3>' . $office_data['office_title'] . '</h3>';

	$office_contact  = '<p>';
	$office_contact .= 	$office_data['office_street'];
	$office_contact .= 	$office_data['office_city'] . ', ';
	$office_contact .= 	$office_data['office_state'];
	$office_contact .= 	$office_data['office_zip'];
	$office_contact .= '<br>';
	$office_contact .= 	'Tel: ';
	$office_contact .= 		'<a href="tel:' . $office_data['office_phone'] . '">' . $office_data['office_phone'] . '</a>&nbsp;&nbsp;';
	$office_contact .= 	'Email: ';
	$office_contact .= 		'<a href="mailto:' . $office_data['office_email'] . '">' . $office_data['office_email'] . '</a>';
	$office_contact .= '</p>';

	$office_package = array('header' => $office_header, 'contact' => $office_contact);
	return $office_package;
}

function build_marquee_parts($marquee_data) {
	$marquee_content  = '<p class="red-special">';
	$marquee_content .= 	$marquee_data['content'];
	$marquee_content .= '</p>';

	$marquee_parts_package = array(
		'header' => $header,
		'link' => $link,
		'content' => $marquee_content
	);
	return $marquee_parts_package;
}

function build_umbrella_content_parts($content_data) {
	$content_title = '<h3>' . $content_data['column_title'] . '</h3>';
	$item_title = '<h4><a href="' . $content_data['link'] . '">' . $content_data['item_title'] . '</a></h4>';
	$image = '<img src="' . $content_data['thumb_src'] . '">';
	$description  = '<p class="aside">' . $content_data['description'] . '</p>';
	
	$content_parts = array(
		'grid' => $content_data['grid_class'],
		'content_title' => $content_title,
		'item_title' => $item_title,
		'image' => $image,
		'description' => $description
	);
	return $content_parts;
}

// ENTITY FIELDS
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

			$entity_content  = 	'<h4><a href="' . $link . '">' . $fullName . '</a></h4>';
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

function build_ethics_file_parts($raw_ethics_data) {
	$ethics_package = array();
	$i++;
	foreach($raw_ethics_data as $ethics_data) {
		$anchor_tag  = 	'<a href="' . $ethics_data['url'] . '" target="_blank">';
		$anchor_tag .= 		$ethics_data['title'];
		$anchor_tag .= 	'</a>';

		$description  = '<p class="aside">';
		$description .= 	$ethics_data['description'];
		$description .= '</p>';

		$ethics_markup = $anchor_tag . $description;
		array_push($ethics_package, $ethics_markup);
	}
	return $ethics_package;
}
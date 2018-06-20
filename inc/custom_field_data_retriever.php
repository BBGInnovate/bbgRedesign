<?php
// GET DATA FROM CUSTOM FIELDS
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

function get_marquee_data() {
	// $marquee_heading = get_sub_field('marquee_heading');
	$marquee_link = get_sub_field('marquee_link');
	$marquee_content = get_sub_field('marquee_content');

	$marquee_data = array(
		'heading' => get_sub_field('marquee_heading'), 
		'link' => $marquee_link, 
		'content' => $marquee_content
	);
	return $marquee_data;
}

function assemble_umbrella_marquee($umbrella_parts) {
	$marquee  = '<div class="outer-container">';
	$marquee .= 	'<div class="grid-container">';
	$marquee .= 		'<p class="red-special">' . $umbrella_parts['content'] . '</p>';
	$marquee .= 	'</div>';
	$marquee .= '</div>';
	return $marquee;
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

// BUILD PARTS
function build_umbrella_main_parts($umbrella_main_data) {
	if ($umbrella_main_data['header'] != "") {
		$header  = '<h2>' . $umbrella_main_data['header'] . '</h2>';
	}
	if ($umbrella_main_data['intro_text'] != "") {
		$overhead_text  = '<p class="lead-in">' . $umbrella_main_data['intro_text'] . '</p>';
	}
	$umbrella_main_package = array('main_header' => $header, 'intro_text' => $overhead_text);
	return $umbrella_main_package;
}

function build_umbrella_content_parts($umbrella_content_data, $grid) {
	$post_id = $umbrella_content_data['link'][0]->ID;
	$post_title = $umbrella_content_data['link'][0]->post_title;
	$post_excerpt = $umbrella_content_data['link'][0]->post_excerpt;

	$header  = '<h2>' . $umbrella_content_data['column_title'] . '</h2>';

	// IMAGES TAKING TOO LONG TO LOAD
	// LOAD OTHER CROP SIZE?
	$post_image  = 	'<a href="' . get_the_permalink($post_id) . '" rel="bookmark" tabindex="-1">';
	$post_image .= 		'<div class="umbrella-bg-image" ';
	$post_image .= 			'style="background-image: url(\'' .wp_get_attachment_url(get_post_thumbnail_id($post_id)) . '\')">';
	$post_image .= 		'</div>';
	$post_image .= 	'</a>';

	if (!empty($umbrella_content_data['item_title'])) {
		$title  = '<h4>' . $umbrella_content_data['item_title'] . '</h4>';
	}
	
	if ($umbrella_content_data['include_title']) {
		$header = "";
		$title  = '<h4>' . $post_title . '</h4>';

	}
	if ($umbrella_content_data['include_excerpt']) {
		$excerpt  = '<p>' . $post_excerpt . '</p>';
	}

	// MARKUP
	$umbrella_content_markup  = '<div class=' . $grid . '>';
	if (!empty($header)) {
		$umbrella_content_markup .= 	$header;
	}
	$umbrella_content_markup .= 	$post_image;
	$umbrella_content_markup .= 	$title;
	$umbrella_content_markup .= 	$excerpt;
	$umbrella_content_markup .= '</div>';

	${"content_parts_package" . $i} = array('markup' => $umbrella_content_markup);
	return $content_parts_package;
}

// INSERT PARTS INTO GRID
function assemble_umbrella_content_section($umbrella_main_parts, $umbrella_content_chunks) {
	$umbrella_content_markup  = '<div class="outer-container">';
	$umbrella_content_markup .= 	'<div class="inner-container">';
	if (!empty($umbrella_main_parts['intro_text'])) {
		$umbrella_content_markup .= 	'<div class="grid-container">';
		$umbrella_content_markup .= 		$umbrella_main_parts['main_header'];
		$umbrella_content_markup .= 		$umbrella_main_parts['intro_text'];
		$umbrella_content_markup .= 	'</div>';
	}
	foreach($umbrella_content_chunks as $umbrella_block) {
		$umbrella_content_markup .= 	$umbrella_block['markup'];
	}
	$umbrella_content_markup .= 	'</div>';
	$umbrella_content_markup .= '</div>';

	return $umbrella_content_markup;
}
?>
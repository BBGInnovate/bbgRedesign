<?php
// This file takes custom field data and build parts 
// (headings, links, images, tags, etc).
// Prepares elements for grid

// ----------------
// CONTENTS:
// ----------------
// HOMEPACE OPTIONS
// FLEXIBLE ROWS
// ENTITY FIELDS
// ABOUT (OFFICE)


// HOMEPACE OPTIONS
function build_soapbox_parts($soap_data) {
	$article_class = $soap_data['article_class'];

	// BUILD PARTS
	if (!empty($soap_data['post_link'])) {
		$soap_heading = '<h2 class="section-subheader"><a href="' . $soap_data['header_link'] . '">' . $soap_data['header_text'] . '</a></h2>';
	} else if (!empty($soap_data['header_text'])) {
		$soap_heading = '<h2 class="section-subheader">' . $soap_data['header_text'] . '</h2>';
	}

	$soap_title  = '<h3 class="article-title">';
	$soap_title .= 	'<a href="' . $soap_data['post_link'] . '">';
	$soap_title .= 		$soap_data['title'];
	$soap_title .= 	'</a>';
	$soap_title .= '</h3>';

	$soap_content  = '<p class="sans">';
	$soap_content .= 	my_excerpt($soap_data['post_id']);
	$soap_content .= '</p>';

	$soap_image = '';
	if (!empty($soap_data['profile_image'])) {
		$soap_image  = '<img src="' . $soap_data['profile_image'] . '" alt="Profile image">';
		if ($soap_data['profile_name'] != "") {
			$soap_image .= '<p class="sans">' . $soap_data['profile_name'] . '</p>';
		}
	}

	$soapbox_content = array(
		'class' => $article_class,
		'heading' => $soap_heading,
		'title' => $soap_title,
		'image' => $soap_image,
		'content' => $soap_content
	);

	return $soapbox_content;
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

		$threat_markup  = '<div class="inner-container threat-article">';
		$threat_markup .= 	'<div class="medium-side-content-container">';
		$threat_markup .= 		$threat_image;
		$threat_markup .= 	'</div>';
		$threat_markup .= 	'<div class="medium-main-content-container">';
		$threat_markup .= 		$threat_content;
		$threat_markup .= 	'</div>';
		$threat_markup .= '</div>';

		${"threat_block" . $i} = $threat_markup;
		array_push($threat_markup_set, ${"threat_block" . $i});
		$i++;
	}
	return $threat_markup_set;
}

// FLEXIBLE ROWS
function build_ribbon_parts($ribbon_data) {
	if ($ribbon_data['label_link'] != "") {
		$ribbon_label  = '<h2 class="section-subheader">';
		$ribbon_label .= 	'<a href="'. get_permalink($ribbon_data['label_link']) . '">' . $ribbon_data['label'] . '</a>';
		$ribbon_label .= '</h2>';
	} else {
		$ribbon_label = '<h2 class="section-subheader">' . $ribbon_data['label'] . '</h2>';
	}
	if ($ribbon_data['headline_link'] != "") {
		$ribbon_headline  = '<h3 class="article-title">';
		$ribbon_headline .= 	'<a href="' . get_permalink($ribbon_data['headline_link']) . '">' . $ribbon_data['headline'] . '</a>';
		$ribbon_headline .= '</h3>';
	} else {
		$ribbon_headline = '<h3 class="article-title">' . $ribbon_data['headline'] . '</h3>';
	}
	$ribbon_summary = $ribbon_data['summary'];
	$ribbon_image = '';
	if (!empty($ribbon_data['image_url'])) {
		$ribbon_image = '<div class="ribbon-image" style="background-image: url(' . $ribbon_data['image_url'] . ');"></div>';
	}

	$ribbon_package = array(
		'label' => $ribbon_label,
		'headline' => $ribbon_headline,
		'summary' => $ribbon_summary,
		'image' => $ribbon_image,
		'image_position' => $ribbon_data['image_position'],
		'bg_color' => $ribbon_data['bg_color']
	);
	return $ribbon_package;
}

function build_marquee_parts($marquee_data) {
	$link = $marquee_data['link'];
	if (!empty($marquee_data['heading'])) {
		if (!empty($link)) {
			$header = '<h3><a href="' . $link . '">' . $marquee_data['heading'] . '</a></h3>';
		} else {
			$header = '<h3>' . $marquee_data['heading'] . '</h3>';
		}
	} else {
		$header = '';
	}
	$marquee_content = '<p>' . $marquee_data['content'] . '</p>';
	$marquee_content = do_shortcode($marquee_content);

	$marquee_parts_package = array(
		'header' => $header,
		'link' => $link,
		'content' => $marquee_content,
		'bg_color' => $marquee_data['bg_color']
	);
	return $marquee_parts_package;
}

function build_umbrella_main_parts($umbrella_main_data) {
	$header = '';
	$overhead_text = '';
	if ($umbrella_main_data['header'] != "") {
		if (!empty($umbrella_main_data['intro_text'])) {
			$header  = '<h3 class="section-subheader">' . 	$umbrella_main_data['header'] . '</h3>';
		} else {
			$header  = '<h3 class="section-subheader">' . 	$umbrella_main_data['header'] . '</h3>';
		}
	}
	if ($umbrella_main_data['intro_text'] != "") {
		$overhead_text  = '<p class="lead-in">' . $umbrella_main_data['intro_text'] . '</p>';
	}
	$umbrella_main_package = array(
		'bg_color' => $umbrella_main_data['bg_color'],
		'section_header' => $header, 
		'intro_text' => $overhead_text
	);
	return $umbrella_main_package;
}

function build_umbrella_content_parts($content_data) {
	$item_title = '';
	$link_target = '';
	$image = '';
	$description = '';
	$column_title = '';
	if ($content_data['column_type'] == 'umbrella_content_external' || $content_data['column_type'] == 'umbrella_content_file') {
		$link_target = ' target="_blank"';
	}

	if ($content_data['column_title']) {
		if (!empty($content_data['link'])) {
			$column_title = '<h3 class="section-subheader"><a href="' . $content_data['link'] . '">' . $content_data['column_title'] . '</a></h3>';
		} else {
			$column_title = '<h3 class="section-subheader">' . $content_data['column_title'] . '</h3>';
		}
	}

	if ($content_data['should_use_card'] == true) {
		if ($content_data['item_title']) {
			$item_title  = '<h3>';
			if (!empty($content_data['link'])) {
				$item_title .= 	'<a href="' . $content_data['link'] . '" ' . $link_target . '>' . $content_data['item_title'] . '</a>';
			} else {
				$item_title .= 		$content_data['item_title'];
			}
			$item_title .= '</h3>';
		}

		if ($content_data['thumb_src']) {
			$image .= 	'<a href="' . $content_data['link'] . '" ' . $link_target . '>';
			$image .= 		'<img src="' . $content_data['thumb_src'] . '" alt="Thumbnail image">';
			$image .= 	'</a>';
			$image .= '<div class="cards__backdrop-shadow"></div>';
		}

		if ($content_data['description']) {
			$description  = '<p>' . $content_data['description'] . '</p>';
		}
	} else {
		if ($content_data['item_title']) {
			$item_title  = '<h4 class="article-title">';
			if (!empty($content_data['link'])) {
				$item_title .= 	'<a href="' . $content_data['link'] . '" ' . $link_target . '>' . $content_data['item_title'] . '</a>';
			} else {
				$item_title .= 		$content_data['item_title'];
			}
			if ($content_data['column_type'] == 'umbrella_content_file') {
				$item_title .= ' <p class="sans">(' . $content_data['file_ext'] . ', ' . $content_data['file_size'] . ')</p>';
			}
			$item_title .= '</h4>';
		}

		if ($content_data['thumb_src']) {
			$image  = '<div class="hd_scale">';
			$image .= 	'<a href="' . $content_data['link'] . '" ' . $link_target . '>';
			$image .= 		'<img src="' . $content_data['thumb_src'] . '" alt="Thumbnail image">';
			$image .= 	'</a>';
			$image .= '</div>';
		}

		if ($content_data['description']) {
			$description  = '<p>' . $content_data['description'] . '</p>';
		}
	}
	
	$content_parts = array(
		'grid' => $content_data['grid_class'],
		'column_title' => $column_title,
		'item_title' => $item_title,
		'image' => $image,
		'description' => $description,
		'should_use_card' => $content_data['should_use_card']
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
			$entity_image .= 	'<img src="' . $imgSrc . '" alt="' . $abbreviation . ' logo">';
			$entity_image .= '</a>';

			$entity_title = 	'<h3 class="section-subheader"><a href="' . $link . '">' . $fullName . '</a></h3>';
			$entity_content = 	'<p class="">' . $description . '</p>';
			
			$entity_pieces = array(
				'image' => $entity_image, 
				'title' => $entity_title,
				'content' => $entity_content
			);
			${"entity_block" . $i} = $entity_pieces;

			array_push($entity_set, ${"entity_block" . $i});
			$i++;
		}
	}
	$entity_parts_package = array(
		'class' => $placement_class, 
		'parts' => $entity_set
	);
	assemble_entity_section($entity_parts_package);
}

function build_ethics_file_parts($raw_ethics_data) {
	if (!empty($raw_ethics_data)) {
		$ethics_package = array();
		foreach($raw_ethics_data as $ethics_data) {
			$anchor_tag  = 	'<a href="' . $ethics_data['url'] . '" target="_blank">';
			$anchor_tag .= 		$ethics_data['title'];
			$anchor_tag .= 	'</a>';

			$description  = '<p class="sans">';
			$description .= 	$ethics_data['description'];
			$description .= '</p>';

			$ethics_markup = $anchor_tag . $description;
			array_push($ethics_package, $ethics_markup);
		}
		return $ethics_package;
	}
}

// ABOUT (OFFICE)
function build_office_contact_parts($office_contact_data) {
	$contact_chunks = array();
	foreach ($office_contact_data as $contact_chunk) {
		$office_name = '<p>' . $contact_chunk['name'] . '</p>';
		$office_title = '<p>' . $contact_chunk['title'] . '</p>';
		$office_phone = '<p class="sans"><a href="tel:' . $contact_chunk['phone'] . '">' . $contact_chunk['phone'] . '</a></p>';
		$office_email = '<p class="sans"><a href="tel:' . $contact_chunk['email'] . '">' . $contact_chunk['email'] . '</a></p>';

		array_push($contact_chunks, array(
			'office_name' => $office_name,
			'office_title' => $office_title,
			'office_phone' => $office_phone,
			'office_email' => $office_email
		));
	}
	return $contact_chunks;
}

function build_office_highlights_parts($office_highlights_data) {
	$office_hightlight_post_group = array();

	if (!empty($office_highlights_data)) {
		if ($office_highlights_data -> have_posts()) {
			while ($office_highlights_data -> have_posts()) {
				$office_highlights_data -> the_post();

				$highlight_image = get_the_post_thumbnail();
				$hightlight_title = '<h4 class="article-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h4>';
				$hightlight_meta = '<p class="date-meta">' . get_the_date() . '</p>';
				$hightlight_excerpt = '<p class="sans">' . wp_trim_words(get_the_excerpt(), 50) . '</p>';

				array_push($office_hightlight_post_group, array(
					'image' => $highlight_image,
					'title' => $hightlight_title,
					'meta' => $hightlight_meta,
					'excerpt' => $hightlight_excerpt
				));
			}
		}
	}
	return $office_hightlight_post_group;
}
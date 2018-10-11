<?php
// Insert parts into grid architecture

// ----------------
// CONTENTS:
// ----------------
// HOMEPACE OPTIONS
// ABOUT FLEXIBLE ROWS
// ENTITY FIELDS
// ABOUT (OFFICE)

// HOMEPAGE OPTIONS
function assemble_mentions_full_width($mention_data, $impact_group) {
	$mention_full  = '<div class="custom-grid-container">';
	$mention_full .= 	'<div class="inner-container">';
	$mention_full .= 		'<div class="side-content-container">';
	$mention_full .= 			'<div class="soap-corner-share-grid">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) {
		$mention_full .= 		$data;
	}
	$mention_full .= 			'</div>';
	$mention_full .= 		'</div>';
	$mention_full .= 		'<div class="main-content-container impact-share">';
	// IMPACT STORY (ONLY ONE FOR THIS LAYOUT)
	$mention_full .= 			'<h2>Impact Stories</h2>';
	$mention_full .= 			$impact_group[0];
	$mention_full .= 		'</div>';
	$mention_full .= 	'</div>';
	$mention_full .= '</div>';
	echo $mention_full;
}

function assemble_mentions_share_space($mention_data, $impact_group) {
	$mention_share  = '<div class="custom-grid-container">';
	$mention_share .= 	'<div class="inner-container">';
	$mention_share .= 		'<div class="side-content-container">';
	$mention_share .= 			'<div class="soap-corner-share-grid">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) {
		$mention_share .= 		$data;
	}
	$mention_share .= 			'</div>';
	$mention_share .= 		'</div>';
	$mention_share .= 		'<div class="main-content-container impact-share">';
	// IMPACT STORY (ONLY ONE FOR THIS LAYOUT)
	$mention_share .= 			'<h2>Impact Stories</h2>';
	$mention_share .= 			$impact_group[0];
	$mention_share .= 		'</div>';
	$mention_share .= 	'</div>';
	$mention_share .= '</div>';
	echo $mention_share;
}

function assemble_threats_to_press_ribbon($threat_data) {
	$theat_ribbon  = '<div class="bbg__ribbon threats-ribbon">';
	$theat_ribbon .= 	'<div class="outer-container">';
	$theat_ribbon .= 		'<div class="grid-container">';
	$theat_ribbon .= 			'<h2>Threats to Press</h2>';
	$theat_ribbon .= 		'</div>';
	foreach ($threat_data as $data) {
		$theat_ribbon .= 			$data;
	}
	$theat_ribbon .= 	'</div>';
	$theat_ribbon .= '</div>';
	echo $theat_ribbon;
}

// ABOUT FLEXIBLE ROWS
function assemble_umbrella_main($main) {
	if (!empty($main['section_header']) || !empty($main['intro_text'])) {
		$umbrella_main  = '<div class="inner-container">';
		$umbrella_main .= 	'<div class="grid-container">';
		$umbrella_main .= 		$main['section_header'];
		$umbrella_main .= 		$main['intro_text'];
		$umbrella_main .= 	'</div>';
		$umbrella_main .= '</div>';
		return $umbrella_main;
	}
}

function assemble_marquee_module($umbrella_parts) {
	if (is_page('who-we-are')) {
		$marquee  = '<div class="inner-container red-special">';
		$marquee .= 	$umbrella_parts['content'];
		$marquee .= '</div>';
	} else {
		$marquee  = '<div class="outer-container">';
		$marquee .= 	'<div class="grid-container box-special">';
		$marquee .= 		'<a href="' . $umbrella_parts['link'] . '">';
		$marquee .= 			$umbrella_parts['header'];
		$marquee .= 		'</a>';
		$marquee .= 		$umbrella_parts['content'];
		$marquee .= 	'</div>';
		$marquee .= '</div>';
	}

	return $marquee;
}

function assemble_umbrella_content_section($umbrella_parts) {
	if (!empty($umbrella_parts)) {
		$umbrella_content_block  = '<div class="outer-container">';
		$umbrella_content_block .= 	'<div class="grid-container">';
		foreach($umbrella_parts as $umbrella_chunk) {
			$umbrella_content_block .= '<div class="' . $umbrella_chunk['grid'] . '">';
			$umbrella_content_block .= 	$umbrella_chunk['column_title'];
			$umbrella_content_block .= 	$umbrella_chunk['image'];
			$umbrella_content_block .= 	$umbrella_chunk['item_title'];
			$umbrella_content_block .= 	$umbrella_chunk['description'];
			$umbrella_content_block .= '</div>';
		}
		$umbrella_content_block .= 	'</div>';
		$umbrella_content_block .= '</div>';
		return $umbrella_content_block;
	}
}

function assemble_ribbon_module($ribbon_parts) {
	if (!empty($ribbon_parts)) {
		$ribbon_box  = '<div class="bbg__ribbon inner-ribbon">';
		$ribbon_box .= 	'<div class="outer-container">';
		if (!empty($ribbon_parts['image'])) {
			$ribbon_box .= 		'<div class="side-content-container">';
			$ribbon_box .= 			$ribbon_parts['image'];
			$ribbon_box .= 		'</div>';
			$ribbon_box .= 		'<div class="main-content-container">';
		} else {
			$ribbon_box .= 		'<div class="grid-container">';
		}
		$ribbon_box .= 			$ribbon_parts['label'];
		$ribbon_box .= 			$ribbon_parts['headline'];
		$ribbon_box .= 			$ribbon_parts['summary'];
		$ribbon_box .= 		'</div>';
		$ribbon_box .= 	'</div>';
		$ribbon_box .= '</div>';
		return $ribbon_box;
	}
}

// ENTITY
function assemble_entity_section($entity_data) {
	$entity_class = $entity_data['class'];
	$entity_chuncks = $entity_data['parts'];

	$entity_markup  = 	'<section class="outer-container" id="entities">';
	$entity_markup .= 		'<div class="grid-container">';
	$entity_markup .= 			'<h1 class="header-outliner">Entities</h1>';
	$entity_markup .= 			'<h2><a href="' . get_permalink(get_page_by_path('networks')) . '">Our Networks</a></h2>';
	$entity_markup .= 			'<p class="lead-in">Every week, more than ' . do_shortcode('[audience]') . ' listeners, viewers and internet users around the world turn on, tune in and log onto U.S. international broadcasting programs. The day-to-day broadcasting activities are carried out by the individual USAGM international broadcasters.</p>';
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
		$entity_markup .= 				$entity_part['title'];
		$entity_markup .= 			'</div>';
		$entity_markup .= 			'<div class="entity-desc hide-small">';
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

// ABOUT (OFFICE)
function assemble_office_contact_module($office_contact_parts) {
	$office_contact_block  = '<article class="office-side">';
	foreach ($office_contact_parts as $contact) {
		$office_contact_block .= '<div class="office-contact">';
		$office_contact_block .= 	$contact['office_name'];
		$office_contact_block .= 	$contact['office_title'];
		$office_contact_block .= 	$contact['office_phone'];
		$office_contact_block .= 	$contact['office_email'];
		$office_contact_block .= '</div>';
	}
	$office_contact_block .= '</article>';
	return $office_contact_block;
}

function assemble_office_highlights_module($office_highlights_parts) {
	$highlights_module = "";
	if (!empty($office_highlights_parts)) {
		$highlights_module  = '<div class="office-highlights">';
		$highlights_module .= 	'<h5>Recent Highlights</h5>';
		$counter = 0;
		foreach ($office_highlights_parts as $office_highlight) {
			if ($counter == 0) {
				$highlights_module .= '<article>';
				$highlights_module .= 	'<div class="nest-container">';
				$highlights_module .= 		'<div class="inner-container">';
				$highlights_module .= 			'<div class="side-content-container">';
				$highlights_module .= 				$office_highlight['image'];
				$highlights_module .= 			'</div>';
				$highlights_module .= 			'<div class="main-content-container">';
				$highlights_module .= 				$office_highlight['title'];
				$highlights_module .= 				$office_highlight['meta'];
				$highlights_module .= 				$office_highlight['excerpt'];
				$highlights_module .= 			'</div>';
				$highlights_module .= 		'</div>';
				$highlights_module .= 	'</div>';
				$highlights_module .= '</article>';
			} else {
				$highlights_module .= '<article>';
				$highlights_module .= 	$office_highlight['title'];
				$highlights_module .= 	$office_highlight['meta'];
				$highlights_module .= 	$office_highlight['excerpt'];
				$highlights_module .= '</article>';
			}
			$counter++;
		}
		$highlights_module .= '</div>';
	}
	return $highlights_module;
}
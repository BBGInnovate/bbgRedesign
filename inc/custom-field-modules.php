<?php
// INSERT PARTS INTO MODULE
// Insert parts into div architecture
// * INCLUDE A NOTE AS TO WHERE THE STYLES ARE LOCATED (AFTER PROPERLY PLACED)

// GLOBAL
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

// HOMEPAGE OPTIONS
function assemble_mentions_full_width($mention_data, $impact_group) {
	$mention_full  = '<div class="inner-container">';
	$mention_full .= 	'<div class="grid-container soap-corner-full">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $mention) { 
		$mention_full .= 	$mention;
	}
	$mention_full .= 	'</div>';
	$mention_full .= '</div>';

	$mention_full .= '<div class="grid-container">';
	$mention_full .= 	'<h2>Impact Stories</h2>';
	$mention_full .= '</div>';
	$mention_full .= '<div class="inner-container ">';
	// IMPACT STORIES
	foreach($impact_group as $impact) {
		$mention_full .= '<div class="split-grid">';
		$mention_full .= 	$impact;
		$mention_full .= '</div>';
	}
	$mention_full .= '</div>';
	echo $mention_full;
}

function assemble_mentions_share_space($mention_data, $impact_group) {
	$mention_share  = '<div class="inner-container">';
	$mention_share .= 	'<div class="soap-corner-share-grid">';
	// SOAPBOX AND/OR CORNER HERO
	foreach($mention_data as $data) { 
		$mention_share .= 				$data;
	}
	$mention_share .= 	'</div>';
	$mention_share .= 	'<div class="impacts-share">';
	$mention_share .= 		'<h2>Impact Stories</h2>';
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

// ABOUT FLEXIBLE ROWS
function assemble_office_module($office_parts) {
	$office_module  = 	'<div class="inner-container">';
	// $office_module .= 		$office_parts['header'];
	$office_module .= 		$office_parts['contact'];
	$office_module .= 	'</div>';

	return $office_module;
}

function assemble_umbrella_marquee($umbrella_parts) {
	$marquee  = '<div class="outer-container">';
	$marquee .= 	'<div class="grid-container">';
	$marquee .= 		$umbrella_parts['content'];
	$marquee .= 	'</div>';
	$marquee .= '</div>';

	return $marquee;
}

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
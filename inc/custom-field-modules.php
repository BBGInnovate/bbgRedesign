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
function assemble_threats_to_press_ribbon($threat_data) {
	$theat_ribbon  = '<div class="bbg__ribbon threats-ribbon">';
	$theat_ribbon .= 	'<div class="outer-container">';
	$theat_ribbon .= 		'<div class="grid-container">';
	$theat_ribbon .= 			'<h2 class="section-header">Threats to Press</h2>';
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
		$umbrella_main  = '<div class="inner-container"';
		if (!empty($main['bg_color'])) {
			$umbrella_main .= ' style="background-color:' . $main['bg_color'] . '; padding-top: 15px;"';
		}
		$umbrella_main .= '>';
		$umbrella_main .= 	'<div class="grid-container">';
		$umbrella_main .= 		$main['section_header'];
		$umbrella_main .= 		$main['intro_text'];
		$umbrella_main .= 	'</div>';
		$umbrella_main = do_shortcode($umbrella_main);
		// $umbrella_main .= '</div>';
		return $umbrella_main;
	}
}

function assemble_marquee_module($umbrella_parts) {
	$bg_color = $umbrella_parts['bg_color'];
	
	if (is_page('who-we-are')) {
		$marquee  = '<div class="inner-container red-special">';
		$marquee .= 	$umbrella_parts['content'];
		$marquee .= '</div>';
	} else {
		$marquee  = '<div class="outer-container marquee">';
		$marquee .= 	'<div class="grid-container box-special"';
		if (!empty($bg_color)) {
			$marquee .= ' style="background-color: ' . $bg_color .'"';
		}
		$marquee .= 	'>';
		$marquee .= 		$umbrella_parts['header'];
		$marquee .= 		$umbrella_parts['content'];
		$marquee .= 	'</div>';
		$marquee .= '</div>';
	}
	$marquee = do_shortcode($marquee);
	return $marquee;
}

function assemble_umbrella_content_section($umbrella_parts, $special_grouping) {
	if (!empty($umbrella_parts)) {
		$umbrella_content_block  = '';
		if (!$special_grouping) {
			$umbrella_content_block .= 	'<div class="grid-container">';
		}
		foreach($umbrella_parts as $umbrella_chunk) {
			if ($umbrella_chunk['should_use_card']) {
				$umbrella_content_block .= assemble_umbrella_content_section_card($umbrella_chunk);
			} else {
				$umbrella_content_block .= assemble_umbrella_content_section_umbrella($umbrella_chunk);
			}
			$umbrella_content_block = do_shortcode($umbrella_content_block);
		}
		if (!$special_grouping) {
			$umbrella_content_block .= 	'</div>';
		}
		$umbrella_content_block .= '</div>'; // THIS SERVES AS DIV ENDING FOR .inner-container TO UMBRELLA MAIN CONTENT OPENING IN assemble_umbrella_main() ABOVE
		return $umbrella_content_block;
	}
}

function assemble_umbrella_content_section_umbrella($umbrella_chunk) {
	$umbrella_content_block = '';

	$umbrella_content_block .= '<div class="' . $umbrella_chunk['grid'] . '" style="float: left; padding: 16px;">';
	if (!empty($umbrella_chunk['column_title'])) {
		$umbrella_content_block .= 	'<div>';
		$umbrella_content_block .= 		$umbrella_chunk['column_title'];
		$umbrella_content_block .= 	'</div>';
	}
	if (!empty($umbrella_chunk['image'])) {
		$umbrella_content_block .= 	'<div>';
		$umbrella_content_block .= 		$umbrella_chunk['image'];
		$umbrella_content_block .= 	'</div>';
	}
	$umbrella_content_block .= 	'<div class="umbrella-content-group">';
	$umbrella_content_block .= 		$umbrella_chunk['item_title'];
	$umbrella_content_block .= 		$umbrella_chunk['description'];
	$umbrella_content_block .= 	'</div>';
	$umbrella_content_block .= '</div>';

	return $umbrella_content_block;
}

function assemble_umbrella_content_section_card($umbrella_chunk) {
	$umbrella_content_block = '';
	$umbrella_content_block .= '<div class="cards cards--layout-general ' . $umbrella_chunk['grid'] . ' margin-top-small">';
	$umbrella_content_block .= '    <div class="cards__fixed">';
	$umbrella_content_block .= '    	<div class="cards__wrapper">';
	$umbrella_content_block .= '    		<div class="cards__backdrop">';
	$umbrella_content_block .= '                ' . $umbrella_chunk['image'];
	$umbrella_content_block .= '    	 	</div>';
	$umbrella_content_block .= '    		<div class="cards__footer">';
	$umbrella_content_block .= '    			' . $umbrella_chunk['item_title'];
	$umbrella_content_block .= '    		</div>';
	$umbrella_content_block .= '    	</div>';
	$umbrella_content_block .= '    </div>';
	$umbrella_content_block .= '    <div class="cards__flexible">';
	$umbrella_content_block .= '        <div class="cards__excerpt">';
	$umbrella_content_block .= '            ' . $umbrella_chunk['description'];
	$umbrella_content_block .= '        </div>';
	$umbrella_content_block .= '    </div>';
	$umbrella_content_block .= '</div>';

	return $umbrella_content_block;
}

function assemble_ribbon_module($ribbon_parts) {
	$ribbon_box = '';
	$ribbon_pos = $ribbon_parts['image_position'];
	$bg_color = $ribbon_parts['bg_color'];

	$ribbon_box  = '<article class="bbg__ribbon inner-ribbon"';
	if (!empty($bg_color)) {
		$ribbon_box .= ' style="background-color: ' . $bg_color . '"';
	}
	$ribbon_box .= '>';
	$ribbon_box .= 	'<div class="outer-container">';
	if (!empty($ribbon_parts) && $ribbon_pos == 'left') {
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
	} else if (!empty($ribbon_parts) && $ribbon_pos == 'right') {
		$ribbon_box .= 		'<div class="main-content-container">';
		$ribbon_box .= 			$ribbon_parts['label'];
		$ribbon_box .= 			$ribbon_parts['headline'];
		$ribbon_box .= 			$ribbon_parts['summary'];
		$ribbon_box .= 		'</div>';
		$ribbon_box .= 		'<div class="side-content-container">';
		$ribbon_box .= 			$ribbon_parts['image'];
		$ribbon_box .= 		'</div>';
	}
	$ribbon_box .= 	'</div>';
	$ribbon_box .= '</article>';

	$ribbon_box = do_shortcode($ribbon_box);
	return $ribbon_box;
}

// ENTITY
function assemble_entity_section($entity_data) {
	$entity_class = $entity_data['class'];
	$entity_chuncks = $entity_data['parts'];

	$entity_markup  = 	'<section class="outer-container" id="entities">';
	$entity_markup .= 		'<div class="grid-container">';
	$entity_markup .= 			'<h1 class="header-outliner">Entities</h1>';
	$entity_markup .= 			'<h2 class="section-subheader"><a href="' . get_permalink(get_page_by_path('networks')) . '">Our Networks</a></h2>';
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
	$office_contact_block .= 	'<h3 class="header-outliner">Office Director</h3>';
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
		$highlights_module .= 	'<h3 class="section-subheader">Recent Highlights</h3>';
		$counter = 0;
		foreach ($office_highlights_parts as $office_highlight) {
			if ($counter == 0) {
				$highlights_module .= '<article class="article-teaser">';
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
				$highlights_module .= '<article class="article-teaser">';
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
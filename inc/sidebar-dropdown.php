<?php
function build_sidebar_dropdown($header, $list) {
	$dropdown_list  = '<aside class="speaker-list">';
	$dropdown_list .= 	'<h3 class="sidebar-section-header">' . $header . '</h3>';
	$dropdown_list .= 	'<ul class="unstyled-list meeting-speakers">';
	foreach($list as $list_data) {
		if (!empty($list_data['bio'])) {
			$dropdown_list .= '<li class="usa-accordion speaker-accordion">';
			$dropdown_list .= 	'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $list_data['entry'] . '">';
			$dropdown_list .= 		'<p class="speaker-name sidebar-paragraph-header">' . $list_data['name'] . '</p>';
			if (!empty($list_data['title'])) {
				$dropdown_list .= 	'<p class="speaker-title sans">' . $list_data['title'] . '</p>';
			}
			$dropdown_list .= 		' <i class="fas fa-plus"></i>';
			$dropdown_list .= 	'</button>';
			$dropdown_list .= 	'<div id="collapsible-faq-' . $list_data['entry'] . '" aria-hidden="true" class="usa-accordion-content">';
			$dropdown_list .= 		'<p class="sans speaker-bio">';
			$dropdown_list .= 			$list_data['bio'];
			$dropdown_list .= 		'</p>';
			if (!empty($list_data['link'])) {
				$dropdown_list .= 		'<p class="sans">';
				$dropdown_list .= 			'<a href="' . $list_data['link'] . '" target="_blank">View Profile</a>';
				$dropdown_list .= 		'</p>';
			}
			$dropdown_list .= 	'</div>';
			$dropdown_list .= '</li>';
		} else {
			$dropdown_list .= '<li>';
			$dropdown_list .= 	'<p class="speaker-name sidebar-paragraph-header">' . $list_data['name'] . '</p>';
			if (!empty($list_data['title'])) {
				$dropdown_list .= 	'<p class="speaker-title sans">' . $list_data['title'] . '</p>';
			}
			$dropdown_list .= '</li>';
		}
	}
	$dropdown_list .= 	'</ul>';
	$dropdown_list .= '</aside>';
	return $dropdown_list;
}
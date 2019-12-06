<?php
// GET THE INFORMATION FROM POSTS THAT ONLY HAVE 'AWARD' CATEGORY
function get_standalone_award($award_post_id, $archive = NULL) {
	$all_awards = array();
	if (!empty(get_field('standardpost_award_organization', $award_post_id, true))) {
		$award_organization = get_field('standardpost_award_organization', $award_post_id, true);
		$award_organization = $award_organization->name;
		$award_logo = get_field('standardpost_award_logo', $award_post_id, true);
		$award_logo = $award_logo['url'];
	} else {
		$award_organization = '';
		$award_organization = '';
		$award_logo = '';
		$award_logo = '';
	}

	$award_info = array(
		'award_title' => get_field('standardpost_award_title', $award_post_id, true),
		'award_year' => get_field('standardpost_award_year', $award_post_id, true),
		'award_recipient' => get_field('standardpost_award_recipient', $award_post_id, true),
		'award_winning_work' => get_field('standardpost_award_winning_work', $award_post_id, true),
		'award_winner' => get_field('standardpost_award_winner', $award_post_id, true),
		'award_link' => get_field('standardpost_award_link', $award_post_id, true),
		'award_organization' => $award_organization,
		'award_logo' => $award_logo,
		'award_org_url' => get_field('standardpost_award_org_url', $award_post_id, true),
		'award_description' => get_field('standardpost_award_description', $award_post_id, true),
		'award_page_url' => get_the_permalink($award_post_id)
	);

	$all_awards[] = $award_info;
	$standalone_award_package = $all_awards;

	if ($archive) {
		return $standalone_award_package;
	} else {
		return build_award_dropdown_list($standalone_award_package);
	}
}

// GET THE INFORMATION FROM POSTS THAT HAVE BOT 'AWARD' AND 'PRESS RELEASE' CATEGORYS
// CAN BE MULTIPLE, SO THIS FUNCTION HAS SEARCHS SUB_FIELDS
function get_press_release_award($award_post_id, $archive = NULL) {
	$single_award_markup = '';
	$all_press_release_awards = array();

	if (have_rows('award_repeater', $award_post_id)) {
		while (have_rows('award_repeater', $award_post_id)) {
			the_row();
			$award_organization = get_sub_field('award_organization');
			$award_organization = $award_organization->name;
			$award_logo = get_sub_field('award_logo');
			$award_logo = $award_logo['url'];

			$award_info = array(
				'award_title' => get_sub_field('award_title'),
				'award_year' => get_sub_field('award_year'),
				'award_recipient' => get_sub_field('award_entity_recipient'),
				'award_winning_work' => get_sub_field('award_winning_work'),
				'award_winning_work_description' => get_sub_field('award_winning_work_description'),
				'award_winner' => get_sub_field('award_winner'),
				'award_link' => get_sub_field('award_link'),
				'award_organization' => $award_organization,
				'award_logo' => $award_logo,
				'award_org_url' => get_sub_field('award_organization_link'),
				'award_description' => get_sub_field('award_description'),
				'press_release_url' => get_the_permalink($award_post_id)
			);
			$all_press_release_awards[] = $award_info;
		}

		if ($archive) {
			return $all_press_release_awards;
		} else {
			return build_award_dropdown_list($all_press_release_awards);
		}
	}
}


// AWARD FOR DROPDOWNS LIST, PASS TO BUILD DROPDOWN COMPONENENT 
function build_award_dropdown_list($award_package) {
	$award_list = array();

	$award_markup  = '<aside class="award-dropdown">';
	$award_markup .= 	'<h3 class="sidebar-section-header">Award Information</h3>';
	$award_markup .= 	'<div class="about-the-award-dropdown">';
	$award_markup .= 		'<ul class="unstyled-list">';
	foreach($award_package as $cur_award_data) {
		$award_markup .= 		'<li class="dropdown-item">';
		$award_markup .= 			'<div class="nest-container">';
		$award_markup .= 				'<div class="inner-container">';
		$award_markup .= 					'<div class="award-image">';
		if ($cur_award_data['award_logo']) {
			$award_markup .= 					'<img class="award-logo" src="' . $cur_award_data['award_logo'] . '">';
		} else {
			$award_markup .= 					'<img class="award-logo" src="http://dev.usagm.com/wp-content/uploads/2019/08/blank_award.jpg">';
		}
		$award_markup .= 					'</div>';
		$award_markup .= 					'<div class="award-title">';
		$award_markup .= 						'<h3 class="sidebar-article-title">' . $cur_award_data['award_title'] . ' (' . $cur_award_data['award_year'] . ')</h4>';
		$award_markup .= 					'</div>';
		$award_markup .= 					'<div class="award-toggle">';
		$award_markup .= 						'<i class="fas fa-angle-down"></i>';
		$award_markup .= 					'</div>';
		$award_markup .= 				'</div>';
		$award_markup .= 			'</div>';
		$award_markup .= 			'<ul>';
		$award_markup .= 				'<div class="about-the-award">';
		$award_markup .= 					'<p><span class="detail">Project:</span> ';
		$award_markup .= 						'<a href="' . $cur_award_data['award_link'] . '">' . $cur_award_data['award_winning_work'] . '</a>';
		$award_markup .= 					'</p>';
		$award_markup .= 					'<p><span class="detail">Winner:</span> ' . $cur_award_data['award_winner'] . '</p>';
		$award_markup .= 					'<p><span class="detail">Network:</span> ' . $cur_award_data['award_recipient'] . '</p>';
		$award_markup .= 					'<p><span class="detail">Presented By: </span>';
		$award_markup .= 						'<a href="' . $cur_award_data['award_org_url'] . ' target="_blank">' . $cur_award_data['award_organization'] . '</a>';
		$award_markup .= 					'</p>';
		$award_markup .= 					'<p>' . $cur_award_data['award_description'] . '</p>';
		$award_markup .= 				'</div>';
		$award_markup .= 			'</ul>';
		$award_markup .= 		'</li>';

		$award_list[] = $award_markup;
	}
	$award_markup .= 		'</ul>';
	$award_markup .= 	'</div>';
	$award_markup .= '</aside>';

	return $award_markup;
}


// SET UP AWARDS CONTAINER
function build_award_archive_blocks($awards, $filter) {
	$filter = strtoupper($filter);
	if ($filter == 'RFERL') {
		$filter = 'RFE/RL';
	}

	$award_markup_string = '';
	$filtered_award_string = '';

	$block_container  = '<div class="nest-container">';
	$block_container .= 	'<div class="inner-container">';
	foreach ($awards as $award_in) {
		if (isset($award_in[0]) && is_array($award_in[0])) {
			foreach ($award_in as $award_array) {
				if ($award_array['award_recipient'] == $filter) {
					$filtered_award = make_award_blocks($award_array);
					$filtered_award_string .= $filtered_award;
				} else {
					$block_string = make_award_blocks($award_array);
					$award_markup_string .= $block_string;
				}
			}
		} else {
			if ($award_in['award_recipient'] == $filter) {
				$filtered_award = make_award_blocks($award_in);
				$filtered_award_string .= $filtered_award;
			} else {
				$block_string = make_award_blocks($award_in);
				$award_markup_string .= $block_string;
			}
		}
	}
	if (!empty($filter)) {
		$block_container .= 		$filtered_award_string;
	} else {
		$block_container .= 		$award_markup_string;
	}
	$block_container .= 	'</div>';
	$block_container .= '</div>';

	return $block_container;
}
// SET UP AWARDS ARCHIVE BLOCKS TO FILL CONTAINER
function make_award_blocks($award_data) {
	$network = $award_data['award_recipient'];
	$upload_dir = wp_upload_dir();

	$single_award  = 	'<div class="grid-third award-archive-block">';
	$single_award .= 		'<div class="award-image">';
	if ($award_data['award_logo']) {
		$single_award .= 		'<img src="' . $award_data['award_logo'] . '">';
	} else {
		$single_award .= 		'<img src="http://dev.usagm.com/wp-content/uploads/2019/08/blank_award.jpg">';
	}
	$single_award .= 		'</div>';
	$single_award .= 		'<div class="award-title">';
	$single_award .=  			'<h3 class="article-title">';
	if (!empty($award_data['press_release_url'])) {
		$single_award .= 			'<a href="' . $award_data['press_release_url'] . '">' . $award_data['award_title'] . '</a>';
	} else {
		$single_award .= 			'<a href="' . $award_data['award_page_url'] . '">' . $award_data['award_title'] . '</a>';
	}
	$single_award .= 			'</h3>';
	$single_award .= 		'</div>';

	$single_award .= 		'<div class="nest-container">';
	$single_award .= 			'<div class="inner-container">';
	$single_award .= 				'<div class="about-the-award-archive">';
	$single_award .= 					'<p><span class="detail">Network: ' . $network . '</span>';
	$single_award .= 					'</p>';
	$single_award .= 					'<p><span class="detail">Project: </span><a href="' . $award_data['award_link'] . '">' .$award_data['award_winning_work'] . ' (' . $award_data['award_year'] . ')</a></p>';
	$single_award .= 					'<p><span class="detail">Winner: </span>' . $award_data['award_winner'] . '</p>';
	$single_award .= 					'<p><span class="detail">Presented by: </span> <a href="' . $award_data['award_org_url'] . '" target="_blank">' . $award_data['award_organization'] . '</a></p>';
	$single_award .= 				'</div>';
	$single_award .= 			'</div>';
	$single_award .= 		'</div>';
	$single_award .= 	'</div>';

	return $single_award;
}
?>
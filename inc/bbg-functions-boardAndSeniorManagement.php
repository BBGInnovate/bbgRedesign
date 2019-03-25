<?php
function outputBoardMembers($showActive) {
	// $showActive SHOULD BE A 0 or 1 PASSED IN 'active' IN THE SHORTCODE
	$board_page = get_page_by_title('The Board');
	$board_page_id = $board_page -> ID;

	$former_css = "";
	$former_governors_link = "";

	if ($showActive == 0) {
		$former_css = " bbg__former-member";
	}

	$board_page_query = array(
		'post_type' => array('page'),
		'post_status' => array('publish'),
		'post_parent' => $board_page_id,
		'order' => 'ASC',
		'orderby' => 'meta_value',
		'meta_key' => 'last_name',
		'posts_per_page' => 100
	);
	$board_query = new WP_Query($board_page_query);

	// DEFAULT ADDS SPACE ABOVE HEADER IF NO NO IMAGE SET
	$featuredImageClass = " bbg__article--no-featured-image";

	$board_member_markup = "";
	$chairperson_markup = "";
	$secretary_markup = "";
	$under_secretary_markup = "";

	while ($board_query -> have_posts())  {
		$board_query -> the_post();
		$board_member_id = get_the_ID();
		$active = get_post_meta($board_member_id, 'active', true);
		if (!isset($active) || $active == "" || !$active) {
			$active = 0;
		}
		if ((get_the_title() != 'Special Committees') && ($showActive == $active)) {
			$is_chairperson = get_post_meta($board_member_id, 'chairperson', true);
			$is_secretary = get_post_meta($board_member_id, 'secretary_of_state', true);
			$is_under_secretary = get_post_meta($board_member_id, 'under_secretary_of_state', true);
			$is_acting = get_post_meta($board_member_id, 'acting', true);

			$email = get_post_meta($board_member_id, 'email', true);
			$phone = get_post_meta($board_member_id, 'phone', true);
			$twitter_profile_handle = get_post_meta($board_member_id, 'twitter_handle', true);
			$board_photo_id = get_post_meta($board_member_id, 'profile_photo', true);
			$board_member_photo = "";

			if ($board_photo_id) {
				$board_member_photo = wp_get_attachment_image_src($board_photo_id , 'mugshot');
				$board_member_photo = $board_member_photo[0];
			}
			$board_member_name = get_the_title();

			$occupation = '<span class="bbg__profile-excerpt__occupation">';

			if ($is_acting && !$is_under_secretary && !$is_secretary) {
				$occupation .= 'Acting ';
			} else if ($is_acting && $is_under_secretary) {
				$occupation .= 'Acting ';
				$occupation .= get_field('occupation', $board_member_id);
			}

			if ($is_chairperson) {
				$occupation .= 'Chairman of the Board';
			} else if ($is_secretary) {
				$occupation .= 'Ex officio board member';
			}
			$occupation .= '</span>';

			$member_block  = '<div class="grid-half profile-clears"';
			if ($is_under_secretary) {
				// $member_block .= ' style="clear: both; border-top: 1px solid #f1f1f1; padding-top: 1.5em"';
			}
			$member_block .= '>';

			if ($board_member_photo != '') {
				$member_photo  = '<a href="' . get_the_permalink() . '">';
				$member_photo .= 	'<img src="';
				$member_photo .= 		$board_member_photo;
				$member_photo .= 		'" class="bbg__profile-excerpt__photo';
				$member_photo .= 		$former_css;
				$member_photo .= 		'" alt="Photo of BBG Governor ';
				$member_photo .= 		get_the_title();
				$member_photo .= 		'"';
				if ($is_under_secretary) {
					// $member_photo .= ' style="width: 18%;"';
				}
				$member_photo .= 	'>';
				$member_photo .= '</a>';

				$member_block .= $member_photo;
			}

			if (!$is_under_secretary) {
				$member_name  = '<h4 class="bbg__profile-excerpt__name">';
				$member_name .= 	'<a href="' . get_the_permalink() . '">';
				if ($is_secretary && $is_acting) {
					$member_name .= 	'Acting ';
				}
				$member_name .= 		$board_member_name;
				$member_name .= 	'</a>';
				$member_name .= '</h4>';

				$member_block .= $member_name;
			}

			if (!$is_under_secretary) {
				$member_position = '<p>' . $occupation . get_the_excerpt() . '</p>';

				$member_block .= $member_position;
			} else {
				$memeber_content  = '<h4 class="bbg__profile-excerpt__name">';
				$memeber_content .= 	'<a href="' . get_the_permalink() . '">' . $board_member_name . '</a>';
				$memeber_content .= '</h4>';
				$memeber_content .= '<p style="margin-top: 0;">' . get_the_excerpt() . '</p>';

				$member_block .= $memeber_content;
			}
			$member_block .= '</div>';

			if ($is_chairperson) {
				$chairperson_markup = $member_block;
			} else if ($is_secretary) {
				$secretary_markup = $member_block;
			} else if ($is_under_secretary) {
				$under_secretary_markup = $member_block;
			} else {
				$board_member_markup .= $member_block;
			}
		}
	}

	$board_members_markup  = '<div class="nest-container">';
	$board_members_markup .= 	'<div class="inner-container">';
	$board_members_markup .= 		$chairperson_markup;
	$board_members_markup .= 		$board_member_markup;
	$board_members_markup .= 		$secretary_markup;
	$board_members_markup .= 		$under_secretary_markup;
	$board_members_markup .= 	'</div>';
	$board_members_markup .= '</div>';
	$board_members_markup .= $former_governors_link;

	return $board_members_markup;
}
function board_member_list_shortcode($atts) {
	return outputBoardMembers($atts['active']);
}
add_shortcode('board_member_list', 'board_member_list_shortcode');



function outputSeniorManagement($type) {
	$mgmt_page = get_page_by_title('Management Team');
	$mgmt_id = $mgmt_page -> ID;

	if ($type == 'ibb') {
		$mgmt_profile_ids = get_field('senior_management_management_team_ordered', $mgmt_id, true);
	} else if ($type == 'broadcast') {
		$mgmt_profile_ids = get_field('senior_management_network_leaders_ordered', $mgmt_id, true);
	}
	else if ($type == 'experts') {
		$mgmt_profile_ids = get_field('senior_management_senior_experts', $mgmt_id, true);
	}
	$mgmg_profile_block = "";

	foreach ($mgmt_profile_ids as $id) {
		$active = get_post_meta($id, 'active', true);

		if ($active) {
			$isGrantee = get_post_meta($id, 'grantee_leadership', true);
			$occupation = get_post_meta($id, 'occupation', true);
			$is_acting = get_post_meta($id, 'acting', true);
			$email = get_post_meta($id, 'email', true);
			$phone = get_post_meta($id, 'phone', true);
			$twitter_profile_handle = get_post_meta($id, 'twitter_handle', true);
			$profilePhotoID = get_post_meta($id, 'profile_photo', true);

			$profilePhoto = "";
			$actingTitle = "";

			if ($is_acting) {
				$actingTitle = 'Acting ';
			}

			if  ($profilePhotoID) {
				$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
				$profilePhoto = $profilePhoto[0];
			}

			$profileName = get_the_title($id);
			// .senior-profile is tracked in js/usagm-main.js 
			$b  = '<div class="grid-half profile-clears">';

			if ($profilePhoto != "") {
				$b .= 	'<a href="' . get_the_permalink($id) . '">';
				$b .= 		'<img src="' . $profilePhoto . '" class="bbg__profile-excerpt__photo" alt="Photo of ' . $profileName . ', ' . $occupation . '"/>';
				$b .= 	'</a>';
			}

			$b .= 	'<h4>';
			$b .= 		'<a href="' . get_the_permalink($id) . '">' . $profileName . '</a>';
			$b .= 	'</h4>';

			$b .= 	'<p class="bbg__profile-excerpt__text">';
			$b .= 		'<span class="bbg__profile-excerpt__occupation">' . $actingTitle . $occupation . '</span>';
			$b .= 		my_excerpt($id);
			$b .= 	'</p>';
			$b .= '</div>';

			$mgmg_profile_block .= $b;
		}
	}
	
	$s  = '<div class="nest-container">';
	$s .= 	$mgmg_profile_block;
	$s .= '</div>';

	return $s;
}
function senior_management_list_shortcode($atts) {
	return outputSeniorManagement($atts['type']);
}
add_shortcode('senior_management_list', 'senior_management_list_shortcode');
?>
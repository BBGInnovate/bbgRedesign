<?php
function outputBoardMembers($showActive) {
	// $showActive SHOULD BE A 0 or 1 PASSED IN 'active' IN THE SHORTCODE
	$board_page = get_page_by_title('The Board');
	$the_post_id = $board_page -> ID;

	$former_css = "";
	$former_governors_link = "";

	if ($showActive == 0) {
		$former_css = " bbg__former-member";
	}

	$qParams = array(
		'post_type' => array('page'),
		'post_status' => array('publish'),
		'post_parent' => $the_post_id,
		'order' => 'ASC',
		'orderby' => 'meta_value',
		'meta_key' => 'last_name',
		'posts_per_page' => 100
	);
	$custom_query = new WP_Query($qParams);

	// DEFAULT ADDS SPACE ABOVE HEADER IF NO NO IMAGE SET
	$featuredImageClass = " bbg__article--no-featured-image";

	$boardStr = "";
	$chairpersonStr = "";
	$secretaryStr = "";
	$underSecretaryStr = "";
	$actingStr = "";

	while ($custom_query -> have_posts())  {
		$custom_query -> the_post();
		$id = get_the_ID();
		$active = get_post_meta($id, 'active', true);
		if (!isset($active) || $active == "" || !$active) {
			$active = 0;
		}
		if ((get_the_title() != "Special Committees") && ($showActive == $active)) {
			$isChairperson = get_post_meta($id, 'chairperson', true);
			$isSecretary = get_post_meta($id, 'secretary_of_state', true);
			$isUnderSecretary = get_post_meta($id, 'under_secretary_of_state', true);
			$isActing = get_post_meta($id, 'acting', true);

			$email = get_post_meta($id, 'email', true);
			$phone = get_post_meta($id, 'phone', true);
			$twitterProfileHandle = get_post_meta($id, 'twitter_handle', true);
			$profilePhotoID = get_post_meta($id, 'profile_photo', true);
			$profilePhoto = "";

			if ($profilePhotoID) {
				$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
				$profilePhoto = $profilePhoto[0];
			}
			$profileName = get_the_title();

			$occupation = '<span class="bbg__profile-excerpt__occupation">';

			if ($isActing && !$isUnderSecretary && !$isSecretary) {
				$occupation .= 'Acting ';
			} else if ($isActing && $isUnderSecretary) {
				$occupation .= 'Acting ';
				$occupation .= get_field('occupation', $id);
			}

			if ($isChairperson) {
				$occupation .= 'Chairman of the Board';
			} else if ($isSecretary) {
				$occupation .= 'Ex officio board member';
			}
			$occupation .= '</span>';

			$b  = '<div class="mgmt-profile grid-half"';
			if ($isUnderSecretary) {
				$b .= ' style="clear: both; border-top: 1px solid #f1f1f1; padding-top: 1.5em"';
			}
			$b .= '>';

			if (!$isUnderSecretary) {
				$member_name  = '<h4 class="bbg__profile-excerpt__name">';
				$member_name .= 	'<a href="' . get_the_permalink() . '">';
				if ($isSecretary && $isActing) {
					$member_name .= 	'Acting ';
				}
				$member_name .= 		$profileName;
				$member_name .= 	'</a>';
				$member_name .= '</h4>';
				$b .= $member_name;
			}

			if ($profilePhoto != "") {
				$member_photo  = '<a href="' . get_the_permalink() . '">';
				$member_photo .= 	'<img src="';
				$member_photo .= 		$profilePhoto;
				$member_photo .= 		'" class="bbg__profile-excerpt__photo';
				$member_photo .= 		$former_css;
				$member_photo .= 		'" alt="Photo of BBG Governor ';
				$member_photo .= 		get_the_title();
				$member_photo .= 		'"';
				if ($isUnderSecretary) {
					$member_photo .= ' style="width: 18%"';
				}
				$member_photo .= 	' />';
				$member_photo .= '</a>';
				$b .= $member_photo;
			}
			if (!$isUnderSecretary) {
				$member_position = '<p>' . $occupation . get_the_excerpt() . '</p>';
				$b .= $member_position;
			} else {
				$memeber_content .= '<h4 class="bbg__profile-excerpt__name" style="clear: none;">';
				$memeber_content .= 	'<a href="' . get_the_permalink() . '">' . $profileName . '</a>';
				$memeber_content .= '</h4>';
				$memeber_content .= '<p style="margin-top: 0;">' . get_the_excerpt() . '</p>';
				$b .= $memeber_content;
			}
			$b .= '</div><!-- .bbg__profile-excerpt -->';

			if ( $isChairperson ) {
				$chairpersonStr = $b;
			} else if ( $isSecretary ) {
				$secretaryStr = $b;
			} else if ($isUnderSecretary) {
				$underSecretaryStr = $b;
			} else {
				$boardStr .= $b;
			}
		}
	}

	$board_members_markup  = '<div class="nest-container">';
	$board_members_markup .= 	'<div class="inner-container">';
	$board_members_markup .= 		$chairpersonStr;
	$board_members_markup .= 		$boardStr;
	$board_members_markup .= 		$secretaryStr;
	$board_members_markup .= 		$underSecretaryStr;
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
	$board_page = get_page_by_title('Management Team');
	$the_post_id = $board_page -> ID;

	if ($type == 'ibb') {
		$ids = get_field("senior_management_management_team_ordered", $the_post_id, true);
	} else if ( $type == 'broadcast' ) {
		$ids = get_field("senior_management_network_leaders_ordered", $the_post_id, true);
	}
	$peopleStr = "";

	foreach ($ids as $id) {
		$active = get_post_meta($id, 'active', true);

		if ($active) {
			$isGrantee = get_post_meta($id, 'grantee_leadership', true);
			$occupation = get_post_meta($id, 'occupation', true);
			$isActing = get_post_meta($id, 'acting', true);
			$email = get_post_meta($id, 'email', true);
			$phone = get_post_meta($id, 'phone', true);
			$twitterProfileHandle = get_post_meta($id, 'twitter_handle', true);
			$profilePhotoID = get_post_meta($id, 'profile_photo', true);

			$profilePhoto = "";
			$actingTitle = "";

			if ($isActing) {
				$actingTitle = 'Acting ';
			}

			if  ($profilePhotoID) {
				$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
				$profilePhoto = $profilePhoto[0];
			}

			$profileName = get_the_title($id);
			// .senior-profile is tracked in js/usagm-main.js 
			$b  = '<div class="mgmt-profile grid-half">';
			$b .= 	'<h4>';
			$b .= 		'<a href="' . get_the_permalink($id) . '">' . $profileName . '</a>';
			$b .= 	'</h4>';

			if ($profilePhoto != "") {
				$b .= 	'<a href="' . get_the_permalink($id) . '">';
				$b .= 		'<img src="' . $profilePhoto . '" class="bbg__profile-excerpt__photo" alt="Photo of ' . $profileName . ', ' . $occupation . '"/>';
				$b .= 	'</a>';
			}

			$b .= 	'<p class="bbg__profile-excerpt__text">';
			$b .= 		'<span class="bbg__profile-excerpt__occupation">' . $actingTitle . $occupation . '</span>';
			$b .= 		my_excerpt($id);
			$b .= 	'</p>';
			$b .= '</div>';

			$peopleStr .= $b;
		}
	}
	
	$s .= '<div class="nest-container">';
	$s .= 	$peopleStr;
	$s .= '</div>';

	return $s;
}
function senior_management_list_shortcode($atts) {
	return outputSeniorManagement($atts['type']);
}
add_shortcode('senior_management_list', 'senior_management_list_shortcode');
?>
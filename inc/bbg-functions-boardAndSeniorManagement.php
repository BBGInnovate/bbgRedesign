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
				$member_name  = '<p class="article-title">';
				$member_name .= 	'<a href="' . get_the_permalink() . '">';
				if ($is_secretary && $is_acting) {
					$member_name .= 	'Acting ';
				}
				$member_name .= 		$board_member_name;
				$member_name .= 	'</a>';
				$member_name .= '</p>';

				$member_block .= $member_name;
			}

			if (!$is_under_secretary) {
				$member_position = '<p>' . $occupation . get_the_excerpt() . '</p>';

				$member_block .= $member_position;
			} else {
				$memeber_content  = '<p class="article-title">';
				$memeber_content .= 	'<a href="' . get_the_permalink() . '">' . $board_member_name . '</a>';
				$memeber_content .= '</p>';
				$memeber_content .= '<p style="margin-top: 0;">' . get_the_excerpt() . '</p>';

				$member_block .= $memeber_content;
			}
			$member_block .= '</div>';

			if ($is_chairperson) {
				$chairperson_markup .= $member_block;
			} else if ($is_secretary) {
				$secretary_markup .= $member_block;
			} else if ($is_under_secretary) {
				$under_secretary_markup .= $member_block;
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

function outputFormerCeos() {
    $formerCeosLandingPage = get_page_by_title('Former CEOs');
    $formerCeosLandingPageId = $formerCeosLandingPage->ID;

    $ceos_markup = '';
    if (have_rows('former_ceos_ordered', $formerCeosLandingPageId)) {
        while (have_rows('former_ceos_ordered', $formerCeosLandingPageId)) {
            the_row();

            $formerCeoPageId = get_sub_field('ceo');
            $shouldOverrideExcerpt = get_sub_field('should_override_excerpt');
            $formerCeoExcerptOverride = get_sub_field('excerpt_override');

            $formerCeoName = get_the_title($formerCeoPageId);

            $formerCeoExcerpt = '';
            if ($shouldOverrideExcerpt) {
                $formerCeoExcerpt = $formerCeoExcerptOverride;
            } else {
                $formerCeoExcerpt = my_excerpt($formerCeoPageId);
            }

            $formerCeoPermalink = get_the_permalink($formerCeoPageId);

            $ceos_markup .= '<div class="grid-half profile-clears">';

            $formerCeoPhotoId = get_post_meta($formerCeoPageId, 'profile_photo', true);

            if ($formerCeoPhotoId) {
                $formerCeoPhoto = wp_get_attachment_image_src($formerCeoPhotoId , 'mugshot');
                $formerCeoPhoto = $formerCeoPhoto[0];

                $ceos_markup .= '<a href="' . $formerCeoPermalink . '">';
                $ceos_markup .=     '<img src="';
                $ceos_markup .=         $formerCeoPhoto;
                $ceos_markup .=         '" class="bbg__profile-excerpt__photo';
                $ceos_markup .=         ' bbg__former-member';
                $ceos_markup .=         '" alt="Photo of former CEO ';
                $ceos_markup .=         $formerCeoName;
                $ceos_markup .=         '"';
                $ceos_markup .=     '>';
                $ceos_markup .= '</a>';
            }

            $ceos_markup .= '<p class="article-title">';
            $ceos_markup .=     '<a href="' . $formerCeoPermalink . '">';
            $ceos_markup .=         $formerCeoName;
            $ceos_markup .=     '</a>';
            $ceos_markup .= '</p>';
            $ceos_markup .= '<p>' . $formerCeoExcerpt . '</p>';

            $ceos_markup .= '</div>';
        }
    }

    return $ceos_markup;
}
function former_ceos_list_shortcode() {
	return outputFormerCeos();
}
add_shortcode('former_ceos_list', 'former_ceos_list_shortcode');

function outputSeniorManagement($type, $shouldHideProfilePhoto) {
	// $mgmt_page = get_page_by_title('Management Team');
	// $mgmt_id = $mgmt_page -> ID;
	$mgmt_id = get_the_ID();

	if ($type == 'ceo') {
		$mgmt_profile_ids = array(get_field('senior_management_ceo', $mgmt_id, true));
	} else if ($type == 'ibb') {
		$mgmt_profile_ids = get_field('senior_management_management_team_ordered', $mgmt_id, true);
	} else if ($type == 'ibab') {
		$mgmt_profile_ids = get_field('senior_management_ibab_ordered', $mgmt_id, true);
	} else if ($type == 'broadcast') {
		$mgmt_profile_ids = get_field('senior_management_network_leaders_ordered', $mgmt_id, true);
	} else if ($type == 'experts') {
		$mgmt_profile_ids = get_field('senior_management_senior_experts', $mgmt_id, true);
	}
	$mgmg_profile_block = "";

	foreach ($mgmt_profile_ids as $id) {
		$active = get_post_meta($id, 'active', true);

		if ($active) {
			$isCEO = get_post_meta($id, 'ceo', true);
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

			if  ($shouldHideProfilePhoto != true && $profilePhotoID) {
				$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
				$profilePhoto = $profilePhoto[0];
			}

			$profileName = get_the_title($id);
			// .senior-profile is tracked in js/usagm-main.js 
			$b = '';
			if ($isCEO) {
				$b  .= '<div class="grid-full bbg__profile-ceo profile-clears">';
			} else {
				$b  .= '<div class="grid-half profile-clears">';
			}

			if ($profilePhoto != "") {
				$b .= 	'<a href="' . get_the_permalink($id) . '">';
				$b .= 		'<img src="' . $profilePhoto . '" class="bbg__profile-excerpt__photo" alt="Photo of ' . $profileName . ', ' . $occupation . '"/>';
				$b .= 	'</a>';
			}

			$b .= 	'<h3 class="article-title">';
			$b .= 		'<a href="' . get_the_permalink($id) . '">' . $profileName . '</a>';
			$b .= 	'</h3>';

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
	$attributes = shortcode_atts(array(
			'type' => '',
			'hide_profile_photo' => false
	), $atts);

	$hideProfilePhoto = $attributes['hide_profile_photo'];
	if ($hideProfilePhoto === 'true') {
		$attributes['hide_profile_photo'] = true;
	} else if ($hideProfilePhoto === 'false') {
		$attributes['hide_profile_photo'] = false;
	}

	return outputSeniorManagement($attributes['type'], $attributes['hide_profile_photo']);
}
add_shortcode('senior_management_list', 'senior_management_list_shortcode');


function usagm_experts_list_shortcode() {
	if (!empty($_GET['tag'])) {
		$tag = htmlspecialchars($_GET['tag']);
	} else {
		$tag = '';
	}
	$usagm_experts_args = array (
		'posts_per_page' => -1,
		'category_name' => 'usagm-experts',
		'tag_slug__in' => $tag
	);
	$usagm_experts_query = new WP_Query($usagm_experts_args);
	$usagm_experts_array = $usagm_experts_query->posts;

	$expert_id_list = array();
	$all_profiles = '';
	foreach ($usagm_experts_array as $expert_id) {
		$first_name = get_post_meta($expert_id->ID, 'first_name', true);
		$last_name = get_post_meta($expert_id->ID, 'last_name', true);
		$occupation = get_post_meta($expert_id->ID, 'occupation', true);
		$profile_expertise = get_post_meta($expert_id->ID, 'profile_expertise', true);
		$profile_language = get_post_meta($expert_id->ID, 'profile_language', true);
		$profile_location = get_post_meta($expert_id->ID, 'profile_location', true);
		$email = get_post_meta($expert_id->ID, 'email', true);
		$phone = get_post_meta($expert_id->ID, 'phone', true);
		$twitter_profile_handle = get_post_meta($expert_id->ID, 'twitter_handle', true);
		$profile_photo_id = get_post_meta($expert_id->ID, 'profile_photo', true);
		$profile_name = $first_name . ' ' . $last_name;
		$profile_link = get_the_permalink($expert_id->ID);

		if  ($profile_photo_id) {
			$profile_photo = wp_get_attachment_image_src($profile_photo_id , 'mugshot');
			$profile_photo = $profile_photo[0];
		}

		$expert_profile  = '<div class="grid-half profile-clears">';
		if (!empty($profile_photo)) {
			$expert_profile .= '<a href="' . $profile_link . '">';
			$expert_profile .= 	'<img src="' . $profile_photo . '" class="bbg__profile-excerpt__photo" alt="Photo of ' . $profile_name . '">';
			$expert_profile .= '</a>';
		}
		$expert_profile .= 	'<h3 class="article-title">';
		$expert_profile .= 		'<a href="' . $profile_link . '">' . $profile_name . '</a>';
		$expert_profile .= 	'</h3>';
		$expert_profile .= 	'<p>';
		$expert_profile .= 		'<span class="bbg__profile-excerpt__occupation">' . $occupation . '</span>';
		if (!empty($profile_expertise)) {
			$expert_profile .= 	'<strong>Expertise:</strong> ' .$profile_expertise;
		}
		if (!empty($profile_language)) {
			$expert_profile .= 	'<br><strong>Language(s):</strong> ' . $profile_language;
		}
		if (!empty($profile_location)) {
			$expert_profile .= 	'<br><strong>Location:</strong> ' . $profile_location;
		}
		$expert_profile .= 	'</p>';
		$expert_profile .= '</div>';

		$all_profiles .= $expert_profile;
	}

	$experts_markup  = '<p>Filter by: ';
	$experts_markup .= 	'<a href="' . add_query_arg('tag', 'voa', '/usagm-experts/') . '">VOA</a>, ';
	$experts_markup .= 	'<a href="' . add_query_arg('tag', 'ocb', '/usagm-experts/') . '">OCB</a>, ';
	$experts_markup .= 	'<a href="' . add_query_arg('tag', 'rferl', '/usagm-experts/') . '">RFE/RL</a>, ';
	$experts_markup .= 	'<a href="' . add_query_arg('tag', 'rfa', '/usagm-experts/') . '">RFA</a>, ';
	$experts_markup .= 	'<a href="' . add_query_arg('tag', 'mbn', '/usagm-experts/') . '">MBN</a>, ';
	$experts_markup .= 	'<a href="' . add_query_arg('tag', 'otf', '/usagm-experts/') . '">OTF</a>';
	if (!empty($tag)) {
		$experts_markup .= '<br><span class="date-meta"><a href="' . get_the_permalink() . '">Remove filter</a></span>';
	}
	$experts_markup .= '</p>';
	$experts_markup .= '<div class="nest-container">';
	$experts_markup .= 	$all_profiles;
	$experts_markup .= '</div>';

	return $experts_markup;
}
add_shortcode('usagm_experts_list', 'usagm_experts_list_shortcode');
?>
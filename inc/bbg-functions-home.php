<?php
// CUSTOM FIELD DATA
function get_homepage_banner_data() {
	$homepageBannerType = get_field('homepage_banner_type', 'option');

	if ($homepageBannerType == 'revolution_slider') {
		$bannerBackgroundPosition = get_field('homepage_banner_background_position', 'option');
		$sliderAlias = get_field('homepage_banner_revolution_slider_alias', 'option');

		echo '<section class="usa-section bbg-banner__section" style="position: relative; z-index:9990;">';
		echo do_shortcode('[rev_slider alias="' . $sliderAlias . '"]');
		echo '</section>';
	} else {
		$useRandomImage = true;
		$bannerCutline = '';
		$bannerAdjustStr = '';

		if ($homepageBannerType == 'specific_image') {
			$img = get_field('homepage_banner_image', 'option');
			if ($img) {
				$attachment_id = $img['ID'];
				$useRandomImage = false;
				$featuredImageCutline = '';
				$thumbnail_image = get_posts(
					array(
						'p' => $attachment_id,
						'post_type' => 'attachment'
					)
				);
				if ($thumbnail_image && isset($thumbnail_image[0])) {
					$bannerCutline = $thumbnail_image[0]->post_excerpt;
				}

				$bannerAdjustStr = '';
				$bannerBackgroundPosition = get_field('homepage_banner_background_position', 'option');
				if ($bannerBackgroundPosition) {
					$bannerAdjustStr = $bannerBackgroundPosition;
				}
			}
		}

		// NO 'else' IN CASE USER CHECKED 'specific_image' WITHOUT SELECTING ONE
		if ($useRandomImage) {
			$randomImg = getRandomEntityImage();
			$attachment_id = $randomImg['imageID'];
			$bannerCutline = $randomImg['imageCutline'];
			$bannerAdjustStr = $randomImg['bannerAdjustStr'];
		}

		$tempSources = bbgredesign_get_image_size_links( $attachment_id );

		// SORT SOURCES IN NUMERIC ORDER
		ksort( $tempSources );
		$prevWidth = 0;

		// NO IMAGE LARGER THAN 1200px WIDE??
		foreach($tempSources as $key => $tempSource) {
			if ($key > 1900) {
				unset( $tempSources[$key] );
			}
		}

		foreach($tempSources as $key => $tempSourceObj) {
			$tempSource = $tempSourceObj['src'];
		}

		$banner_data = array(
			'image_source' => $tempSource,
			'position' => $bannerAdjustStr,
			'caption' => $bannerCutline
		);
		return $banner_data;
	}
}

// USAGM NEWS
function get_recent_posts($qty) {
	$used_posts = array();
	$all_recent_posts = array();
	// THE BELLOW ARRAY ONLY GETS FILLED IF THERE IS A SELECTED POST TO FEATURE
	$selected_post_ids = array();

	$selected_featured_post = get_field('homepage_featured_post', 'option');
	if (!empty($selected_featured_post)) {
		$selection_array = $selected_featured_post;
		
		// IF MULTIPLE STORIES ARE SELECTED
		if (count($selection_array) > 1) {
			foreach($selected_featured_post as $cur) {
				$selected_post_ids[] = $cur->ID;
				$used_posts[] = $cur->ID;
			}
		} else {
			$selected_post_ids[] = $selected_featured_post[0]->ID;
			$used_posts[] = $selected_post_ids[0];
		}

		$all_recent_posts = $selection_array;
		$qty -= count($used_posts);
	}

	if ($qty != 0) {
		// CATEGORIES TO EXCLUDE
		// 3 Event:, 35: Profile, 36: Intern Testimonial, 45: Statement 55: Media Advisory, 56: Media Developent Map, 68: Threats to Press, 1046: From the CEO, 1244: Special Days
		$recent_posts_args = array(
			'posts_per_page' => $qty,
			'post_type' => array('post'),
			'orderby' => 'post_date',
			'order' => 'desc',
			'post__not_in' => $selected_post_ids,
			'category__not_in' => array(3, 35, 36, 38, 45, 55, 56, 68, 1046, 1244),
			'tax_query' => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => 'post-format-quote',
					'operator' => 'NOT IN'
				),
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => array('press-release'),
					'operator' => 'IN'
				)
			)
		);
		$recent_post_query = new WP_Query($recent_posts_args);
		$recent_query_array = $recent_post_query->posts;
		foreach ($recent_query_array as $recent_query) {
			$used_posts[] = $recent_query->ID;
			$all_recent_posts[] = $recent_query;
		}
	}

	$post_package = array(
		'posts' => $all_recent_posts,
		'used_posts' => $used_posts
	);
	return $post_package;
}

// IMPACT STORIES
function select_impact_story_id_at_random($used) {
	$qParams = array(
		'post_type'=> 'post',
		'post_status' => 'publish',
		'cat' => get_cat_id('impact'),
		'post__not_in' => $used,
		'posts_per_page' => 12,
		'orderby' => 'post_date',
		'order' => 'desc',
	);

	$custom_query = new WP_Query( $qParams );
	$allIDs = [];
	if ($custom_query -> have_posts()) {
		while ( $custom_query -> have_posts() ) {
			$custom_query -> the_post();
			$allIDs[] = get_the_ID();
		}
	}

	if (count( $allIDs ) > 2) {
		shuffle( $allIDs );
		$ids = [];
		$ids[] = array_pop($allIDs);
		$ids[] = array_pop($allIDs);
	} else {
		$ids = $allIDs;
	}
	return $ids;
}
?>
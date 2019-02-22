<?php
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



// BUILDIND BLOCKS
function build_featured_post_blocks($feat_post_data) {
	$post_media  = '<a href="' . get_the_permalink() . '">';
	$post_media .= 		$feat_post_data['media'];
	$post_media .= '</a>';

	$post_title  = '<a href="' . get_the_permalink() . '">';
	$post_title .= 		get_the_title();
	$post_title .= '</a>';

	$feature_blocks = array(
		'id' => get_the_ID(),
		'linked_media' => $post_media,
		'linked_title' => $post_title,
		'date' => get_the_date(),
		'excerpt' => get_the_excerpt()
	);
	return $feature_blocks;
}


// NEW HOME PAGE FUNCTIONS
function get_recent_posts($qty) {
	$used_posts = array();
	$all_recent_posts = array();

	$selected_featured_post = get_field('homepage_featured_post', 'option');
	if (!empty($selected_featured_post)) {
		$selection_array = $selected_featured_post;
		$used_posts = $selected_featured_post[0]->ID;
		$all_recent_posts = $selection_array;
		$qty--;
	}

	if ($qty != 0) {
		$recent_posts_args = array(
			'posts_per_page' => $qty,
			'post_type' => array('post'),
			'orderby' => 'post_date',
			'order' => 'desc',
			'category__not_in' => array(3, 24, 36, 38, 55, 56, 1046, 1244),
			'post__not_in' => $used_id,
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
	}
	$recent_post_query = new WP_Query($recent_posts_args);
	$recent_query_array = $recent_post_query->posts;
	foreach ($recent_query_array as $recent_query) {
		$all_recent_posts[] = $recent_query;
	}
	
	return $all_recent_posts;
}

function build_vertical_post_main($article_data) {
	$article_structure  = '<article>';
	$article_structure .= 	'<div class="post-image">' . get_the_post_thumbnail($article_data) . '</div>';
	$article_structure .= 	'<div class="article-info">';
	$article_structure .= 		'<h4>' . get_the_title($article_data) . '</h4>';
	$article_structure .= 		'<p class="date-meta">' . get_the_date() . '</p>';
	$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <span class="new-learn-more">Read More</span></p>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}
function build_post_aside($article_data) {
	$article_structure  = '<article class="article-aside">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	$article_structure .= 			'<div class="article-image post-image">' . get_the_post_thumbnail($article_data) . '</div>';
	$article_structure .= 			'<div class="article-desc article-info">';
	$article_structure .= 				'<h4>' . get_the_title($article_data) . '</h4>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date() . '</p>';
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}
?>
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
	if ( $custom_query -> have_posts() ) :
		while ( $custom_query -> have_posts() ) : $custom_query -> the_post();
			$allIDs[] = get_the_ID();
		endwhile;
	endif;

	if ( count( $allIDs ) > 2 ) {
		shuffle( $allIDs );
		$ids = [];
		$ids[] = array_pop( $allIDs );
		$ids[] = array_pop( $allIDs );
	} else {
		$ids = $allIDs;
	}
	return $ids;
}

function create_threats_post_query_params($numPosts, $used) {
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $numPosts,
		'orderby' => 'post_date',
		'order' => 'desc',
		'cat' => get_cat_id('Threats to Press'),
		'post__not_in' => $used
	);
	$threats_package = array('params' => $qParams);
	// return $threats_package;
}

// CUSTOM FIELD DATA
function get_site_settings_data() {
	$intro_content = get_field('site_setting_mission_statement','options','false');
	$intro_link = get_field('site_setting_mission_statement_link', 'options', 'false');
	$site_setting_package = array('intro_content' => $intro_content, 'intro_link' => $intro_link);
	return $site_setting_package;
}

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
			$img = get_field( 'homepage_banner_image', 'option' );
			if ($img) {
				$attachment_id = $img['ID'];
				$useRandomImage = false;
				$featuredImageCutline='';
				$thumbnail_image = get_posts(
					array(
						'p' => $attachment_id,
						'post_type' => 'attachment'
					)
				);
				if ($thumbnail_image && isset($thumbnail_image[0])) {
					$bannerCutline=$thumbnail_image[0]->post_excerpt;
				}

				$bannerAdjustStr = '';
				$bannerBackgroundPosition = get_field( 'homepage_banner_background_position', 'option' );
				if ( $bannerBackgroundPosition ) {
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
		$prevWidth=0;

		// NO IMAGE LARGER THAN 1200px WIDE??
		foreach( $tempSources as $key => $tempSource ) {
			if ( $key > 1900 ) {
				unset( $tempSources[$key] );
			}
		}

		foreach( $tempSources as $key => $tempSourceObj ) {
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

function get_impact_stories_data($numPosts) {
	global $includePortfolioDescription;
	global $postIDsUsed;

	$impactPostIDs = select_impact_story_id_at_random($postIDsUsed);
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $numPosts,
		'orderby' => 'post_date',
		'order' => 'desc',
		'post__in' => $impactPostIDs
	);
	// return query_posts($qParams);
	$impact_data = new WP_Query($qParams);
	return $impact_data;

	if (have_posts()) {
		while (have_posts()) { 
			the_post();
			$includePortfolioDescription = false;
			$postIDsUsed[] = get_the_ID();

			//build_impact_markup();
		}
	}
	wp_reset_query();
}

function get_field_post_data($type, $qty) {
	if ($type == "featured") {
		$post_data = get_field('homepage_featured_post', 'option');
	}
	if ($post_data) {
		$post_data = $post_data[0];
		$postIDsUsed[] = $post_data -> ID;

		$qParams = array(
			'posts_per_page' => $qty,
			'post__in' => array($post_data -> ID),
			'post_status' => array('publish', 'future')
		);
	} else {
		$qParams = select_recent_post_query_params(1, $postIDsUsed, $STANDARD_POST_CATEGORY_EXCLUDES);
	}

	if (!empty($qParams)) {
		query_posts($qParams);

		if (have_posts()) {
			the_post();
			$feature_post_data = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'media' => get_feature_media_data()
			);
			return build_featured_post_blocks($feature_post_data);
		}
	}
	wp_reset_query();
}

function get_recent_post_data($maxPostsToShow) {
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $maxPostsToShow,
		'orderby' => 'post_date',
		'order' => 'desc',
		'category__not_in' => $catExclude,
		'post__not_in' => $used,
		'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => 'post-format-quote',
				'operator' => 'NOT IN'
			)
		)
	);
	$param_data = query_posts($qParams);
	return $param_data;
	// return build_recent_post_blocks($param_data);
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
		'linked_media' => $post_media,
		'linked_title' => $post_title,
		'date' => get_the_date(),
		'excerpt' => get_the_excerpt()
	);
	return $feature_blocks;
}

function build_impact_markup() {
	$impact_markup  = '<div>';
	$impact_markup .=  	'<a href="' . get_the_permalink() . '">';
	if (get_the_post_thumbnail()) {
		$impact_markup .= 		get_the_post_thumbnail();
	} else {
		$impact_markup .= 		'<img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="White BBG logo on medium gray background" />';
	}
	$impact_markup .= 	'</a>';
	$impact_markup .= 	'<h5><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h5>';
	$impact_markup .= 	'<p>';
	$impact_markup .= 		get_the_excerpt();
	$impact_markup .= 	'</p>';
	$impact_markup .= '</div>';
	echo $impact_markup;
}

?>
<?php

function check_featured_media_type() {
	global $videoUrl;
	global $addFeaturedGallery;

	$bannerPosition = get_field('adjust_the_banner_image', '', true);
	$videoUrl = get_field('featured_video_url', '', true);

	if ($videoUrl != "") {
		$hideFeaturedImage = true;
		$video_data = featured_video($videoUrl);

		$video_markup  = '<iframe scrolling="no" src="';
		$video_markup .= 	$video_data['url'];
		$video_markup .= 	'" frameborder="0" allowfullscreen="" data-ratio="NaN" data-width="" data-height="" style="display: block; margin: 0px;">';
		$video_markup .= '</iframe>';
		$featured_data = $video_markup;
	} 
	elseif (has_post_thumbnail()) {
		$featuredImageClass = "";
		$featuredImageCutline = "";
		$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');

		if ( $thumbnail_image && isset($thumbnail_image[0]) ) {
			$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
		}

		$post_featured_image  = '<div class="feature-image">';
		$post_featured_image .= 	'<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner" ';
		$post_featured_image .= 		'style="background-image: url(' . $src[0] . '); background-position: ' . $bannerPosition . '">';
		$post_featured_image .= 	'</div>';
		$post_featured_image .= '</div>';

		$featured_data =  $post_featured_image;
	}

	// ACTUAL BANNER MARKUP
	if ($videoURL != "" || has_post_thumbnail()) {
		$featured_markup  = '<div class="container">';
		$featured_markup  = 	'<div class="full-grid">';
		$featured_markup .= 		'<div class="feature-element">';
		$featured_markup .= 		$featured_data;
		$featured_markup .= 		'</div>';
		$featured_markup .= 	'</div>';
		$featured_markup .= '</div>';
		echo $featured_markup;
	}

	// if ($videoUrl != "") {
	// 	$hideFeaturedImage = true;
	// 	$video_tags  = '<div class="usa-grid">';
	// 	$video_tags .= 		'<div class="header-feature feature-spot">';
	// 	$video_tags .= 			featured_video($videoUrl);
	// 	$video_tags .= '</div></div>';
	// 	echo $video_tags;
	// }
	// elseif (has_post_thumbnail() && ($hideFeaturedImage != 1)) {
	// 	$featuredImageClass = "";
	// 	$featuredImageCutline = "";
	// 	$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
	// 	if ($thumbnail_image && isset($thumbnail_image[0])) {
	// 		$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
	// 	}
	// 	$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');

	// 	// $feat_image_markup  = '<div class="usa-grid">';
	// 	$feat_image_markup  = '<div class="container">';
	// 	$feat_image_markup .= 	'<div class="full-grid">';
	// 	$feat_image_markup .= 		'<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner" ';
	// 	$feat_image_markup .= 			'style="background-image: url(' . $src[0] . '); background-position: ' . $bannerPosition . '">';
	// 	$feat_image_markup .= 		'</div>';

	// 	if ($featuredImageCutline != "") {
	// 		$cutline  = 			'<div class="usa-grid">';
	// 		$cutline .= 				'<div class="bbg__article-header__caption">' . $featuredImageCutline . '</div>';
	// 		$cutline .= 			'</div><!-- usa-grid -->';
	// 		$feat_image_markup .= $cutline;
	// 	}
	// 	$feat_image_markup .= 	'</div>'; // END .full-grid
	// 	$feat_image_markup .= '</div>';// END .containter
	// 	echo $feat_image_markup;
	// }
}

function get_flexible_row_data($str) {
	if ($str == 'marquee') {
		$marqueeHeading = get_sub_field('marquee_heading');
		$marqueeLink = get_sub_field('marquee_link');
		$marqueeContent = get_sub_field('marquee_content');
		$marqueeContent = apply_filters( 'the_content', $marqueeContent );
		$marqueeContent = str_replace( ']]>', ']]&gt;', $marqueeContent );

		$flex_row_data = array('type' => 'marquee', 'heading' => $marqueeHeading, 'link' => $marqueeLink, 'content' => $marqueeContent);
	}
	elseif (get_row_layout() == 'about_office') {
		$flex_row_data = array('type' => 'office');
	}
	elseif (get_row_layout() == 'about_ribbon_page') {
		$labelText = get_sub_field( 'about_ribbon_label' );
		$labelLink = get_sub_field( 'about_ribbon_label_link' );
		$headlineText = get_sub_field( 'about_ribbon_headline' );
		$headlineLink = get_sub_field( 'about_ribbon_headline_link' );
		$summary = get_sub_field( 'about_ribbon_summary' );
		$imageURL = get_sub_field( 'about_ribbon_image' );
		// INCLUDE SHORTCODES
		$summary = apply_filters( 'the_content', $summary );
		$summary = str_replace( ']]>', ']]&gt;', $summary );

		$flex_row_data = array('type' => 'ribbon', 'label' => $labelText, 'label_link' => $labelLink, 'headline' => $headlineText, 'headline_link' => $headlineLink, 'summary' => $summary, 'img_url' => $imageURL);
	}
	elseif (get_row_layout() == 'umbrella') {
		$flex_row_data = array('type' => 'umbrella');
	}
	
	// return build_flexible_row($flex_row_data);
	return $flex_row_data;
}

function build_flexible_row($row_data) {
	if ($row_data['type'] == 'marquee') {
		$marquee_markup  = '<section class="usa-grid bbg__about__children--row bbg__about--marquee">';
		$marquee_markup .= 		'<article id="post-25948" class="bbg__about__excerpt bbg__about__child bbg__about__child--mission bbg-grid--1-1-1 post-25948 page type-page status-publish has-post-thumbnail hentry">';
		$marquee_markup .= 			'<div class="entry-content bbg__about__excerpt-content">' . $row_data['content'] . '</div>';
		$marquee_markup .= 		'</article>';
		$marquee_markup .= '</section>';
		return $marquee_markup;
	}
	elseif ($row_data['type'] == 'office') {
		echo "you got the ";
	}
	elseif ($row_data['type'] == 'ribbon') {
		echo "you got the ";
	}
	elseif ($row_data['type'] == 'umbrella') {
		echo "you got the ";
	}
}

function get_entity_data() {
	$entityParentPage = get_page_by_path('networks');
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $entityParentPage->ID,
		'orderby' => 'meta_value_num',
		'meta_key' => 'entity_year_established',
		'order' => 'ASC'
	);
	$custom_query = new WP_Query($qParams);
	return build_entity_markup($custom_query);
}

function build_entity_markup($data) {
	$entity_markup  = '<section id="entities" class="">';
	$entity_markup .= 	'<h1 class="header-outliner">Entities</h1>';
	$entity_markup .= 	'<div class="usa-grid">';
	$entity_markup .= 		'<h2><a href="' . get_permalink(get_page_by_path('networks')) . '" title="A list of the BBG broadcasters.">Our networks</a></h2>';
	$entity_markup .= 		'<div class="usa-intro bbg__broadcasters__intro">';
	$entity_markup .= 			'<h3 class="usa-font-lead">Every week, more than ' . do_shortcode('[audience]') . ' listeners, viewers and internet users around the world turn on, tune in and log onto U.S. international broadcasting programs. The day-to-day broadcasting activities are carried out by the individual BBG international broadcasters.</h3>';
	$entity_markup .= 		'</div>';
	$entity_markup .= 		'<div id="home-entities">';

	if ($data -> have_posts()) {
		$entity_markup .= '<div id="entity-grid">';

		while ($data -> have_posts())  {
			$data -> the_post();
			$id = get_the_ID();
			$fullName = get_post_meta($id, 'entity_full_name', true);
			if ($fullName != "") {
				$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
				$abbreviation = str_replace("/", "",$abbreviation);
				$description = get_post_meta($id, 'entity_description', true);
				$description = apply_filters('the_content', $description);
				$link = get_permalink( get_page_by_path("/networks/$abbreviation/"));
				$imgSrc = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png'; //need to fix this

				$entity_markup .= '<article class="home_entity">';
				$entity_markup .= 	'<div class="bbg__entity__icon" >';
				$entity_markup .= 		'<a href="' . $link . '" tabindex="-1">';
				$entity_markup .= 			'<div class="bbg__entity__icon__image" style="background-image: url(' . $imgSrc . ');"></div>';
				$entity_markup .= 		'</a>';
				$entity_markup .= 	'</div>';
				$entity_markup .= 	'<div>';
				$entity_markup .= 		'<h5><a href="' . $link . '">' . $fullName . '</a></h5>';
				$entity_markup .= 			'<p class="">' . $description . '</p>';
				$entity_markup .= 	'</div>';
				$entity_markup .= '</article>';
			}
		}
		$entity_markup .= 	'</div>'; // END HOME ENTITIES
		$entity_markup .= '</div></section>';
		return $entity_markup;
	}
}
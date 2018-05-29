<?php

function check_featured_media_type() {
	global $videoUrl;
	global $addFeaturedGallery;

	if ($videoUrl != "") {
		$hideFeaturedImage = true;
		$video_tags  = '<div class="usa-grid">';
		$video_tags .= 		'<div class="header-feature feature-spot">';
		$video_tags .= 			featured_video($videoUrl);
		$video_tags .= '</div></div>';
		echo $video_tags;
	}
	elseif (has_post_thumbnail() && ($hideFeaturedImage != 1)) {
		$featured_featured_image_markup  = '<div class="usa-grid">';
			$featuredImageClass = "";
			$featuredImageCutline = "";
			$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));

			if ($thumbnail_image && isset($thumbnail_image[0])) {
				$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
			}

			$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');

			// Output featured image
			$featured_featured_image_markup .= '<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner" style="background-image: url(' . $src[0] . '); background-position: ' . $bannerPosition . '"></div>';

			// Output caption for featured image
			if ($featuredImageCutline != "") {
				$cutline  = '<div class="usa-grid">';
				$cutline .= 	'<div class="bbg__article-header__caption">' . $featuredImageCutline . '</div>';
				$cutline .= '</div><!-- usa-grid -->';
				$featured_featured_image_markup .= $cutline;
			}
		$featured_featured_image_markup .= '</div>';
		echo $featured_featured_image_markup;
	}
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

function showUmbrellaArea($atts) {
	$itemTitle = $atts['itemTitle'];
	$columnTitle = $atts['columnTitle'];
	$link = $atts['link'];
	$gridClass = $atts['gridClass'];
	$description = $atts['description'];
	$forceContentLabels = $atts['forceContentLabels'];
	$thumbPosition = "center center";
	$subTitle = $atts['subTitle'];
	$thumbSrc = $atts['thumbSrc'];
	$columnType = $atts['columnType'];
	$anchorTarget = "";
	$layout = $atts['layout'];
	$linkSuffix = "";

	if ($columnType == "file") {
		$fileSize = $atts['fileSize'];
		$fileExt = $atts['fileExt'];
		$linkSuffix = ' <span class="bbg__file-size">(' . $fileExt . ', ' . $fileSize . ')</span>';
	
	}
	if ($columnType == "external" || $columnType == "file") {
		$anchorTarget = " target='_blank' ";
	}

	$layout_package = array('layout' => $layout, 'grid' => $gridClass, 'column_title' => $columnTitle, 'force_content_label' => $forceContentLabels, 'anchor' => $anchorTarget, 'link' => $link, 'link_suffix' => $linkSuffix, 'thumb' => $thumbSrc, 'item_title' => $itemTitle, 'sub_title' => $subTitle, 'description' => $description);
	return build_layout_module($layout_package);
}

function build_layout_module($build_data) {
	if ($build_data['layout'] == 'full') {
		$layout_markup = '<article class="' . $build_data['grid'] . ' bbg__about__grandchild bbg__about__child">';
		if ($build_data['column_title'] == "") {
			if ($build_data['force_content_label']) {
				$layout_markup .= '<h6 class="bbg__label">&nbsp;</h6>';	
			}
		} else {
			if ($build_data['link'] != "") {
				$columnTitle = '<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '">' . $build_data['column_title'] . '</a>';
			}
			$layout_markup .= '<h6 class="bbg__label">' . $build_data['column_title'] . '</h6>';
		}
		
		if ($build_data['thumb']) {
			$layout_markup .= '<div class="single-post-thumbnail clear bbg__article-header__thumbnail--medium">'; 
			$layout_markup .= 	'<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '" rel="bookmark" tabindex="-1">';
			$layout_markup .= 		'<img width="1040" height="624" src="' . $build_data['thumb'] .  '" class="attachment-large-thumb size-large-thumb">';
			$layout_markup .= 	'</a>';
			$layout_markup .= '</div>';	
		}
		
		$layout_markup .= '<h3 class="bbg__about__grandchild__title">';
		$layout_markup .= 	'<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '">' . $build_data['item_title'] . '</a>';
		$layout_markup .= 	$build_data['link_suffix'];
		$layout_markup .= '</h3>';

		if ($build_data['sub_title'] != "") {
			$layout_markup .= '<h5 class="bbg__about__grandchild__subtitle">' . $build_data['sub_title'] . '</h5>';
		}
		$layout_markup .= 	$build_data['description'];
		$layout_markup .= '</article>';
		echo $layout_markup;
	}
	else {
		$layout_markup  = '<article class="' . $build_data['grid'] . ' bbg__about__grandchild">';
		$columnTitle = $itemTitle;
		if ($build_data['link'] != "") {
			$columnTitle = '<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '">' . $build_data['column_title'] . '</a>';
		}
		$columnTitle = $columnTitle . $build_data['link_suffix'];
		$layout_markup .= '<h3 class="bbg__about__grandchild__title">' . $columnTitle . '</h3>';	
		$layout_markup .= '<a '  . $build_data['anchor'] . ' href="' . $build_data['link'] . '">';
		$layout_markup .= '<div class="bbg__about__grandchild__thumb" style="background-image: url(' . $build_data['thumb'] . '); background-position:center center;"></div></a>' . $build_data['description'];
		$layout_markup .= '</article>';
		echo $layout_markup;
	}
}
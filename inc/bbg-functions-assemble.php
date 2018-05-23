<?php

function check_featured_media_type() {
	global $videoUrl;
	global $addFeaturedGallery;

	if ($videoUrl != "") {
		$hideFeaturedImage = true;
		$video_tags  = '<div class="usa-grid">';
		$video_tags .= 		'<div class="usa-width-two-thirds feature-spot">';
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
	
	return build_flexible_row($flex_row_data);;
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
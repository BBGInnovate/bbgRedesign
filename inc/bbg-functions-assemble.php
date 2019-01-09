<?php
// test function, original is underneath
function get_feature_media_data($home_page = NULL) {
	$feature_gallery = get_post_meta(get_the_ID(), 'featured_gallery_add', true);
	$banner_position = get_field('adjust_the_banner_image', '', true);
	$banner_position_css_override = get_field('adjust_the_banner_image_css', '', true);
	if (!empty($banner_position_css_override)) {
		$banner_position = $banner_position_css_override;
	}
	$video_url = get_field('featured_video_url', '', true);
	$addFeaturedMap = get_post_meta(get_the_ID(), 'featured_map_add', true);
	$media_dev_map = get_field('media_dev_coordinates');

	if ($video_url != "") {
		$featured_data = "";
		$video_data = featured_video($video_url);

		$video_markup  = '<div class="page-featured-media">';
		if ($video_data['extra_classes'] == 'facebook') {
			$video_markup .= 	$video_data['url'];
		} else {
			$video_markup .= 	'<iframe class="bbg-banner" scrolling="no" src="';
			$video_markup .= 		$video_data['url'];
			$video_markup .= 		'" frameborder="0" allowfullscreen="" data-ratio="NaN" data-width="" data-height="" style="display: block; margin: 0px;">';
			$video_markup .= 	'</iframe>';
		}
		$video_markup .= '</div>';
		$featured_data = $video_markup;
	}
	elseif (!empty($feature_gallery)) {
		$gallery_id = get_post_meta(get_the_ID(), 'featured_gallery_id', true);
		if (!empty($gallery_id)) {
			echo '<div class="inner-container">';
			putUniteGallery($gallery_id);
			echo '</div>';
		}
	}
	elseif (has_post_thumbnail()) {
		$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');
		$featuredImageClass = "";
		$featuredImageCutline = "";

		if ($thumbnail_image && isset($thumbnail_image[0])) {
			$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
		}
		if (!empty($src[0])) {
			$post_featured_image  = '<div class="page-post-featured-graphic">';
			$post_featured_image .= 	'<div class="bbg__article-header__banner" ';
			$post_featured_image .= 		'style="background-image: url(' . $src[0] . '); background-position: ' . $banner_position . '">';
			$post_featured_image .= 	'</div>';
			$post_featured_image .= '</div>';

			$featured_data = $post_featured_image;
		}
	}
	elseif ($addFeaturedMap || $media_dev_map) {
		$featuredMapCaption = get_post_meta(get_the_ID(), 'featured_map_caption', true);
		$featured_map  = 	'<div id="map-featured" class="bbg__map--banner"></div>';
		if ($featuredMapCaption != "") {
			$featured_map .= '<p class="bbg__article-header__caption">';
			$featured_map .= 	$featuredMapCaption;
			$featured_map .= '</p>';
		}
		$featured_data = $featured_map;
	}

	if ($home_page) {
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');
		$post_featured_image  = '<div class="page-post-featured-graphic">';
		$post_featured_image .= 	'<div class="bbg__article-header__banner" ';
		$post_featured_image .= 		'style="background-image: url(' . $src[0] . '); background-position: ' . $banner_position . '">';
		$post_featured_image .= 	'</div>';
		$post_featured_image .= '</div>';
		$featured_data = $post_featured_image;
	}

	$featured_setup  = '<div class="feautre-banner">';
	$featured_setup .= 		$featured_data;
	$featured_setup .= '</div>';

	if (!empty($featured_data)) {
		return $featured_setup;
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
	// elseif ($row_data['type'] == 'office') {
	// 	echo "you got the ";
	// }
	// elseif ($row_data['type'] == 'ribbon') {
	// 	echo "you got the ";
	// }
	// elseif ($row_data['type'] == 'umbrella') {
	// 	echo "you got the ";
	// }
}
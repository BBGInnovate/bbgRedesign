<?php
// STYLES FOR THESE ARTICLE TEASERS ARE IN _scss/bbg/components/_article-structure-style.scss

function build_main_head_article($article_data) {
	$article_structure  = '<div class="main-head-article article-teaser">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$img_pos = get_post_meta($article_data -> ID, 'adjust_the_banner_image');
		$img_pos = $img_pos[0];
		$article_structure .= '<div class="feature-article-image">';
		$article_structure .= 	'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 		'<div class="article-image-bg" style="background-image: url(' . get_the_post_thumbnail_url($article_data, 'large') . '); background-position: ' . $img_pos . ';"></div>';
		$article_structure .= 	'</a>';
		$article_structure .= '</div>';
	}
	$article_structure .= 	'<div class="article-info">';
	$article_structure .= 		'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3> ';
	$article_structure .= 		'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$content = wpautop($article_data->post_content);
		$shortened_text = substr($content, 0, strpos($content, '</p>') + 4);
		$article_structure .= 	'<p class="content-teaser">' . strip_tags($shortened_text, '<a>') . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 	'</div>';
	$article_structure .= '</div>';
	return $article_structure;
}

function build_article_standard_vertical($article_data) {
	$article_structure  = '<article class="vertical-article article-teaser">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$image_url = get_the_post_thumbnail_url($article_data, 'large');

		$article_structure .= '<div class="article-image">';
		$article_structure .= 	'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 		'<div class="article-image-bg" style="background-image: url(' . $image_url . ');"></div>';
		$article_structure .= 	'</a>';
		$article_structure .= '</div>';
	}
	$article_structure .= 	'<div class="article-info">';
	$article_structure .= 		'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3>';
	$article_structure .= 		'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		if (!empty($article_data->post_content)) {
			$content = wpautop($article_data->post_content);
			$shortened_text = substr($content, 0, strpos($content, '</p>') + 4);
			$article_structure .= 	'<p class="content-teaser">' . strip_tags($shortened_text, '<a>') . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
		}
	}
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_horizontal_half_article($article_data) {
	$article_structure  = '<article class="horizontal-half-article article-teaser">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	$article_structure .= 			'<div class="grid-half">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$article_structure .= 			'<div class="article-image">';
		$article_structure .= 				'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 		 			'<img src="' . get_the_post_thumbnail_url($article_data, 'large') . '" alt="Image link to ' . get_the_title($article_data) . ' post">';
		$article_structure .= 				'</a>';
		$article_structure .= 			'</div>';
	}
	$article_structure .= 			'</div>';
	$article_structure .= 			'<div class="grid-half">';
	$article_structure .= 				'<div class="article-info">';
	$article_structure .= 					'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3> ';
	$article_structure .= 					'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 				'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$content = wpautop($article_data->post_content);
		$shortened_text = substr($content, 0, strpos($content, '</p>') + 4);
		$article_structure .= 	'<p class="content-teaser">' . strip_tags($shortened_text, '<a>') . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 				'</div>';
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_horizontal_one_third_image_article($article_data) {
	$article_structure  = '<article class="horizontal-one-third-image-article article-teaser">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$article_structure .= 		'<div class="article-image">';
		$article_structure .= 			'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 				'<img src="' . get_the_post_thumbnail_url($article_data, 'large') . '" alt="Image link to ' . get_the_title($article_data) . ' post">';
		$article_structure .= 			'</a>';
		$article_structure .= 		'</div>';
	}
	$article_structure .= 			'<div class="article-desc article-info">';
	$article_structure .= 				'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$content = wpautop($article_data->post_content);
		$shortened_text = substr($content, 0, strpos($content, '</p>') + 4);
		$article_structure .= 	'<p class="content-teaser">' . strip_tags($shortened_text, '<a>') . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_horizontal_small_image_article($article_data) {
	$article_structure  = '<article class="horizontal-small-image-article article-teaser">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$article_structure .= 		'<div class="article-image">';
		$article_structure .= 			'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 				'<img src="' . get_the_post_thumbnail_url($article_data, 'large') . '" alt="Image link to ' . get_the_title($article_data) . ' post">';
		$article_structure .= 			'</a>';
		$article_structure .= 		'</div>';
	}
	$article_structure .= 			'<div class="grid-container article-desc article-info">';
	$article_structure .= 				'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$content = wpautop($article_data->post_content);
		$shortened_text = substr($content, 0, strpos($content, '</p>') + 4);
		$article_structure .= 	'<p class="content-teaser">' . strip_tags($shortened_text, '<a>') . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_no_image_article($article_data) {
	$article_structure  = '<article class="no-image-article article-teaser">';
	$article_structure .= 	'<div class="article-info">';
	$article_structure .= 		'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3> ';
	$article_structure .= 		'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 	'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$content = wpautop($article_data->post_content);
		$shortened_text = substr($content, 0, strpos($content, '</p>') + 4);
		$article_structure .= 	'<p class="content-teaser">' . strip_tags($shortened_text, '<a>') . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_article_collapsible_image_title($article_data) {
	$article_structure  = '<article class="image-title-article article-teaser">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$image_url = get_the_post_thumbnail_url($article_data, 'large');
		$article_structure .= 		'<div class="article-image">';
		$article_structure .= 			'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 				'<div class="article-image-bg" style="background-image: url(' . $image_url . ');"></div>';
		$article_structure .= 			'</a>';
		$article_structure .= 		'</div>';
		$article_structure .= 		'<div class="article-desc article-info">';
	} else {
		$article_structure .= 		'<div class="grid-container">';
	}
	$article_structure .= 				'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_horizontal_image_title_only_article($article_data) {
	$article_structure  = '<article class="horizontal-image-title-only article-teaser">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	if (!empty(get_the_post_thumbnail_url($article_data))) {
		$article_structure .= 		'<div class="article-image">';
		$article_structure .= 			'<a href="' . get_the_permalink($article_data) . '">';
		$article_structure .= 				'<img src="' . get_the_post_thumbnail_url($article_data, 'large') . '" alt="Image link to ' . get_the_title($article_data) . ' post">';
		$article_structure .= 			'</a>';
		$article_structure .= 		'</div>';
	}
	$article_structure .= 			'<div class="article-desc article-info">';
	$article_structure .= 				'<h3 class="article-title"><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h3>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}
?>
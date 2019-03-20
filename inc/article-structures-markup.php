<?php
// STYLES FOR THESE ARTICLE TEASERS ARE IN _scss/bbg/components/_article-structure-style.scss

function build_main_head_article($article_data) {
	$article_structure  = '<article class="main-head-article article-teaser">';
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= '<div class="feature-article-image">';
		$article_structure .= 	'<a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a>';
		$article_structure .= '</div>';
	}
	$article_structure .= 	'<div class="article-info">';
	$article_structure .= 		'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4> ';
	$article_structure .= 		'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$article_structure .= 	'<p class="excerpt">' . wp_trim_words($article_data->post_content, 60) . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_vertical_article($article_data) {
	$article_structure  = '<article class="vertical-article article-teaser">';
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= '<div class="article-image">';
		$article_structure .= 	'<a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a>';
		$article_structure .= '</div>';
	}
	$article_structure .= 	'<div class="article-info">';
	$article_structure .= 		'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4> ';
	$article_structure .= 		'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
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
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= 			'<div class="article-image">';
		$article_structure .= 				'<a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a>';
		$article_structure .= 			'</div>';
	}
	$article_structure .= 			'</div>';
	$article_structure .= 			'<div class="grid-half">';
	$article_structure .= 				'<div class="article-info">';
	$article_structure .= 					'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4> ';
	$article_structure .= 					'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 				'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$article_structure .= 	'<p class="excerpt">' . wp_trim_words($article_data->post_content, 60) . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
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
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= 			'<div class="article-image"><a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a></div>';
	}
	$article_structure .= 			'<div class="article-desc article-info">';
	$article_structure .= 				'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
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
	if (get_the_post_thumbnail($article_data)) {
		if (!empty(get_the_permalink($article_data))) {
			$article_structure .= 			'<div class="grid-container article-image"><a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a></div>';
		}
	}
	$article_structure .= 			'<div class="grid-container article-desc article-info">';
	$article_structure .= 				'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 		'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
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
	$article_structure .= 		'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4> ';
	$article_structure .= 		'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	if (!empty($article_data->post_excerpt)) {
		$article_structure .= 	'<p class="excerpt">' . $article_data->post_excerpt . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	} else {
		$article_structure .= 	'<p class="excerpt">' . wp_trim_words($article_data->post_content, 60) . ' <a class="read-more" href="' . get_the_permalink($article_data) . '">Read More</a></p>';
	}
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}

function build_image_title_article($article_data) {
	$article_structure  = '<article class="image-title-article article-teaser">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= 			'<div class="article-image"><a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a></div>';
	}
	$article_structure .= 			'<div class="article-desc article-info">';
	$article_structure .= 				'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4>';
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
	if (!empty(get_the_post_thumbnail($article_data))) {
		if (!empty(get_the_permalink($article_data))) {
			$article_structure .= 	'<div class="article-image"><a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data, 'large') . '</a></div>';
		}
	}
	$article_structure .= 			'<div class="article-desc article-info">';
	$article_structure .= 				'<h4><a href="' . get_the_permalink($article_data) . '">' . get_the_title($article_data) . '</a></h4>';
	$article_structure .= 				'<p class="date-meta">' . get_the_date('F j, Y', $article_data) . '</p>';
	$article_structure .= 			'</div>';
	$article_structure .= 		'</div>';
	$article_structure .= 	'</div>';
	$article_structure .= '</article>';
	return $article_structure;
}
?>
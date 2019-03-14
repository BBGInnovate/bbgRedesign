<?php
function build_vertical_post($article_data) {
	$article_structure  = '<article>';
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= '<div class="post-image">';
		$article_structure .= 	'<a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data) . '</a>';
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

function build_aside_post($article_data) {
	$article_structure  = '<article class="article-aside">';
	$article_structure .= 	'<div class="nest-container">';
	$article_structure .= 		'<div class="inner-container">';
	if (!empty(get_the_permalink($article_data))) {
		$article_structure .= 			'<div class="article-image post-image"><a href="' . get_the_permalink($article_data) . '">' . get_the_post_thumbnail($article_data) . '</a></div>';
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
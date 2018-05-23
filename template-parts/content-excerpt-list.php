<?php
/**
 * Template part for displaying excerpts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */


// SET GRID BREAKPOINTS
global $gridClass;
if (!isset($gridClass)) {
	$gridClass = "";
}

// IMAGE INCLUDED BY DEFUALT
global $includeImage;
if (!isset($includeImage)) {
	$includeImage =  TRUE;
}

// DISPLAY BYLINE BY DEFUALT
global $includeMeta;
if (!isset($includeMeta)) {
	$includeMeta = TRUE;
}

// DISPLAY ARTICLE EXCERPT BY DEFAULT
global $includeExcerpt;
if (!isset($includeExcerpt)) {
	$includeExcerpt = TRUE;
}

// ARTICLE, EVENT DATE HIDDEN BY DEFAULT
global $includeDate;
if (!isset($includeDate)) {
	$includeDate = FALSE;
}

//Concatenate misc. classes
$classNames = "bbg-blog__excerpt--list " . $gridClass . " ";

// Define the link to the post
$link = sprintf('<a href="%s" rel="bookmark">', esc_url(get_permalink()));
$linkImage = sprintf('<a href="%s" rel="bookmark" tabindex="-1">', esc_url(get_permalink()));



// MARKUP
$excerpt_list  = '<article id="'. get_the_ID() . '">';
$excerpt_list .= 	'<header>';
if ($includeDate) { // FOR EVENT PAGE EXCERPTS ONLY
	$excerpt_list .= 	'<h5 class="bbg__excerpt__event-date">' . get_the_date() . '</h5>';
}
$excerpt_list .= 		'<h5 class="entry-title bbg-blog__excerpt-title--list ';
if ($includeDate) {
	$excerpt_list .= 		'bbg__excerpt-title--showDate';
}
$excerpt_list .= 		'">';
$excerpt_list .= 			'<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
$excerpt_list .= 		'</h5>';
$excerpt_list .= 	'</header>';
// if ($includeImage && has_post_thumbnail()) {
// 	$excerpt_list .= '<div class="single-post-thumbnail clear bbg__excerpt-header__thumbnail--small">';
// 	$excerpt_list .= 	$linkImage;
// 	$excerpt_list .= 		the_post_thumbnail('small-thumb');
// 	$excerpt_list .= 	'</a>';
// 	$excerpt_list .= '</div>';
// }
if ('post' === get_post_type()) {
	if ($includeMeta) {
		$excerpt_list .= '<div class="entry-meta bbg__excerpt-meta">';
		$excerpt_list .= 	bbginnovate_posted_on();
		$excerpt_list .= '</div>';
	}
}
if ($includeExcerpt) {
	$excerpt_list .= '<div class="entry-content bbg-blog__excerpt-content">';
	$excerpt_list .= 	'<p>' . get_the_excerpt() . '</p>';
						// wp_link_pages(array(
						// 	'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
						// 	'after'  => '</div>',
						// ));
	$excerpt_list .= '</div>';
}
$excerpt_list .= '</article>';
echo $excerpt_list;
?>
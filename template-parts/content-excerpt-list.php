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
if ($includeDate) { // FOR EVENT PAGE EXCERPTS ONLY
	$excerpt_list .= 	'<p class="aside date-meta">' . get_the_date() . '</p>';
}
$excerpt_list .= 		'<h6>';
$excerpt_list .= 			'<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
$excerpt_list .= 		'</h6>';

if ('post' === get_post_type()) {
	if ($includeMeta) {
		$excerpt_list .= '<div class="entry-meta bbg__excerpt-meta">';
		$excerpt_list .= 	bbginnovate_posted_on();
		$excerpt_list .= '</div>';
	}
}
if ($includeExcerpt) {
	$excerpt_list .= '<div class="entry-content bbg-blog__excerpt-content">';
	$excerpt_list .= 	'<p class="aside">' . get_the_excerpt() . '</p>';
	$excerpt_list .= '</div>';
}
$excerpt_list .= '</article>';
echo $excerpt_list;
?>
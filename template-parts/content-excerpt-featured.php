<?php
/**
 * Template part for displaying a featured excerpt.
 * Large full width photo and large excerpt text.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */

//The byline meta info is displayed by default
global $includeMetaFeatured;
if (! isset ($includeMetaFeatured)) {
	$includeMetaFeatured = true;
}

$postPermalink = esc_url( get_permalink() );

/*** the only way you should ever have a future post status here is if a future event is featured on the homepage */
if (get_post_status() == 'future') {
	global $post;
	$my_post = clone $post;
	$my_post->post_status = 'published';
	$my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
	$postPermalink = get_permalink($my_post);
}

if (isset($_GET['category_id'])) {
	$postPermalink = add_query_arg('category_id', $_GET['category_id'], $postPermalink);
}

// ADD FEATURED VIDEO
$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
$hideFeaturedImage = false;


// MARKUP
$featured_post .= '<article id="' . get_the_ID() . '">';
$hideFeaturedImage = false;
if ($videoUrl != "") {
	$hideFeaturedImage = true;
	$featured_post .= featured_video($videoUrl);
}
elseif (has_post_thumbnail() && ($hideFeaturedImage != 1)) {
	$featuredImageClass = "";
	$featuredImageCutline = "";
	$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id(get_the_ID()), 'post_type' => 'attachment'));

	if ($thumbnail_image && isset($thumbnail_image[0])) {
		$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
	}
	$featured_post .= '<a href="' . $postPermalink . '" rel="bookmark">';
	$featured_post .= 		the_post_thumbnail( 'large-thumb' );
	$featured_post .= '</a>';
	$featured_post .= '<h4><a href="' . $postPermalink . '" rel="bookmark">';
	$featured_post .= 	get_the_title();
	$featured_post .= '</a></h4>';
	if ($includeMetaFeatured) {
		$featured_post .= bbginnovate_posted_on();
	}
	$featured_post .= '<p>' . get_the_excerpt() . '</p>';
						wp_link_pages(array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
							'after'  => '</div>',
						));
	$featured_post .= '</article>';
	echo $featured_post;
}
?>
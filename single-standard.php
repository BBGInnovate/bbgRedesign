<?php
/**
 * The template for displaying standard single project posts.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package bbgRedesign
 */

// RESET VARS FOR OG TAGS
if (have_posts()) {
	the_post();
	$metaAuthor = get_the_author();
	$ogTitle = get_the_title();

	$metaKeywords = strip_tags(get_the_tag_list('', ', ', ''));

	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'Full');
	$ogImage = $thumb['0'];

	$socialImageID = get_post_meta($post->ID, 'social_image', true);
	$coordinates = get_post_meta($post->ID, 'media_dev_coordinates', true);

	if ($socialImageID) {
		$socialImage = wp_get_attachment_image_src($socialImageID , 'Full');
		$ogImage = $socialImage[0];
	}

	$ogDescription = get_the_excerpt();
	rewind_posts();
}

get_header();

while (have_posts()) {
	the_post();
	get_template_part('template-parts/content', 'single'); 
}

get_sidebar();
get_footer();
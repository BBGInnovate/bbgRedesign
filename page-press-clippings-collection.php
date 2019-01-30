<?php
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Press Clippings Collection
 */

/* 
 * THIS IS NOT A TEMPLATE. 
 * THIS IS A LIST OF SORTED ARTICLES 
 * FROM THE SIDEBAR FROM page-press-clippings-archive.php 
 */

include 'inc/functions-press-clippings.php';

// 1. GET CLIP TYPE SELECTION (URL PARAMETER) FROM page-press-clippings-archive.php 
if (!empty($_GET['clip-type'])) {
	$press_clip_type = htmlspecialchars($_GET['clip-type']);
}
$dash = '-';
if (\strpos($press_clip_type, $dash) !== false) {
	$press_clip_type = str_replace($dash, ' ', $press_clip_type);
	$press_clip_type = explode(' ', $press_clip_type);
}
if (is_array($press_clip_type)) {
	if ($press_clip_type[0] == 'citation') {
		$field_key = 'media_clip_citation';
	} else if ($press_clip_type[0] == 'about') {
		$field_key = 'media_clip_about';
	}
}

// 2. SET PAGINATION FOR PRESS CLIPPINGS LIST
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// USE SELECTED CLIP TYPE TO GET QUERY ARGS TO CALL CORRESPONDING CITED POSTS, 
// WHETER ABOUT US OR CITED, AND MAKE QUERY
if ($press_clip_type == 'interest') {
	$press_clip_query_args = array(
		'post_type' => 'media_clips',
		'meta_query'	=> array(
			'relation' => 'OR',
			array(
				'key' => 'media_clip_type',
				'value' => 'ofInterest',
				'compare' => 'LIKE'
			)
		)
	);
} else if (is_array($press_clip_type)) {
	$press_clip_query_args = array(
		'post_type' => 'media_clips',
		'posts_per_page' => 5,
		'paged' => $paged,
		'meta_query'	=> array(
			'relation' => 'OR',
			array(
				'key' => 'media_clip_type',
				'value' => $press_clip_type[0],
				'compare' => 'LIKE'
			)
		),
		'meta_query'	=> array(
			'relation' => 'OR',
			array(
				'key' => $field_key,
				'value' => $press_clip_type[1],
				'compare' => 'LIKE'
			)
		)
	);
}
$all_media_clips = new WP_Query($press_clip_query_args);

// 3. GO TO functions.php AND PERFORM FUNCTION, RETURN THE POST'S DATA
$press_clippings_data = request_media_query_data($all_media_clips);

if ($press_clip_type[1] == 'rferl') {
	$press_clip_type[1] = 'rfe/rl';
}

get_header();
?>

<main id="main"  role="main">

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						echo '<heading>';
						if (is_array($press_clip_type)) {
							if ($press_clip_type[0] == 'about') {
								echo 	'<h2>Press Clippings ' . $press_clip_type[0] . ' ' . $press_clip_type[1] . '</h2>';
							} else if ($press_clip_type[0] == 'citation') {
								echo 	'<h2>Press Clippings with ' . $press_clip_type[1] . ' ' . $press_clip_type[0] . 's</h2>';
							}
						} else {
							echo '<h2>Press Clippings Of Interest</h2>';
						}
						echo '</heading>';
						if (!empty($press_clippings_data)) {
							foreach ($press_clippings_data as $cited_post) {
								$cur_cited_post  = '<article>';
								$cur_cited_post .= 	'<h4><a href="' . $cited_post['story_link'] . '" target="_blank">' . $cited_post['title'] . '</a></h4>';
								$cur_cited_post .= 	'<p class="paragraph-header">' . $cited_post['outlet'] . ' &nbsp;<span class="aside">' . $cited_post['date'] . '</span></p>';
								$cur_cited_post .= 	'<p>' . $cited_post['description'] . '</p>';
								$cur_cited_post .= '</article>';
								echo $cur_cited_post;
							}
							if ($press_clippings_data >= 5) {
								next_posts_link('Older Entries', $cited_post['query_var']->max_num_pages);
								previous_posts_link('Newer Entries');
							}
						}
					?>
				</div>

				<div class="side-content-container">
					<?php
						echo '<h5>Press Clipping Collections</h5>';
						$clip_types = array('About Networks', 'Citations', 'Of Interest');
						$entity_dropdown = build_media_clips_entity_dropdown($clip_types);
						echo $entity_dropdown;
					?>
				</div>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
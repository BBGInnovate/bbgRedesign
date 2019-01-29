<?php
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Citing List
 */

/* 
 * THIS IS NOT A TEMPLATE. 
 * THIS IS A LIST OF ARTICLES 
 * FROM THE LINKS FROM page-press-list.php 
 */

include 'inc/media-clips-reference.php';

// 1. GET ENTITY SELECTION FRO page-press-list.php
if (!empty($_GET['entity'])) {
	$selected_entity = htmlspecialchars($_GET['entity']);
}

// 2. SET PAGINATION FOR PRESS CLIPPINGS LIST
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// USE SELECTED ENTITY TO GET QUERY ARGS TO CALL CORRESPONDING CITED POSTS, 
// WHETER ABOUT US OR CITED, AND MAKE QUERY
if ($selected_entity == 'usagm') {
	$press_clip_query_args = array(
		'post_type' => 'media_clips',
		'meta_query'	=> array(
			'relation' => 'OR',
			array(
				'key' => 'outlet_category',
				'value' => 'about_us',
				'compare' => 'LIKE'
			)
		)
	);
} else {
	$press_clip_query_args = array(
		'post_type' => 'media_clips',
		'posts_per_page' => 5,
		'paged' => $paged,
		'meta_query'	=> array(
			'relation' => 'OR',
			array(
				'key' => 'outlet_citations',
				'value' => $selected_entity,
				'compare' => 'LIKE'
			)
		)
	);
}
$all_media_clips = new WP_Query($press_clip_query_args);

// 3. GO TO functions.php AND PERFORM FUNCTION, RETURN THE POST'S DATA
$press_clippings_data = request_media_query_data($all_media_clips);

get_header();
?>

<main id="main"  role="main">

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						if ($selected_entity == 'usagm') {
							echo '<h2>Press Clips about USAGM</h2>';
						}
						else {
							echo '<h2>' . $selected_entity . ' Cited Press Clips</h2>';
						}
						foreach ($press_clippings_data as $cited_post) {
							$cur_cited_post  = '<article>';
							$cur_cited_post .= 	'<h4><a href="' . $cited_post['story_link'] . '" target="_blank">' . $cited_post['title'] . '</a></h4>';
							$cur_cited_post .= 	'<p class="paragraph-header">' . $cited_post['outlet'] . ' &nbsp;<span class="aside">' . $cited_post['date'] . '</span></p>';
							$cur_cited_post .= 	'<p>' . $cited_post['description'] . '</p>';
							$cur_cited_post .= '<article>';
							echo $cur_cited_post;
						}
						if ($press_clippings_data >= 5) {
							next_posts_link('Older Entries', $cited_post['query_var']->max_num_pages);
							previous_posts_link('Newer Entries');
						}
					?>
				</div>

				<div class="side-content-container">
					<?php
						echo '<h5>Press Clippings Group</h5>';
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
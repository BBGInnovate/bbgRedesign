<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Press Clippings List
 */

// START SESSION TO CARRY SELECTION OF CITED ENTITY TO page-press-citing.php TO DYNAMICALLY POPULATE WITH POSTS
// session_start();

// COLLECT ALL PRESS CLIPPINGS AND SET PAGINATION
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$all_media_clips = new WP_Query(
	array(
		'post_type' => 'media_clips',
		'posts_per_page' => 5,
		'paged' => $paged
	)
);
if ($all_media_clips->have_posts()) {
	$posts_set = array();
	while ($all_media_clips->have_posts()) {
		$all_media_clips->the_post();

		$press_post_id = get_the_id();
		$press_post_title = get_the_title();
		$press_post_outlet = get_post_meta($press_post_id, 'media_clip_outlet', true);
		$press_post_outlet_link = get_post_meta($press_post_id, 'media_clip_story_url', true);
		$term_data = get_term($press_post_outlet);
		$outlet_name = $term_data -> name;
		$press_post_date = get_post_meta($press_post_id, 'media_clip_published_on', true);
		$press_post_description = wp_trim_words(get_the_content(), 40);

		$press_post_data = array(
			'title' => $press_post_title,
			'outlet' => $outlet_name,
			'story_link' => $press_post_outlet_link,
			'date' => get_the_date(),
			'description' => $press_post_description
		);
		array_push($posts_set, $press_post_data);
	}
	wp_reset_postdata();
}

get_header();
?>
<main id="main"  role="main">
	<div class="outer-container">
		<div class="grid-container">
			<h2>Press Clippings</h2>
			<p class="leadin">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
		</div>
	</div>

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						foreach ($posts_set as $press_post) {
							$cur_press_post  = '<article>';
							$cur_press_post .= '<h4>' . $press_post['title'] . '</h4>';
							$cur_press_post .= '<p class="paragraph-header"><a href="' . $press_post['story_link'] . '" target="_blank">' . $press_post['outlet'] . '</a> &nbsp;<span class="aside">' . $press_post['date'] . '</span></p>';
							$cur_press_post .= '<p>' . $press_post['description'] . '</p>';
							$cur_press_post .= '<article>';
							echo $cur_press_post;
						}
						next_posts_link('Older Entries', $all_media_clips->max_num_pages);
						previous_posts_link('Newer Entries');
					?>
				</div>
				<div class="side-content-container">
					<h5>Citings</h5>
					<?php
						$network_citings = outputBroadcasters(1, "citing");
						if (!empty($network_citings)) {
							echo $network_citings;
						}
					?>
				</div>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
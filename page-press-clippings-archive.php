<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Press Clippings Archive
 */

include 'inc/functions-press-clippings.php';

// COLLECT ALL PRESS CLIPPINGS AND SET PAGINATION
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$press_clip_query_args = array(
	'post_type' => 'media_clips',
	'posts_per_page' => 5,
	'paged' => $paged
);
$all_media_clips = new WP_Query($press_clip_query_args);

// GO TO functions.php AND PERFORM FUNCTION, RETURN THE POST'S DATA
$press_clippings_data = request_media_query_data($all_media_clips);

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
		$page_title = get_the_title();
	}
}

get_header();
?>
<main id="main"  role="main">
	<section class="outer-container">
		<header class="grid-container">
			<h2><?php echo $page_title; ?></h2>
		</header>
	</section>

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<section class="main-content-container">
					<h1 class="header-outliner">All Press Clipping Posts</h1>
					<?php
						foreach ($press_clippings_data as $press_post) {
							$cur_press_post  = '<article>';
							$cur_press_post .= 	'<header>';
							$cur_press_post .= 		'<h4><a href="' . $press_post['story_link'] . '" target="_blank">' . $press_post['title'] . '</a></h4>';
							$cur_press_post .= 	'</header>';
							$cur_press_post .= 	'<p class="paragraph-header">' . $press_post['outlet'] . ' &nbsp;<span class="aside">' . $press_post['date'] . '</span></p>';
							$cur_press_post .= 	'<p>' . $press_post['description'] . '</p>';
							$cur_press_post .= '</article>';
							echo $cur_press_post;
						}
						if ($press_clippings_data >= 5) {
							next_posts_link('Older Entries', $press_post['query_var']->max_num_pages);
							previous_posts_link('Newer Entries');
						}
					?>
				</section>

				<aside class="side-content-container">
					<?php
						echo '<h5>Press Clipping Collections</h5>';
						$clip_types = array('About Networks', 'Citations', 'Of Interest');
						$entity_dropdown = build_media_clips_entity_dropdown($clip_types);
						echo $entity_dropdown;
					?>
				</aside>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
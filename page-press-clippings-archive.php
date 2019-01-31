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

// GO TO functions-press-clippings.php AND RETURN THE POST'S DATA
$press_clippings_data = request_media_query_data($all_media_clips);

// COLLECT DATA FROM THIS MAIN PAGE
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
					<h2 class="header-outliner">All Press Clipping Posts</h2>
					<?php
						if (!empty($press_clippings_data)) {
							foreach ($press_clippings_data as $press_clip) {
								$cur_cited_post = build_press_clipping_article_list($press_clip);
								echo $cur_cited_post;
							}
							if ($press_clippings_data >= 5) {
								next_posts_link('Older Entries', $press_clip['query_var']->max_num_pages);
								previous_posts_link('Newer Entries');
							}
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
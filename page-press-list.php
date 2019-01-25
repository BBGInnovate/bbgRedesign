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

$press_clip_query_args = array(
	'post_type' => 'media_clips',
	'posts_per_page' => 5,
	'paged' => $paged
);
$all_media_clips = new WP_Query($press_clip_query_args);

// GO TO functions.php AND PERFORM FUNCTION, RETURN THE POST'S DATA
$press_clippings_data = request_media_query_data($all_media_clips);

get_header();
?>
<main id="main"  role="main">
	<div class="outer-container">
		<div class="grid-container">
			<h2>Press Clippings</h2>
			<!-- <p class="leadin">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
		</div>
	</div>

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						foreach ($press_clippings_data as $press_post) {
							$cur_press_post  = '<article>';
							$cur_press_post .= 	'<h4><a href="' . $press_post['story_link'] . '" target="_blank">' . $press_post['title'] . '</a></h4>';
							$cur_press_post .= 	'<p class="paragraph-header">' . $press_post['outlet'] . ' &nbsp;<span class="aside">' . $press_post['date'] . '</span></p>';
							$cur_press_post .= 	'<p>' . $press_post['description'] . '</p>';
							$cur_press_post .= '<article>';
							echo $cur_press_post;
						}
						if ($press_clippings_data >= 5) {
							next_posts_link('Older Entries', $press_post['query_var']->max_num_pages);
							previous_posts_link('Newer Entries');
						}
					?>
				</div>

				<div class="side-content-container">
					<h5>Sort Articles</h5>
					<p class="aside">Group articles by category and entity.</p>

					<h6>ABOUT US</h6>
					<?php
						$usagm_icon  = '<div class="sidebar-entities">';
						$usagm_icon .= 	'<div class="inner-container">';
						$usagm_icon .= 		'<div class="entity-image-side">';
						$usagm_icon .= 			'<img src="' . get_template_directory_uri() . '/img/logo_usagm--circle-200.png">';
						$usagm_icon .= 		'</div>';
						$usagm_icon .= 		'<div class="entity-text-side">';
						$usagm_icon .= 			'<h4 class="entity-title">';
						$usagm_icon .= 				'<a href="' . add_query_arg('entity', 'usagm', '/press-citing-listing/') . '">U.S. Agency for Global Media</a>';
						$usagm_icon .= 			'</h4>';
						$usagm_icon .= 		'</div>';
						$usagm_icon .= 	'</div>';
						$usagm_icon .= '</div>';
						echo $usagm_icon;
					?>

					<h6>NETWORK CITINGS</h6>
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
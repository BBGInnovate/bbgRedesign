<?php
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Citing List
 */

/* THIS IS NOT A TEMPLATE. THIS IS A LIST OF ARTICLES FROM THE LINKS FROM page-press-list.php */
if (!empty($_GET['entity'])) {
	$selected_entity = htmlspecialchars($_GET['entity']);
}

// USE $selected_entity TO GET ALL CITED POSTS FOR THAT SPECIFIC ENTITY
if ($selected_entity == 'usagm') {
	$outlet_args = array(
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
}
else {
	$outlet_args = array(
		'post_type' => 'media_clips',
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
$outlet_post_list = new WP_Query($outlet_args);

if ($outlet_post_list->have_posts()) {
	$citing_post_list = array();
	while ($outlet_post_list->have_posts()) {
		$outlet_post_list->the_post();

		$cited_post_id = get_the_ID();
		$press_post_date = get_post_meta($press_post_id, 'media_clip_published_on', true);
		$date = new DateTime($press_post_date);
		$press_post_date = $date->format('F d, Y');

		$cited_post_data = array(
			'title' => get_the_title(),
			'story_link' => get_post_meta($press_post_id, 'media_clip_story_url', true),
			'date' => $press_post_date,
			'description' => wp_trim_words(get_the_content(), 40)
		);
		array_push($citing_post_list, $cited_post_data);
	}
	wp_reset_postdata();
}

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
						foreach ($citing_post_list as $cited_post) {
							$cur_cited_post  = '<article>';
							$cur_cited_post .= 	'<h4><a href="' . $cited_post['story_link'] . '" target="_blank">' . $cited_post['title'] . '</a></h4>';
							$cur_cited_post .= 	'<p class="paragraph-header">' . $cited_post['outlet'] . ' &nbsp;<span class="aside">' . $cited_post['date'] . '</span></p>';
							$cur_cited_post .= 	'<p>' . $cited_post['description'] . '</p>';
							$cur_cited_post .= '<article>';
							echo $cur_cited_post;
						}
						// next_posts_link('Older Entries', $all_media_clips->max_num_pages);
						// previous_posts_link('Newer Entries');
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
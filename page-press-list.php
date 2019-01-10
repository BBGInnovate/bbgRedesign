<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Press Clippings List
 */


// COLLECT ALL PRESS CLIPPINGS AND SET PAGINATION
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$the_query = new WP_Query(
	array(
		'post_type' => 'media_clips',
		'posts_per_page' => 5,
		'paged' => $paged
	)
);
if ($the_query->have_posts()) {
	$posts_set = array();
	while ($the_query->have_posts()) {
		$the_query->the_post();

		$press_post_id = get_the_id();
		$press_post_title = get_the_title();
		$press_post_outlet = get_post_meta($press_post_id, 'media_clip_outlet', true);
		$term_data = get_term($press_post_outlet);
		$outlet_name = $term_data -> name;
		$press_post_date = get_post_meta($press_post_id, 'media_clip_published_on', true);
		$press_post_description = wp_trim_words(get_the_content(), 25);

		$press_post_data = array(
			'title' => $press_post_title,
			'outlet' => $outlet_name,
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
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<h2>Press Clippings</h2>
					<p class="leadin">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					<?php
						foreach ($posts_set as $press_post) {
							$cur_press_post  = '<article>';
							$cur_press_post .= '<h4>' . $press_post['title'] . '</h4>';
							$cur_press_post .= '<p class="paragraph-header">' . $press_post['outlet'] . ' <span class="aside">' . $press_post['date'] . '</span></p>';
							$cur_press_post .= '<p>' . $press_post['description'] . '</p>';
							$cur_press_post .= '<article>';
							echo $cur_press_post;
						}
						next_posts_link('Older Entries', $the_query->max_num_pages);
						previous_posts_link('Newer Entries');
					?>
				</div>
				<div class="side-content-container">
					<h5>Outlets</h5>
					<?php
						$all_taxonomies = get_terms([
								'taxonomy' => 'outlet',
								'hide_empty' => false,
							]);
						
						foreach ($all_taxonomies as $tax_term) {
							echo '<h6>' . $tax_term->name . '</h6>';
						}
						// echo $all_taxonomies;
					?>
				</div>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
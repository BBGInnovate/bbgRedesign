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

// 1a. GET PRESS CLIPPING TYPE SELECTION FROM URL PARAMETER
if (!empty($_GET['clip-type'])) {
	$press_clip_type = htmlspecialchars($_GET['clip-type']);
}
if (!empty($_GET['clip-entity'])) {
	$press_clip_entity = htmlspecialchars($_GET['clip-entity']);
}
// 2a. GET OUTLET SELECTION FROM URL PARAMETER FROM POSTS
if (!empty($_GET['outlet-name'])) {
	$cur_outlet_slug = htmlspecialchars(urldecode($_GET['outlet-name']));
	$cur_outlet_data = get_term_by('slug', $cur_outlet_slug, 'outlet');
	$cur_outlet_id = $cur_outlet_data -> term_id;
	$cur_outlet_name = $cur_outlet_data -> name;
}

// SET PAGINATION FOR PRESS CLIPPINGS LIST
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// 1b. USE SELECTED CLIP TYPE TO GET QUERY ARGS TO CALL CORRESPONDING CITED POSTS, 
// WHETER ABOUT US, CITED, OR OF INTEREST AND MAKE QUERY
if (!empty($press_clip_type) && empty($cur_outlet_id)) {
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

	if ($press_clip_type == 'interest') {
		$press_clip_query_args = array(
			'post_type' => 'media_clips',
			'meta_query' => array(
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
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'media_clip_type',
					'value' => $press_clip_type[0],
					'compare' => 'LIKE'
				)
			),
			'meta_query' => array(
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

	// 1b. GO TO functions-press-clippings.php AND PERFORM FUNCTION, RETURN THE POST'S DATA
	$press_clippings_data = request_media_query_data($all_media_clips);

	if ($press_clip_type[1] == 'rferl') {
		$press_clip_type[1] = 'rfe/rl';
	}
}

// 2b. PROCESS PRESS CLIPPING OUTLET SELECTION (URL PARAMETER)
if (!empty($cur_outlet_id)) {
	$press_clip_query_args = array(
		'post_type' => 'media_clips',
		'posts_per_page' => 5,
		'paged' => $paged,
		'tax_query' => array(
			array (
				'taxonomy' => 'outlet',
				'field' => 'slug',
				'terms' => $cur_outlet_name,
			)
		),
	);
	$all_media_clips = new WP_Query($press_clip_query_args);

	$press_clippings_data = request_media_query_data($all_media_clips);
}

get_header();
?>

<main id="main"  role="main">
	<?php if (empty($press_clip_type) && empty($cur_outlet_id)) { ?>
	<?php //HEADER OPITON FOR PLAIN ARCHIVE ?>
		<section class="outer-container">
			<header class="grid-container">
				<h2><?php echo $page_title; ?></h2>
			</header>
		</section>
	<?php } ?>

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<section class="main-content-container">
					<?php
					echo '<heading>';
						if (!empty($press_clip_type)) {
							// HEADER OPTIONS FOR PRESS CLIPS TYPES
							if (is_array($press_clip_type)) {
								if ($press_clip_type[0] == 'about') {
									echo 	'<h2>Press Clippings ' . $press_clip_type[0] . ' ' . $press_clip_type[1] . '</h2>';
								} else if ($press_clip_type[0] == 'citation') {
									echo 	'<h2>Press Clippings with ' . $press_clip_type[1] . ' ' . $press_clip_type[0] . 's</h2>';
								}
							} else if ($press_clip_type == 'interest' && empty($cur_outlet_id)) {
								echo '<h2>Press Clippings Of Interest</h2>';
							}
							// HEADER OPTIONS FOR PRESS CLIPS TYPE FROM OUTLETS
							if (!empty($cur_outlet_id)) {
								if ($press_clip_type == 'about') {
									echo 	'<h2>Press Clippings ' . $press_clip_type . ' ' . $press_clip_entity . ' from ' . $cur_outlet_name . '</h2>';;
								} else if ($press_clip_type == 'citation') {
									echo 	'<h2>Press Clippings with ' . $press_clip_entity . ' ' . $press_clip_type . 's from ' . $cur_outlet_name . '</h2>';;
								} else if ($press_clip_type == 'interest') {
									echo 	'<h2>Press Clippings Of Interest from ' . $cur_outlet_name . '</h2>';
								}
							}
						}
						else if (!empty($cur_outlet_id) && empty($press_clip_type)) {
							echo 	'<h2>Press Clippings from ' . $cur_outlet_name . '</h2>';
						}
						echo '</heading>';

						if (!empty($press_clippings_data)) {
							foreach ($press_clippings_data as $press_clip) {
								if ($press_clip_type) {
									$cur_cited_post = build_press_clipping_article_list($press_clip, $press_clip_type);
								} else {
									$cur_cited_post = build_press_clipping_article_list($press_clip);
								}
								echo $cur_cited_post;
							}
							if ($press_clippings_data >= 5) {
								next_posts_link('Older Entries', $press_clip['query_var']->max_num_pages);
								previous_posts_link('Newer Entries');
							}
						} else {
							$entity = strtoupper($press_clip_type[1]);
							if ($press_clip_type[0] == 'about') {
								echo '<p class="leadin">There are currently no press clippings about ' . $entity . '<p>';
							} else if ($press_clip_type[0] == 'citation') {
								echo '<p class="leadin">There are currently no press clippings with ' . $entity . ' citations<p>';
							} else if ($press_clip_type[0] == 'interst') {
								echo '<p class="leadin">There are currently no press clippings from ' . $entity . ' of interest<p>';
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

						echo '<h5>Media Outlet Tags</h5>';
						$tax_cloud_args = array(
							'taxonomy' 					=> 'outlet',
							'smallest' 					=> 8,
							'largest' 					=> 22,
							'unit'                      => 'pt', 
							'format'                    => 'flat',
							'separator'                 => ', ',
							'echo'                      => true,
							'show_count'                  => 0,
						);
						wp_tag_cloud($tax_cloud_args);
					?>
				</aside>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
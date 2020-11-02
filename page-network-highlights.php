<?php
/**
 * The template for displaying highlights from across the 5 BBG entities.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Network Highlights
 */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_id = get_the_ID();
		$page_title = get_the_title();
	}
}
wp_reset_postdata();
wp_reset_query();

// GET PRESS RELEASES FOR EACH ENTITY
$entities = ['usagm', 'voa', 'rferl', 'ocb', 'rfa', 'mbn', 'otf'];
$entity_data = array();
foreach ($entities as $cur_entity) {
	$entity_title = $cur_entity;
	$entity_slug = $cur_entity;

	if ($entity_slug != "") {
		$prCategoryObj = get_category_by_slug($entity_slug);
		if (is_object($prCategoryObj)) {
			$prCategoryID = $prCategoryObj->term_id;
			$press_release_params = array(
				'post_type' => array('post'),
				'posts_per_page' => 5,
				'category__and' => array(
						$prCategoryID,
						get_cat_ID('Press Release')
				),
				'orderby', 'date',
				'order', 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => 'post-format-quote',
						'operator' => 'NOT IN'
					)
				)
			);
			$press_release_query = new WP_Query($press_release_params);
			if ($press_release_query->have_posts()) {
				$entity_press_releases = $press_release_query->posts;
			}
			wp_reset_postdata();
			wp_reset_query();

			$entity_group = array(
				'entity-title' => $entity_title,
				'entity-slug' => $entity_slug,
				'press-releases' => $entity_press_releases,
			);
			$entity_data[] = $entity_group;
		}
	}
}

$pageTagline = get_post_meta(get_the_ID(), 'page_tagline', true);
if ($pageTagline && $pageTagline!=""){
	$pageTagline = '<p class="lead-in">' . $pageTagline . '</p>';
}

get_header();

$featured_media_result = get_feature_media_data();
if ($featured_media_result != "") {
	echo $featured_media_result;
}
?>

<main id="main" role="main">
	<div class="outer-container">
		<div class="grid-container">
			<header class="page-header">
				<h2 class="section-header"><?php echo $page_title; ?></h2>
				<?php echo $pageTagline; ?>
			</header>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
					<?php
						$pressReleaseCatId = get_category_by_slug('press-release')->term_id;
						foreach($entity_data as $entity) {
							$cat_slug = get_category_by_slug($entity['entity-title']);
							$cat_id = $cat_slug->term_id;
							if ($entity['entity-title'] == 'rferl') {
								$entity['entity-title'] = 'rfe/rl';
							}
							$press_release_markup  = '<div class="inner-container entity-press-release-group">';
							$press_release_markup .= 	'<div class="main-column">';
							$press_release_markup .= 		'<header>';
							$press_release_markup .= 			'<h3 class="section-subheader">';
							$press_release_markup .= 				'<a href="' . get_the_permalink(get_page_by_path('news-and-information/press-releases/' . $entity['entity-slug'])) . '">' . strtoupper($entity['entity-title']) . '</a>';
							$press_release_markup .= 			'</3>';
							$press_release_markup .= 		'</header>';
							$press_release_markup .= 		'<div class="entity-press-release">';
							$press_release_markup .= 			'<header>';
							$press_release_markup .= 				'<h4 class="article-title">';
							$press_release_markup .=  					'<a href="' . get_the_permalink($entity['press-releases'][0]->ID) . '">' . $entity['press-releases'][0]->post_title . '</a>';
							$press_release_markup .= 				'</h4>';
							$press_release_markup .= 			'<header>';
							$press_release_markup .= 			'<p class="date-meta">' . get_the_date('F j, Y', $entity['press-releases'][0]->ID) . '</p>';

							$thumbnail = get_the_post_thumbnail_url($entity['press-releases'][0]->ID, 'large');
							if ($thumbnail) {
								$press_release_markup .= 		'<div class="entity-press-release-image">';
								$press_release_markup .= 			'<a href="' . get_the_permalink($entity['press-releases'][0]->ID) . '">';
								$press_release_markup .= 				'<img src="' . $thumbnail . '" alt="Image link to ' . get_the_title($entity['press-releases'][0]->ID) . ' post">';
								$press_release_markup .= 			'</a>';
								$press_release_markup .= 		'</div>';
							}


							$press_release_markup .= 			'<p>' . $entity['press-releases'][0]->post_excerpt . '</p>';
							$press_release_markup .= 		'</div>';
							$press_release_markup .= 	'</div>';
							$press_release_markup .= 	'<div class="side-column divider-left">';
							array_shift($entity['press-releases']);
							foreach($entity['press-releases'] as $addtl_release) {
								$press_release_markup .= 	'<div class="entity-press-release">';
								$press_release_markup .= 		'<header>';
								$press_release_markup .= 			'<h4 class="sidebar-article-title">';
								$press_release_markup .=  				'<a href="' . get_the_permalink($addtl_release->ID) . '">' . $addtl_release->post_title . '</a>';
								$press_release_markup .= 			'</h4>';
								$press_release_markup .= 		'</header>';
								$press_release_markup .= 		'<p class="date-meta">' . get_the_date('F j, Y', $addtl_release->ID) . '</p>';
								$press_release_markup .= 	'</div>';
							}
							$press_release_markup .= 	'<p class="read-more"><a href="' . get_the_permalink(get_page_by_path('news-and-information/press-releases/' . $entity['entity-slug'])) . '">Read more ' . strtoupper($entity['entity-title']) . ' news</a></p>';
							$press_release_markup .= 	'</div>';
							$press_release_markup .= '</div>';

							echo $press_release_markup;
						}
					?>
			</div>
		</div>
	</div>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
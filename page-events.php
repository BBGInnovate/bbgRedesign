<?php
/**
 * The template for displaying upcoming + previous events.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Events
 */

require 'inc/bbg-functions-assemble.php';

/***** BEGIN EVENT PAGINATION LOGIC 
There are some nuances to this.  Note that we're not using the paged parameter because we don't have the same number of posts on every page.  Instead we use the offset parameter.  The 'posts_per_page' limits the number displayed on the current page and is used to calculate offset.
http://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
****/

$featuredEvent = get_field('homepage_featured_event', 'option');
$showFeaturedEvent = get_field('show_homepage_event', 'option');

$currentPage =  (get_query_var('paged')) ? get_query_var('paged') : 1;

$postIDsUsed = array();

$numPostsFirstPage = 10;
$numPostsSubsequentPages = 9;

$postsPerPage = $numPostsFirstPage;
$offset = 0;
if ($currentPage > 1) {
	$postsPerPage = $numPostsSubsequentPages;
	$offset = $numPostsFirstPage + ($currentPage - 2) * $numPostsSubsequentPages;
}

$hasTeamFilter = false;

// QUERY TO GET FIRST POST - EITHER FEATURED OR FIRST REVERSE CHRON
if ($showFeaturedEvent && $featuredEvent && has_category('event', $featuredEvent)) {
	$qParamsFirst=array(
		'p' => $featuredEvent->ID,
		'post_status' => array('publish', 'future')
	);
} else {
	$qParamsFirst=array(
		'post_type' => array('post'),
		'cat' => get_cat_id('Event'),
		'posts_per_page' => 1,
		'post_status' => array('publish')
	);
}
 
$featured_event_query = new WP_Query($qParamsFirst);
while ($featured_event_query->have_posts()) {
	$featured_event_query->the_post(); 
	// $postIDsUsed[] = get_the_ID();
}

// QUERY PAST EVENTS FOR MAIN PAGE LOOP
$qParams = array(
	'post_type' => array('post'),
	'cat' => get_cat_id('Event'),
	'posts_per_page' => $postsPerPage,
	'offset' => $offset,
	'post_status' => array('publish'),
	'post__not_in' => $postIDsUsed
);
// KR EDIT
// var_dump($qParams);
$past_events_query_args = $qParams;
$past_events_query = new WP_Query($past_events_query_args);

$totalPages = 1;
if ($past_events_query->found_posts > $numPostsFirstPage) {
	$totalPages = 1 + ceil(($past_events_query->found_posts - $numPostsFirstPage) / $numPostsSubsequentPages);
}

/**** QUERY FUTURE EVENTS + BUILD STRING  ***/
$qParamsUpcoming = array(
	'post_type' => array('post'),
	'cat' => get_cat_id('Event'),
	'posts_per_page' => $postsPerPage,
	'offset' => $offset,
	'post_status' => array('future'),
	'order' => 'ASC',
	'post__not_in' => $postIDsUsed
);

$future_events_query_args = $qParamsUpcoming;
$future_events_query = new WP_Query($future_events_query_args);

$upcomingEvents = "";
if (!is_paged()) {
	if ($future_events_query->have_posts()) {
		$upcomingEvents = '<h3>Upcoming events</h3>';
		while ($future_events_query->have_posts()) {
			$future_events_query->the_post(); 
			$upcomingEvents .= '<article id="post-' .get_the_ID() . '" class="' . implode(" ", get_post_class()) . '">';
			global $post;
			$my_post = clone $post;
			$my_post->post_status = 'published';
			$my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
			$permalink = get_permalink($my_post);
			$upcomingEvents .= '<h4><a href="' . $permalink . '">' . get_the_title() . '</a><h4>';
			$upcomingEvents .= '<p>' . get_the_excerpt() . '</p>';
			$upcomingEvents .= '</article>';
		}
	}
}
wp_reset_query();
wp_reset_postdata();


get_header(); ?>

<main id="main" class="site-main" role="main">
	<?php
		if (!is_paged()) {
			while ($featured_event_query->have_posts()) {
				$featured_event_query->the_post();
				$featured_post_id = get_the_ID();
				$banner_position = get_field('adjust_the_banner_image', $featured_post_id, true);
				$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($featured_post_id), 'post_type' => 'attachment'));
				$src = wp_get_attachment_image_src(get_post_thumbnail_id($featured_post_id), array(700, 450), false, '');

				$post_featured_image  = '<div class="page-post-featured-graphic">';
				$post_featured_image .= 	'<div class="bbg__article-header__banner" ';
				$post_featured_image .= 		'style="background-image: url(' . $src[0] . '); background-position: ' . $banner_position . '">';
				$post_featured_image .= 	'</div>';
				$post_featured_image .= '</div>';
				echo $post_featured_image;
			}
		}
	?>
	
	<div class="outer-container">
		<div class="grid-container">
			<?php 
				if ($hasTeamFilter) {
					echo '<h2>' . $teamCategory->cat_name . ' events' . '</h2>';
				} else {
					echo '<h2>Events</h2>';
				}
			?>
		</div>
	</div>

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php echo $upcomingEvents; ?>
				</div>
				<div class="side-content-container past-events">
					<?php
						if ($past_events_query->have_posts()) {
							echo '<h5>Past events</h5>';
							while ($past_events_query->have_posts()) {
								$past_events_query->the_post(); 

								$past_event  = '<article id="'. get_the_ID() . '">';
								$past_event .= 	'<p class="aside date-meta">' . get_the_date() . '</p>';
								$past_event .= 	'<h6>';
								$past_event .= 		'<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
								$past_event .= 	'</h6>';
								$past_event .= '</article>';
								echo $past_event;
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<?php 
				$nextLink = get_next_posts_link('Older Events', $totalPages);
				$prevLink = get_previous_posts_link('Newer Events');

				$event_nav  = '<nav class="navigation posts-navigation" role="navigation">';
				$event_nav .= 	'<div class="nav-links">';
				if ($nextLink != "") {
					$event_nav .= 	'<div class="nav-previous">';
					$event_nav .= 		$nextLink;
					$event_nav .= 	'</div>';
				}
				if ($prevLink != "") {
					$event_nav .= 	'<div class="nav-next">';
					$event_nav .= 		$prevLink;
					$event_nav .= 	'</div>';	
				}
				$event_nav .= 	'</div>';
				$event_nav .= '</nav>';
				echo $event_nav;
			?>
		</div>
	</div>

</main><!-- #main -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
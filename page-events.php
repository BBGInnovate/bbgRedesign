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

$featuredEventTypeField = get_field('featured_event_type');
$featuredEventField = get_field('featured_event');

$currentPage =  (get_query_var('paged')) ? get_query_var('paged') : 1;

$postIDsUsed = array();

$numPostsFirstPage = 9;
$numPostsSubsequentPages = 9;

$postsPerPage = $numPostsFirstPage;
$offset = 0;
if ($currentPage > 1) {
	$postsPerPage = $numPostsSubsequentPages;
	$offset = $numPostsFirstPage + ($currentPage - 2) * $numPostsSubsequentPages;
}

$hasTeamFilter = false;

/* Featured Event */

$featuredEvent = '';

if ($featuredEventTypeField == 'next') {
	$qParamsNextEvent = array(
		'post_type' => array('post'),
		'cat' => get_cat_id('Event'),
		'posts_per_page' => 1,
		'post_status' => array('future'),
		'order' => 'ASC'
	);

	$next_event_query_args = $qParamsNextEvent;
	$next_event_query = new WP_Query($next_event_query_args);

	if ($next_event_query->have_posts()) {
		$featuredEvent .= '<h3 class="section-subheader">Featured Event</h3>';
		$featuredEvent .= '<div class="inner-container">';
		while ($next_event_query->have_posts()) {
			$next_event_query->the_post();
			$postIDsUsed[] = get_the_ID();

			$featuredEvent .= getCard(get_permalink(), has_post_thumbnail(), get_the_ID(), get_the_title(), get_the_date(), get_the_excerpt());
		}
		$featuredEvent .= '</div>';
	}
	wp_reset_query();
	wp_reset_postdata();
} else if ($featuredEventTypeField == 'manual') {
	if ($featuredEventField && has_category('event', $featuredEventField)) {
		$postIDsUsed[] = $featuredEventField->ID;
		$qParamsFirst = array(
			'p' => $featuredEventField->ID,
			'post_status' => array('publish', 'future')
		);

		$featured_event_query = new WP_Query($qParamsFirst);

		if (!is_paged()) {
			if ($featured_event_query->have_posts()) {
				$featuredEvent .= '<h3 class="section-subheader">Featured Event</h3>';
				$featuredEvent .= '<div class="inner-container">';
				while ($featured_event_query->have_posts()) {
					$featured_event_query->the_post();

					$featuredEvent .= getCard(get_permalink(), has_post_thumbnail(), get_the_ID(), get_the_title(), get_the_date(), get_the_excerpt());
				}
				$featuredEvent .= '</div>';
			}
		}
		wp_reset_query();
		wp_reset_postdata();
	}
} else {
	// Do nothing
}

/* Future Events */
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
		$upcomingEvents .= '<h3 class="section-subheader events-header">Upcoming Events</h3>';
		$upcomingEvents .= '<div class="inner-container css--grid-3">';
		while ($future_events_query->have_posts()) {
			$future_events_query->the_post(); 
			global $post;
			$my_post = clone $post;
			$my_post->post_status = 'published';
			$my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
			$permalink = get_permalink($my_post);

			$upcomingEvents .= getCard($permalink, has_post_thumbnail(), get_the_ID(), get_the_title(), get_the_date(), get_the_excerpt());
		}
		$upcomingEvents .= '</div>';
	}
}
wp_reset_query();
wp_reset_postdata();

/* Past Events */
$past_event_parameters = array(
	'post_type' => array('post'),
	'cat' => get_cat_id('Event'),
	'posts_per_page' => $postsPerPage,
	'offset' => $offset,
	'post_status' => array('publish'),
	'post__not_in' => $postIDsUsed
);
$past_events_query_args = $past_event_parameters;
$past_events_query = new WP_Query($past_events_query_args);

$totalPages = 1;
if ($past_events_query->found_posts > $numPostsFirstPage) {
	$totalPages = 1 + ceil(($past_events_query->found_posts - $numPostsFirstPage) / $numPostsSubsequentPages);
}

$pastEvents = '';
if ($past_events_query->have_posts()) {
	$pastEvents .= '<h3 class="section-subheader events-header">Past Events</h3>';
	$pastEvents .= '<div class="inner-container css--grid-3">';
	while ($past_events_query->have_posts()) {
		$past_events_query->the_post();

		$pastEvents .= getCard(get_permalink(), has_post_thumbnail(), get_the_ID(), get_the_title(), get_the_date(), get_the_excerpt());
	}
	$pastEvents .= '</div>';
}

wp_reset_query();
wp_reset_postdata();




function getCard($permalink, $hasPostThumbnail, $id, $title, $date, $excerpt) {
	$event = '';
	$event .= '    <div class="cards cards--layout-general cards--size-1-3-small-small cards--events css--grid margin-top-small">';
	$event .= '        <div class="cards__fixed">';
	$event .= '            <div class="cards__wrapper">';
	$event .= '                <div class="cards__backdrop">';
	$event .= '                    <a href="' . $permalink . '">';
	if ($hasPostThumbnail) {
		$event .=                      get_the_post_thumbnail($id, 'medium-thumb');
	} else {
		$event .= '                    <img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="White USAGM logo on medium gray background" />';
	}
	$event .= '                    </a>';
	$event .= '                    <div class="cards__backdrop-shadow"></div>';
	$event .= '                </div>';
	$event .= '                <div class="cards__footer">';
	$event .= '                    <h3><a href="' . $permalink . '">' . $title . '</a></h3>';
	$event .= '                </div>';
	$event .= '            </div>';
	$event .= '        </div>';
	$event .= '        <div class="cards__flexible">';
	$event .= '            <div class="cards__excerpt">';
	$event .= '                <div class="cards__date">' . $date . '</div>';
	$event .= '                <p>' . $excerpt . '</p>';
	$event .= '            </div>';
	$event .= '        </div>';
	$event .= '    </div>'; // END .cards

	return $event;
}

get_header();
?>
<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>
<main id="main" role="main">
	
	<div class="outer-container">
		<div class="grid-container">
			<?php 
				if ($hasTeamFilter) {
					echo '<h2 class="section-header">' . $teamCategory->cat_name . ' events' . '</h2>';
				} else {
					echo '<h2 class="section-header">Events</h2>';
				}
			?>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<?php echo $featuredEvent; ?>
						<?php echo $upcomingEvents; ?>
						<?php echo $pastEvents; ?>
					</div>
					<div class="side-column">
						<?php
							$secondaryColumnLabel = get_field('secondary_column_label');
							$secondaryColumnContent = get_field('secondary_column_content');

							if ($secondaryColumnContent != "") {
								echo '<aside>';
								if ($secondaryColumnLabel != "") {
									echo '<h2 class="sidebar-section-header">' . $secondaryColumnLabel . '</h2>';
								}
								echo $secondaryColumnContent;
								echo '</aside>';
							}
						?>
					</div>
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
				$event_nav .= 	'<h3 class="header-outliner">Events Navigation</h3>';
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
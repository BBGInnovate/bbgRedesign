<?php
/**
 * The template for displaying the BBG Portfolio.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Category Browser
 */

require 'inc/bbg-functions-assemble.php';

$page_tag_line = get_post_meta( get_the_ID(), 'page_tagline', true );
if ($page_tag_line && !empty($page_tag_line)){
	$page_tag_line = '<p class="lead-in">' . $page_tag_line . '</p>';
}

$page_content = "";
$pageTitle = "";
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$pageTitle = get_the_title();
		$pageTitle = str_replace("Private: ", "", $pageTitle);
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
		$ogDescription = get_the_excerpt();
	}
}
wp_reset_postdata();
wp_reset_query();


/***** BEGIN PROJECT PAGINATION LOGIC
There are some nuances to this. Note that we're not using the paged parameter because we don't have the same number of posts on every page. Instead we use the offset parameter. The 'posts_per_page' limits the number displayed on the current page and is used to calculate offset.
http://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
****/

$currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;

$paginationLabel = get_post_meta(get_the_ID(), 'category_browser_pagination_label', true);
$category_browser_type = get_post_meta(get_the_ID(), 'category_browser_type', true);
$burkeYear =  get_post_meta(get_the_ID(), 'category_browser_burke_year', true);

$videoUrl = get_field('featured_video_url', '', true);
$hasIntroFeature = FALSE;
if ($videoUrl != "") {
	$hasIntroFeature = true;
} elseif (has_post_thumbnail() && ($hideFeaturedImage != 1)) {
	$hasIntroFeature = true;
}

$numPostsFirstPage = 7;
if ($hasIntroFeature) {
	$numPostsFirstPage = 6;
}
$numPostsSubsequentPages = 6;


$postsPerPage = $numPostsFirstPage;
$offset = 0;
if ($currentPage > 1) {
	$postsPerPage = $numPostsSubsequentPages;
	$offset = $numPostsFirstPage + ($currentPage - 2) * $numPostsSubsequentPages;
}

$hasTeamFilter = false;
$mobileAppsPostContent = "";

if ($category_browser_type == "Page Children") {
	/*** USED FOR APPS LANDING PAGE ****/
	$qParams = array (
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => get_the_ID(),
		'order' => 'DESC'
	);
	if ($pageTitle == "Burke Awards archive") {
		$qParams['post_status'] = array('publish'); // you can add 'private','pending','draft' for development
		$qParams['orderby'] = 'menu_order';
		$qParams['order'] = 'ASC';
	}
} else if ($category_browser_type == "Custom Post Type") {
	/*** USED FOR AWARDS AND BURKE CANDIDATES ****/
	$categoryBrowsePostType = get_post_meta(get_the_ID(), 'category_browser_post_type', true);

	/*** categoryBrowsePostType ***/
	$qParams = array(
		'post_type' => array($categoryBrowsePostType),
		'posts_per_page' => $postsPerPage,
		'offset' => $offset,
		'order' => 'DESC'
	);

	if ($categoryBrowsePostType == 'burke_candidate') {
		$qParams['posts_per_page'] = -1;
		$qParams['meta_query'] = array(
		    'relation' => 'OR',
		    array('key' => 'burke_award_info_0_burke_ceremony_year','compare' => '=','value' => $burkeYear),
		    array('key' => 'burke_award_info_1_burke_ceremony_year','compare' => '=','value' => $burkeYear),
		    array('key' => 'burke_award_info_2_burke_ceremony_year','compare' => '=','value' => $burkeYear)
		);
		$qParams['post_status'] = array( 'publish' ); // you can add 'private','pending','draft' for development
	}
} else {
	$categoryToBrowse = get_field('category_browser_category', get_the_ID(), true);
	$projectCatObj = get_category_by_slug($categoryToBrowse -> slug);

	$awardYear = get_query_var('awardyear', '');
	$entity = get_query_var('entity', '');

	$qParams = array(
		'post_type' => array('post'),
		'cat' => $projectCatObj -> term_id,
		'posts_per_page' => $postsPerPage,
		'offset' => $offset,
		'post_status' => array('publish')
	);

	if ($awardYear != '' || $entity != '') {
		$meta_query = array (
			'relation' => 'AND'
		);

		if ($awardYear != '') {
			$meta_query[] = array(
				'key' => 'standardpost_award_year',
				'value' => $awardYear,
				'compare' => '='
			);
		}
		if ($entity != '') {
			$meta_query[] = array(
				'key' => 'standardpost_award_recipient',
				'value' => $entity,
				'compare' => '='
			);
		}
		$qParams['meta_query'] = $meta_query;
	}
}

/*** late in the game we ran into a pagination issue, so we're running a second query here ***/
$custom_query_args = $qParams;
$custom_query = new WP_Query($custom_query_args);

$totalPages = 1;
if ($custom_query -> found_posts > $numPostsFirstPage) {
	$totalPages = 1 + ceil(($custom_query -> found_posts - $numPostsFirstPage) / $numPostsSubsequentPages);
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
	<?php
		if ($custom_query -> have_posts()) {
			$page_title  = '<div class="outer-container"';
			if (is_page('deep-dive-series')) {
				$page_title .= 	' style="margin-bottom: 1.5rem;"';
			}
			$page_title .= '>';
			$page_title .= 	'<div class="grid-container">';
			$page_title .= 		'<h2>' . get_the_title() . '</h2>';
			$page_title .= 		$page_tag_line;
			$page_title .= 	'</div>';
			$page_title .= '</div>';
			echo $page_title;

			if (is_page('deep-dive-series')) {
				echo '<div class="outer-container" style="margin-bottom: 3rem;">';
				echo 	'<div class="grid-container">';
				echo 		'<div class="page-content">';
				echo 			$page_content;
				echo 		'</div>';
				echo 	'</div>';
				echo '</div>';
			}

			$counter = 0;
			while ($custom_query -> have_posts())  {
				$custom_query -> the_post();
				$counter = $counter + 1;

				if ($counter == 1 && $currentPage == 1 && !$hasIntroFeature) {
					$featured_post  = '<div class="outer-container">';
					$featured_post .= 	'<div class="grid-container">';
					$featured_media_result = get_feature_media_data();
					if ($featured_media_result != "") {
						$featured_post .= $featured_media_result;
					}
					$featured_post .= 		'<h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
					$featured_post .= 		'<p>' . get_the_excerpt() . '</p><br><br>';
					$featured_post .= 	'</div>';
					$featured_post .= '</div>';
					echo $featured_post;
				}
				if (($counter == 2 && $currentPage == 1) || ($counter == 1 && $currentPage > 1)) {
					echo '<div class="outer-container">';
				}
				if ($counter > 1 && $currentPage == 1 || $currentPage > 1) {
					echo '<div class="grid-third">';
					$post_image = '<a href="' . $postPermalink . '" rel="bookmark" tabindex="-1">';
					if (has_post_thumbnail()) {
						$post_image .= the_post_thumbnail('medium-thumb');
					} else {
						$post_image .= '<img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="White USAGM logo on medium gray background" />';
					}
					$post_image .= '</a>';
					echo $post_image;

					$link_header  = '<h4>';
					$link_header .= 	'<a href="' . get_the_permalink() . '" rel="bookmark">';
					$link_header .= 		get_the_title();
					$link_header .= 	'</a>';
					$link_header .= '</h4>';
					echo $link_header;

					if (is_page('deep-dive-series')) {
						echo '<p class="aside" style="margin-bottom: 1.5rem;">' . get_the_date() . '</p>';
						echo get_the_excerpt();
					} else {
						echo wp_trim_words(get_the_excerpt(), 10);
					}
					echo '<br><br><br></div>'; // END .grid-third
				}
			}
			echo '</div>'; // END .outer-container

			if ($pageTitle != "Burke Awards archive") {
				echo '<div class="outer-container">';
				echo 	'<div class="grid-container">';
				echo 		'<nav class="navigation posts-navigation" role="navigation">';
				echo 			'<h2 class="screen-reader-text">Event navigation</h2>';
				echo 			'<div class="nav-links">';
				$nextLink = get_next_posts_link('Older ' . $paginationLabel, $totalPages);
				$prevLink = get_previous_posts_link('Newer ' . $paginationLabel);
				if ($nextLink != "") {
					echo 			'<div class="nav-previous">';
					echo 				$nextLink;
					echo 			'</div>';
				}
				if ($prevLink != "") {
					echo 			'<div class="nav-next">';
					echo 				$prevLink;
					echo 			'</div>';
				}
				echo 			'</div>';
				echo 		'</nav>';
				echo 	'</div>';
				echo '</div><!-- .usa-grid -->';
			}
		}
		else {
			get_template_part('template-parts/content', 'none');
		}
		if (!is_page('deep-dive-series')) {
			echo $page_content;
		}
	?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php
/**
 * The template for displaying Author archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 */

require get_template_directory() . '/inc/bbg-functions-assemble.php';

// AUTHOR INFORMATION
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
$author_id = $curauth -> ID;
$user_meta = get_user_meta($author_id);
$author_name = $curauth -> display_name;
$add_separator = FALSE;

// AUTHOR PROFILE
// ================================================================================
$profile_page_url = "";
if (isset($user_meta['author_profile_page'])) {
	$profile_page_url = esc_url(get_page_link($user_meta['author_profile_page'][0]));
}

$occupation = "";
if (isset($user_meta['occupation'])) {
	$occupation = $user_meta['occupation'][0];
}

// SET AVATAR BLOCK
$avatar = get_avatar($author_id, apply_filters('change_avatar_css', 100));
$user_meta = get_user_meta($author_id);

$description = "";
if (isset($user_meta['description'])) {
	$description = $user_meta['description'][0];
}
// ================================================================================

$isCEO = false;
if (stristr($occupation, 'ceo')) {
	$isCEO = true;
}

// PODCASTS
$showPodcasts = false;
if (isset($_GET['showPodcasts'])) {
	$showPodcasts = true;
}

// TWITTER INFORMATION
$twitterHandle = "";
if (isset( $user_meta['twitterHandle'])) {
	$twitterHandle = $user_meta['twitterHandle'][0];
}
$tweets = [];
$profilePageID = "";
$latestTweetsStr = "";
$featuredPostID = 0;
if (isset($user_meta['author_profile_page'])) {
	$profilePageID =  $user_meta['author_profile_page'][0];

	$tweets = get_field('profile_related_author_page_tweets', $profilePageID, true);
	$featuredPostID = get_field('profile_related_author_page_featured_post', $profilePageID, true);

	if (count($tweets)) {
		$randKey = array_rand($tweets);
		$latestTweetsStr = $tweets[$randKey]['profile_related_author_page_tweet'];
		/* THE HTML OF A TWEET SHOULD LOOK LIKE THIS
		$latestTweetsStr = '<blockquote class="twitter-tweet" data-theme="light"><p lang="en" dir="ltr">Our Impact Model measures 40+ indicators beyond audience size to hold our activities accountable. <a href="https://twitter.com/hashtag/BBGannualReport?src=hash">#BBGannualReport</a> <a href="https://t.co/r8geNg47OP">https://t.co/r8geNg47OP</a> <a href="https://t.co/e6T3Zea443">pic.twitter.com/e6T3Zea443</a></p>&mdash; BBG (@BBGgov) <a href="https://twitter.com/BBGgov/status/881886454485528576">July 3, 2017</a></blockquote> <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
	*/
	}
}
if ($isCEO) {
	$postIDsUsed = [];
	// GET PRIMARY POST DATA (IF NOT SELECTED)
	// ============================================================================
	function get_primary_post_data() {
		global $author_primary_post;
		global $postIDsUsed;

		if (!empty($featuredPostID) && ($featuredPostID == 0)) {
			$author_primary_post_query = array(
				'post_type' => array('post'),
				'posts_per_page' => 1,
				'orderby' => 'post_date',
				'order' => 'desc',
				'post__not_in' => $postIDsUsed,
				'tax_query' => array(
					array(
						'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => array('from-the-ceo'),
						'operator' => 'AND'
					)
				)
			);
			$author_primary_post = new WP_Query($author_primary_post_query);
			wp_reset_query();
		}
	}
	
	// GET SECONDARY POSTS
	// ============================================================================
	function get_secondary_posts() {
		global $author_secondary_posts;
		global $postIDsUsed;

		$author_secondary_posts_query = array(
			'post_type' => array('post'),
			'posts_per_page' => 2,
			'orderby' => 'post_date',
			'order' => 'desc',
			'post__not_in' => $postIDsUsed,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => array('from-the-ceo', 'blog'),
					'operator' => 'AND'
				)
			)
		);
		$author_secondary_posts = new WP_Query($author_secondary_posts_query);
		wp_reset_query();
	}

	// GET STATEMENTS
	// ============================================================================
	function get_statement_posts() {
		global $statements_query;
		global $postIDsUsed;

		$statements_query_params = array(
			'post_type' => array( 'post' ),
			'posts_per_page' => 1,
			'orderby' => 'post_date',
			'order' => 'desc',
			'post__not_in' => $postIDsUsed,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => array('statement', 'from-the-ceo'),
					'operator' => 'AND'
				)
			)
		);
		$statements_query = new WP_Query($statements_query_params);
		wp_reset_query();
	}

	// GET OP EDS
	// ============================================================================
	function get_op_ed_posts() {
		global $op_ed_query;
		global $postIDsUsed;

		$op_ed_query_params = array(
			'post_type' => array( 'post' ),
			'posts_per_page' => 1,
			'orderby' => 'post_date',
			'order' => 'desc',
			'post__not_in' => $postIDsUsed,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => array('op-ed', 'from-the-ceo'),
					'operator' => 'AND'
				)
			)
		);
		$op_ed_query = new WP_Query($op_ed_query_params);
		wp_reset_query();
	}
}

get_header();
?>

<div id="main" class="site-content" role="main">
	<?php
		if ($isCEO) {
			$author_avatar  = '<div class="outer-container">';
			$author_avatar .= 	'<div class="grid-container avatar-container" >';
			$author_avatar .= 		'<div class="avatar-image">';
			$author_avatar .= 			'<a href="' . $profile_page_url . '">';
			$author_avatar .= 				'<img src="' . get_template_directory_uri() . '/img/john_lansing_ceo-sq-200x200.jpg">';
			$author_avatar .= 			'</a>';
			$author_avatar .= 		'</div>';
			$author_avatar .= 		'<div class="avatar-information">';
			$author_avatar .= 			'<h3 class="article-title"><a href="' . $profile_page_url . '">' . $author_name . '</a></h3>';
			$author_avatar .= 			'<p class="avatar-title aside">' . $occupation . '</p>';
			$author_avatar .= 		'<p class="avatar-description">' . $description . '</p>';
			$author_avatar .= 		'</div>';
			$author_avatar .= 	'</div>';
			$author_avatar .= '</div>';
			echo $author_avatar;

			get_primary_post_data();
			if (!empty($author_primary_post) && $author_primary_post -> have_posts()) {
				$author_featured_post  = '<div class="outer-container">';
				$author_featured_post .= 	'<div class="grid-container">';
				$author_featured_post .= 		'<h2 class="section-header"><a href="/category/from-the-ceo">From the CEO</a></h2>';
				while ($author_primary_post -> have_posts()) { 
					$author_primary_post -> the_post();
					$postIDsUsed[] = get_the_ID();

					$featured_media_result = get_feature_media_data();
					if ($featured_media_result != "") {
						$author_featured_post .= $featured_media_result;
					}
					$author_featured_post .= '<h4><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h4>';
					$author_featured_post .= '<p class="lead-in">' . get_the_excerpt() . '</p>';
				}
				$author_featured_post .= 	'</div>';
				$author_featured_post .= '</div>';
				echo $author_featured_post;
			}

			get_secondary_posts();
			if ($author_secondary_posts -> have_posts()) {
				$blogLink = '/category/from-the-ceo+blog/';
				echo '<div class="outer-container">';
				echo 	'<div class="grid-container">';
				echo 		'<h3 class="section-subheader"><a href="' . $blogLink . '">Blog</a></h3>';
				echo 	'</div>';
				echo 	'<div class="custom-grid-container" >';
				echo 		'<div class="inner-container">';
				// MAIN BLOG SECTION
				echo 			'<div class="main-content-container">';
				echo 				'<div class="nest-container">';
				echo 					'<div class="inner-container">';
				while ($author_secondary_posts -> have_posts()) {
					$author_secondary_posts -> the_post();
					$postIDsUsed[] = get_the_ID();

					echo 					'<div class="grid-half">';
					echo 						'<a href="' . get_the_permalink() . '">' . get_the_post_thumbnail() . '</a>';
					echo 						'<h4><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h4>';
					echo 						'<p>' . get_the_excerpt() . '</p>';
					echo 					'</div>';
				}
				echo 					'<div class="grid-container">';
				echo 						'<p class="learn-more"><a href="' . $blogLink . '">MORE BLOG POSTS</a></p>';
				echo 					'</div>';
				echo 					'</div>';
				echo 				'</div>';
				echo 			'</div>';
				// SIDE TWITTER SECION
				echo 			'<div class="side-content-container">';
				echo 				'<h5><a target="_blank" href="https://twitter.com/' . $twitterHandle . '">Featured Tweet</a></h5>';
				echo 				$latestTweetsStr;
				echo 			'</div>';
				echo 		'</div>';
				echo 	'</div>';
				echo '</div>';
			}

			$author_ribbon  = '<div class="bbg__ribbon inner-ribbon">';
			$author_ribbon .= 	'<div class="outer-container">';
			$author_ribbon .= 		'<div class="side-content-container">';
			$author_ribbon .= 			'<div style="background-image: url(/wp-content/media/2017/07/lansingspeaks.jpg);"></div>';
			$author_ribbon .= 		'</div>';
			$author_ribbon .= 		'<div class="main-content-container">';
			$author_ribbon .= 			'<h2 class="section-header">On the record</h2>';
			$author_ribbon .= 			'<h3 class="article-title"><a href="">Speeches and Remarks</a></h3>';
			$author_ribbon .= 			'<p>View transcripts of CEO Lansing’s remarks and statements at each of his appearances since he joined the BBG in September 2015. <span class="learn-more"><a href="/ceo-speeches-remarks/">View All</a></span></p>';
			$author_ribbon .= 		'</div>';
			$author_ribbon .= 	'</div>';
			$author_ribbon .= '</div>';
			echo $author_ribbon;

			echo '<div class="outer-container">';
			get_statement_posts();
			if ($statements_query -> have_posts()) {
				$statementsLink = "/category/from-the-ceo+statement/";
				while ($statements_query -> have_posts()) {
					$statements_query -> the_post();
					$postIDsUsed[] = get_the_ID();

					echo 	'<div class="grid-half">';
					echo 		'<h2 class="section-subheader"><a target="_blank" href="' . $statementsLink . '">Statements</a></h2>';
					echo 		get_the_post_thumbnail();
					echo 		'<h3 class="article-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
					echo 		'<p>' . get_the_excerpt() . '</p>';
					echo 		'<p class="learn-more"><a href="' . $statementsLink . '">More statements</a></p>';
					echo 	'</div>';
				}
			}

			get_op_ed_posts();
			if ($op_ed_query -> have_posts()) {
				$opEdLink = "/category/from-the-ceo+op-ed/";
				while ($op_ed_query -> have_posts()) {
					$op_ed_query -> the_post();
					$postIDsUsed[] = get_the_ID();

					echo 	'<div class="grid-half">';
					echo 		'<h2 class="section-subheader"><a target="_blank" href="' . $opEdLink . '">Op-Eds</a></h2>';
					echo 		get_the_post_thumbnail();
					echo 		'<h3 class="article-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
					echo 		'<p>' . get_the_excerpt() . '</p>';
					echo 		'<p class="learn-more"><a href="' . $opEdLink . '">More op-eds</a></p>';
					echo 	'</div>';
				}
			}
			echo '</div>';
		}
	?>

	<?php
		if ($isCEO) {
			if ($showPodcasts) {
				$qParams = array(
					'post_type' => array( 'post' ),
					'posts_per_page' => 1,
					'orderby' => 'post_date',
					'order' => 'desc',
					'post__not_in' => $postIDsUsed,
					'tax_query' => array(
						array(
							'taxonomy' => 'category',
							'field' => 'slug',
							'terms' => array('podcasts', 'from-the-ceo'),
							'operator' => 'AND'
						)
					)
				);

				$podcastsLink = "/category/from-the-ceo+podcasts/";
				echo '<div class="' . $containerClass . '">';
				echo 	'<h6 class="bbg__label"><a target="_blank" href="' . $opEdLink . '">Podcasts</a></h6>';
				query_posts( $qParams );
				if (have_posts()) {
					$counter = 0;
					while ( have_posts() ) : the_post();
						$postIDsUsed []= get_the_ID();
						get_template_part('template-parts/content-portfolio', get_post_format());
					endwhile;
				}
				wp_reset_query();
				echo 	'<div align="right"><a href="' . $podcastsLink . '" class="bbg__kits__intro__more--link">More podcasts »</a></div>';
				echo '</div>';
			}
		} else {
			// BEGIN REGULAR NON-CEO AUTHOR PAGE
			while ( have_posts() ) : the_post();
				$counter = $counter + 1;
				$gridClass = "";
				if ($counter < 2) {
					$gridClass = "bbg-grid--1-2-2";
					get_template_part('template-parts/content-portfolio', get_post_format());
				} elseif ($counter == 2){
					$gridClass = "bbg-grid--1-2-2";
					get_template_part('template-parts/content-portfolio', get_post_format());
					echo '</section>';
					echo '<section class="usa-section usa-grid">';
				} else {
					$gridClass = "";
					$includeMeta = FALSE;
					get_template_part('template-parts/content-excerpt-list', get_post_format());
				}
			endwhile;
			// END REGULAR NON-CEO AUTHOR PAGE
		}
?>
</div>
<?php get_footer(); ?>
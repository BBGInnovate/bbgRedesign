<?php
/**
 * The custom home page for the Broadcasting Board of Governors.
 * It includes:
 *      - the mission
 *      - a portfolio of recent impact stories,
 *      - recent stories,
 *      - an optional soapbox for senior leadership commentary,
 *      - updates on threats to press around the world
 *      - and a list of the entities.
 *
 * @package bbgRedesign
  template name: Custom BBG Home
 */

// FUNCTION THAT BUILD SECTIONS
require 'inc/bbg-functions-home.php';
require 'inc/bbg-functions-assemble.php';

$templateName = 'customBBGHome';

// GET CUSTOM FIELDS

/*** store a handful of page links that we'll use in a few places ***/
$impactPermalink = get_permalink( get_page_by_path('our-work/impact-and-results'));
$impactPortfolioPermalink = get_permalink( get_page_by_path('our-work/impact-and-results/impact-portfolio'));


/*** add any posts from custom fields to our array that tracks post IDs that have already been used on the page ***/
$postIDsUsed = array();

$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
$threatsPermalink = get_permalink(get_page_by_path('threats-to-press'));
$randomFeaturedThreatsID = false;
if ($threatsToPressPost) {
	$randKey = array_rand($threatsToPressPost);
	$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
}

get_header();
?>

<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<main id="bbg-home" class="site-content bbg-home-main" role="main">
			
			<?php
				$banner_result = get_homepage_banner_data();

				$banner_markup  = '<div class="page-post-featured-graphic">';
				$banner_markup .= 	'<div class="bbg-banner" ';
				$banner_markup .= 		'style="background-image: url(' . $banner_result['image_source'] . ') !important; background-position: ' . $banner_result['position'] . '">';
				$banner_markup .= 	'</div>';
				$banner_markup .=	'<div class="bbg-banner__cutline usa-grid">';
				$banner_markup .=		$banner_result['caption'];
				$banner_markup .=	'</div>';
				$banner_markup .= '</div>';
				echo $banner_markup;
			?>

			<section id="mission" class="outer-container">
				<h1 class="header-outliner">About the BBG</h1>
				<div class="grid-container">
				<?php
					$settings_result = get_site_settings_data();

					$mission  = '<p class="lead-in">';
					$mission .= 	$settings_result['intro_content'];
					$mission .= 	'<a href="';
					$mission .= 		$settings_result['intro_link'];
					$mission .= 		'" class="bbg__read-more">LEARN MORE »';
					$mission .= 	'</a>';
					$mission .= '</p>';
					echo $mission;
				?>
				</div>
			</section>

			<!-- BBG NEWS -->
			<section id="recent-posts" class="outer-container">
				<h1 class="header-outliner">BBG News</h1>
				<div class="container-grid">
					<h2><a href="<?php echo get_permalink(get_page_by_path('news')); ?>">BBG News</a></h2>
				</div>
				<?php
					$featured_post = get_featured_post_data();

					// $main_featured_post  = '<div class="home-feature-primary-post">';
					$main_featured_post  = '<div class="home-feature-primary-post">';
					$main_featured_post .= 		'<a href="' . get_the_permalink() . '">';
					$main_featured_post .= 			$featured_post['media'];
					$main_featured_post .= 		'</a>';
					$main_featured_post .= 		'<h3>';
					$main_featured_post .= 			'<a href="' . get_the_permalink() . '">' . $featured_post['title'] . '</a>';
					$main_featured_post .= 		'</h3>';
					$main_featured_post .= 		'<p class="post-date">' . get_the_date() . '</p>';
					$main_featured_post .= 		'<p>' . get_the_excerpt() . '</p>';
					$main_featured_post .= '</div>';
					echo $main_featured_post;
				?>
				<?php
					$post_qty = 2;
					$recent_result = get_recent_post_data($post_qty);
					if (have_posts()) {
						echo '<div class="home-recent-posts">';
						while (have_posts()) {
							the_post();
							$recent_post  = '<div>';
							$recent_post .= 	'<h4>';
							$recent_post .= 		'<a href="' . get_the_permalink() . '">';
							$recent_post .= 			get_the_title();
							$recent_post .= 		'</a>';
							$recent_post .= 	'</h4>';
							$recent_post .= 	'<p class="post-date">' . get_the_date() . '</p>';
							$recent_post .= 	'<p>' . wp_trim_words(get_the_content(), 50) . '</p>';
							$recent_post .= '</div>';
							echo $recent_post;
						}
						$news_page_link  = 	'<nav class="navigation posts-navigation bbg__navigation__pagination" role="navigation">';
						$news_page_link .= 		'<h2 class="screen-reader-text">Recent Posts Navigation</h2>';
						$news_page_link .= 			'<div class="nav-links">';
						$news_page_link .= 			'<div class="nav-previous">';
						$news_page_link .= 				'<a href="' . get_permalink(get_page_by_path('news-and-information')) .'" >Previous posts</a>';
						$news_page_link .= 			'</div>';
						$news_page_link .= 		'</div>';
						$news_page_link .= 	'</nav>';
						echo $news_page_link;
						echo '</div>'; // END .home-recent-posts
					}
				?>
			</section>
			<!-- END BBG NEWS -->
			
			<!-- SOAPBOX, CORNER HERO, IMPACT STORIES -->
			<!-- <section class="outer-container mentions" style="border: 1px solid red;"> -->
				<!-- SOAPBOX, CORNER HERO -->
				<!-- <div class="container-grid" style="border: 1px solid blue;"> -->
				<?php
					$soap_result = get_soapbox_data();
					$corner_hero_result = get_corner_hero_data();

					$mention_begin  = '<section class="outer-container mentions" style="border: 1px solid red;">';
					$mention_begin .= 	'<div class="container-grid" style="border: 1px solid blue;">';
					if (($soap_result['toggle'] == 'on') && !empty($corner_hero_result)) {
						$layout = 'side';
						$mentions = array($soap_result, $corner_hero_result);
						$mention_inner = '<div class="split-mentions" style="border: 1px solid orange;">';
					} else {
						$sb = ($soap_result['toggle'] == 'on') ? true : false;
						$ch = (!empty($corner_hero_result)) ? true : false;
						if ($sb) {
							$mentions = array($soap_result);
						} elseif ($ch) {
							$mentions = array($corner_hero_result);
						}
						$layout = 'full';
						$mention_inner = '<div class="container-grid" style="border: 1px solid purple;">';
					}
					if (!empty($mentions)) {
						foreach ($mentions as $mention) {
							$mention_inner .= '<div class="container-grid" style="border: 1px solid #00ffff;">';
							if (($mention['type'] == 'soapbox') && ($layout == 'side')) {
								$mention_inner .= 	'<div class="mention-large-side">' . $mention['content'] . '</div>';
								$mention_inner .= 	'<div class="mention-small-side">' . $mention['image'] . '</div>';
							} else {
								$mention_inner .= 	'<div class="mention-small-side">' . $mention['image'] . '</div>';
								$mention_inner .= 	'<div class="mention-large-side">' . $mention['content'] . '</div>';
							}
							$mention_inner .= '</div>';
						}
					}
					$mention_end = '</div>';
					echo $mention_begin . $mention_inner . $mention_end;
				?>
				</div>
				<!-- END SOAPBOX, CORNER HERO -->
				<!-- IMPACT STORIES -->
				<!-- END IMPACT STORIES -->
			</section>
			<!-- END SOAPBOX, CORNER HERO, IMPACT STORIES -->

			<!-- THREATS TO PRESS  -->
			<section id="threats-to-journalism" class="usa-section bbg__ribbon">
				<div class="usa-grid">
					<h2 class="bbg__label small"><a href="<?php echo $threatsPermalink; ?>">Threats to Press</a></h2>
				</div>

				<div class="usa-grid">
					<div class="threats-box">
					<?php
$randomFeaturedThreatsID = false;
function get_threats_post_data() {
	$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
	$threatsPermalink = get_permalink(get_page_by_path('threats-to-press'));
	$randomFeaturedThreatsID = false;
	
	if ($threatsToPressPost) {
		$randKey = array_rand($threatsToPressPost);
		$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
	}
	build_threat_params();
}
get_threats_post_data();
						$threatsUsedPosts = array();
						$counter = 0;
						$threat_post_qty = 2;

						function build_threat_params() {
							if ($randomFeaturedThreatsID) {
								$qParams = array(
									'post__in' => array($randomFeaturedThreatsID),
								);
							} else {
								$qParams = array(
									'post_type' => array('post'),
									'posts_per_page' => 2,
									'orderby' => 'post_date',
									'order' => 'desc',
									'cat' => get_cat_id('Threats to Press'),
									'post__not_in' => $threatsUsedPosts
								);
							}
							query_posts($qParams);
							while (have_posts()) : the_post();
								$counter++;
								$id = get_the_ID();
								$threatsUsedPosts[] = $id;
								$postIDsUsed[] = $id;
								$permalink = get_the_permalink();

								$threat_markup  = '<article id="post-' . $id . '">';
								$threat_markup .= 	'<div class="image-block">';
								$threat_markup .= 		'<a href="' . $permalink . '" rel="bookmark" tabindex="-1">';
								$threat_markup .= 			get_the_post_thumbnail();
								$threat_markup .= 		'</a>';
								$threat_markup .= 	'</div>';
								$threat_markup .= 	'<div class="threat-copy">';
								$threat_markup .= 		'<header>';
								$threat_markup .= 			'<h5><a href="'.get_the_permalink().'">' . get_the_title() . '</a></h5>';
								$threat_markup .= 		'</header>';
								$threat_markup .= 		'<p>' . get_the_excerpt() . '</p>';
								$threat_markup .= 	'</div>';
								$threat_markup .= '</article>';
								echo $threat_markup;
							endwhile;
						}
						if ($counter > $threat_post_qty) {
							get_threats_post_data();
						}
					?>
					</div>
				</div>
			</section>

			<!-- ENTITY LIST -->
			<?php echo get_entity_data(); ?>

			<!-- Quotation -->
			<section class="usa-section ">
				<div class="usa-grid">
					<?php
						$q = getRandomQuote( 'allEntities', $postIDsUsed );
						if ($q) {
							$postIDsUsed[] = $q["ID"];
							outputQuote($q);
						}
					?>
				</div>
			</section><!-- Quotation -->

		</main>
	</div><!-- #primary .content-area -->
	<div id="secondary" class="widget-area" role="complementary">
	</div><!-- #secondary .widget-area -->
</div><!-- #main .site-main -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>


<script type="text/javascript">
function navSlide(){
	var currentScroll = jQuery( "html" );
	//console.log("Currently scrolled to: " + currentScroll.scrollTop());

	var p = jQuery( "#threats-to-journalism" );
	var offset = p.offset();
	//console.log("#threats-to-journalism position: " + offset.top);

	if (currentScroll.scrollTop() > offset.top){
		//console.log("the Threats-to-press section should be at the top of the page");
		jQuery(".bbg__social__container").hide();
	} else {
		//console.log("the Threats-to-press section is below the top of the page");
		jQuery(".bbg__social__container").show();
	}
}

jQuery(window).scroll(navSlide);
</script>

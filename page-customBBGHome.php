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
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

require 'inc/bbg-functions-home.php';
require 'inc/bbg-functions-assemble.php';

$templateName = 'customBBGHome';

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

<?php
	$banner_result = get_homepage_banner_data();

	$banner_markup  = '<div class="page-featured-media">';
	$banner_markup .= 	'<div class="bbg-banner" ';
	$banner_markup .= 		'style="background-image: url(' . $banner_result['image_source'] . ') !important; background-position: ' . $banner_result['position'] . '">';
	$banner_markup .= 	'</div>';
	$banner_markup .= 		'<div class="grid-container">';
	$banner_markup .= 			'<p class="graphic-caption">';
	$banner_markup .= 				$banner_result['caption'];
	$banner_markup .= 			'</p>';
	$banner_markup .= 		'</div>';
	$banner_markup .= '</div>';
	echo $banner_markup;
?>

<main id="main" class="site-content bbg-home-main" role="main">

	<section id="mission" class="outer-container">
		<h1 class="header-outliner">About USAGM</h1>
		<div class="grid-container">
		<?php
			$settings_result = get_site_settings_data();

			$mission  = '<p class="lead-in">';
			$mission .= 	$settings_result['intro_content'];
			$mission .= '</p>';
			echo $mission;
		?>
		</div>
	</section>

	<!-- USAGM NEWS -->
	<section class="outer-container featured-row">
		<h1 class="header-outliner">USAGM News</h1>
		<div class="grid-container">
			<h2><a href="<?php echo get_permalink(get_page_by_path('news-and-information')); ?>">USAGM News</a></h2>
		</div>
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
				<?php
					// $STANDARD_POST_CATEGORY_EXCLUDES is located in functions.php
					$featured_post_result = get_field_post_data('featured', 1, $STANDARD_POST_CATEGORY_EXCLUDES);

					$main_featured_post .= 	$featured_post_result['linked_media'];
					$main_featured_post .= 	'<h4>' . $featured_post_result['linked_title'] . '</h4>';
					$main_featured_post .= 	'<p class="aside date-meta">' . $featured_post_result['date'] . '</p>';
					$main_featured_post .= 	'<p>' . $featured_post_result['excerpt'] . '</p>';
					echo $main_featured_post;
				?>
				</div>
				<div class="side-content-container">
				<?php
					$recent_post_quantity = 2;
					$used_ids = array();
					array_push($used_ids, $featured_post_result['id']);
					$recent_result = get_recent_post_data($recent_post_quantity, $used_ids, $STANDARD_POST_CATEGORY_EXCLUDES);

					if (have_posts()) {
						while (have_posts()) {
							the_post();
							$recent_post  = '<div class="inner-container">';
							$recent_post .= 	'<h4>';
							$recent_post .= 		'<a href="' . get_the_permalink() . '">';
							$recent_post .= 			get_the_title();
							$recent_post .= 		'</a>';
							$recent_post .= 	'</h4>';
							$recent_post .= 	'<p class="aside date-meta">' . get_the_date() . '</p>';
							$recent_post .= 	'<p>';
							$recent_post .= 		wp_trim_words(get_the_content(), 50);
							$recent_post .= 		' <a class="read-more" href="' . get_the_permalink() . '">READ MORE</a>';
							$recent_post .= 	'</p>';
							$recent_post .= '</div>';
							echo $recent_post;
						}
					}
				?>
					<!-- NEWS PAGE LINK -->
					<nav class="navigation posts-navigation bbg__navigation__pagination" role="navigation">
						<h2 class="screen-reader-text">Recent Posts Navigation</h2>
						<div class="nav-links">
							<div class="nav-previous">
								<a href="<?php echo get_permalink(get_page_by_path('news-and-information')) ?>" >Previous posts</a>
							</div>
						</div>
					</nav>

				</div>
			</div><!-- end nest -->
		</div><!-- end grid -->
	</section><!-- END USAGM NEWS -->
	
	<?php
		// PREP: SOAPBOX, CORNER HERO, IMPACT STORIES
		$soap_result = get_soapbox_data();
		$corner_hero_result = get_corner_hero_data();

		$soap_layout = "";
		$impact_quantity = "";
		if (($soap_result['toggle'] == 'on') && ($corner_hero_result['toggle'] == 'on')) {
			$soap_layout = 'image-right';
			$impact_quantity = 1;
		} else {
			$soap_layout = 'image-top';
			$impact_quantity = 1;
		}

		$impact_result = get_impact_stories_data($impact_quantity);

		$mentions_group = array();
		if ($soap_result['toggle'] == 'on') {
			$show_soap = true;
			$soap_layout = (($soap_result['toggle'] == 'on') && ($corner_hero_result['toggle'] == 'on')) ? 'image-right' : 'image-top';
			$soap_parts = build_soapbox_parts($soap_result, $soap_layout);
			array_push($mentions_group, $soap_parts);
		}
		if ($corner_hero_result['toggle'] == 'on') {
			$show_corner = true;
			$corner_hero_parts = build_corner_hero_parts($corner_hero_result);
			array_push($mentions_group, $corner_hero_parts);
		}
		
		$impact_markup = build_impact_markup($impact_result);

		// MARKUP: SOAPBOX, CORNER HERO, IMPACT STORIES
		echo '<section class="outer-container mentions">';
		if ($show_soap && $show_corner) {
			assemble_mentions_share_space($mentions_group, $impact_markup);
		} else {
			assemble_mentions_full_width($mentions_group, $impact_markup);
		}
		echo '</section>';
	?>

	<!-- THREATS TO PRESS RIBBON -->
	<?php
		$threats_result = get_threats_to_press_data();
		$threats_parts = build_threat_parts($threats_result);
		assemble_threats_to_press_ribbon($threats_parts);
	?>

	<!-- NETWORK ENTITY LIST -->
	<?php
		// $entity_placement can be ["entity-main" | "entity-side"]
		$entity_data = get_entity_data("entity-main");
	?>

	<!-- Quotation -->
	<div class="outer-container">
		<div class="grid-container">
			<?php
				$q = getRandomQuote('allEntities', $postIDsUsed);
				if ($q) {
					$postIDsUsed[] = $q["ID"];
					outputQuote($q);
				}
			?>
		</div>
	</div><!-- Quotation -->

</main>

<?php get_footer(); ?>
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


// USAGM NEWS
// GET RECENT POSTS, SEPARATE INTO MANAGEABLE ARRAYS FOR PLACEMENT
$recent_posts = get_recent_posts(3);
$recent_post_counter = 0;
$feature_recent_post = '';
$secondary_recent_posts = array();

foreach ($recent_posts as $x) {
	if ($recent_post_counter == 0) {
		$feature_recent_post = $x;
	} else {
		$secondary_recent_posts[] = $x;
	}
	$recent_post_counter++;
}


/*** store a handful of page links that we'll use in a few places ***/
$impactPermalink = get_permalink( get_page_by_path('our-work/impact-and-results'));
$impactPortfolioPermalink = get_permalink( get_page_by_path('our-work/impact-and-results/impact-portfolio'));

/*** add any posts from custom fields to our array that tracks post IDs that have already been used on the page ***/
$postIDsUsed = array();

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

<main id="main" class="site-content" role="main">

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

	<?php // USAGM NEWS ?>
	<section class="outer-container">
		<div class="grid-container">
			<h2 class="new_heading"><a href="<?php echo get_permalink(get_page_by_path('news-and-information')); ?>">USAGM News</a></h2>
		</div>

		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<?php
							$featured_post = build_vertical_post_main($feature_recent_post);
							echo $featured_post;
						?>
					</div>
					<!-- <div class="side-column divider-left"> -->
					<div class="side-column">
						<?php
							foreach($secondary_recent_posts as $cur_secondary_post) {
								$secondary_post_element = build_post_aside($cur_secondary_post);
								echo $secondary_post_element;
							}
						?>
					</div>
				</div>
			</div>
		</div><!-- END .grid-container -->
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
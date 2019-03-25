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

// USAGM NEWS
// GET RECENT POSTS, SEPARATE INTO MANAGEABLE ARRAYS FOR PLACEMENT
$recent_posts = get_recent_posts(3);
$recent_post_counter = 0;
$feature_recent_post = '';
$secondary_recent_posts = array();

foreach ($recent_posts['posts'] as $cur_recent_post) {
	if ($recent_post_counter == 0) {
		$feature_recent_post = $cur_recent_post;
	} else {
		$secondary_recent_posts[] = $cur_recent_post;
	}
	$recent_post_counter++;
}


// IMPACT STORIES LINKS
$impactPermalink = get_permalink( get_page_by_path('our-work/impact-and-results'));
$impactPortfolioPermalink = get_permalink( get_page_by_path('our-work/impact-and-results/impact-portfolio'));

// THREATS TO PRESS DATA
$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
$threatsPermalink = get_permalink(get_page_by_path('threats-to-press'));
$randomFeaturedThreatsID = false;
if ($threatsToPressPost) {
	$randKey = array_rand($threatsToPressPost);
	$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
}

get_header();
echo '<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700" rel="stylesheet">';
?>

<?php
	$banner_result = get_homepage_banner_data();

	$banner_markup  = '<div class="full-width-banner">';
	$banner_markup .= 	'<div class="banner-image" ';
	$banner_markup .= 	'style="background-image: url(' . $banner_result['image_source'] . '); background-position: ' . $banner_result['position'] . '">';
	$banner_markup .= 	'</div>';
	$banner_markup .= 	'<p class="banner-caption">';
	$banner_markup .= 		$banner_result['caption'];
	$banner_markup .= 	'</p>';
	$banner_markup .= '</div>';
	echo $banner_markup;
?>

<main id="main" class="site-content" role="main">

	<div class="outer-container" id="mission">
		<div class="grid-container">
		<?php
			$settings_result = get_site_settings_data();

			$mission  = '<p class="lead-in">';
			$mission .= 	$settings_result['intro_content'];
			$mission .= '</p>';
			echo $mission;
		?>
		</div>
	</div>

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
							$featured_post = build_vertical_article($feature_recent_post);
							echo $featured_post;
						?>
					</div>
					<div class="side-column divider-left">
						<?php
							foreach($secondary_recent_posts as $cur_secondary_post) {
								$secondary_post_element = build_image_title_article($cur_secondary_post);
								echo $secondary_post_element;
							}
						?>
					</div>
				</div>
			</div>
			<nav class="navigation posts-navigation bbg__navigation__pagination" role="navigation">
				<h2 class="screen-reader-text">Recent Posts Navigation</h2>
				<div class="nav-links">
					<div class="nav-previous">
						<a href="<?php echo get_permalink(get_page_by_path('news-and-information')) ?>" >Previous posts</a>
					</div>
				</div>
			</nav>
		</div>
	</section>

	
	<?php
		// SOAPBOX
		$soapbox_result = get_soapbox_data();
		$soapbox_parts = build_soapbox_parts($soapbox_result);

		if (!empty($soapbox_parts)) {
			$soapbox  = '<div class="outer-container">';
			$soapbox .= 	'<div class="grid-container soapbox ' . $soapbox_parts['class'] . '">';
			if (!empty($soapbox_parts['image'])) {
				$soapbox .= 	'<div class="soapbox-image-side">';
				$soapbox .= 		$soapbox_parts['image'];
				$soapbox .= 	'</div>';
				$soapbox .= 	'<div class="soapbox-content-side">';
			}
			$soapbox .= 		$soapbox_parts['heading'];
			$soapbox .= 		$soapbox_parts['title'];
			$soapbox .= 		$soapbox_parts['content'];
			if (!empty($soapbox_parts['image'])) {
				$soapbox .= 	'</div>';
			}
			$soapbox .= 	'</div>';
			$soapbox .= '</div>';
			echo $soapbox;
		}
	?>

	<?php
		// IMPACT STORIES AND EVENTS
		$impact_option = get_field('corner_hero_toggle', 'options');
		$corner_hero_label = get_field('corner_hero_label', 'options');
		$corner_hero_data = get_corner_hero_data();
		$impact_page_page = 'our-work/impact-and-results/impact-portfolio/';

		if ($impact_option == 'on') {
			$impact_result = get_impact_stories_data(1);
		}
		else {
			$impact_result = get_impact_stories_data(2);
		}

		if ($impact_option == 'on') {
			$impact_and_events  = '<section class="outer-container">';
			$impact_and_events .= 	'<div class="grid-container sidebar-grid--large-gutter">';
			$impact_and_events .= 		'<div class="nest-container">';
			$impact_and_events .= 			'<div class="inner-container">';
			$impact_and_events .= 				'<div class="main-column">';
			$impact_and_events .= 					'<h2><a href="' . get_permalink(get_page_by_path($impact_page_page)) . '">Impact Stories</a></h2>';
			foreach ($impact_result as $impact_post_id) {
				$impact_post = get_post($impact_post_id);
				$impact_and_events .= build_vertical_article($impact_post);
			}
			$impact_and_events .= 				'</div>';
			$impact_and_events .= 				'<div class="side-column divider-left">';
			$impact_and_events .= 					'<h2>' . $corner_hero_label . '</h2>';
			$impact_and_events .= 					build_vertical_article($corner_hero_data);
			$impact_and_events .= 				'</div>';
			$impact_and_events .= 			'</div>';
			$impact_and_events .= 		'</div>';
			$impact_and_events .= 	'</div>';
			$impact_and_events .= '</section>';
			echo $impact_and_events;
		} else {
			$impacts_only  = '<section class="outer-container">';
			$impacts_only .= 	'<div class="grid-container">';
			$impacts_only .= 		'<h2><a href="' . get_permalink(get_page_by_path($impact_page_page)) . '">Impact Stories</a></h2>';
			$impacts_only .= 	'</div>';
			foreach ($impact_result as $impact_post_id) {
				$impact_post = get_post($impact_post_id);

				$impacts_only .= '<div class="grid-half">';
				$impacts_only .= 	build_vertical_article($impact_post);
				$impacts_only .= '</div>';
			}
			$impacts_only .= 	'</div>';
			$impacts_only .= '</section>';
			echo $impacts_only;
		}
	?>

	<!-- THREATS TO PRESS RIBBON -->
	<?php		
		$threat_article_list = get_threats_to_press_posts($recent_posts['used_posts']);

		$threat_structure  = '<section class="threats-box" id="homepage-threats">';
		$threat_structure .= 	'<div class="outer-container">';
		$threat_structure .= 		'<div class="grid-half" id="threats-main-column">';
		$threat_structure .= 			'<h2><a href="' . get_permalink(get_page_by_path('threats-to-press-2')) . '">Threats to Press</a></h2>';
		$threat_structure .= 			'<article>';
		$threat_structure .= 				'<div class="article-image">';
		$threat_structure .= 					'<a href="' . get_the_permalink($threat_article_list[0]) . '">';
		$threat_structure .= 						'<img src="' . get_the_post_thumbnail_url($threat_article_list[0], 'large') . '" alt="Image link to ' . get_the_title($threat_article_list[0]) . ' post">';
		$threat_structure .= 					'</a>';
		$threat_structure .= 				'</div>';
		$threat_structure .= 				'<div class="article-info">';
		$threat_structure .= 					'<h4><a href="' . get_the_permalink($threat_article_list[0]) . '">' . get_the_title($threat_article_list[0]) . '</a></h4>';
		$threat_structure .= 				'</div>';
		$threat_structure .= 			'</article>';
		$threat_structure .= 		'</div>';
		$threat_structure .= 		'<div class="grid-half" id="threats-side-column">';
		$secondary_threats = array_shift($threat_article_list);
		foreach ($threat_article_list as $recent_threat) {
			$threat_structure .= 		'<article>';
			$threat_structure .= 			'<h4><a href="' . get_the_permalink($recent_threat) . '">' . get_the_title($recent_threat) . '</a></h4>';
			$threat_structure .= 		'</article>';
		}
		$threat_structure .= 		'</div>';
		$threat_structure .= 	'</div>';
		$threat_structure .= '</section>';
		echo $threat_structure;
	?>

	<!-- NETWORK ENTITY LIST -->
	<?php
		// $entity_placement can be ["entity-main" | "entity-side"]
		$entity_data = get_entity_data("entity-main");
	?>

	<?php // QUOTE ?>
	<div class="outer-container">
		<div class="grid-container">
			<?php
				$quote_result = getRandomQuote('allEntities', $postIDsUsed);
				if ($quote_result) {
					$postIDsUsed[] = $quote_result["ID"];
					output_quote($quote_result);
				}
			?>
		</div>
	</div>

</main>

<?php get_footer(); ?>
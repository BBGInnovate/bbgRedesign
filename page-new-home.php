<?php
/**
 * @package bbgRedesign
  template name: New Home
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

foreach ($recent_posts as $x) {
	if ($recent_post_counter == 0) {
		$feature_recent_post = $x;
	} else {
		$secondary_recent_posts[] = $x;
	}
	$recent_post_counter++;
}


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

<main id="main" class="site-content" role="main">

	<div id="new-home-test">
		
		<?php
			$banner_result = get_homepage_banner_data();

			$banner_markup .= '<div class="new_homepage_banner" ';
			$banner_markup .= 	'style="background-image: url(' . $banner_result['image_source'] . '); background-position: ' . $banner_result['position'] . '">';
			$banner_markup .= '</div>';
			echo $banner_markup;
		?>

		<div class="outer-container" id="home-about">
			<div class="grid-container">
			<?php
				$settings_result = get_site_settings_data();

				$mission  = '<p class="new-home-lead-in">';
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
								$featured_post = build_vertical_post_main($feature_recent_post);
								echo $featured_post;
							?>
						</div>
						<div class="side-column divider-left">
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

	</div> <!-- END #new-home-test -->
</main>

<?php get_footer(); ?>
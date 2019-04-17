<?php

/**
 * The custom page for the Burke Awards.
 * It includes:
 *      - the lead image
 *      - the blurb
 *      - three randomly selected winners
 *      - david burke ribbon
 *      - a quote from John Lansing
 *
 * @package bbgRedesign
  template name: Burke Awards
 */

require 'inc/bbg-functions-assemble.php';

/******* BEGIN BURKE AWARDS ****/

function getBurkeImage() {
	return array(
		'imageID' => 37332,
		'imageCutline' => '', //'imageCutline' => 'Burke Awards Logo',
		'bannerAdjustStr' => 'center center'
	);
}
$bannerText = "David Burke Awards";
$bannerLogo = "/wp-content/media/2017/07/burkeDemo.jpg";
$siteIntroContent = "The David Burke Awards are named after David W. Burke, founding chairman of the Broadcasting Board of Governors and leader for its first three years. The Burke Awards are presented annually since 2001 to recognize the courage, integrity, and professionalism of journalists with the BBG networks.";
$burkeBioLink = "/who-we-are/our-leadership/board/david-w-burke/";
$burkeBioImage = get_template_directory_uri() . '/img/david-burke-profile-crop.jpg';
$activeYear = 2017;  //in theory we could let the user pick this

/******* END BURKE AWARDS ****/

/*** output the standard header ***/
get_header();
echo '<style>.bbg__main-navigation .menu-usagm-container {background-color: rgba(228, 235, 236, 0.95);}</style>';
echo '<style>.bbg__main-navigation ul li ul li:hover {background-color: #d7e1e2;}</style>';
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main role="main">

	<div class="outer-container">
		<div class="grid-container">
		<?php
			echo '<p class="lead-in">';
			echo 	$siteIntroContent;
			echo '</p>';
		?>
		</div>
	</div>

	<div class="outer-container" id="winners">
		<div class="grid-container">
			<h4>Meet the winners</h3>
			<p>The BBG networks nominates an exemplary journalist, production team, bureau, or language service for work done in the previous calendar year.</p>
			<p><a href="/burke-awards/burke-awards-archive/2017-winners/" class="bbg__kits__intro__more--link" style="float: right; margin: 1rem 0;">View the complete list of <?php echo $activeYear ?> honorees »</a></p>
		</div>

		<div class="grid-container">
			<?php
				// BEGIN: Create an array of three random IDs of burke candidate winners from this year
				$counter = 0;
				$qParams = array(
					'post_type' => 'burke_candidate',
					'meta_query' => array(
						array(
							'key' => 'burke_year_of_eligibility',
							'value' => $activeYear,
							'compare' => '='
						)
					)
				);
				$custom_query = new WP_Query($qParams);
				$counter = 0;
				$allCandidateIDs = array();
				while ($custom_query -> have_posts())  {
					$custom_query -> the_post();
					$allCandidateIDs [] = get_the_ID();
				}
				shuffle($allCandidateIDs);
				$randomCandidateIDs = array_slice($allCandidateIDs, 0, min(3, count($allCandidateIDs)));
				wp_reset_query();
				// END: Create an array of three random IDs of burke candidate winners from this year

				// BEGIN: Query and display our three burke candidates
				$qParams = array(
					'post_type' => 'burke_candidate',
					'post__in' => $randomCandidateIDs
				);
				$custom_query = new WP_Query($qParams);
				$counter = 0;
				while ($custom_query -> have_posts()) {
					$custom_query -> the_post();
					$counter++;
				}
				wp_reset_query();
			?>
		</div>
	</div>

	<?php echo do_shortcode('[smartslider3 slider=3]'); ?>
	
	<div class="outer-container" id="page-sections">
		<section class="grid-container bbg__kits__section--row">
			<div align="right">
				<a href="/burke-awards/burke-awards-archive/" class="bbg__kits__intro__more--link">View full winner archive »</a>
			</div>
		</section>
	</div>
	
	<div class="bbg__ribbon inner-ribbon bbg__ribbon--thin">
		<div class="outer-container">
			<div class="side-content-container">
				<img src="<?php echo $burkeBioImage; ?>" alt="Burke bio image">
			</div>
			<div class="main-content-container">
				<h2 class="section-header">BBG History</h2>
				<h4><a href="<?php echo $burkeBioLink; ?>">David Burke</a></h4>
				<p>David W. Burke was named to the first Broadcasting Board of Governors (BBG) by President Clinton in 1995 and served as its first chairman. <a href="<?php echo $burkeBioLink; ?>" class="bbg__kits__intro__more--link">Learn More »</a></p>
			</div>
		</div>
	</div>
	
	<div class="outer-container">
		<div class="grid-container">
			<div class="usagm-quotation">
				<img class="quote-image" src="http://dev.usagm.com/wp-content/uploads/2016/07/john_lansing_ceo-sq.jpg" class="bbg__quotation-attribution__mugshot" alt="John Lansing image">
				<h4 class="quote-text">These journalists have exemplified the definition of bravery and courage by risking their lives to report from some of the most dangerous places in the world.</h4>
				<p class="quote-byline">
					John Lansing<br>
					<span class="occupation">BBG CEO and Director</span>
				</p>
			</div>
		</div>
	</div>

</main>

<div id="secondary" class="widget-area" role="complementary">
</div><!-- #secondary .widget-area -->

<?php get_footer(); ?>
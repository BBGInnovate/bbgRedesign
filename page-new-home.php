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

// THREATS TO PRESS DATA
$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
$threatsPermalink = get_permalink(get_page_by_path('threats-to-press'));
$randomFeaturedThreatsID = false;
if ($threatsToPressPost) {
	$randKey = array_rand($threatsToPressPost);
	$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
}

get_header();
?>

<main id="main" class="site-content bbg-home-main" role="main">

	<section class="outer-container" id="home-about">
		<h1 class="header-outliner">About USAGM</h1>
		<div class="grid-container">
		<?php
			$settings_result = get_site_settings_data();

			$mission  = '<p class="new-home-lead-in">';
			$mission .= 	$settings_result['intro_content'];
			$mission .= '</p>';
			echo $mission;
		?>
		</div>
	</section>

	<div id="network-entity-container">
		<section class="outer-container">
			<div class="all-entities">
				<!-- DYNAMIC -->
				<div class="network-entity-chunk">
					<div class="inner-entity entity-image entity-voa"></div>
					<div class="inner-entity entity-overlay entity-voa"></div>
					<div class="entity-title entity-voa"></div>
				</div>
				<!-- DYNAMIC -->
				<!-- THESE BELOW GET DELETED -->
				<div class="network-entity-chunk">
					<div class="inner-entity entity-image entity-rferl"></div>
					<div class="inner-entity entity-overlay entity-rferl"></div>
					<div class="entity-title entity-rferl"></div>
				</div>
				<div class="network-entity-chunk">
					<div class="inner-entity entity-image entity-ocb"></div>
					<div class="inner-entity entity-overlay entity-ocb"></div>
					<div class="entity-title entity-ocb"></div>
				</div>
				<div class="network-entity-chunk">
					<div class="inner-entity entity-image entity-rfa"></div>
					<div class="inner-entity entity-overlay entity-rfa"></div>
					<div class="entity-title entity-rfa"></div>
				</div>
				<div class="network-entity-chunk">
					<div class="inner-entity entity-image entity-mbn"></div>
					<div class="inner-entity entity-overlay entity-mbn"></div>
					<div class="entity-title entity-mbn"></div>
				</div>
				<!-- THESE ABOVE GET DELETED -->
			</div>
		</section>
	</div>

	<!-- USAGM NEWS -->
	<section class="outer-container">
		<h1 class="header-outliner">USAGM News</h1>
		<div class="grid-container">
			<h2><a href="<?php echo get_permalink(get_page_by_path('news-and-information')); ?>">USAGM News</a></h2>
		</div>

		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<!-- TO BE DYNAMIC -->
						<article class="article-inline">
							<div class="nest-container">
								<div class="inner-container">
									<div class="large-column">
										<div class="post-image"></div>
									</div>
									<div class="small-column">
										<h4>Pakistan and Information Warfare</h4>
										<p class="aside date-meta">October 9, 2018</p>
										<p class="excerpt">Russia has been rocked by a series of protests in the year-long run-up to the presidential elections in March 2018. While state television has ignored such events, Current Time has delivered live coverage to its TV and online audiences… <span class="new-learn-more">Read More</span></p>
									</div>
								</div><!-- END .inner-container -->
							</div><!-- END .nest-container -->
						</article>
						<!-- END DYNAMIC -->
						<article class="article-inline">
							<div class="nest-container">
								<div class="inner-container">
									<div class="large-column">
										<div class="post-image"></div>
									</div>
									<div class="small-column">
										<h4>Pakistan and Information Warfare</h4>
										<p class="aside date-meta">October 9, 2018</p>
										<p class="excerpt">Russia has been rocked by a series of protests in the year-long run-up to the presidential elections in March 2018. While state television has ignored such events, Current Time has delivered live coverage to its TV and online audiences… <span class="new-learn-more">Read More</span></p>
									</div>
								</div><!-- END .inner-container -->
							</div><!-- END .nest-container -->
						</article>
					</div>
					<div class="side-column divider-left">

						<!-- TO BE DYNAMIC -->
						<article class="article-block">
							<div class="nest-container">
								<div class="inner-container">
									<div class="post-image-slot">
										<div class="post-image"></div>
									</div>
									<div class="article-descripiton">
										<h4>Pakistan and Information Warfare</h4>
										<p class="aside date-meta">October 9, 2018</p>
									</div>
								</div>
							</div>
						</article>
						<!-- END DYNAMIC -->
						<article class="article-block">
							<div class="nest-container">
								<div class="inner-container">
									<div class="post-image-slot">
										<div class="post-image"></div>
									</div>
									<div class="article-descripiton">
										<h4>Pakistan and Information Warfare</h4>
										<p class="aside date-meta">October 9, 2018</p>
									</div>
								</div>
							</div>
						</article>
					</div>
				</div><!-- END .inner-container -->
			</div><!-- END .nest-container -->
		</div><!-- END .grid-container -->

	</section><!-- END USAGM NEWS -->

</main>

<?php get_footer(); ?>
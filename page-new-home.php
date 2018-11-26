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
echo '<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700" rel="stylesheet">';
?>

<main id="main" class="site-content bbg-home-main" role="main">
	<div id="new-home-test">
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

		<section class="outer-container ribbon-banner soapbox">
			<div class="ribbon-image" style="background-image: url('http://dev.usagm.com/wp-content/uploads/2015/09/John-Lansing_s.jpg')"></div>
			<div class="ribbon-copy">
				<h2>From the CEO</h2>
				<h4>Statement from CEO John F. Lansing on agency rebrand</h4>
				<p>We recognize the overdue need to communicate the evolving, global scope of our work as well as our renewed, urgent focus on the agency’s global priorities, which reflect U.S. national security and public diplomacy interests... <span class="new-learn-more">Read More</span></p>
			</div>
		</section>

		<!-- USAGM NEWS -->
		<section class="outer-container">
			<h1 class="header-outliner">USAGM News</h1>
			<div class="grid-container">
				<h2><a href="<?php echo get_permalink(get_page_by_path('news-and-information')); ?>">Impact Stories</a></h2>
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
						</div>
						<div class="side-column divider-left">

							<!-- TO BE DYNAMIC -->
							<article class="corner-hero">
								<div class="nest-container">
									<div class="inner-container">
										<div class="corner-hero-image"></div>
										<div class="corner-hero-copy">
											<h2>Events</h2>
											<h4>Board Meeting, September 5</h4>
											<p>The Broadcasting Board of Governors will meet at it headquarters in Washington, D.C. More details will be added here as information becomes available.</p>
										</div>
									</div>
								</div>
							</article>
							
						</div>
					</div><!-- END .inner-container -->
				</div><!-- END .nest-container -->
			</div><!-- END .grid-container -->
		</section><!-- END IMPACT STORIES -->

		<!-- THREATS TO PRESS RIBBON -->
		<?php
			$threats_result = get_threats_to_press_data();
			$threats_parts = build_threat_parts($threats_result);
			assemble_threats_to_press_ribbon($threats_parts);
		?>

		<!-- QUOTE -->
		<section class="outer-container">
			<div class="grid-container" style="text-align: center;">
				<img src="https://www.usagm.gov/wp-content/uploads/2018/02/Vitaly-Mansky-100.jpg" style="border-radius: 50%;">
				<p style="font-family: 'Alegreya Sans'; font-size: 2.2rem; font-weight: 300; color: #3f7ad1; margin-bottom: 1.5rem;">“Television is not the only source of information. but it is the source in which many people have the greatest trust. Current Time is the only television in the world that tells these people, in Russian, the truth about the current state of affairs. And it is television that is the most effective way of gaining their trust.”</p>
				<p style="font-family: 'Alegreya Sans'; font-size: 1.8rem; color: #545454; ">Vitaly Mansky, Russian<br><span style="text-transform: uppercase; font-size: 1.6rem;">documentary filmmaker</span></p>
			</div>
		</section>

	</div> <!-- END #new-home-test -->
</main>

<?php get_footer(); ?>
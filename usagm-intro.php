<?php
/**
 * @package bbgRedesign
  template name: USAGM-Intro
 */
get_header();
?>
<div id="usagm-splash-wrapper">

<div class="logo-board">
	<div class="logo-container">
		<img class="logo" id="bbg-logo" src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/bbg-splash-logo.png">
	</div>
</div>

<div class="logo-copy" id="bbg-copy">
	<div class="logo-fader-up"></div>
	<div class="center-copy-block">
		<p>For 75 years we've been telling the truth. And the truth is, our name was outdated.</p>
		<p class="splash-down"><i class="fas fa-angle-down"></i></p>
	</div>
	<div class="logo-fader-down"></div>
</div>

<div class="logo-copy" id="usagm-copy">
	<div class="center-copy-block">
		<p>The Broadcasting Board of Governors is now the United States Agency for Global Media. New name, staying true to mission.</p>
		<p class="splash-down"><i class="fas fa-angle-down"></i></p>
	</div>
</div>

<div class="messaging-section">
	<div class="outer-container">
		<div class="grid-container">
			<div class="inner-container message">
				<div class="grid-container">
					<div class="message-image">
						<img src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/steve-video-img-crop.png" alt="">
					</div>
					<div class="message-text">
						<h4>What We Do</h4>
						<p><i class="fas fa-arrow-right"></i> Video: 30 seconds</p>
					</div>
				</div>
			</div>
			<div class="inner-container message">
				<div class="grid-container">
					<a class="messageing-link" href="http://dev.bbg.gov/who-we-are/">
						<div class="message-image">
							<img src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/who-we-are-video-still.png" alt="">
						</div>
						<div class="message-text">
							<h4>Who We Are</h4>
							<p><i class="fas fa-arrow-right"></i> Video: 60 seconds</p>
						</div>
					</a>
				</div>
			</div>
			<div class="inner-container message">
				<div class="grid-container">
					<div class="message-image">
						<img src="<?php echo get_template_directory_uri(); ?>/img/john_lansing_ceo-sq-200x200.jpg" alt="">
					</div>
					<div class="message-text">
						<h4>Where We Are Going</h4>
						<p><i class="fas fa-arrow-right"></i> Letter from CEO: 60 seconds</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="murrow-quote">
	<div class="outer-container">
		<div class="grid-container">
			<div class="ed-quote">
				<!-- OPENING QUOTE IN CSS TO BE PUSHED OUTSIDE OF TEXT BOX -->
				<p class="splash-quote">To be persuasive we must be believable; to be believable we must be credible, to be credible we must be truthful. It is as simple as that.”</p>
				<p class="splash-quote-by">&mdash;Edward R. Murrow</p>
				<p class="splash-quote-by">Director, US Information Agency <br>(precursor to USAGM) 1961&ndash;1964)</p>
			</div>
		</div>
	</div>
</div>

<div class="messaging-section" id="site-link">
	<div class="outer-container">
		<div class="grid-container">
			<h2><a style="font-size: 2rem; font-weight: 300;" class="site-brand" href="<?php echo get_home_url(); ?>/home">Continue to usagm.gov</a></h2>
		</div>
	</div>
</div>

</div> <!-- END #usagm-splash-wrapper -->
<?php get_footer(); ?>
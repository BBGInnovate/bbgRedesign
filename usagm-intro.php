<?php
/**
 * @package bbgRedesign
  template name: USAGM-Intro
 */
get_header();
?>
<div id="usagm-splash-wrapper">

<div class="logo-board locked">
	<div class="logo-container" id="bbg-logo-container">
		<img class="logo" id="bbg-logo" src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/bbg-splash-logo.png" alt="BBG Logo">
	</div>
</div>

<div id="page-mover">
	<div class="logo-copy" id="bbg-copy">
		<div class="logo-fader-up"></div>
		<div class="center-copy-block">
			<p>The Broadcasting Board of Governors is&nbsp;now&nbsp;the<br>United States Agency for Global Media.</p>
			<p class="splash-down"><i class="fas fa-angle-down"></i></p>
		</div>
	</div>

	<div class="logo-board">
		<div class="logo-container">
			<img class="logo" id="bbg-logo" src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/usagm-splash-logo.png" alt="BBG Logo">
		</div>
	</div>

	<div class="logo-copy" id="usagm-copy">
		<div class="center-copy-block">
			<p>New name, same mission.</p>
			<p class="splash-down"><i class="fas fa-angle-down"></i></p>
		</div>
	</div>
</div>

<div class="messaging-section">
	<div class="outer-container">
		<div class="grid-container">
			<div class="inner-container message">
				<div class="grid-container lightbox">
					<div class="lightbox-link" id="what-we-do">
						<div class="message-image">
							<img src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/what-we-do.jpg" alt="">
							<h4>What We Do</h4>
						</div>
					</div>
				</div>
			</div>
			<div class="inner-container message">
				<div class="grid-container lightbox">
					<div class="lightbox-link" id="who-we-are">
						<div class="message-image">
							<img src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/who-we-are.jpg" alt="">
							<h4>Who We Are</h4>
						</div>
					</div>
				</div>
			</div>
			<div class="inner-container message">
				<div class="grid-container lightbox">
					<div class="lightbox-link" id="ceo-message">
						<div class="message-image">
							<img src="<?php echo get_template_directory_uri(); ?>/img/usagm-splash/ceo-message.jpg" alt="">
						</div>
						<h4>Message from the CEO</h4>
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
				<p class="splash-quote">To be persuasive we must be believable; to be believable we must be credible, to be credible we must be truthful. It is as simple as that.”</p>
				<p class="splash-quote-by">&mdash;Edward R. Murrow</p>
				<p class="splash-quote-by">Director, 1961&ndash;1964<br>U.S. Information Agency (precursor to the USAGM)</p>
			</div>
		</div>
	</div>
</div>

<div class="messaging-section" id="site-link">
	<div class="outer-container">
		<div class="grid-container">
			<p style="font-size: 3.5rem; text-transform: uppercase; font-weight: 300; letter-spacing: 2px;"><a class="site-brand" href="<?php echo get_home_url(); ?>/home">Continue to usagm.gov</a></p>
		</div>
	</div>
</div>

</div> <!-- END #usagm-splash-wrapper -->
<?php get_footer(); ?>